<?php
/**
 * Admin new order email
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wps_email_header' );  ?>

<?php /* translators: %s Customer username */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'wpshop' ), esc_html( $data['contact']['login'] ) ); ?></p>
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
<p><?php printf( __( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: <a target="_blank" href="%3$s">%4$s</a>', 'wpshop' ), esc_html( get_bloginfo() ), '<strong>' . esc_html( $data['contact']['login'] ) . '</strong>', ( esc_url( \wpshop\Pages_Class::g()->get_account_link() ) ), get_bloginfo() ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

<p><?php printf( __( 'Click here to create your password: <strong><a href="%s">Create my password</a></strong>', 'wpshop' ), $data['url'] ); ?></p>

<p><?php esc_html_e( 'We look forward to seeing you soon.', 'wpshop' ); ?></p>

<?php

do_action( 'wps_email_footer' );
