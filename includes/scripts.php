<?php
/**
* Scripts
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add EDD Quaderno scripts
*
* @since  1.0
* @return void
*/
function edd_quaderno_load_scripts() {
	$js_version = '1.15';
	$js_dir = EDD_QUADERNO_PLUGIN_URL . 'assets/js/';

	/* Use minified libraries if SCRIPT_DEBUG is turned off */
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	if ( edd_is_checkout() ) {
		wp_enqueue_script( 'edd-vat_calculator-tax-id', $js_dir . 'edd-quaderno' . $suffix . '.js', array( 'jquery' ), $js_version, true);
	}
}
add_action( 'wp_enqueue_scripts', 'edd_quaderno_load_scripts', 999);

?>