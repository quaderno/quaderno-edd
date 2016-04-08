<?php
/**
* Receipts
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

	// Return if an invoice has already been issued for this order
	$invoice_id = get_post_meta( $payment_id, '_quaderno_invoice_id', true );
	if ( !empty( $invoice_id ) ) {
		return;
	}

	// Get the taxes
	$metadata = $payment->get_meta();
	$tax = edd_quaderno_tax( $payment->address['country'], $payment->address['zip'], $metadata['vat_number'] );

	// Add the invoice params
	$invoice_params = array(
		'issue_date' => date('Y-m-d'),
		'currency' => $payment->currency,
		'po_number' => $payment->number,
		'interval_count' => $payment->parent_payment == 0 ? '0' : '1',
		'notes' => $tax->notes,
		'processor' => ($payment->gateway == 'manual') ? 'edd' : $payment->gateway,
		'processor_id' => $payment->transaction_id
	);

	// Add the contact
	$contact_id = get_user_meta( $payment->customer_id, '_quaderno_contact', true);
	if ( !empty( $contact_id ) ) {
		$invoice_params['contact_id'] = $contact_id;
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

		$invoice_params['contact'] = array(
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

	// Let's create the invoice
  $invoice = new QuadernoInvoice($invoice_params);

	// Add the invoice items
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
		$invoice->addItem( $new_item );
	}

	// Add the payment
	$payment = new QuadernoPayment(array(
		'date' => date('Y-m-d'),
		'amount' => $payment->total,
		'payment_method' => get_quaderno_payment_method( $payment->gateway )
	));
	$invoice->addPayment( $payment );

	// Save the invoice and the location evidences
	if ( $invoice->save() ) {
		add_post_meta( $payment_id, '_quaderno_invoice_id', $invoice->id );
		add_user_meta( $payment->customer_id, '_quaderno_contact', $invoice->contact->id );

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

?>
