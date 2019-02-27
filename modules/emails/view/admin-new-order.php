<?php
/**
 * Admin new order email
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wps_email_header' ); ?>

<p><?php printf( __( 'You’ve received the following order from %s:', 'wpshop' ), $data['third_party']['title'] ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
<?php

do_action( 'wps_email_order_details', $data['order'] );

do_action( 'wps_email_order_meta' );

do_action( 'wps_email_customer_details' );

do_action( 'wps_email_footer' );
