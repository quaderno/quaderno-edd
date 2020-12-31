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
  global $edd_options;
	ob_start(); 

  $current_customer = edd_quaderno_current_customer();
  $tax_id = '';
  if ( isset( $current_customer ) ) {
    $tax_id = $current_customer->get_meta( 'tax_id');
  }

	?>
	<p id="edd_tax_id_wrap">
		<label for="edd_tax_id" class="edd-label"><?php esc_html_e( 'Tax ID', 'edd-quaderno' ); ?></label>
    <span class="edd-description"><?php esc_html_e( 'Enter your VAT number including country identifier e.g. GB123456788', 'edd-quaderno' ); ?></span>
		<input type="text" name="edd_tax_id" id="edd_tax_id" class="tax-id edd-input" value="<?php echo $tax_id; ?>" />
	</p>
	<?php
	echo ob_get_clean();
}
add_action('edd_cc_billing_bottom', 'edd_quaderno_add_tax_id', 100);

/**
* Validate the Tax ID field
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_validate_tax_id_field( $data ) {
  global $edd_options;

  // free downloads
  $cart_total = edd_get_cart_total();
  if ( $cart_total == 0 ) {
    return;
  }

  $selected_country = $data['cc_info']['card_country'];

  // local tax ID is required
	if ( isset( $edd_options['require_tax_id'] ) && empty( $_POST['edd_tax_id'] ) && $selected_country == edd_get_shop_country() ) {
		edd_set_error( 'invalid_tax_id', esc_html__('Please enter your Tax ID', 'edd-quaderno') );
	} 

  // validate EU VAT numbers
  if ( ! empty( $_POST['edd_tax_id'] ) && $selected_country != edd_get_shop_country() ) {
    $valid_number = edd_quaderno_validate_tax_id( $_POST['edd_tax_id'], $data['cc_info']['card_country'] );

    if ( $valid_number === null ) {
      edd_set_error( 'invalid_vat_number', esc_html__('VIES service is down and we cannot validate your VAT Number. Please contact us.', 'edd-quaderno') );
    } elseif ( $valid_number === false ) {
      edd_set_error( 'invalid_vat_number', esc_html__('VAT Number is not valid', 'edd-quaderno') );
    }
  }

}
add_action('edd_checkout_error_checks', 'edd_quaderno_validate_tax_id_field', 100);

/**
 * Validate Tax ID
 *
 * @param string $tax_id
 * @param string $country
 *
 * @return boolean
 *
 * @since 1.25
 */
function edd_quaderno_validate_tax_id( $tax_id, $country ) {
  $params = array(
    'vat_number' => $tax_id,
    'country' => $country
  );

  $slug = 'edd_tax_id_' . md5( implode( $params ) );

  if ( false === ( $valid_number = get_transient( $slug ) ) ) {
    $valid_number = QuadernoTax::validate( $params );
    set_transient( $slug, $valid_number, DAY_IN_SECONDS );
  }

  return $valid_number;
}

/**
* Store the Tax ID in the payment meta
*
* @since  1.4
* @return mixed|void
*/
function edd_quaderno_store_tax_id( $payment_meta ) {
  if ( isset($_POST['edd_tax_id']) ) {
    $tax_id = filter_var( $_POST['edd_tax_id'], FILTER_SANITIZE_STRING );
    $payment_meta['tax_id'] = $tax_id;

    $current_customer = edd_quaderno_current_customer();
    if ( isset( $current_customer ) ) {
      $current_customer->add_meta( 'tax_id', $tax_id);
    }
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
  $tax_id = empty( $payment_meta['vat_number'] ) ? $payment_meta['tax_id'] : $payment_meta['vat_number'];

	?>
  <div class="order-data-address">
    <div class="data column-container">
      <div class="column">
        <p>
          <strong class="order-data-address-line"><?php _e( 'Company Name', 'edd-quaderno' ); ?></strong><br/>
          <input name="edd_business_name" type="text" class="large-text" value="<?php echo $payment_meta['business_name'] ?>"/>
        </p>
      </div>
      <div class="column">
        <p>
          <strong class="order-data-address-line"><?php _e( 'Tax ID', 'edd-quaderno' ); ?></strong><br/>
          <input name="edd_tax_id" type="text" class="med-text" value="<?php echo $tax_id ?>"/>
        </p>
      </div>
    </div>
  </div>
	<?php
}
add_action('edd_payment_billing_details', 'edd_quaderno_show_tax_id', 10);

?>