<?php
/**
 * Formulaire de login
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 *
 * @todo clean
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

global $post;

do_action( 'wps_before_customer_login_form' ); ?>

<?php
$transient = get_transient( 'login_error_' . $_COOKIE['PHPSESSID'] );
delete_transient( 'login_error_' . $_COOKIE['PHPSESSID'] );

if ( ! empty( $transient ) ) :
	?>
	<div class="notice notice-error ">
		<p><?php echo $transient; ?></p>
	</div>
	<?php
endif;
?>

<form class="wpeo-form" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<?php wp_nonce_field( 'handle_login' ); ?>
	<input type="hidden" name="action" value="wps_login" />
	<input type="hidden" name="page" value="<?php echo Pages::g()->get_slug_link_shop_page( $post->ID ); ?>" />
	<?php do_action( 'wps_login_form_start' ); ?>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Username or email address', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="username" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Password', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="password" class="form-field" name="password" />
		</label>
	</div>

	<?php do_action( 'wps_login_form' ); ?>

	<input class="wpeo-button button-main" type="submit" value="<?php esc_attr_e( 'Log in', 'wphsop' ); ?>" />
	<a class="wpeo-button button-grey" href="<?php echo wp_lostpassword_url(); ?>" title="Lost Password"><?php esc_html_e( 'Lost Password', 'wpshop' ); ?></a>

	<?php do_action( 'wps_login_form_end' ); ?>
</form>

<?php do_action( 'wps_after_customer_login_form' ); ?>
