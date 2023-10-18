<?php
/**
* Email tags
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2015-2023, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.35
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the Quaderno email tag.
 *
 * @since 1.35
 * @return void
 */
function edd_quaderno_register_email_tags() {
  edd_add_email_tag(
    'quaderno_invoice_link',
    __( 'Adds a link so users can view and download their invoice.', 'edd-quaderno' ),
    'edd_quaderno_invoice_link_tag',
    __( 'Quaderno invoice', 'edd-quaderno' )
  );
}
add_action( 'edd_add_email_tags', 'edd_quaderno_register_email_tags' );

/**
 * Swap the {quaderno_invoice_link} email tag with the link to the Quaderno invoice
 *
 * @since 1.35
 * @param $payment_id
 *
 * @return mixed
 */
function edd_quaderno_invoice_link_tag( $payment_id ) {
  $payment = new EDD_Payment( $payment_id );
  $link = '';

  if( $payment->get_meta( '_quaderno_invoice_id' ) ) {
    $link = $payment->get_meta( '_quaderno_url' );
  }

  return $link;
}

?>
