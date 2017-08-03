<?php
/**
* Quaderno Income
*
* @package   Quaderno PHP
* @author    Quaderno <hello@quaderno.io>
* @copyright Copyright (c) 2017, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class QuadernoIncome extends QuadernoDocument {
	static protected $model = 'income';

	public function deliver() {
	  $document = unserialize(sprintf(
      'O:%d:"%s"%s',
      strlen('Quaderno'. $this->type),
      'Quaderno' . $this->type,
      strstr(strstr(serialize($this), '"'), ':')
      ));

		return $document->execDeliver();
	}

}
?>