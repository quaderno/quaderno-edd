<?php
/**
* Install Function
*
* @package    Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $edd_quaderno_db_version;
$edd_quaderno_db_version = '1.0';

/**
 * Install
 *
 * Runs on plugin install.
 *
 * @since 1.0
 * @global $wpdb
 * @global $edd_quaderno_db_version
 * @return void
 */
function edd_quaderno_install()
{
	global $wpdb, $edd_quaderno_db_version;
	
	$table_name = $wpdb->prefix . 'edd_quaderno';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		type VARCHAR(255) NOT NULL,
		edd_id VARCHAR(255) NOT NULL,
		quaderno_id INT(10) UNSIGNED NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta($sql);

	add_option('edd_quaderno_db_version', $edd_quaderno_db_version);
}
register_activation_hook(EDD_QUADERNO_PLUGIN_FILE, 'edd_quaderno_install');

/**
 * DB Check
 *
 * @since 1.0
 * @global $edd_quaderno_db_version
 * @return void
 */
function edd_quaderno_db_check()
{
	global $edd_quaderno_db_version;
	if ( get_site_option( 'edd_quaderno_db_version' ) != $edd_quaderno_db_version ) {
		jal_install();
	}
}
add_action('plugins_loaded', 'edd_quaderno_db_check');
