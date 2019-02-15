<?php
/**
 * Login Form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} ?>

<div class="checkout-login hide">
	<div class="wpeo-button button-main button-size-large">
		<span><?php esc_html_e( 'Returning customer? Click here to login', 'wpshop' ); ?></span>
	</div>
	<div class="content-login" style="display: none;">
		<?php include( \wpshop\Template_Util::get_template_part( 'my-account', 'form-login' ) ); ?>
	</div>
</div>
