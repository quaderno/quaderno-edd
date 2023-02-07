<?php
/**
* Quaderno Payment
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class QuadernoPayment extends QuadernoClass {
	static protected $model = 'payments';
}
?>