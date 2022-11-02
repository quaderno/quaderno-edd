<?php
/**
* Invoices
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Create invoice
*
* @since  1.0
* @param  int $payment_id
* @return mixed|void
*/
function edd_quaderno_create_invoice($payment_id, $parent_id = 0) {
	global $edd_options;

	// Get the payment
	$payment = new EDD_Payment($payment_id);

	// Return if the invoice total is zero
	$skip = false; 
	if ( $payment->total == 0 || apply_filters('quaderno_invoice_skip', $skip, $payment) ) {
		return;
	}

	// Return if an invoice has already been issued for this order
	$transaction_id = $payment->get_meta( '_quaderno_invoice_id' );
	if ( !empty( $transaction_id ) ) {
		return;
	}

	if ( $parent_id != 0 ) {
		// if this is a recurring payment, we use metadata from the original payment
		$parent = new EDD_Payment( $parent_id );
		$parent_meta = $parent->get_meta();
		
		$meta = $payment->get_meta();

		$new_meta = array( 
			'tax_id' => empty( $parent_meta['vat_number'] ) ? $parent_meta['tax_id'] : $parent_meta['vat_number'], 
			'business_name' => isset( $parent_meta['business_name'] ) ? $parent_meta['business_name'] : ''
		);

		$merged_meta = array_merge( $meta, $new_meta );
		$payment->update_meta( '_edd_payment_meta', $merged_meta );
		$payment->save();

		$ip_address = $parent->ip;
	} else {
		$ip_address = $payment->ip;		
	}

	// Get metadata
	$metadata = $payment->get_meta();
	$tax_id = empty( $metadata['vat_number'] ) ? $metadata['tax_id'] : $metadata['vat_number'];
	$business_name = isset( $metadata['business_name'] ) ? $metadata['business_name'] : '';
	
	// Get the taxes
	$tax = edd_quaderno_tax( $payment->address['country'], $payment->address['zip'], $payment->address['city'], $tax_id );
	$tax = apply_filters( 'quaderno_invoice_tax', $tax, $payment );

	// Add the invoice params
	$transaction_params = array(
		'type' => 'sale',
		'currency' => $payment->currency,
		'po_number' => $payment->number,
		'interval_count' => $payment->parent_payment == 0 ? '0' : '1',
		'notes' => apply_filters( 'quaderno_invoice_notes', $tax->notes, $payment, $tax ),
		'processor' => 'edd',
		'processor_id' => get_current_blog_id() . '_' . current_time('timestamp') . '_' . $payment_id,
		'payment' => array(
			'method' => get_quaderno_payment_method( $payment->gateway ),
      'processor' => $payment->gateway,
      'processor_id' => $payment->transaction_id
		),
		'evidence' => array( 
			'billing_country' => $payment->address['country'], 
			'ip_address' => $ip_address 
		),
		'custom_metadata' => array( 
			'processor_url' => add_query_arg( 'id', $payment_id, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) ) 
		)
	);

	// Add the contact
	$customer = new EDD_Customer( $payment->customer_id );
	if ( !empty( $business_name ) ) {
		$kind = 'company';
		$first_name = $business_name;
		$last_name = '';
		$contact_name = implode( ' ', array($payment->first_name, $payment->last_name) );
	} else {
		$kind = 'person';
		$first_name = $payment->first_name;
		$last_name = $payment->last_name;
		$contact_name = '';
	}

	$transaction_params['customer'] = array(
		'kind' => $kind,
		'first_name' => $first_name ?: 'EDD Customer',
		'last_name' => $last_name,
		'contact_person' => $contact_name,
		'street_line_1' => $payment->address['line1'] ?: '',
		'street_line_2' => $payment->address['line2'] ?: '',
		'city' => $payment->address['city'],
		'postal_code' => $payment->address['zip'],
		'region' => edd_get_state_name( $payment->address['country'], $payment->address['state'] ),
		'country' => $payment->address['country'],
		'email' => $payment->email,
		'tax_id' => $tax_id,
		'processor' => 'edd'
	);

  $contact_id = $customer->get_meta( '_quaderno_contact', true );

  // The following code is quite hacky in order to skip Quaderno API's customer default initialization 
  // 2 reasons for contact_id to be empty:
  // - This is the first purchase ever for the user and the contact does not exist in Quaderno
  // - This is not the first purchase of the user but she does not have a contact_id saved yet in EDD metadata (until v1.24.3)

  $payments = $customer->get_payments();
  end($payments);
  $last_payment = prev($payments);  

  if(!empty($last_payment)){
    $last_payment_metadata = $last_payment->get_meta();
    $last_payment_business_name = isset( $last_payment_metadata['business_name'] ) ? $last_payment_metadata['business_name'] : '';
  }
  
  // If this is the customer's first purchase or their billing info has changed, then a new contact must be created
  if (empty( $last_payment ) ||
      $last_payment_business_name != $business_name ||
      $last_payment->first_name != $payment->first_name ||
      $last_payment->last_name != $payment->last_name){

    // Force the creation of a new contact
    $hashed_billing_name = md5(implode( '-', array($payment->first_name, $payment->last_name, $business_name)));
    // We don't want Quaderno to initialize the contact by processor_id
    $transaction_params['customer']['processor_id'] = $hashed_billing_name . '_' . $payment->customer_id;
  }

  if(!isset($transaction_params['customer']['processor_id'])){
    if ( empty( $contact_id ) ){
      // Use the processor_id the already exists in Quaderno in order to identify the contact until we have contact_id stored in EDD
      $transaction_params['customer']['processor_id'] = strtotime($customer->date_created) . '_' . $payment->customer_id;
    }else{
      // Use the contact_id to identify the contact
      $transaction_params['customer']['id'] = $contact_id;
      unset($transaction_params['customer']['first_name']);
      unset($transaction_params['customer']['last_name']);
    }
  }
	
	// Let's create the transaction
	$transaction = new QuadernoTransaction($transaction_params);

  // Calculate transaction items and tags
	$tags = array();
	$transaction_items = array();
	foreach ( $payment->cart_details as $cart_item ) {
		$download = new EDD_Download( $cart_item['id'] );

		// Calculate discount rate (if it exists)
		$discount_rate = 0;
		if ( $cart_item['discount'] > 0 ) {
			$discount_rate = $cart_item['discount'] / $cart_item['subtotal'] * 100;
		}

		$new_item = array(
			'product_code' => $download->post_name,
			'description' => get_quaderno_payment_description( $cart_item, $payment->transaction_id ),
			'quantity' => $cart_item['quantity'],
			'amount' => $cart_item['subtotal'] + $cart_item['tax'] - $cart_item['discount'],
			'discount_rate' => $discount_rate,
			'tax' => $tax
		);

    array_push( $transaction_items, $new_item );
		$tags = array_merge( $tags, wp_get_object_terms( $cart_item['id'], 'download_tag', array( 'fields' => 'slugs' ) ) );
	}

	// Calculate gateway fees
	foreach ( $payment->fees as $fee ) {
		$new_item = array(
			'description' => $fee['label'],
			'quantity' => 1,
			'amount' => $fee['amount'] * (1 + $tax->rate / 100.0),
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
	$transaction = apply_filters( 'quaderno_invoice_transaction', $transaction, $payment );

	do_action( 'quaderno_invoice_pre_create', $transaction, $payment );

	// Save the invoice and the location evidences
	if ( $transaction->save() ) {
    $payment->update_meta( '_quaderno_invoice_id', $transaction->id );
    $payment->update_meta( '_quaderno_processor_id', $transaction->processor_id );
		$payment->update_meta( '_quaderno_url', $transaction->permalink );
		$payment->add_note( 'Receipt created on Quaderno' );

    $customer->update_meta( '_quaderno_contact', $transaction->contact->id );

		do_action( 'quaderno_invoice_created', $transaction, $payment );

		// Send the invoice
		if ( isset( $edd_options['autosend_receipts'] ) ) {
			$transaction->deliver();
		}
	}

}
add_action( 'edd_complete_purchase', 'edd_quaderno_create_invoice', 999 );
add_action( 'edd_recurring_record_payment', 'edd_quaderno_create_invoice', 999, 2 );

/**
* Resend invoice
*
* @since  1.23
* @param  array $data Payment Data
* @return void
*/
function edd_quaderno_resend_invoice( $data ) {
	$payment_id = absint( $data['purchase_id'] );

	if( empty( $payment_id ) ) {
		return;
	}

	$payment = new EDD_Payment($payment_id);
	edd_quaderno_create_invoice( $payment_id, $payment->parent_payment );

	wp_redirect( add_query_arg( array( 'edd-message' => 'email_sent', 'edd-action' => false, 'purchase_id' => false ) ) );
	exit;
}
add_action( 'edd_resend_invoice', 'edd_quaderno_resend_invoice', 999 );
