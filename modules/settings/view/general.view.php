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

<form class="wpeo-form wpeo-grid grid-2 grid-padding-1" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo esc_attr( 'wps_update_general_settings' ); ?>" />
	<input type="hidden" name="tab" value="general" />
	<?php wp_nonce_field( 'callback_update_general_settings' ); ?>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Dolibarr URL', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="dolibarr_url" value="<?php echo esc_attr( $dolibarr_option['dolibarr_url'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">
			<span><?php esc_html_e( 'Dolibarr Secret Key', 'wpshop' ); ?></span>
			<span class="wpeo-button button-square-40 button-rounded wpeo-tooltip-event" aria-label="<?php esc_attr_e( 'Secret key used for sell with Dolibarr', 'wpshop' ); ?>">?</span>
			<?php
			if (Settings::g()->dolibarr_is_active()):
				?>
				<span class="wpeo-button button-light button-square-40 button-rounded wpeo-tooltip-event" aria-label="<?php esc_attr_e( 'Connected to Dolibarr', 'wpshop' ); ?>">🥦</span>
				<?php
			else:
				?>
				<span class="wpeo-button button-light button-square-40 button-rounded wpeo-tooltip-event" aria-label="<?php esc_attr_e( 'Connection to dolibarr failed', 'wpshop' ); ?>">❌</span>
				<?php
			endif;
			?>
		</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="dolibarr_secret" value="<?php echo esc_attr( $dolibarr_option['dolibarr_secret'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">
			<span><?php esc_html_e( 'Dolibarr Public Key (Optional)', 'wpshop' ); ?></span>
			<span class="wpeo-button button-square-40 button-rounded wpeo-tooltip-event" aria-label="<?php esc_attr_e( 'Public key used for your theme', 'wpshop' ); ?>">?</span>
		</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="dolibarr_public_key" value="<?php echo esc_attr( $dolibarr_option['dolibarr_public_key'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Shop Email', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="shop_email" value="<?php echo esc_attr( $dolibarr_option['shop_email'] ); ?>" />
		</label>
	</div>

	<div>
		<div class="form-element form-element-required">
			<span class="form-label"><?php esc_html_e( 'WPshop width size', 'wpshop' ); ?></span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="thumbnail_size[width]" value="<?php echo esc_attr( $dolibarr_option['thumbnail_size']['width'] ); ?>" />
			</label>
		</div>
	</div>

	<div>
		<div class="form-element form-element-required">
			<span class="form-label"><?php esc_html_e( 'WPshop height size', 'wpshop' ); ?></span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="thumbnail_size[height]" value="<?php echo esc_attr( $dolibarr_option['thumbnail_size']['height'] ); ?>" />
			</label>
		</div>
	</div>

	<div class="form-element form-align-horizontal">
		<div class="form-field-inline">
			<input type="checkbox" id="use_quotation" class="form-field" name="use_quotation" <?php echo $dolibarr_option['use_quotation'] ? 'checked="checked"' : ''; ?>>
			<label for="use_quotation"><?php esc_html_e( 'Use quotation', 'wpshop' ); ?></label>
		</div>
	</div>

	<div>
		<input type="submit" class="wpeo-button button-main" value="<?php esc_html_e( 'Save Changes', 'wpshop' ); ?>" />
	</div>
</form>
