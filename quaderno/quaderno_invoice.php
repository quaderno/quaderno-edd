<?php
/**
* Quaderno Invoice
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class QuadernoInvoice extends QuadernoDocument {
	static protected $model = 'invoices';

	public function deliver() {
		return $this->execDeliver();
	}

	public function addPayment($payment) {
		return $this->execAddPayment($payment);
	}

	public function getPayments() {
		return $this->execGetPayments();
	}

	public function removePayment($payment) {
		return $this->execRemovePayment($payment);
	}
}
?>