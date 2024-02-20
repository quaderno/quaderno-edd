<?php
/**
* Credit notes
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015-2023, Carlos Hernandez
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
//function edd_quaderno_create_credit( $payment_id, $new_status, $old_status ) {
function edd_quaderno_create_credit( $order_id, $refund_id, $all_refunded ) {
	global $edd_options;

	// Get the order and the refund
	$order = edd_get_order( $order_id );
	$refund = edd_get_order( $refund_id );

	// Return if the original order hasn't generate an invoice on Quaderno
	if ( empty( edd_quaderno_get_payment_meta( $order_id, '_quaderno_invoice_id' ) ) ) {
		return;
	}

	// Return if a credit has already been issued for this order
	if ( !empty( edd_quaderno_get_payment_meta( $refund_id, '_quaderno_credit_id' ) ) ) {
		return;
	}

	// Get the taxes
	$tax = edd_quaderno_tax( $order->address->country,
													 $order->address->postal_code,
													 $order->address->city,
													 edd_quaderno_get_payment_meta( $order_id, 'tax_id' ) );

	// Let's create the transaction
  $transaction = new QuadernoTransaction(array(
		'type' => 'refund',
		'issue_date' => current_time('Y-m-d'),
		'customer' => array(
			'id' => edd_get_customer_meta( $order->customer_id, '_quaderno_contact', true )
		),
		'currency' => $order->currency,
		'po_number' => $order->number,
		'interval_count' => $order->parent_payment == 0 ? '0' : '1',
		'notes' => apply_filters( 'quaderno_credit_notes', $tax->notes, $order, $tax ),
		'processor' => 'edd',
		'processor_id' => edd_quaderno_get_payment_meta( $order_id, '_quaderno_processor_id' ),
		'payment' => array(
			'method' => get_quaderno_payment_method( $order->gateway ),
      'processor' => $order->gateway,
      'processor_id' => $order->get_transaction_id()
		),
		'custom_metadata' => array(
			'processor_url' => add_query_arg( 'id', $order_id, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) )
		)
	));

  // Calculate transaction items and tags
	$transaction_items = array();
	$tags = array();

	foreach ( $refund->items as $item ) {
		$download = new EDD_Download( $item->product_id );
		$sku = $download->get_sku();

		// Calculate discount rate (if it exists)
    $discount_rate = 0;
		if ( !empty( $item->discount )) {
			$discount_rate = $item->discount / $item->subtotal * 100;
		}

		$new_item = array(
			'product_code' => $sku != '-' ? $sku : '',
			'description' => $item->product_name,
			'quantity' => -$item->quantity,
			'amount' => -$item->total,
      'discount_rate' => -$discount_rate,
			'tax' => $tax
		);

    array_push( $transaction_items, $new_item );
		$tags = array_merge( $tags, wp_get_object_terms( $item->id, 'download_tag', array( 'fields' => 'slugs' ) ) );
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
	 * @param \EDD_Order           $refund      The EDD refund object.
	 */
	$transaction = apply_filters( 'quaderno_credit_transaction', $transaction, $refund );

	do_action( 'quaderno_credit_pre_create', $transaction, $refund );

	// Save the credit
	if ( $transaction->save() ) {
		edd_update_order_meta( $refund->id, '_quaderno_credit_id', $transaction->id );
		edd_update_order_meta( $refund->id, '_quaderno_url', $transaction->permalink );

		$payment = edd_get_payment( $order_id );
		$payment->add_note( 'Credit note created on Quaderno' );

		do_action( 'quaderno_credit_created', $transaction, $refund );

		// Send the credit
		if ( isset( $edd_options['autosend_receipts'] ) ) {
			$transaction->deliver();
		}
	}
}
add_action( 'edd_refund_order', 'edd_quaderno_create_credit', 999, 3 );

?>
