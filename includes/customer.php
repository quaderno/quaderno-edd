<?php
/**
* Customer
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2020, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.26.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Return the current customer
*
* @since  1.26.0
* @return mixed|void
*/
function edd_quaderno_current_customer() {
	$customer = null;

	if ( is_user_logged_in() ) {
    $customer = new EDD_Customer( wp_get_current_user()->ID, true );
  }

  return $customer;
}


?>