<?php
/**
* Checkout
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add VAT field to checkout form
*
* @since  1.0
* @return mixed|void
*/
function edd_quaderno_add_vat_number() {
	ob_start(); 
	?>
	<p id="edd_vat_number_wrap">
		<label for="edd_vat_number" class="edd-label"><?php esc_html_e( 'VAT Number', 'edd_quaderno' ); ?></label>
		<span class="edd-description"><?php esc_html_e( 'Enter your VAT number including country identifier e.g. GB123456788', 'edd_quaderno' ); ?></span>
		<input type="text" name="edd_vat_number" id="edd_vat_number" class="vat-number edd-input" />
		<input type="hidden" name="edd_shop_country" id="edd_shop_country" value="<?php echo edd_get_shop_country(); ?>" />
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_cc_billing_bottom', 'edd_quaderno_add_vat_number', 100);

/**
* Validate VAT Numbers
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_validate_vat_number( $data ) {
	if ( ! empty( $_POST['edd_vat_number'] ) ) {
		$params = array(
			'vat_number' => $_POST['edd_vat_number'],
			'country' => $data['cc_info']['card_country']
		);

		$slug = 'vat_number_' . md5( implode( $params ) );

		if ( false === ( $valid_number = get_transient( $slug ) ) ) {
			$valid_number = (int) QuadernoTax::validate( $params );
			set_transient( $slug, $valid_number, 4 * WEEK_IN_SECONDS );
		}

		if ( $valid_number != 1 ) {
			edd_set_error( 'invalid_vat_number', esc_html__('VAT Number is not valid', 'edd_quaderno') );
		}
	}
}
add_action('edd_checkout_error_checks', 'edd_quaderno_validate_vat_number', 100);

/**
* Store the VAT Number in the payment meta
*
* @since  1.4
* @return mixed|void
*/
function edd_quaderno_store_vat_number( $payment_meta ) {
	$payment_meta['vat_number'] = isset($_POST['edd_vat_number']) ? filter_var( $_POST['edd_vat_number'], FILTER_SANITIZE_STRING ) : '';
	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_vat_number', 100);

/**
* Show the VAT Number in the "View Order Details" popup
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_show_vat_number($payment_meta, $user_info) {
	$vat_number = $payment_meta['vat_number'] ?: 'none';
	?>
	<p><?php echo esc_html__('VAT Number', 'edd_quaderno') . ': ' . $vat_number; ?></p>
	<?php
}
add_action('edd_payment_personal_details_list', 'edd_quaderno_show_vat_number', 10, 2);

?>