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
    <p id="edd_business_name_wrap" class="edd-has-js">
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

  // validate tax ID
  if ( ! empty( $_POST['edd_tax_id'] ) && $selected_country != edd_get_shop_country() ) {
    $valid_number = edd_quaderno_validate_tax_id( $_POST['edd_tax_id'], $data['cc_info']['card_country'] );

    if ( !$valid_number ) {
      edd_set_error( 'invalid_vat_number', esc_html__('Your Tax ID cannot be validated', 'edd-quaderno') );
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
  // remove non-word characters from tax ID
  $tax_id = preg_replace('/\W/', '', $tax_id);

  // get the country code from the number if it's empty
  if ( empty($country) ) {
    $country = substr( $tax_id, 0, 2 );
  }

  $params = array(
    'tax_id' => $tax_id,
    'country' => $country
  );

  $slug = 'edd_tax_id_' . md5( implode( $params ) );

  if ( false === ( $valid_number = get_transient( $slug ) ) ) {
    $validation_result = QuadernoTaxId::validate( $params );
    $valid_number = (int) $validation_result;

    // Cache the result, unless the tax ID validation service was down.
    if ( !is_null($validation_result) ) {
      set_transient( $slug, $valid_number, DAY_IN_SECONDS );
    }
  }

  return $valid_number == 1;
}

/**
* Store the Business Name & Tax ID in the order
*
* @since  1.31
* @return mixed|void
*/
function edd_quaderno_store_business_data( $order_id ) {
  $current_customer = edd_quaderno_current_customer();

  if ( isset($_POST['edd_tax_id']) ) {
    $tax_id = htmlspecialchars( $_POST['edd_tax_id'], ENT_QUOTES);
    edd_add_order_meta( $order_id, 'tax_id', $tax_id );

    if ( isset( $current_customer ) ) {
      $current_customer->add_meta( 'tax_id', $tax_id);
    }
  }

  if ( isset($_POST['edd_business_name']) ) {
    $business_name = htmlspecialchars( $_POST['edd_business_name'], ENT_COMPAT );
    edd_add_order_meta( $order_id, 'business_name', $business_name );

    if ( isset( $current_customer ) ) {
      $current_customer->add_meta( 'business_name', $business_name);
    }
  }
}
add_action('edd_built_order', 'edd_quaderno_store_business_data', 100);

/**
* Show the Business Name & Tax ID fields in the "View Order Details" page
*
* @since  1.6
* @return mixed|void
*/
function edd_quaderno_show_business_data( $order_id ) {
  $order = edd_get_order( $order_id );

  $tax_id = edd_get_order_meta( $order_id, 'vat_number', true );
  if( empty( $tax_id ) ) {
    $tax_id = edd_get_order_meta( $order_id, 'tax_id', true );
  }

  // Get the current business name
  $business_name = edd_get_order_meta( $order_id, 'business_name', true );

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

/**
* Process the Business Name & Tax ID fields changes from "View Order Details" page
*
* @since  1.31
* @return mixed|void
*/
function edd_quaderno_update_order_address( $order_id ) {
  if ( isset($_POST['edd_tax_id']) ) {
    $tax_id = htmlspecialchars( $_POST['edd_tax_id'], ENT_QUOTES);
    edd_update_order_meta( $order_id, 'tax_id', $tax_id );
  }

  if ( isset($_POST['edd_business_name']) ) {
    $business_name = htmlspecialchars( $_POST['edd_business_name'], ENT_COMPAT );
    edd_update_order_meta( $order_id, 'business_name', $business_name );
  }

}
add_action('edd_updated_edited_purchase', 'edd_quaderno_update_order_address', 10);

?>
