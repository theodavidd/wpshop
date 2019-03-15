<?php
/**
 * Formulaire de configuration pour les chèques.
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

<form class="wpeo-form" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
	<input type="hidden" name="action" value="wps_update_method_payment_cheque" />

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Title', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="title" value="<?php echo esc_attr( $cheque_options['title'] ); ?>" />
		</label>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Description', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<textarea name="description" class="form-field" rows="3" cols="20"><?php echo $cheque_options['description']; ?></textarea>
		</label>
	</div>

	<input type="submit" class="wpeo-button button-main" value="Enregister les modifications" />
</form>
