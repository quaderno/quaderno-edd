<?php
/**
* Taxes
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Calculate transaction type
*
* @since  1.2
* @return void
*/
function edd_quaderno_transaction_type() 
{
	$cart_items = edd_get_cart_contents();

	if ( ! $cart_items ) {
		$type = 'eservice';
	}
	else {
		$type = 'ebook';
		foreach ( $cart_items as $item ) {
			if ( false === edd_quaderno_is_ebook( $item['id'] )) {
  			$type = 'eservice';
  			break;
  		}
  	}
	}

	return $type;
}

/**
* Calculate tax 
*
* @since  1.0
* @param  string $country
* @param  string $postal_code
* @param  string $tax_id
* @return float
*/
function edd_quaderno_tax($country, $postal_code, $city, $tax_id)
{
	global $edd_options;

	$params = array(
		'country' => $country,
		'postal_code' => $postal_code,
		'city' => $city,
		'tax_id' => $tax_id,
		'transaction_type' => edd_quaderno_transaction_type()
	);

	$slug = 'quaderno_tax_' . md5( implode( $params ) );
	if ( false === ( $tax = get_transient( $slug ) ) ) {
		$tax = QuadernoTax::calculate( $params );
		set_transient( $slug, $tax, WEEK_IN_SECONDS );
	}

	return apply_filters( 'quaderno_tax_calculation', $tax, $country, $postal_code, $city, $tax_id );
}

/**
* Calculate tax rate
*
* @since  1.0
* @param  float $rate
* @param  string $customer_country
* @param  string $customer_state
* @return mixed|void
*/
function edd_quaderno_tax_rate($rate, $customer_country, $customer_state)
{
	global $edd_options;

	$postal_code = isset($_POST['card_zip']) ? $_POST['card_zip'] : '';
	$city = isset($_POST['card_city']) ? $_POST['card_city'] : '';
	$tax_id = isset($_POST['edd_tax_id']) ? $_POST['edd_tax_id'] : '';

	$tax = edd_quaderno_tax($customer_country, $postal_code, $city, $tax_id);

	if ( empty( $tax->name ) && empty( $tax->notes ) ) {
		return $rate;
	} else {
		return $tax->rate / 100;
	}
}
add_filter('edd_tax_rate', 'edd_quaderno_tax_rate', 100, 3);

?>