<?php
/**
 * Admin new order email
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wps_email_header' ); ?>

<p>Reset password</p>

<?php
do_action( 'wps_email_order_details' );

do_action( 'wps_email_order_meta' );

do_action( 'wps_email_customer_details' );

do_action( 'wps_email_footer' );
