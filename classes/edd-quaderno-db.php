<?php
/**
* EDD Quaderno DB class
*
* @package    Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Quaderno_DB {
	public static function find($edd_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'edd_quaderno';
		return $wpdb->get_row("SELECT * FROM $table_name WHERE edd_id = $edd_id");
	}

	public static function save($edd_id, $quaderno_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'edd_quaderno';
		$wpdb->insert($table_name, array(
			'type' => 'customer',
			'edd_id' => $edd_id,
			'quaderno_id' => $quaderno_id
		));
	}
}