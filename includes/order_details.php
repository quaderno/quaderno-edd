<?php
/**
* Order details
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2018, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.16.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add VAT field to checkout form
*
* @since  1.0
* @param  integer $payment_id
* @return mixed|void
*/
function edd_quaderno_add_payment_meta($payment_id) {
	$payment = new EDD_Payment($payment_id);
	ob_start(); 
	?>
	<div class="edd-receipt-url edd-admin-box-inside">
		<p>
			<span class="label"><?php esc_html_e( 'Quaderno Invoice', 'edd-quaderno' ); ?>:</span>&nbsp;
			<span>
				<a href="<?php echo $payment->get_meta( '_quaderno_url' ); ?>" target="_blank">
					<?php esc_html_e( 'View', 'edd-quaderno' ); ?>
				</a>
			</span>
		</p>
	</div>
	<?php
	echo ob_get_clean();
}
add_action('edd_view_order_details_payment_meta_before', 'edd_quaderno_add_payment_meta', 100);

/**
* Output Quaderno Invoice URL inside HelpScout
*
* Source: https://github.com/webzunft/edd-helpscout/blob/master/views/order-row.php#L38
*
* @param  array $order
* @param  array $downloads
* @return mixed|void
 */
function edd_quaderno_helpscout_widget_order_downloads( $order, $downloads ) {
	
	if ( ! isset( $order['payment_id'] ) )
		return; 
	
	$payment = new EDD_Payment( $order['payment_id'] );
	$quaderno_url = $payment->get_meta( '_quaderno_url' );

	ob_start(); 
	?>
	<p>
		<i class="icon-doc"></i><?php esc_html_e( 'Quaderno Invoice', 'edd-quaderno' ); ?>:&nbsp;
		<?php if ( ! empty( $quaderno_url ) ) { ?>
			<a href="<?php echo esc_url( $quaderno_url ); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'View', 'edd-quaderno' ); ?></a>
		<?php } else { ?>
			N/A
		<?php } ?>
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_helpscout_before_order_downloads', 'edd_quaderno_helpscout_widget_order_downloads', 10, 2);
?>
