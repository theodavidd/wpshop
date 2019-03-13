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
<p><?php printf( esc_html__( 'Hi %s,', 'wpshop' ), esc_html( $data['contact']['lastname'] . ' ' . $data['contact']['firstname'] ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<p><?php printf( esc_html__( 'Your invoice #%s for your order #%s', 'wpshop' ), esc_html( $data['invoice']->data['title'] ), esc_html( $data['order']->data['title'] ) ); ?></p>
<?php

do_action( 'wps_email_invoice_details' );

do_action( 'wps_email_invoice_meta' );

do_action( 'wps_email_customer_details' );

do_action( 'wps_email_footer' );
