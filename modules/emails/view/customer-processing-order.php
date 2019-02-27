<?php
/**
 * Admin new order email
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wps_email_header' ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'wpshop' ), esc_html( $data['third_party']['title'] ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<p><?php printf( esc_html__( 'Just to let you know — we\'ve received your order #%s, and it is now being processed:', 'wpshop' ), esc_html( $data['order']->ref ) ); ?></p>

<?php

do_action( 'wps_email_order_details', $data['order'] );

do_action( 'wps_email_order_meta' );

do_action( 'wps_email_customer_details' );

do_action( 'wps_email_footer' );
