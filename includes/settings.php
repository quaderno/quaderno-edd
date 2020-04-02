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
				'desc' => '<a href="https://quadernoapp.com/users/api-keys/?utm_source=wordpress&utm_campaign=edd" target="_blank">' . esc_html__( 'Get your Quaderno private key', 'edd-quaderno' ) . '</a>',
				'type' => 'text'
			),
			'edd_quaderno_url' => array(
				'id'   => 'edd_quaderno_url',
				'name' => esc_html__( 'API URL', 'edd-quaderno' ),
				'desc' => '<a href="https://quadernoapp.com/users/api-keys/?utm_source=wordpress&utm_campaign=edd" target="_blank">' . esc_html__( 'Get your Quaderno API URL', 'edd-quaderno' ) . '</a>',
				'type' => 'text'
			),
			'require_tax_id' => array(
				'id'   => 'require_tax_id',
				'name' => esc_html__( 'Require tax ID', 'edd-quaderno' ),
				'desc' => sprintf(esc_html__( 'Check this if tax ID must be required for all sales in %s.', 'edd-quaderno' ), edd_get_country_name(edd_get_shop_country())),
				'type' => 'checkbox'
			),
			'autosend_receipts' => array(
				'id'   => 'autosend_receipts',
				'name' => esc_html__( 'Autosend documents', 'edd-quaderno' ),
				'desc' => esc_html__( 'Check this if you want Quaderno to automatically email your receipts.', 'edd-quaderno' ),
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
	$settings_link = '<a href="https://quadernoapp.com/signup?utm_source=wordpress&utm_campaign=edd" target="_blank">'.esc_html__( 'Create a Quaderno account', 'edd-quaderno' ).'</a>';
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
		$html .= esc_html__( 'Don\'t you have a Quaderno account? Create a new one <a href="https://quadernoapp.com/signup?utm_source=wordpress&utm_campaign=edd" target="_blank">on this page</a>.', 'edd-quaderno' );
		$html .= '</p></div>';
	  echo $html;
		
		update_option('edd_quaderno_notice_shown', 'true');
	}
}

/**
 * Ask users to leave a review for the plugin on wp.org.
 */
function edd_quaderno_review() {
	global $wpdb;

	$post_count = $wpdb->get_var( "SELECT count(*) FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_quaderno_invoice_id'" );
	$user_id = get_current_user_id();

	if ( $post_count < 5 ) {
		return;
	}
	?>
	<div id="quaderno-review" class="notice notice-info is-dismissible">
  	<p>
  		<?php _e( "Hi there! You've been using Quaderno for a while and we hope it has been a big help for you.", 'edd-quaderno' ); ?>
    	<br>
  		<?php _e( "If you could take a few moments to rate it on WordPress.org, we would really appreciate your help making the plugin better. Thanks!", 'edd-quaderno' ); ?>
    	<br><br>
    	<a href="https://wordpress.org/support/plugin/edd-quaderno/reviews/?filter=5#new-post" target="_blank" class="button-secondary"><?php _e( 'Post review', 'edd-quaderno' ); ?></a>
    </p>
  </div>
<?php
}

/**
 * Loads the inline script to dismiss the review notice.
 */
function edd_quaderno_review_script() {
	echo
		"<script>\n" .
		"jQuery(document).on('click', '#quaderno-review .notice-dismiss', function() {\n" .
		"\tvar quaderno_review_data = {\n" .
		"\t\taction: 'quaderno_review',\n" .
		"\t};\n" .
		"\tjQuery.post(ajaxurl, quaderno_review_data, function(response) {\n" .
		"\t\tif (response) {\n" .
		"\t\t\tconsole.log(response);\n" .
		"\t\t}\n" .
		"\t});\n" .
		"});\n" .
		"</script>\n";
}

/**
 * Disables the notice about leaving a review.
 */
function edd_quaderno_dismiss_review() {
	update_option( 'quaderno_dismiss_review', true, false );
	wp_die();
}

?>