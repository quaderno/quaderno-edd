<?php
/**
* Quaderno Class
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/* Interface that implements every single class */
abstract class QuadernoClass
{
	protected $data = array();

	public function __construct($newdata) {
		if (is_array($newdata)) $this->data = $newdata;
	}

	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	public function __get($name) {
		return array_key_exists($name, $this->data) ? $this->data[$name] : null;
	}

	protected function getArray() {
		return $this->data;
	}

}
?>