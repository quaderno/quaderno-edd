<?php
/**
* Quaderno Receipt
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class QuadernoReceipt extends QuadernoDocument {
	static protected $model = 'receipts';

	public function deliver() {
		return $this->execDeliver();
	}
}
?>