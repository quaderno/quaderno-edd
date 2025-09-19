<?php
/**
* Invoices
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015-2023, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Create invoice
*
* @since  1.0
* @param  int $order_id
* @return mixed|void
*/
function edd_quaderno_create_invoice( $order_id ) {
	global $edd_options;

	// Get the order
	$order = edd_get_order($order_id);

	// Return if the invoice total is zero
	$skip = false; 
	if ( $order->total == 0 || apply_filters('quaderno_invoice_skip', $skip, $order) ) {
		return;
	}

	// Return if an order has already been issued for this order
	if ( !empty( edd_quaderno_get_payment_meta( $order_id, '_quaderno_invoice_id' ) ) ) {
		return;
	}

	// Get the billing address, the tax ID & the business name
	$address = $order->get_address();
	$tax_id = edd_quaderno_get_payment_meta( $order_id, 'tax_id' );
	$business_name = edd_quaderno_get_payment_meta( $order_id, 'business_name' );
	
	// Get the taxes
	$tax = edd_quaderno_tax( $address->country, $address->postal_code, $address->city, $tax_id );
	$tax = apply_filters( 'quaderno_invoice_tax', $tax, $order );

	// Add the invoice params
	$transaction_params = array(
		'type' => 'sale',
		'currency' => $order->currency,
		'po_number' => $order->number,
		'interval_count' => $order->parent_payment == 0 ? '0' : '1',
		'notes' => apply_filters( 'quaderno_invoice_notes', $tax->notes, $order, $tax ),
		'processor' => 'edd',
		'processor_id' => get_current_blog_id() . '_' . current_time('timestamp') . '_' . $order_id,
		'payment' => array(
			'method' => get_quaderno_payment_method( $order->gateway ),
      'processor' => $order->gateway,
      'processor_id' => $order->transaction_id
		),
		'evidence' => array( 
			'billing_country' => $address->country,
			'ip_address' => $order->ip
		),
		'custom_metadata' => array( 
			'processor_url' => add_query_arg( 'id', $order_id, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) )
		)
	);

	// Add the contact
	$customer = edd_get_customer( $order->customer_id );
	if ( !empty( $business_name ) ) {
		$kind = 'company';
		$first_name = $business_name;
		$last_name = '';
		$contact_name = $customer->name ?: $address->name;
	} else {
		$kind = 'person';
		$first_name = $customer->first_name ?: $address->name;
		$last_name = $customer->last_name;
		$contact_name = '';
	}

	$transaction_params['customer'] = array(
		'kind' => $kind,
		'first_name' => $first_name ?: 'EDD Customer',
		'last_name' => $last_name,
		'contact_person' => $contact_name,
		'street_line_1' => $address->address ?: '',
		'street_line_2' => $address->address2 ?: '',
		'city' => $address->city,
		'postal_code' => $address->postal_code,
		'region' => edd_get_state_name( $address->country, $address->region ),
		'country' => $address->country,
		'email' => $order->email,
		'tax_id' => $tax_id,
		'processor' => 'edd'
	);

  $contact_id = $customer->get_meta( '_quaderno_contact', true );

  // The following code is quite hacky in order to skip Quaderno API's customer default initialization 
  // 2 reasons for contact_id to be empty:
  // - This is the first purchase ever for the user and the contact does not exist in Quaderno
  // - This is not the first purchase of the user but she does not have a contact_id saved yet in EDD metadata (until v1.24.3)

  $orders = $customer->get_orders();
  end($orders);
  $last_order = prev($orders);

  if( !empty( $last_order ) ){
    $last_order_business_name = edd_get_order_meta($last_order->id, 'business_name');
  }
  
  // If this is the customer's first purchase or their billing info has changed, then a new contact must be created
  if ( empty( $last_order ) ||
      $last_order_business_name != $business_name ||
      $last_order->address->name != $address->name ){

    // Force the creation of a new contact
    $hashed_billing_name = md5(implode( '-', array($address->name, $business_name)));

    // We don't want Quaderno to initialize the contact by processor_id
    $transaction_params['customer']['processor_id'] = $hashed_billing_name . '_' . $order->customer_id;
  }

  if(!isset($transaction_params['customer']['processor_id'])){
    if ( empty( $contact_id ) ){
      // Use the processor_id that already exists in Quaderno in order to identify the contact until we have contact_id stored in EDD
      $transaction_params['customer']['processor_id'] = strtotime($customer->date_created) . '_' . $order->customer_id;
    }else{
      // Use the contact_id to identify the contact
      $transaction_params['customer']['id'] = $contact_id;
      unset($transaction_params['customer']['first_name']);
    }
  }
	
	// Let's create the transaction
	$transaction = new QuadernoTransaction($transaction_params);

  // Calculate transaction items and tags
	$transaction_items = array();
	$tags = array();

	foreach ( $order->items as $item ) {
		$download = new EDD_Download( $item->product_id );
		$sku = $download->get_sku();

		// Calculate discount rate (if it exists)
    $discount_rate = 0;
		if ( !empty( $item->discount )) {
			$discount_rate = $item->discount / $item->subtotal * 100;
		}

		$new_item = array(
			'product_code' => $sku != '-' ? $sku : '',
			'description' => get_quaderno_item_description( $item ),
			'quantity' => $item->quantity,
			'amount' => round($item->total, 2),
      'discount_rate' => $discount_rate,
			'tax' => $tax
		);

    array_push( $transaction_items, $new_item );
		$tags = array_merge( $tags, wp_get_object_terms( $item->id, 'download_tag', array( 'fields' => 'slugs' ) ) );
	}

	// Calculate gateway fees
	$fees = $order->get_fees();
	foreach ( $fees as $fee ) {
		$new_item = array(
			'description' => $fee->description,
			'quantity' => 1,
			'amount' => round($fee->subtotal * ( 1 + $tax->rate / 100.0 ), 2),
			'tax' => $tax
		);

		array_push( $transaction_items, $new_item );
	}

  // Add items to transaction
  $transaction->items = $transaction_items;

	// Add download tags
	if ( count( $tags ) > 0 ) {
		$transaction->tags = implode( ',', $tags );
	}

	/**
	 * Filters the invoice transaction before the invoice is created.
	 * 
	 * @param \QuadernoTransaction $transaction The transaction object.
	 * @param \EDD_Payment         $payment     The EDD payment object.
	 */
	$transaction = apply_filters( 'quaderno_invoice_transaction', $transaction, $order );

	do_action( 'quaderno_invoice_pre_create', $transaction, $order );

	// Save the invoice and the location evidences
	if ( $transaction->save() ) {
		edd_update_order_meta( $order->id, '_quaderno_invoice_id', $transaction->id );
		edd_update_order_meta( $order->id, '_quaderno_processor_id', $transaction->processor_id );
		edd_update_order_meta( $order->id, '_quaderno_url', $transaction->permalink );

		$payment = edd_get_payment( $order_id );
		$payment->add_note( 'Receipt created on Quaderno' );

    $customer->update_meta( '_quaderno_contact', $transaction->contact->id );
    $customer->update_meta( 'tax_id', $tax_id );
    $customer->update_meta( 'business_name', $business_name );

		do_action( 'quaderno_invoice_created', $transaction, $payment );
	}

}
add_action( 'edd_complete_purchase', 'edd_quaderno_create_invoice', 999 );

