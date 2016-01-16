<?php
/**
* Quaderno Tax
*
* @package   Quaderno PHP
* @author    Quaderno <hello@quaderno.io>
* @copyright Copyright (c) 2015, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class QuadernoTax extends QuadernoModel {

	public static function calculate($params) {
		$return = false;
		$request = new QuadernoRequest();
		$request->calculate('taxes', $params);
		return $request->get_response_body();
	}

	public static function validate($params) {
		$return = false;
		$request = new QuadernoRequest();
		$request->validate('taxes', $params);
		$response = $request->get_response_body();
		return $response->valid;
	}

}
?>