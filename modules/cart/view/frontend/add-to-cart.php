<?php
/**
 * Le bouton add to cart
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

<div class="wps-product-action">
	<div class="wps-product-buy wpeo-button action-attribute <?php echo apply_filters( 'wps_product_add_to_cart_class', '', $product ); ?>"
		<?php echo apply_filters( 'wps_product_add_to_cart_attr', '', $product ); ?>
		data-action="add_to_cart"
		data-nonce="<?php echo wp_create_nonce( 'add_to_cart' ); ?>"
		data-id="<?php echo esc_attr( $product->data['id'] ); ?>"><?php echo esc_html( $a['text'] ); ?></div>
</div>
