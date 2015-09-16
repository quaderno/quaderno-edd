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
function edd_quaderno_delete_customer($customer_id, $confirm, $remove_data) {
  if ( $confirm ) EDD_Quaderno_DB::delete( $customer_id );
}
add_action( 'edd_pre_delete_customer', 'edd_quaderno_delete_customer', 10, 3 );
