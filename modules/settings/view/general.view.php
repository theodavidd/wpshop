<?php
/**
 * La vue principale de la page de réglages
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
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
		<span class="form-label">Dolibarr URL</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="dolibarr_url" value="<?php echo esc_attr( $dolibarr_option['dolibarr_url'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Dolibarr Secret</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="dolibarr_secret" value="<?php echo esc_attr( $dolibarr_option['dolibarr_secret'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Email de la boutique</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="shop_email" value="<?php echo esc_attr( $dolibarr_option['shop_email'] ); ?>" />
		</label>
	</div>

	<div>
		<div class="form-element form-element-required">
			<span class="form-label">Taille miniature largeur</span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="thumbnail_size[width]" value="<?php echo esc_attr( $dolibarr_option['thumbnail_size']['width'] ); ?>" />
			</label>
		</div>
	</div>

	<div>
		<div class="form-element form-element-required">
			<span class="form-label">Taille miniature hauteur</span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="thumbnail_size[height]" value="<?php echo esc_attr( $dolibarr_option['thumbnail_size']['height'] ); ?>" />
			</label>
		</div>
	</div>

	<div>
		<input type="submit" class="wpeo-button button-main" value="Enregister les modifications" />
	</div>
</form>
