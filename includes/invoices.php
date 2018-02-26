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
	if ( $payment->total == 0 ) {
		return;
	}

	// Return if an invoice has already been issued for this order
	$invoice_id = $payment->get_meta( '_quaderno_invoice_id' );
	if ( !empty( $invoice_id ) ) {
		return;
	}
	
	// Get the taxes
	$metadata = $payment->get_meta();
	$tax = edd_quaderno_tax( $payment->address['country'], $payment->address['zip'], $metadata['vat_number'] );

	// Add the invoice params
	$invoice_params = array(
		'issue_date' => $payment->date,
		'currency' => $payment->currency,
		'po_number' => $payment->number,
		'interval_count' => $payment->parent_payment == 0 ? '0' : '1',
		'notes' => $tax->notes,
		'processor' => ($payment->gateway == 'manual') ? 'edd' : $payment->gateway,
		'processor_id' => $payment->transaction_id ?: $payment->number,
		'payment_method' => get_quaderno_payment_method( $payment->gateway )
	);

	// Add the contact
	$customer = new EDD_Customer( $payment->customer_id );
	$contact_id = $customer->get_meta( '_quaderno_contact' );
	if ( !empty( $contact_id ) ) {
		$invoice_params['contact'] = array(
			'id' => $contact_id,
			'street_line_1' => $payment->address['line1'] ?: '',
			'street_line_2' => $payment->address['line2'] ?: '',
			'city' => $payment->address['city'],
			'postal_code' => $payment->address['zip'],
			'region' => $payment->address['state'],
			'vat_number' => $metadata['vat_number']
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

		$invoice_params['contact'] = array(
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

	// Let's create the invoice
	$invoice = new QuadernoIncome($invoice_params);

	// Add the invoice item
	foreach ( $payment->cart_details as $cart_item ) {
		$product_name = $cart_item['name'];
		$price_id = edd_get_cart_item_price_id( $cart_item );
		if ( edd_has_variable_prices( $cart_item['id'] ) && ! is_null( $price_id ) ) {
			$product_name .= ' - ' . edd_get_price_option_name( $cart_item['id'], $price_id, $payment->transaction_id );
		}

		$item = new QuadernoDocumentItem(array(
			'description' => $product_name,
			'quantity' => $cart_item['quantity'],
			'total_amount' => $cart_item['price'],
			'tax_1_name' => $tax->name,
			'tax_1_rate' => $tax->rate,
			'tax_1_country' => $tax->country
		));
		$invoice->addItem( $item );
	}

	// Add gateway fees
	foreach ( $payment->fees as $fee ) {
		$item = new QuadernoDocumentItem(array(
			'description' => $fee['label'],
			'quantity' => 1,
			'unit_price' => $fee['amount'],
			'tax_1_name' => $tax->name,
			'tax_1_rate' => $tax->rate,
			'tax_1_country' => $tax->country
		));
		$invoice->addItem( $item );
	}	

	// Save the invoice and the location evidences
	if ( $invoice->save() ) {
		$payment->update_meta( '_quaderno_invoice_id', $invoice->id );
		$customer->add_meta( '_quaderno_contact', $invoice->contact->id );

		// Save the location evidence
		$evidence = new QuadernoEvidence(array(
			'document_id' => $invoice->id,
			'billing_country' => $payment->address['country'],
			'ip_address' => $payment->ip
		));
		$evidence->save();

		// Send the invoice
		if ( isset( $edd_options['autosend_receipts'] ) ) {
			$invoice->deliver();
		}
	}

}
add_action( 'edd_complete_purchase', 'edd_quaderno_create_invoice', 999 );
add_action( 'edd_recurring_record_payment', 'edd_quaderno_create_invoice', 999, 2 );

// Merge cart items description
function get_cart_descriptions( $carry, $item ) {
	if ( is_null($carry) ) {
		$carry = $item['name'];
	}
	else {
		$carry = $carry . '<br>' . $item['name'];
	}
	return $carry;
}

?>
