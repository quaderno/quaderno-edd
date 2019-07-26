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

// Countries wbere tax ID is required in local purchases
if ( ! defined( 'TAX_ID_COUNTRIES' ) ) {
  define( 'TAX_ID_COUNTRIES', ['BG', 'CY', 'ES', 'HR', 'IT', 'PT'] );
} 

/**
* Add VAT field to checkout form
*
* @since  1.0
* @return mixed|void
*/
function edd_quaderno_add_tax_id() {
  global $edd_options;
	ob_start(); 
	?>
	<p id="edd_tax_id_wrap">
		<label for="edd_tax_id" class="edd-label">
			<?php esc_html_e( 'Tax ID', 'edd-quaderno' ); ?>
			<?php if ( in_array(edd_get_shop_country(), TAX_ID_COUNTRIES) ) { ?>
        <span class="edd-required-indicator">*</span>
      <?php } ?>
		</label>
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
  global $edd_options;

  $cart_total = edd_get_cart_total();

  // free downloads
  if ( $cart_total == 0 ) {
    return;
  }

  $selected_country = $data['cc_info']['card_country'];
	if ( in_array(edd_get_shop_country(), TAX_ID_COUNTRIES) && $selected_country == edd_get_shop_country() && empty( $_POST['edd_tax_id'] ) ) {
		edd_set_error( 'invalid_tax_id', esc_html__('Please enter your Tax ID', 'edd-quaderno') );
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
  if ( isset($_POST['edd_tax_id']) ) {
    $payment_meta['tax_id'] = filter_var( $_POST['edd_tax_id'], FILTER_SANITIZE_STRING );
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
  $payment = new EDD_Payment( $payment_id );
  $payment_meta = $payment->get_meta();
	?>
	<div class="edd-order-payment edd-admin-box-inside">
		<p>
			<span class="label"><?php _e( 'Tax ID', 'edd-quaderno' ); ?>:</span>&nbsp;
			<input name="edd_tax_id" type="text" class="med-text" value="<?php echo isset( $payment_meta['tax_id'] ) ? $payment_meta['tax_id'] : '' ?>"/>
		</p>
	</div>
	<?php
}
add_action('edd_view_order_details_totals_after', 'edd_quaderno_show_tax_id', 10, 2);

?>