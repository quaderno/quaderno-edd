<?php
/**
* Purchase History
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2018, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.17
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add header to purchase history
*
* @since  1.17
* @return void
*/
function edd_quaderno_history_header_after()
{
?>
	<th class="edd_purchase_details"><?php _e('Invoices','edd-quaderno' ); ?></th>
<?php
}
add_action('edd_purchase_history_header_after', 'edd_quaderno_history_header_after');

/**
* Add link to the invoice in the purchase history
*
* @since  1.17
* @param  integer $payment_id
* @param  array $payment_meta
* @return void
*/
function edd_quaderno_history_row_end($payment_id, $payment_meta)
{
	$payment = new EDD_Payment($payment_id);
	$permalink = $payment->get_meta( '_quaderno_url' );
	
	$html = '<td class="edd_purchase_details">';
	if ( !empty($permalink) ) {
		$html .= '<a href="' . esc_url( $permalink ) . '" target="_blank">' . __( 'View Invoice', 'edd-quaderno' ) . '</a>';
	}
	else {
		$html .= '-';
	}	
	$html .= '</td>';

	echo $html;
}
add_action('edd_purchase_history_row_end', 'edd_quaderno_history_row_end', 10, 2);
