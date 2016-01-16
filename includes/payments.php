<?php
/**
* Receipts
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2016, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.6
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get payment method for Quaderno
 *
 * @param $order_id
 */
function get_quaderno_payment_method( $gateway ) {
	$method = '';
	switch( $gateway ) {
		case 'manual':
			$method = 'cash';
			break;
		case 'paypal':
			$method = 'paypal';
			break;
		default:
			$method = 'credit_card';
	}
	return $method;
}

?>