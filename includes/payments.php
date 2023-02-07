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
      $method = 'paypal';
      break;
    default:
      $method = 'other';
  }
  
  return apply_filters( 'quaderno_payment_method', $method, $gateway );
}

/**
 * Get payment description for Quaderno from a cart item
 *
 * @param $item
 * @param $transaction_id
 */
function get_quaderno_payment_description( $item, $transaction_id ) {
  $download = new EDD_Download( $item['id'] );
  $product_name = $download->post_title;

  // Check if the item is a product variant
  $price_id = edd_get_cart_item_price_id( $item );
  if ( edd_has_variable_prices( $item['id'] ) && ! is_null( $price_id ) ) {
    $product_name .= ' - ' . edd_get_price_option_name( $item['id'], $price_id, $transaction_id );
  }

  // Check if the item is an ugrade or a renewal (for recurring payments)
  if ( ! empty( $item['item_number']['options']['is_upgrade'] ) ) {
    $product_name .= sprintf(' (%s)', esc_html__('Upgrade'));
  } elseif ( ! empty( $item['item_number']['options']['is_renewal'] ) ) {
    $product_name .= sprintf(' (%s)', esc_html__('Renewal'));
  }

  return apply_filters( 'quaderno_payment_description', $product_name, $item );
}

?>