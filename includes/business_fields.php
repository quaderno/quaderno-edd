<?php
/**
* Business fields
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
function edd_quaderno_add_business_fields() {
  global $edd_options;
	ob_start(); 

  $tax_id = '';
  $business_name = '';

  $current_customer = edd_quaderno_current_customer();
  if ( isset( $current_customer ) ) {
    $tax_id = $current_customer->get_meta( 'tax_id');
    $business_name = $current_customer->get_meta( 'business_name');
  }

	?>
  <fieldset id="edd_business_fields">
    <legend><?php _e( 'Business Info', 'edd-quaderno' ); ?></legend>
  	<p id="edd_tax_id_wrap">
	 	 <label for="edd_tax_id" class="edd-label"><?php esc_html_e( 'Tax ID', 'edd-quaderno' ); ?></label>
     <span class="edd-description"><?php esc_html_e( 'Enter your VAT/GST number', 'edd-quaderno' ); ?></span>
		  <input type="text" name="edd_tax_id" id="edd_tax_id" class="tax-id edd-input" value="<?php echo $tax_id; ?>" />
  	</p>
    <p id="edd_business_name_wrap">
      <label for="edd_business_name" class="edd-label"><?php esc_html_e( 'Business Name', 'edd-quaderno' ); ?></label>
      <input type="text" name="edd_business_name" id="edd_business_name" class="business-name edd-input" value="<?php echo $business_name; ?>" />
    </p>
  </fieldset>
  <?php
	echo ob_get_clean();
}
add_action('edd_purchase_form_after_cc_form', 'edd_quaderno_add_business_fields', 1000);

/**
* Validate the Tax ID field
*
* @since  1.10
* @return mixed|void
*/
function edd_quaderno_validate_business_fields( $data ) {
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

  // validate business name
  if ( ! empty( $_POST['edd_tax_id'] ) && empty( $_POST['edd_business_name'] ) && $selected_country != edd_get_shop_country() ) {
    edd_set_error( 'invalid_business_name', esc_html__('Please enter your business name', 'edd-quaderno') );
  }

}
add_action('edd_checkout_error_checks', 'edd_quaderno_validate_business_fields', 100);

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
    'tax_id' => $tax_id,
    'country' => $country
  );

  $slug = 'edd_tax_id_' . md5( implode( $params ) );

  if ( false === ( $valid_number = get_transient( $slug ) ) ) {
    $valid_number = QuadernoTaxId::validate( $params );
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
function edd_quaderno_store_business_data( $payment_meta ) {
  if ( isset($_POST['edd_tax_id']) ) {
    $tax_id = htmlspecialchars( $_POST['edd_tax_id'], ENT_QUOTES);
    $payment_meta['tax_id'] = $tax_id;

    $current_customer = edd_quaderno_current_customer();
    if ( isset( $current_customer ) ) {
      $current_customer->add_meta( 'tax_id', $tax_id);
    }
  }

  if ( isset($_POST['edd_business_name']) ) {
    $business_name = htmlspecialchars( $_POST['edd_business_name'], ENT_COMPAT );
    $payment_meta['business_name'] = $business_name;

    $current_customer = edd_quaderno_current_customer();
    if ( isset( $current_customer ) ) {
      $current_customer->add_meta( 'business_name', $business_name);
    }
  }

	return $payment_meta;
}
add_filter('edd_payment_meta', 'edd_quaderno_store_business_data', 100);

/**
* Show the Tax ID in the "View Order Details" popup
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_show_business_data($payment_id) {
  $payment = new EDD_Payment( $payment_id );
  $payment_meta = $payment->get_meta();

  // Get the current tax ID if exists
  $tax_id = '';
  if ( !empty( $payment_meta['vat_number'] ) ) {
    $tax_id = $payment_meta['vat_number'];
  } elseif ( !empty( $payment_meta['tax_id'] ) ) {
    $tax_id = $payment_meta['tax_id'];
  }

  // Get the current business name
  $business_name = '';
  if ( !empty( $payment_meta['business_name'] ) ) {
    $business_name = $payment_meta['business_name']; 
  }

	?>
  <div class="order-data-address">
    <div class="data column-container">
      <div class="column">
        <p>
          <strong class="order-data-address-line"><?php esc_html_e( 'Business Name', 'edd-quaderno' ); ?></strong><br/>
          <input name="edd_business_name" type="text" class="large-text" value="<?php echo $business_name ?>"/>
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
add_action('edd_payment_billing_details', 'edd_quaderno_show_business_data', 10);

?>
