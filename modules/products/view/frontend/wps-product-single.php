<?php
/**
 * Single Product view.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2006-2018 Eoxia <dev@eoxia.com>
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 * @package   WPshop\Templates
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

$product = Product::g()->get( array( 'id' => get_the_ID() ), true ); ?>

<div class="wps-product-content">
	<div class="wps-product-price"><?php echo ! empty( $product->data['price'] ) ? esc_html( number_format( $product->data['price_ttc'], 2, ',', '' ) ) . ' €' : ''; ?></div>
	<div class="wps-product-description"><?php echo apply_filters( 'wps-product-single', $post->post_content, $product ); ?></div>
	<div class="wps-product-buy wpeo-button action-attribute <?php echo apply_filters( 'wps-product-add-to-cart-class', '', $product ); ?>"
		<?php echo apply_filters( 'wps-product-add-to-cart-attr', '', $product ); ?>
		data-action="add_to_cart"
		data-nonce="<?php echo wp_create_nonce( 'add_to_cart' ); ?>"
		data-id="<?php echo esc_attr( the_ID() ); ?>"><?php esc_html_e( 'Add to cart', 'wpshop' ); ?></div>
</div>
