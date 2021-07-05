<?php
/**
* Tax Code Field
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2021, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.28
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Display the tax code field
*
* @since  1.28
* @param  int $post_id
* @return mixed|void
*/
function edd_quaderno_display_tax_code_field( $post_id ) {
  $tax_codes = array( 'eservice' => __('e-Service', 'edd-quaderno'),
    'ebook' => __('e-Book', 'edd-quaderno'),
    'saas' => __('SaaS', 'edd-quaderno'),
    'standard' => __('Standard rate', 'edd-quaderno'),
    'reduced' => __('Reduced rate', 'edd-quaderno'),
    'exempt' => __('Tax-exempt', 'edd-quaderno')
  );

  $current_tax_code = edd_quaderno_read_tax_code_field( $post_id );

  $output = '<p><select name="_quaderno_tax_code" id="_quaderno_tax_code" class="edd-select">';

  foreach ( $tax_codes as $code => $name ) {
    $output .= '<option value="' . $code . '" '
      . ( $code == $current_tax_code ? 'selected="selected"' : '' ) . '>'
      . $name . '</option>';
  }

  $output .= sprintf('</select>&nbsp;<label for="_quaderno_tax_code">' . __('Select a <a href="%s" target="_blank">tax code</a>', 'edd-quaderno') . '</label></p>', 'https://support.quaderno.io/setting-up-taxes#74e683bb26cf4d4d8bc01ad7706528ae');

  echo $output . PHP_EOL;
}
add_action( 'edd_meta_box_price_fields', 'edd_quaderno_display_tax_code_field' );

/**
* Save the tax code field
*
* @since  1.28
* @param  int $post_id
* @return mixed|void
*/
function edd_quaderno_save_tax_code_field( $post_id ) {
  if ( isset( $_POST['_quaderno_tax_code'] ) ) {
    update_post_meta( $post_id, '_quaderno_tax_code', $_POST['_quaderno_tax_code'] );
  }
}
add_action( 'save_post', 'edd_quaderno_save_tax_code_field' );

/**
* Get the product's tax code
*
* @since  1.28
* @param  int $post_id
* @return bool
*/
function edd_quaderno_read_tax_code_field( $post_id ) {
  $tax_code = get_post_meta( $post_id, '_quaderno_tax_code', true );
  $is_ebook = get_post_meta( $post_id, '_ebook', true );

  if ( isset( $is_ebook ) && 'yes' == $is_ebook ) {
    return 'ebook';
  } elseif ( isset( $tax_code ) ) {
    return $tax_code;
  } else {
    return 'eservice';
  }
}