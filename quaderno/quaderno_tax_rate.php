<?php
/**
* Quaderno Tax Rate
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class QuadernoTaxRate extends QuadernoModel {

	public static function calculate($params) {
		$return = false;
		$request = new QuadernoRequest();
		$request->calculate('tax_rates', $params);
		return $request->get_response_body();
	}

}
?>