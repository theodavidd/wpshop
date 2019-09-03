<?php
/**
 * Bouton pour aller au panier.
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

<div class="wpeo-notification notification-active notification-add-to-cart notification-blue">
	<i class="notification-icon fas fa-info"></i>
	<div class="notification-title">
		<?php printf( __( 'Product "%1$s" x%2$d added to the card', 'wpshop' ), $product->data['title'], $qty ); ?>

		<a href="<?php echo esc_url( Pages::g()->get_cart_link() ); ?>" class="view-cart wpeo-button button-grey">
			<?php esc_html_e( 'View cart', 'wpshop' ); ?>
		</a>
	</div>
	<div class="notification-close"><i class="far fa-times"></i></div>
</div>
