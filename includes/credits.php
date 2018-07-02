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
	$credit_id = $payment->get_meta( '_quaderno_credit_id' );
	if ( !empty( $credit_id ) ) {
		return;
	}

	// Get the taxes
	$metadata = $payment->get_meta();
	$tax = edd_quaderno_tax( $payment->address['country'], $payment->address['zip'], $metadata['vat_number'] );

	// Add the credit params
	$credit_params = array(
		'issue_date' => current_time('Y-m-d'),
		'currency' => $payment->currency,
		'po_number' => $payment->number,
		'interval_count' => $payment->parent_payment == 0 ? '0' : '1',
		'notes' => $tax->notes,
		'processor' => 'edd',
		'processor_id' => time() . '_' . $payment_id,
		'payment_method' => get_quaderno_payment_method( $payment->gateway )
	);

	// Add the contact
	$customer = new EDD_Customer( $payment->customer_id );
	$contact_id = $customer->get_meta( '_quaderno_contact' );
	if ( !empty( $contact_id ) ) {
		$credit_params['contact'] = array(
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

		$credit_params['contact'] = array(
			'kind' => $kind,
			'first_name' => $first_name ?: 'EDD Customer',
			'last_name' => $last_name,
			'contact_name' => $contact_name,
			'street_line_1' => $payment->address['line1'] ?: '',
			'street_line_2' => $payment->address['line2'] ?: '',
			'city' => $payment->address['city'],
			'postal_code' => $payment->address['zip'],
			'region' => $payment->address['state'],
			'country' => $payment->address['country'],
			'email' => $payment->email,
			'vat_number' => $metadata['vat_number'],
			'tax_id' => $metadata['tax_id'],
			'processor' => 'edd',
			'processor_id' => $payment->customer_id
		);
	}

	// Let's create the credit
  $credit = new QuadernoCredit($credit_params);

	// Add the credit item
	foreach ( $payment->cart_details as $cart_item ) {
		$product_name = $cart_item['name'];

		// Check if the item is a product variant
		$price_id = edd_get_cart_item_price_id( $cart_item );
		if ( edd_has_variable_prices( $cart_item['id'] ) && ! is_null( $price_id ) ) {
			$product_name .= ' - ' . edd_get_price_option_name( $cart_item['id'], $price_id, $payment->transaction_id );
		}

		// Calculate discount rate (if it exists)
    $discount_rate = 0;
		if ( $cart_item['discount'] > 0 ) {
			$discount_rate = $cart_item['discount'] / $cart_item['subtotal'] * 100;
		}

		$item = new QuadernoDocumentItem(array(
			'description' => $product_name,
			'quantity' => $cart_item['quantity'],
      'discount_rate' => $discount_rate,
			'total_amount' => $cart_item['price'],
			'tax_1_name' => $tax->name,
			'tax_1_rate' => $tax->rate,
			'tax_1_country' => $tax->country
		));

		$credit->addItem( $item );
	}

	// Save the credit
	if ( $credit->save() ) {
		$payment->update_meta( '_quaderno_credit_id', $credit->id );
		$payment->update_meta( '_quaderno_url', $credit->permalink );
		$customer->add_meta( '_quaderno_contact', $credit->contact->id );
		$payment->add_note( 'Credit note created on Quaderno' );

		// Send the credit
		if ( isset( $edd_options['autosend_receipts'] ) ) {
			$credit->deliver();
		}
	}
}
add_action( 'edd_update_payment_status', 'edd_quaderno_create_credit', 999, 3 );

?>
