<?php
/**
* Receipts
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2016-2023, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.6
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get payment method for Quaderno
 *
 * @param $gateway
 */
function get_quaderno_payment_method( $gateway ) {
  $method = '';
  switch( $gateway ) {
    case 'manual':
      $method = 'cash';
      break;
    case 'stripe':
      $method = 'credit_card';
      break;
    case 'braintree':
      $method = 'credit_card';
      break;
    case 'paypal':
    case 'paypal_commerce':
      $method = 'paypal';
      break;
    default:
      $method = 'other';
  }
  
  return apply_filters( 'quaderno_payment_method', $method, $gateway );
}

/**
 * Get item description for Quaderno from an order item
 *
 * @param $item
 */
function get_quaderno_item_description( $order_item ) {
  $download = edd_get_download( $order_item->product_id );
  $name = $download->get_name();

  // Check if the item is a product variant
  if ( $download->has_variable_prices() && ! empty( $order_item->price_id ) ) {
    $variation_name = edd_get_price_option_name( $download->ID, $order_item->price_id );
    if ( ! empty( $variation_name ) ) {
      $name .= ' - ' . $variation_name;
    }
  }

  // Check if the item is an ugrade or a renewal (for recurring payments)
  $payment   = edd_get_payment( $order_item->order_id );
  $cart_item = $payment->cart_details[$order_item->cart_index];

  if ( ! empty( $cart_item['item_number']['options']['is_upgrade'] ) ) {
    $name .= sprintf(' (%s)', esc_html__('Upgrade'));
  } elseif ( ! empty( $cart_item['item_number']['options']['is_renewal'] ) ) {
    $name .= sprintf(' (%s)', esc_html__('Renewal'));
  }

  return apply_filters( 'quaderno_payment_description', $name, $order_item );
}

?>
