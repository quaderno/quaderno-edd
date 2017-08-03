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
* Add VAT field to checkout form
*
* @since  1.0
* @return mixed|void
*/
function edd_quaderno_add_tax_id() {
	ob_start(); 
	?>
	<p id="edd_tax_id_wrap">
		<label for="edd_tax_id" class="edd-label"><?php esc_html_e( 'Tax ID', 'edd_quaderno' ); ?></label>
		<input type="text" name="edd_tax_id" id="edd_tax_id" class="vat-number edd-input" />
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_cc_billing_bottom', 'edd_quaderno_add_tax_id', 100);

/**
* Validate Business Names
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_validate_tax_id( $data ) {
  $countries = array('BE', 'DE', 'ES', 'IT');
  $cart_total = edd_get_cart_total();

  // free downloads
  if ( $cart_total == 0 ) {
    return;
  }

	if (  in_array( $_POST['billing_country'], $countries ) && isset( $edd_options['edd_quaderno_threshold'] ) && $cart_total >= intval( $edd_options['edd_quaderno_threshold'] ) && empty( $_POST['edd_tax_id'] ) ) {
		edd_set_error( 'invalid_tax_id', esc_html__('Please enter your Tax ID', 'edd_quaderno') );
	}
}
add_action('edd_checkout_error_checks', 'edd_quaderno_validate_tax_id', 100);

/**
* Store the Tax ID in the payment meta
*
* @since  1.4
* @return mixed|void
*/
function edd_quaderno_store_tax_id( $payment_meta ) {
  if ( !isset($payment_meta['tax_id']) ) {
    $payment_meta['tax_id'] = isset($_POST['edd_tax_id']) ? filter_var( $_POST['edd_tax_id'], FILTER_SANITIZE_STRING ) : '';
  }
	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_tax_id', 100);

/**
* Show the Tax ID in the "View Order Details" popup
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_show_tax_id($payment_id) {
	$payment = new EDD_Payment($payment_id);
	?>
	<div class="edd-order-payment edd-admin-box-inside">
		<p>
			<span class="label"><?php _e( 'Tax ID', 'edd_quaderno' ); ?>:</span>&nbsp;
			<input name="tax_id" type="text" class="med-text" value="<?php echo $payment->meta['tax_id'] ?>"/>
		</p>
	</div>
	<?php
}
add_action('edd_view_order_details_totals_after', 'edd_quaderno_show_tax_id', 10, 2);

/**
* Update the Tax ID in the "View Order Details" popup
*
* @since  1.12
* @return mixed|void
*/
function edd_quaderno_update_tax_id( $payment_id ) {
	$payment = new EDD_Payment( $payment_id );
	$payment->update_meta('tax_id', isset($_POST['tax_id']) ? filter_var( $_POST['tax_id'], FILTER_SANITIZE_STRING ) : '');
}
add_action('edd_update_edited_purchase', 'edd_quaderno_update_tax_id', 100);

?>