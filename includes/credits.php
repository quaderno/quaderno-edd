<?php
/**
* Credit notes
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Create credit
*
* @since  1.4
* @param $data Arguments passed
* @return mixed|void
*/
function edd_quaderno_create_credit( $payment_id, $new_status, $old_status ) {
	global $edd_options;

	if( 'publish' != $old_status && 'revoked' != $old_status && 'inherit' != $old_status && 'edd_subscription' != $old_status ) {
		return;
	}

	if( 'refunded' != $new_status ) {
		return;
	}

	// Get the payment
	$payment = edd_get_payment($payment_id);

	// Return if a credit has already been issued for this order
	$transaction_id = $payment->get_meta( '_quaderno_credit_id' );
	if ( !empty( $transaction_id ) ) {
		return;
	}

	// Get metdata
	$metadata = $payment->get_meta();
	$tax_id = empty( $metadata['vat_number'] ) ? $metadata['tax_id'] : $metadata['vat_number'];

	// Get the taxes
	$tax = edd_quaderno_tax( $payment->address['country'], $payment->address['zip'], $payment->address['city'], $tax_id );

	// Add the credit params
	$transaction_params = array(
		'type' => 'refund',
		'issue_date' => current_time('Y-m-d'),
		'currency' => $payment->currency,
		'po_number' => $payment->number,
		'interval_count' => $payment->parent_payment == 0 ? '0' : '1',
		'notes' => apply_filters( 'quaderno_credit_notes', $tax->notes, $payment, $tax ),
		'processor' => 'edd',
		'processor_id' => $payment->get_meta( '_quaderno_processor_id' ),
		'payment' => array(
			'method' => get_quaderno_payment_method( $payment->gateway )
		),
		'custom_metadata' => array( 
			'processor_url' => add_query_arg( 'id', $payment_id, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) ) 
		)
	);

	// Add the contact
	$customer = new EDD_Customer( $payment->customer_id );
	$contact_id = $customer->get_meta( '_quaderno_contact' );
	if ( !empty( $contact_id ) ) {
		$transaction_params['customer'] = array(
			'id' => $contact_id
		);

	} else {
		if ( !empty( $metadata['business_name'] ) ) {
			$kind = 'company';
			$first_name = $metadata['business_name'];
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
			'contact_name' => $contact_name,
			'street_line_1' => $payment->address['line1'] ?: '',
			'street_line_2' => $payment->address['line2'] ?: '',
			'city' => $payment->address['city'],
			'postal_code' => $payment->address['zip'],
			'region' => edd_get_state_name( $payment->address['country'], $payment->address['state'] ),
			'country' => $payment->address['country'],
			'email' => $payment->email,
			'tax_id' => $tax_id,
			'processor' => 'edd',
			'processor_id' => strtotime($customer->date_created) . '_' . $payment->customer_id
		);
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
			'amount' => $cart_item['price'],
      'discount_rate' => $discount_rate,
			'tax' => $tax
		);

    array_push( $transaction_items, $new_item );
		$tags = array_merge( $tags, wp_get_object_terms( $cart_item['id'], 'download_tag', array( 'fields' => 'slugs' ) ) );
	}

  // Add items to transaction
  $transaction->items = $transaction_items;

	// Add download tags
	if ( count( $tags ) > 0 ) {
		$transaction->tags = implode( ',', $tags );
	}

	/**
	 * Filters the credit transaction before the credit is created.
	 * 
	 * @param \QuadernoTransaction $transaction The transaction object.
	 * @param \EDD_Payment         $payment     The EDD payment object.
	 */
	$transaction = apply_filters( 'quaderno_credit_transaction', $transaction, $payment );

	do_action( 'quaderno_credit_pre_create', $transaction, $payment );

	// Save the credit
	if ( $transaction->save() ) {
		$payment->update_meta( '_quaderno_credit_id', $transaction->id );
		$payment->add_note( 'Credit note created on Quaderno' );

		do_action( 'quaderno_credit_created', $transaction, $payment );

		// Send the credit
		if ( isset( $edd_options['autosend_receipts'] ) ) {
			$transaction->deliver();
		}
	}
}
add_action( 'edd_update_payment_status', 'edd_quaderno_create_credit', 999, 3 );

?>
