<?php
/**
 * Le formulaire pour régler le paiement en boutique
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 *
 * @todo: Homogéniser les vues ?
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<form class="wpeo-form" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>" />
	<?php wp_nonce_field( $nonce ); ?>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Title', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="title" value="<?php echo esc_attr( $payment_data['title'] ); ?>" />
		</label>
	</div>

	<div class="form-element bloc-activate">
		<span class="form-label"><?php esc_html_e( 'Activate', 'wpshop' ); ?></span>
		<input type="hidden" name="activate" class="activate" value="<?php echo (int) 1 === (int) $payment_data['active'] ? 'true' : 'false'; ?>" />
		<i style="font-size: 2em;" class="toggle fas fa-toggle-<?php echo $payment_data['active'] ? 'on' : 'off'; ?>" data-bloc="bloc-activate" data-input="activate"></i>
	</div>

	<div class="form-element">
		<span class="form-label"><?php esc_html_e( 'Description', 'wpshop' ); ?></span>
		<label class="form-field-container">
			<textarea name="description" class="form-field" rows="3" cols="20"><?php echo $payment_data['description']; ?></textarea>
		</label>
	</div>

	<input type="submit" class="wpeo-button button-main" value="<?php esc_html_e( 'Save Changes', 'wpshop' ); ?>" />
</form>
