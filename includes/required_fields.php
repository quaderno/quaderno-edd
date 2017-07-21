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
    $required_fields['card_address'] = array(   
        'error_id' => 'invalid_card_address',
        'error_message' => __( 'Please enter your billing address.', 'edd_quaderno' )
    );
    return $required_fields;
}
add_filter( 'edd_purchase_form_required_fields', 'edd_quaderno_purchase_form_required_fields' );

?>