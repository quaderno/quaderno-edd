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

	// Return if a credit has already been issued for this order
	$credit_id = get_post_meta( $payment_id, '_quaderno_credit_id', true );
	if ( !empty( $credit_id ) ) {
		return;
	}

	// Connect to Quaderno API
	QuadernoBase::init( $edd_options['edd_quaderno_token'], $edd_options['edd_quaderno_url'] );

	// Get the taxes
	$customer_info = edd_get_payment_meta_user_info( $payment_id );
	$tax = edd_quaderno_tax( $customer_info['address']['country'], $customer_info['address']['zip'], $customer_info['tax_id'] );

	// Add the credit params
	$credit_params = array(
		'issue_date' => edd_get_payment_completed_date($payment_id),
		'currency' => edd_get_payment_currency_code($payment_id),
		'po_number' => $payment_id,
		'notes' => $tax->notes,
		'processor' => 'edd',
		'processor_id' => $payment_id
	);

	// Add the contact
	$customer_id = edd_get_payment_customer_id($payment_id);
	$contact_id = get_user_meta( $customer_id, '_quaderno_contact', true);
	if ( !empty( $contact_id ) ) {
		$credit_params['contact_id'] = $contact_id;
	} else {
		if ( !empty( $customer_info['company'] ) ) {
			$kind = 'company';
			$first_name = $customer_info['company'];
			$last_name = '';
			$contact_name = $customer_info['first_name'].' '.$customer_info['last_name'];
		} else {
			$kind = 'person';
			$first_name = $customer_info['first_name'];
			$last_name = $customer_info['last_name'];
			$contact_name = '';
		}

		$credit_params['contact'] = array(
			'kind' => $kind,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'contact_name' => $contact_name,
			'street_line_1' => isset($customer_info['address']['line1']) ? '' : $customer_info['address']['line1'],
			'street_line_2' => isset($customer_info['address']['line2']) ? '' : $customer_info['address']['line2'],
			'city' => $customer_info['address']['city'],
			'postal_code' => $customer_info['address']['zip'],
			'region' => $customer_info['address']['state'],
			'country' => $payment_data['address']['country'],
			'email' => $customer_info['email'], 
			'tax_id' => $customer_info['tax_id'],
			'processor' => 'edd',
			'processor_id' => $customer_id
		);
	}

	// Let's create the credit
  $credit = new QuadernoCredit($credit_params);

	// Add the credit items
	$items = edd_get_payment_meta_cart_details( $payment_id );
	foreach ( $items as $item ) {
	  $discounted_amount = edd_get_cart_item_discount_amount( $item );
		$new_item = new QuadernoDocumentItem(array(
			'description' => $item['name'],
			'quantity' => $item['quantity'],
			'unit_price' => $item['subtotal'] / $item['quantity'],
			'discount_rate' => $discounted_amount / $item['subtotal'] * 100.0,
			'tax_1_name' => $tax->name,
			'tax_1_rate' => $tax->rate,
			'tax_1_country' => $customer_info['address']['country']
		));
		$credit->addItem( $new_item );
	}

	// Add the payment
	$payment_method = edd_get_payment_gateway( $payment_id );
	$payment = new QuadernoPayment(array(
		'date' => edd_get_payment_completed_date( $payment_id ),
		'amount' => edd_get_payment_amount( $payment_id ),
		'payment_method' => 'credit_card'
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
