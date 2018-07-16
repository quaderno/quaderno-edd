<?php
/**
* Checkout
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2016, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.10
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add Business Name field to checkout form
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_add_business_name() {
	ob_start(); 
	?>
	<p id="edd_business_name_wrap">
		<label for="edd_business_name" class="edd-label"><?php esc_html_e( 'Company name (optional)', 'edd-quaderno' ); ?></label>
		<span class="edd-description"><?php esc_html_e( 'Only if this is a business purchase', 'edd-quaderno' ); ?></span>
		<input type="text" name="edd_business_name" id="edd_business_name" class="business-name edd-input" />
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_cc_billing_top', 'edd_quaderno_add_business_name', 100);

/**
* Validate Business Names
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_validate_business_name( $data ) {
	if ( ! empty( $_POST['edd_vat_number'] ) && empty( $_POST['edd_business_name'] )) {
		edd_set_error( 'invalid_business_name', esc_html__('Please enter your company name', 'edd-quaderno') );
	}
}
add_action('edd_checkout_error_checks', 'edd_quaderno_validate_business_name', 100);

/**
* Store the Business Name in the payment meta
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_store_business_name( $payment_meta ) {
	if ( empty($payment_meta['business_name']) ) {
		$payment_meta['business_name'] = isset($_POST['edd_business_name']) ? filter_var( $_POST['edd_business_name'], FILTER_SANITIZE_STRING ) : '';
	}
	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_business_name', 100);

/**
* Show the Business Name in the "View Order Details" popup
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_show_business_name($payment_meta, $user_info) {
	$business_name = $payment_meta['business_name'] ?: 'none';
	?>
	<p><?php echo esc_html__('Company name', 'edd-quaderno') . ': ' . $business_name; ?></p>
	<?php
}
add_action('edd_payment_personal_details_list', 'edd_quaderno_show_business_name', 10, 2);

?>