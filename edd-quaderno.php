<?php

/**
 * Plugin Name: EDD Quaderno
 * Plugin URI: https://wordpress.org/plugins/edd-quaderno/
 * Description: Automatically send customizable sales receipts and invoices with every order in your Easy Digital Downloads store.
 * Version: 1.16.10
 * Author: Quaderno
 * Author URI: https://quaderno.io/
 * License: GPL v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Quaderno' ) ) :

final class EDD_Quaderno {
	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Quaderno ) ) {
			self::$instance = new EDD_Quaderno;
			self::$instance->setup_constants();
			self::$instance->includes();
		}
		return self::$instance;
	}

	private function setup_constants() {
		// Plugin Folder
		if ( ! defined( 'EDD_QUADERNO_PLUGIN_DIR' ) ) {
			define( 'EDD_QUADERNO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'EDD_QUADERNO_PLUGIN_URL' ) ) {
			define('EDD_QUADERNO_PLUGIN_URL', plugin_dir_url( __FILE__ ));
		}

		// Plugin Root File
		if ( ! defined( 'EDD_QUADERNO_PLUGIN_FILE' ) ) {
			define( 'EDD_QUADERNO_PLUGIN_FILE', __FILE__ );
		}
	}

	private function includes() {
		require_once EDD_QUADERNO_PLUGIN_DIR . 'quaderno/quaderno_load.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/ebook_field.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/business_name_field.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/vat_number_field.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/tax_id_field.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/required_fields.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/settings.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/scripts.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/taxes.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/invoices.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/credits.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/payments.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/order_details.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/settings.php';
	}

}
endif;

function EDDQ() {
	return EDD_Quaderno::instance();
}

/**
* Get EDD Quaderno Running
*/
EDDQ();

?>
