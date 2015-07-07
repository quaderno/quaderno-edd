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
function edd_quaderno_create_receipt($payment_id)
{
	global $edd_options;
	
	// Return if an invoice has already been issued for this order
	$invoice_id = get_post_meta( $payment_id, '_quaderno_invoice_id', true );
	if ( !empty( $invoice_id ) ) {
		return;
	}

	// Connect to Quaderno API
	QuadernoBase::init( $edd_options['edd_quaderno_token'], $edd_options['edd_quaderno_url'] );

	// Get the taxes
	$customer_info = edd_get_payment_meta_user_info( $payment_id );
	$tax = edd_quaderno_tax( $customer_info['address']['country'], $customer_info['address']['zip'], $_POST['tax_id'] );

	// Let's create the invoice
	$invoice = new QuadernoInvoice(array(
		'issue_date' => edd_get_payment_completed_date($payment_id),
		'currency' => edd_get_payment_currency_code($payment_id),
		'po_number' => $payment_id,
		'notes' => $tax->notes
	));
	
	// Add the contact
	$customer = EDD_Quaderno_DB::find( edd_get_payment_customer_id($payment_id) );
	if ( !empty( $customer ) ) {
		$contact = QuadernoContact::find( $customer->quaderno_id );
	}
	else {
		if ( !empty( $_POST['company'] ) )
		{
			$kind = 'company';
			$first_name = $_POST['company'];
			$last_name = '';
			$contact_name = $customer_info['first_name'].' '.$customer_info['last_name'];
		}
		else
		{
			$kind = 'person';
			$first_name = $customer_info['first_name'];
			$last_name = $customer_info['last_name'];
			$contact_name = '';
		}

		$contact = new QuadernoContact(array(
			'kind' => $kind,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'contact_name' => $contact_name,
			'street_line_1' => isset($customer_info['address']['line1']) ? '' : $customer_info['address']['line1'],
			'street_line_2' => isset($customer_info['address']['line2']) ? '' : $customer_info['address']['line2'],
			'city' => $customer_info['address']['city'],
			'postal_code' => $customer_info['address']['zip'],
			'region' => $customer_info['address']['state'],
			'country' => $customer_info['address']['country'],
			'email' => $customer_info['email'], 
			'tax_id' => $_POST['tax_id']
		));

		if ( $contact->save() ) {
			EDD_Quaderno_DB::save( edd_get_payment_customer_id($payment_id), $contact->id );
		}
	}
	$invoice->addContact( $contact );

	// Add the invoice items
	$items = edd_get_payment_meta_cart_details( $payment_id );
	foreach ( $items as $item ) {
	  $discounted_amount = edd_get_cart_item_discount_amount( $item );
		$new_item = new QuadernoItem(array(
			'description' => $item['name'],
			'quantity' => $item['quantity'],
			'unit_price' => $item['item_price'],
			'discount_rate' => $discounted_amount / $item['subtotal'] * 100.0,
			'tax_1_name' => $tax->name,
			'tax_1_rate' => $tax->rate
		));
		$invoice->addItem( $new_item );
	}

	// Add the payment
	$payment_method = edd_get_payment_gateway( $payment_id );
	$payment = new QuadernoPayment(array(
		'date' => edd_get_payment_completed_date( $payment_id ),
		'amount' => edd_get_payment_amount( $payment_id ),
		'payment_method' => 'credit_card'
	));
	$invoice->addPayment( $payment );
	
	// Save the invoice and the location evidences
	if ( $invoice->save() ) {
		add_post_meta( $payment_id, '_quaderno_invoice_id', $invoice->id );
		
		$evidence = new QuadernoEvidence(array(
			'document_id' => $invoice->id,
			'billing_country' => $contact->country,
			'ip_address' => edd_get_payment_user_ip($payment_id)
		));
		$evidence->save();
		
		if ( $edd_options['autosend_receipts'] ) $invoice->deliver();
	}
}
add_action( 'edd_complete_purchase', 'edd_quaderno_create_receipt' );
