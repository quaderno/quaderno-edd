<?php
/**
* Checkout
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.13
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Make billing address mandatory
*
* @since  1.12
* @return mixed|void
*/
function edd_quaderno_purchase_form_required_fields( $required_fields ) {
  global $edd_options;

  $cart_total = edd_get_cart_total();

  if ( $cart_total != 0 ) {
    $required_fields['card_address'] = array(   
      'error_id' => 'invalid_card_address',
      'error_message' => __( 'Please enter your billing address', 'edd-quaderno' )
      );
  } else {
    unset( $required_fields['card_city'] );
    unset( $required_fields['card_state'] );
  }

  return $required_fields;
}
add_filter( 'edd_purchase_form_required_fields', 'edd_quaderno_purchase_form_required_fields' );

/**
* Validate Required fields
*
* @since  1.13
* @return mixed|void
*/
function edd_quaderno_validate_required_fields( $data ) {
  global $edd_options;

  $cart_total = edd_get_cart_total();

  // free downloads
  if ( $cart_total == 0 ) {
    return;
  }

	if (  $_POST['billing_country'] != edd_get_shop_country() || !empty( $_POST['edd_vat_number'] ) || !empty( $_POST['edd_tax_id'] ) ) {

    if ( empty( $_POST['card_address'] ) )
      edd_set_error( 'invalid_card_address', esc_html__( 'Please enter your billing address', 'edd-quaderno' ));

    if ( empty( $_POST['card_city'] ) )
      edd_set_error( 'invalid_card_city', esc_html__( 'Please enter your billing city', 'easy-digital-downloads' ));

  }
}
add_action( 'edd_checkout_error_checks', 'edd_quaderno_validate_required_fields' );

?>