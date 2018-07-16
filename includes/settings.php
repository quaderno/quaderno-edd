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
function edd_quaderno_settings( $settings ) {
	$quaderno_settings = array(
		'quaderno' => array(
			'edd_quaderno_token' => array(
				'id'   => 'edd_quaderno_token',
				'name' => esc_html__( 'Private key', 'edd-quaderno' ),
				'desc' => esc_html__( 'Get this key from your Quaderno account', 'edd-quaderno' ),
				'type' => 'text'
			),
			'edd_quaderno_url' => array(
				'id'   => 'edd_quaderno_url',
				'name' => esc_html__( 'API URL', 'edd-quaderno' ),
				'desc' => esc_html__( 'Get this URL from your Quaderno account', 'edd-quaderno' ),
				'type' => 'text'
			),
			'show_tax_id' => array(
				'id'   => 'show_tax_id',
				'name' => esc_html__( 'Show Tax ID', 'edd-quaderno' ),
				'desc' => esc_html__( 'Additional tax number that is mandatory in some countries. This is not the EU VAT number', 'edd-quaderno' ),
				'type' => 'checkbox'
			),
			'autosend_receipts' => array(
				'id'   => 'autosend_receipts',
				'name' => esc_html__( 'Autosend documents', 'edd-quaderno' ),
				'desc' => esc_html__( 'Send automatically your sales receipts to your customers', 'edd-quaderno' ),
				'type' => 'checkbox'
			)
		)
	);

	return array_merge($settings, $quaderno_settings);
}
add_filter('edd_registered_settings', 'edd_quaderno_settings');

/**
* Retrieve the absolute path to the file upload directory without the trailing slash
*
* @since  1.0
* @return string $path Absolute path to the EDD Quaderno upload directory
*/
function edd_quaderno_get_upload_dir() {
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
	$settings_link = '<a href="'.admin_url( 'edit.php?post_type=download&page=edd-settings&tab=quaderno' ).'">'.esc_html__( 'Settings', 'edd-quaderno' ).'</a>';
	if ( $file == 'edd-quaderno/edd-quaderno.php' )
		array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'edd_quaderno_plugin_action_links', 10, 2 );

function edd_quaderno_meta_links( $links, $file ) {
	$settings_link = '<a href="https://quadernoapp.com/signup" target="_blank">'.esc_html__( 'Create a Quaderno account', 'edd-quaderno' ).'</a>';
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
function edd_quaderno_admin_messages() {
	if (!get_option('edd_quaderno_notice_shown') && is_plugin_active('edd-quaderno/edd-quaderno.php'))
	{
		$html = '<div class="updated"><p>';
		$html .= esc_html__( 'Don\'t you have a Quaderno account? Create a new one <a href="https://quadernoapp.com/signup" target="_blank">on this page</a>.', 'edd-quaderno' );
		$html .= '</p></div>';
	  echo $html;
		
		update_option('edd_quaderno_notice_shown', 'true');
	}
}
add_action( 'admin_notices', 'edd_quaderno_admin_messages' );

function edd_quaderno_review_notice() {
	global $wpdb;

	$post_count = $wpdb->get_var( "SELECT count(*) FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_quaderno_invoice_id'" );
	$user_id = get_current_user_id();

	if ( get_user_meta( $user_id, 'quaderno_review_dismissed' ) || $post_count < 5 ) {
		return;
	}
	?>
	<div class="notice notice-info">
  	<p><?php _e( "Awesome, you've been using <strong>EDD Quaderno</strong> for a while.<br>Could you please do me a BIG favor and give a <strong>5-star rating</strong> on WordPress? Just to help us spread the word and boost our motivation.<br><br>Your help is much appreciated. Thank you very much,<br> ~Carlos Hernandez, Founder", 'edd-quaderno' ); ?>
    </p>
    <ul>
        <li><a href="https://wordpress.org/support/plugin/edd-quaderno/reviews/?filter=5#new-post" target="_blank"><?php _e( 'Ok, you deserve it', 'edd-quaderno' ); ?></a></li>
        <li><a href="?review-dismissed"><?php _e( 'Nope, maybe later', 'edd-quaderno' ); ?></a></li>
        <li><a href="?review-dismissed"><?php _e( 'I already did it', 'edd-quaderno' ); ?></a></li>
    </ul>
  </div>
<?php
}
add_action( 'admin_notices', 'edd_quaderno_review_notice');

function edd_quaderno_review_dismised() {
	$user_id = get_current_user_id();
  if ( isset( $_GET['review-dismissed'] ) ) {
    add_user_meta( $user_id, 'quaderno_review_dismissed', 'true', true );
  }
}
add_action( 'admin_init', 'edd_quaderno_review_dismised' );

?>