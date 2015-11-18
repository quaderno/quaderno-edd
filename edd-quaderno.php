<?php
/*
Plugin Name: EDD Quaderno
Plugin URL: https://wordpress.org/plugins/edd-quaderno/
Description: Send beautiful receipts to EDD customers and comply with the EU VAT rules for digital goods & services.
Version: 1.3.1
Author: Quaderno
Author URI: http://quaderno.io
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Quaderno' ) ) :

final class EDD_Quaderno {
	private static $instance;
	
	public static function instance()
	{
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Quaderno ) )
		{
			self::$instance = new EDD_Quaderno;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->load_textdomain();
		}
		return self::$instance;
	}
	
	private function setup_constants()
	{
		// Plugin Folder
		if ( ! defined( 'EDD_QUADERNO_PLUGIN_DIR' ) )
		{
			define( 'EDD_QUADERNO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'EDD_QUADERNO_PLUGIN_URL' ) )
		{
			define('EDD_QUADERNO_PLUGIN_URL', plugin_dir_url( __FILE__ ));
		}
		
		// Plugin Root File
		if ( ! defined( 'EDD_QUADERNO_PLUGIN_FILE' ) )
		{
			define( 'EDD_QUADERNO_PLUGIN_FILE', __FILE__ );
		}
	}

	private function includes()
	{
		require_once EDD_QUADERNO_PLUGIN_DIR . 'quaderno-php/quaderno_load.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'classes/edd-quaderno-db.php';

		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/ebook_field.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/checkout.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/scripts.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/taxes.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/receipts.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/customers.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/settings.php';
		require_once EDD_QUADERNO_PLUGIN_DIR . 'includes/install.php';
	}

	public function load_textdomain()
	{
		$edd_lang_dir = EDD_QUADERNO_PLUGIN_DIR . '/languages/';
		$locale = apply_filters( 'plugin_locale', get_locale(), 'edd_quaderno' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'edd', $locale );

		/* Setup paths to current locale file */
		$mofile_local = $edd_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/edd-quaderno/' . $mofile;

		if ( file_exists( $mofile_global ) )
		{
			/* Look in global /wp-content/languages/edd folder */
			load_textdomain( 'edd_quaderno', $mofile_global );
		}
		elseif ( file_exists( $mofile_local ) )
		{
			/* Look in local /wp-content/plugins/easy-digital-downloads/languages/ folder */
			load_textdomain( 'edd_quaderno', $mofile_local );
		}
		else
		{
			/* Load the default language files */
			load_plugin_textdomain( 'edd_quaderno', false, $edd_lang_dir );
		}
	}
	
}
endif; 

function EDDQ()
{
	return EDD_Quaderno::instance();
}

/**
* Get EDD Quaderno Running
*/
EDDQ();

?>