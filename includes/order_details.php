<?php
/**
* Order details
*
* @package    EDD Quaderno
* @copyright  Copyright (c) 2018-2023, Carlos Hernandez
* @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since      1.16.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Add link to Quaderno invoice in payment details page
*
* @since  1.0
* @param  integer $payment_id
* @return mixed|void
*/
function edd_quaderno_add_payment_meta($payment_id) {
	$payment = new EDD_Payment($payment_id);	
	ob_start(); 
	?>
	<div class="edd-receipt-url edd-admin-box-inside">
		<p>
			<span class="label"><?php esc_html_e( 'Quaderno Invoice', 'edd-quaderno' ); ?>:</span>&nbsp;
			<span>
			<?php if( $payment->get_meta( '_quaderno_invoice_id' ) ) { ?>
				<a href="<?php echo $payment->get_meta( '_quaderno_url' ); ?>" target="_blank">
					<?php esc_html_e( 'View', 'edd-quaderno' ); ?>
				</a>
			<?php } else { ?>	
				<a href="<?php echo esc_url( add_query_arg( array( 'edd-action' => 'resend_invoice', 'purchase_id' => $payment_id ) ) ); ?>">
					<?php esc_html_e( 'Create', 'edd-quaderno' ); ?>
				</a>
			<?php } ?>	
			</span>
		</p>
	</div>
	<?php
	echo ob_get_clean();
}
add_action('edd_view_order_details_payment_meta_before', 'edd_quaderno_add_payment_meta', 100);

/**
* Call edd_get_order_meta with fallback to the payment meta
*
* @since  1.26.0
* @param  mixed $order_id
* @param  string $key
* @return @return mixed|void
*/
function edd_quaderno_get_order_meta($order_id, $key) {
  $value = edd_get_order_meta( $order_id, $key, true );
  if ( empty ( $value ) ) {
    $payment = new EDD_Payment( $order_id );
    $value = $payment->get_meta( $key );
  }

  return $value;
}

/**
* Add link to Quaderno invoice in payment details page
*
* @since  1.26.0
* @param  array $columns
* @return array
*/
function edd_quaderno_payment_columns( $columns ) {
	$columns['quaderno'] = __( 'Quaderno Invoice', 'edd-quaderno' );
	return $columns;
}
add_filter('edd_payments_table_columns', 'edd_quaderno_payment_columns', 100);

/**
* Add link to Quaderno invoice in payment details page
*
* @since  1.26.0
* @param  string $value
* @param  mixed $payment
* @param  string $column_name
* @return string
*/
function edd_quaderno_payment_column( $value, $payment_id, $column_name ) {
	$payment = new EDD_Payment($payment_id);

	if ( $column_name == 'quaderno' ) {
		if( $payment->get_meta( '_quaderno_invoice_id' ) ) {
			$value = '<a href="' . $payment->get_meta( '_quaderno_url' ) . '" target="_blank">' . __( 'View', 'edd-quaderno' ) . '</a>';
		}
		else {
			$value = '<a href="' . esc_url( add_query_arg( array( 'edd-action' => 'resend_invoice', 'purchase_id' => $payment_id ) ) ) . '">' . __( 'Create', 'edd-quaderno' ) . '</a>';
		}
	}

	return $value;
}
add_filter('edd_payments_table_column', 'edd_quaderno_payment_column', 3, 100);

?>
