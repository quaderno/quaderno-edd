<?php
/**
* Quaderno Load
*
* Interface to load every needed file.
* This is the ONLY file to include from external code
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

// This can put quaderno wrapper in its own folder
$quaderno_script_path = str_replace('\\', '/', dirname(__FILE__)).'/';

require_once $quaderno_script_path.'quaderno_request.php';
require_once $quaderno_script_path.'quaderno_class.php';
require_once $quaderno_script_path.'quaderno_model.php';
require_once $quaderno_script_path.'quaderno_contact.php';
require_once $quaderno_script_path.'quaderno_document.php';
require_once $quaderno_script_path.'quaderno_transaction.php';
require_once $quaderno_script_path.'quaderno_receipt.php';
require_once $quaderno_script_path.'quaderno_invoice.php';
require_once $quaderno_script_path.'quaderno_credit.php';
require_once $quaderno_script_path.'quaderno_payment.php';
require_once $quaderno_script_path.'quaderno_tax_rate.php';
require_once $quaderno_script_path.'quaderno_tax_id.php';
