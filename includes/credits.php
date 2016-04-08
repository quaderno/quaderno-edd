<?php
/**
* Receipts
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

	if( 'publish' != $old_status && 'revoked' != $old_status ) {
		return;
	}

	if( 'refunded' != $new_status ) {
		return;
	}

	// Get the payment
	$payment = new EDD_Payment($payment_id);

	// Return if a credit has already been issued for this order
	$credit_id = get_post_meta( $payment_id, '_quaderno_credit_id', true );
	if ( !empty( $credit_id ) ) {
		return;
	}

	// Get the taxes
	$metadata = $payment->get_meta();
	$tax = edd_quaderno_tax( $payment->address['country'], $payment->address['zip'], $metadata['vat_number'] );

	// Add the credit params
	$credit_params = array(
		'issue_date' => date('Y-m-d'),
		'currency' => $payment->currency,
		'po_number' => $payment->number,
		'interval_count' => $payment->parent_payment == 0 ? '0' : '1',
		'notes' => $tax->notes,
		'processor' => ($payment->gateway == 'manual') ? 'edd' : $payment->gateway,
		'processor_id' => $payment->transaction_id
	);

	// Add the contact
	$customer_id = edd_get_payment_customer_id($payment_id);
	$contact_id = get_user_meta( $customer_id, '_quaderno_contact', true);
	if ( !empty( $contact_id ) ) {
		$credit_params['contact_id'] = $contact_id;
	} else {
		if ( !empty( $payment->get_meta()['vat_number'] ) ) {
			$kind = 'company';
			$first_name = $payment->first_name;
			$last_name = '';
		} else {
			$kind = 'person';
			$first_name = $payment->first_name;
			$last_name = $payment->last_name;
		}

		$credit_params['contact'] = array(
			'kind' => $kind,
			'first_name' => $first_name ?: 'EDD Customer',
			'last_name' => $last_name,
			'street_line_1' => $payment->address['line1'] ?: '',
			'street_line_2' => $payment->address['line2'] ?: '',
			'city' => $payment->address['city'],
			'postal_code' => $payment->address['zip'],
			'region' => $payment->address['state'],
			'country' => $payment->address['country'],
			'email' => $payment->email,
			'vat_number' => $payment->get_meta()['vat_number'],
			'tax_id' => $payment->get_meta()['tax_id'],
			'processor' => 'edd',
			'processor_id' => $payment->customer_id
		);
	}

	// Let's create the credit
  $credit = new QuadernoCredit($credit_params);

	// Add the credit items
	foreach ( $payment->cart_details as $item ) {
		$new_item = new QuadernoDocumentItem(array(
			'description' => $item['name'],
			'quantity' => $item['quantity'],
			'total_amount' => $item['price'],
			'discount_rate' => $item['discount'] / $item['subtotal'] * 100.0,
			'tax_1_name' => $tax->name,
			'tax_1_rate' => $tax->rate,
			'tax_1_country' => $tax->country
		));
		$credit->addItem( $new_item );
	}

	// Add the payment
	$payment = new QuadernoPayment(array(
		'date' => date('Y-m-d'),
		'amount' => $payment->total,
		'payment_method' => get_quaderno_payment_method( $payment->gateway )
	));
	$credit->addPayment( $payment );

	// Save the credit
	if ( $credit->save() ) {
		add_post_meta( $payment_id, '_quaderno_credit_id', $credit->id );

		// Send the credit
		if ( isset( $edd_options['autosend_receipts'] ) ) {
			$credit->deliver();
		}
	}
}
add_action( 'edd_update_payment_status', 'edd_quaderno_create_credit', 999, 3 );
