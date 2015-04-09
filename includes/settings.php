<?php
/**
* Settings
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add EDD Quaderno tab
*
* @since  1.0
* @return array $tabs
*/
function edd_quaderno_tab( $tabs ) {
	$tabs['quaderno'] = 'Quaderno';
	return $tabs;
}
add_filter( 'edd_settings_tabs', 'edd_quaderno_tab' );

/**
* Add EDD Quaderno settings
*
* @since  1.0
* @return void
*/
function edd_quaderno_register_settings()
{
	add_settings_section(
		'edd_settings_quaderno',
		__return_null(),
		'__return_false',
		'edd_settings_quaderno'
	);

	add_settings_field(
		'edd_settings[token]',
		__('Private key', 'edd_quaderno'),
		'edd_text_callback',
		'edd_settings_quaderno',
		'edd_settings_quaderno',
		array(
			'id'      => 'edd_quaderno_token',
			'name' => __('Private key', 'edd_quaderno'),
			'desc' => __('Get this key from your Quaderno account', 'edd_quaderno'),
			'section' => 'quaderno'
		)
	);

	add_settings_field(
		'edd_settings[url]',
		__('API URL', 'edd_quaderno'),
		'edd_text_callback',
		'edd_settings_quaderno',
		'edd_settings_quaderno',
		array(
			'id' => 'edd_quaderno_url',
			'name' => __('API URL', 'edd_quaderno'),
			'desc' => __('Get this URL from your Quaderno account', 'edd_quaderno'),
			'section' => 'quaderno'
		)
	);
	
	add_settings_field(
		'edd_settings[autosend_receipts]',
		__('Autosend receipts', 'edd_quaderno'),
		'edd_checkbox_callback',
		'edd_settings_quaderno',
		'edd_settings_quaderno',
		array(
			'id' => 'autosend_receipts',
			'name' => __('Autosend receipts', 'edd_quaderno'),
			'desc' => __('Check this to automatically send your receipts when an order is marked as complete.', 'edd_quaderno'),
			'section' => 'quaderno'
		)
	);

	add_settings_field(
		'edd_settings[ebook_rates]',
		__('E-book Taxes', 'edd_quaderno'),
		'edd_checkbox_callback',
		'edd_settings_taxes',
		'edd_settings_taxes',
		array(
			'id' => 'ebook_rates',
			'name' => __('E-book Taxes', 'edd_quaderno'),
			'desc' => __('Check this if you only sell e-books.', 'edd_quaderno'),
			'section' => 'quaderno'
		)
	);
}
add_action('admin_init', 'edd_quaderno_register_settings');

/**
* Retrieve the absolute path to the file upload directory without the trailing slash
*
* @since  1.0
* @return string $path Absolute path to the EDD Quaderno upload directory
*/
function edd_quaderno_get_upload_dir()
{
	$wp_upload_dir = wp_upload_dir();
	wp_mkdir_p( $wp_upload_dir['basedir'].'/edd-quaderno' );
	$path = $wp_upload_dir['basedir'].'/edd-quaderno';

	return apply_filters( 'edd_quaderno_get_upload_dir', $path );
}

/**
* Plugins row action links
*
* @author Michael Cannon <mc@aihr.us>
* @since 1.8
* @param array $links already defined action links
* @param string $file plugin file path and name being processed
* @return array $links
*/
function edd_quaderno_plugin_action_links( $links, $file ) {
	$settings_link = '<a href="'.admin_url( 'edit.php?post_type=download&page=edd-settings&tab=quaderno' ).'">'.esc_html__( 'Settings', 'edd_quaderno' ).'</a>';
	if ( $file == 'edd-quaderno/edd-quaderno.php' )
		array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'edd_quaderno_plugin_action_links', 10, 2 );

function edd_quaderno_meta_links( $links, $file ) {
	$settings_link = '<a href="https://quadernoapp.com/signup" target="_blank">'.esc_html__( 'Create a Quaderno account', 'edd_quaderno' ).'</a>';
	if ( $file == 'edd-quaderno/edd-quaderno.php' )
		array_push( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_row_meta', 'edd_quaderno_meta_links', 10, 4 );

/**
* Show Message when the plugin is activated
*
* @since 1.0
* @return void
*/
function edd_quaderno_admin_messages()
{
	if (!get_option('edd_quaderno_notice_shown') && is_plugin_active('edd-quaderno/edd-quaderno.php'))
	{
		$html = '<div class="updated"><p>';
		$html .= __( 'Don\'t you have a Quaderno account? Create a new one <a href="https://quadernoapp.com/signup" target="_blank">on this page</a>.', 'edd_quaderno' );
		$html .= '</p></div>';
	  echo $html;
		
		update_option('edd_quaderno_notice_shown', 'true');
	}
}
add_action( 'admin_notices', 'edd_quaderno_admin_messages' );

?>