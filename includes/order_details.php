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
* @return mixed|void
*/
function edd_quaderno_add_payment_meta($payment_id) {
	$payment = new EDD_Payment($payment_id);
	ob_start(); 
	?>
	<div class="edd-receipt-url edd-admin-box-inside">
		<p>
			<span class="label"><?php esc_html_e( 'Quaderno URL:', 'edd-quaderno' ); ?></span>&nbsp;
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

?>