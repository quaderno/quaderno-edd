<?php
/**
* Checkout
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Display the E-book field
*
* @since  1.2
* @param  int $post_id
* @return mixed|void
*/
function edd_quaderno_display_ebook_field( $post_id ) {
	echo '<p><label for="edd_ebook_rates"><input type="checkbox" name="_ebook" id="edd_ebook_rates" value="1" ' . checked( true, edd_quaderno_is_ebook( $post_id ), false ) . '> E-Book</label></p>' . PHP_EOL;
}
add_action( 'edd_meta_box_price_fields', 'edd_quaderno_display_ebook_field' );

/**
* Save the E-Book field
*
* @since  1.2
* @param  int $post_id
* @return mixed|void
*/
function edd_quaderno_save_ebook_field( $post_id ) {
	$is_ebook = isset( $_POST['_ebook'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_ebook', $is_ebook );
}
add_action( 'save_post', 'edd_quaderno_save_ebook_field' );

/**
* Check if the product is an E-book
*
* @since  1.2
* @param  int $post_id
* @return bool
*/
function edd_quaderno_is_ebook( $post_id ) {
	$is_ebook = get_post_meta( $post_id, '_ebook', true );
	return isset( $is_ebook ) && 'yes' == $is_ebook;
}