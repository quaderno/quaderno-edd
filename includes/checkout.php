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
* Add Company & VAT fields to checkout form
*
* @since  1.0
* @return mixed|void
*/
function edd_quaderno_add_tax_id() {
	ob_start(); 
	?>
	<p id="edd-company-wrap">
		<label for="edd_company" class="edd-label"><?php _e( 'Billing Company Name (optional)', 'edd_quaderno' ); ?></label>
		<input type="text" name="edd_company" id="edd_company" class="company edd-input" />
	</p>
	<p id="edd-tax-id-wrap">
		<label for="edd_tax_id" class="edd-label"><?php _e( 'Billing Tax ID', 'edd_quaderno' ); ?></label>
		<input type="text" name="edd_tax_id" id="edd_tax_id" class="tax-id edd-input" placeholder="<?php _e( 'Tax ID / EU-VAT ID', 'edd_quaderno' ); ?>" />
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_cc_billing_top', 'edd_quaderno_add_tax_id', 100);

/**
* Store the custom field data in the payment meta
*
* @since  1.4
* @return mixed|void
*/
function edd_quaderno_store_custom_fields( $payment_meta ) {
	$payment_meta['user_info']['company'] = isset($_POST['edd_company']) ? $_POST['edd_company'] : '';
	$payment_meta['user_info']['tax_id'] = isset($_POST['edd_tax_id']) ? $_POST['edd_tax_id'] : '';
	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_custom_fields', 100);

?>