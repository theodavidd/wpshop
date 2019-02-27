<?php
/**
 * La vue principale de la page des produits (wps-third-party)
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

<form class="wpeo-form" method="post" action="<?php echo $third_party->data['id'] == 0 ? admin_url( 'admin-post.php' ) : admin_url( 'admin-ajax.php' ); ?>">
	<input type="hidden" name="action" value="third_party_save_title" />
	<input type="hidden" name="post_id" value="<?php echo $third_party->data['id']; ?>" />

	<div class="form-element">
		<label class="form-field-container">
			<input type="text" class="form-field" name="title" placeholder="<?php esc_attr_e( 'New third party', 'wpshop' ); ?>" value="<?php echo $third_party->data['title']; ?>" />
			<span class="form-field-label-next">
				<?php
				if ( $third_party->data['id'] == 0 ) :
					?>
					<input type="submit" class="wpeo-button button-square-30" value='Save' />
					<?php
				else :
					?>
					<div data-parent="wpeo-form"
						class="action-input wpeo-button button-square-30">
						<i class="button-icon fas fa-save"></i>
					</div>
					<?php
				endif;
				?>
			</span>
		</label>
	</div>
</form>
