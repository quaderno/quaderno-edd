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
	    
	$current_customer = edd_quaderno_current_customer();
  if ( isset( $current_customer ) ) {
    $business_name = $current_customer->get_meta( 'business_name');
  }

	?>
	<p id="edd_business_name_wrap">
		<label for="edd_business_name" class="edd-label"><?php esc_html_e( 'Company Name', 'edd-quaderno' ); ?></label>
		<span class="edd-description"><?php esc_html_e( 'Only if this is a business purchase', 'edd-quaderno' ); ?></span>
		<input type="text" name="edd_business_name" id="edd_business_name" class="business-name edd-input" value="<?php echo $business_name; ?>" />
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
	$selected_country = $data['cc_info']['card_country'];

	if ( ! empty( $_POST['edd_tax_id'] ) && empty( $_POST['edd_business_name'] ) && $selected_country != edd_get_shop_country() ) {
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
  if ( isset($_POST['edd_business_name']) ) {
  	$business_name = filter_var( $_POST['edd_business_name'], FILTER_SANITIZE_STRING );
    $payment_meta['business_name'] = $business_name;

		$current_customer = edd_quaderno_current_customer();
	  if ( isset( $current_customer ) ) {
	    $current_customer->add_meta( 'business_name', $business_name);
	  }
  }
	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_business_name', 100);

?>