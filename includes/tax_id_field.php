<?php
/**
* Checkout
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.6
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add VAT field to checkout form
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_add_tax_id() {
	if ( edd_get_shop_country() != 'ES' ) {
		return;
	}

	ob_start();
	?>
	<p id="edd-tax-id-wrap">
		<label for="edd_tax_id" class="edd-label">
			<?php _e( 'Tax ID', 'edd_quaderno' ); ?>
			<span class="edd-required-indicator">*</span>
		</label>
		<input type="text" name="edd_tax_id" id="edd_tax_id" class="tax-id edd-input" placeholder="<?php _e( 'Tax ID', 'edd_quaderno' ); ?>" required />
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_cc_billing_bottom', 'edd_quaderno_add_tax_id', 200);

/**
* Validate Tax ID
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_validate_tax_id( $data ) {
	if ( edd_get_shop_country() != 'ES' ) {
		return;
	}

	if ( $_POST['billing_country'] == 'ES' && ( !isset( $_POST['edd_tax_id'] ) || empty( $_POST['edd_tax_id'] ) ) ) {
		edd_set_error( 'invalid_tax_id', __('Please enter your Tax ID', 'edd_quaderno') );
	}
}
add_action('edd_checkout_error_checks', 'edd_quaderno_validate_tax_id', 100);

/**
* Store the Tax ID in the payment meta
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_store_tax_id( $payment_meta ) {
	$payment_meta['tax_id'] = isset($_POST['edd_tax_id']) ? $_POST['edd_tax_id'] : '';
	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_tax_id', 100);

?>
