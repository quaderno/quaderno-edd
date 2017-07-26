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

  if ( !isset( $edd_options['edd_quaderno_threshold'] ) || edd_get_cart_total() >= intval( $edd_options['edd_quaderno_threshold'] ) ) {
    $required_fields['card_address'] = array(   
      'error_id' => 'invalid_card_address',
      'error_message' => __( 'Please enter your billing address.', 'edd_quaderno' )
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

	if (  $_POST['billing_country'] != edd_get_shop_country() || !isset( $edd_options['edd_quaderno_threshold'] ) || edd_get_cart_total() >= intval( $edd_options['edd_quaderno_threshold'] ) ) {

    if ( empty( $_POST['card_address'] ) )
      edd_set_error( 'invalid_card_address', esc_html__( 'Please enter your billing address', 'edd_quaderno' ));

    if ( empty( $_POST['card_city'] ) )
      edd_set_error( 'invalid_card_city', esc_html__( 'Please enter your billing city', 'easy-digital-downloads' ));

  }
}
add_action( 'edd_checkout_error_checks', 'edd_quaderno_validate_required_fields' );

?>