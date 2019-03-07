<?php
/**
 * Login Form
 *
 */
namespace wpshop;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

do_action( 'wps_before_customer_login_form' ); ?>

<form class="wpeo-form" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<input type="hidden" name="action" value="wps_login" />
	<input type="hidden" name="page" value="<?php echo Pages_Class::g()->get_slug_link_shop_page( $post->ID ); ?>" />
	<?php do_action( 'wps_login_form_start' ); ?>

	<div class="form-element">
		<span class="form-label">Username or email address</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="username" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Password</span>
		<label class="form-field-container">
			<input type="password" class="form-field" name="password" />
		</label>
	</div>

	<?php do_action( 'wps_login_form' ); ?>

	<input class="wpeo-button button-main" type="submit" value="<?php esc_attr_e( 'Log in', 'wphsop' ); ?>" />

	<?php do_action( 'wps_login_form_end' ); ?>
</form>

<?php do_action( 'wps_after_customer_login_form' ); ?>