/**
* Process recurring payment
*
* @since  1.31
* @param  int $order_id
* @param  int $parent_id
* @return void
*/
function edd_quaderno_process_recurring_payment( $order_id, $parent_id ) {
	global $edd_options;

	$order = edd_get_order($order_id);
	$customer = edd_get_customer( $order->customer_id );

	// get the tax ID and the business name from the customer's current data
	if( !empty($customer) ) {
		edd_add_order_meta( $order_id, 'tax_id', $customer->get_meta( 'tax_id' ) );
		edd_add_order_meta( $order_id, 'business_name', $customer->get_meta( 'business_name' ) );
	}

	// copy the IP address from the original order
	$parent_order = edd_get_order( $parent_id );

	if ( !empty($parent_order) ) {
		edd_update_order( $order_id, array( 'ip' => $parent_order->ip ) );
		edd_add_order_address(
			array(
        'order_id'    => $order_id,
        'name'        => $parent_order->address->name,
				'address'     => $parent_order->address->address,
				'address2'    => $parent_order->address->address2,
				'city'        => $parent_order->address->city,
				'region'      => $parent_order->address->region,
				'postal_code' => $parent_order->address->postal_code,
				'country'     => $parent_order->address->country
			)
		);
	}

	// generate the invoice
	edd_quaderno_create_invoice( $order_id );
}
add_action( 'edd_recurring_record_payment', 'edd_quaderno_process_recurring_payment', 999, 2 );

/**
* Resend invoice
*
* @since  1.23
* @param  array $data Payment Data
* @return void
*/
function edd_quaderno_resend_invoice( $data ) {
	$order_id = absint( $data['purchase_id'] );

	if( empty( $order_id ) ) {
		return;
	}

	$order = edd_get_order($order_id);
	edd_quaderno_process_recurring_payment( $order_id, $order->parent );

	wp_redirect( add_query_arg( array( 'edd-message' => 'email_sent', 'edd-action' => false, 'purchase_id' => false ) ) );
	exit;
}
add_action( 'edd_resend_invoice', 'edd_quaderno_resend_invoice', 999 );
