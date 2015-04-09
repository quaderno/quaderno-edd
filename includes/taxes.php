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
* Calculate tax 
*
* @since  1.0
* @param  string $country
* @param  string $postal_code
* @param  string $tax_id
* @return mixed|void
*/
function edd_quaderno_tax($country, $postal_code, $tax_id)
{
	global $edd_options;

	$params = array(
		'country' => $country,
		'postal_code' => $_POST['card_zip'],
		'vat_number' => $tax_id
	);

	// Let's cache the taxes
	$file = edd_quaderno_get_upload_dir().'/'.md5(implode($params)).'.txt';
	$current_time = time();
	$expire_time = 72 * 60 * 60;
	$file_time = filemtime($file);

	if( file_exists($file) && ($current_time - $expire_time < $file_time) )
	{
		$tax = json_decode(file_get_contents($file));
	}
	else
	{
		QuadernoBase::init($edd_options['edd_quaderno_token'], $edd_options['edd_quaderno_url']);
		$tax = QuadernoTax::calculate($params);
		
		switch ($country) {
			case 'FR':
				$tax->ebook_rate = 5.5;
				break;
			case 'IT':
				$tax->ebook_rate = 4.0;
				break;
			case 'LU':
				$tax->ebook_rate = 3.0;
				break;
			case 'MT':
				$tax->ebook_rate = 5.0;
				break;
			default:
				$tax->ebook_rate = $tax->rate;
	  }
	
		file_put_contents($file, json_encode(array(
			'name' => $tax->name,
			'rate' => $tax->rate,
			'ebook_rate' => $tax->ebook_rate,
			'notes' => $tax->notes
		)));
	}

	return $tax;
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
	
	$tax = edd_quaderno_tax($customer_country, $_POST['card_zip'], $_POST['tax_id']);
	$rate = $edd_options['ebook_rates'] ? $tax->ebook_rate : $tax->rate;

	return $rate / 100;
}
add_filter('edd_tax_rate', 'edd_quaderno_tax_rate', 100, 3);

?>