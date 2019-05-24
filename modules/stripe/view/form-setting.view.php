<?php
/**
 * La vue principale de la page de réglages
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<form class="wpeo-form" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
	<input type="hidden" name="action" value="wps_update_method_payment_stripe" />
	<?php wp_nonce_field( 'update_method_payment_stripe' ); ?>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Title', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="title" value="<?php echo esc_attr( $stripe_options['title'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Publish key', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="publish_key" value="<?php echo esc_attr( $stripe_options['publish_key'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Secret key', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="secret_key" value="<?php echo esc_attr( $stripe_options['secret_key'] ); ?>" />
		</label>
	</div>

	<div class="form-element form-align-horizontal">
		<div class="form-field-inline">
			<input type="checkbox" id="use-stripe-sandbox" class="form-field" <?php echo $stripe_options['use_stripe_sandbox'] ? 'checked' : ''; ?> name="use_stripe_sandbox" />
			<label for="use-stripe-sandbox"><?php esc_html_e( 'Stripe Sandbox', 'wpshop' ); ?></label>
		</div>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Description', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<textarea name="description" class="form-field" rows="3" cols="20"><?php echo $stripe_options['description']; ?></textarea>
		</label>
	</div>

	<input type="submit" class="wpeo-button button-main" value="Enregister les modifications" />
</form>
