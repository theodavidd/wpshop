<?php
/**
 * La vue du tableau récapitulatif du panier.
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

<?php do_action( 'wps_before_cart_table' ); ?>

<div class="wps-cart">
	<?php wp_nonce_field( 'ajax_update_cart' ); ?>

	<div class="wpeo-gridlayout grid-3">
		<div class="wps-list-product gridw-2">
			<?php
			if ( ! empty( $cart_contents ) ) :
				foreach ( $cart_contents as $key => $product ) :
					if ( $shipping_cost_option['shipping_product_id'] !== $product['id'] ) :
						include( Template_Util::get_template_part( 'products', 'wps-product-list-edit' ) );
					endif;
				endforeach;
			endif;
			?>
		</div>

		<ul class="wps-cart-resume">
			<?php do_action( 'wps_before_cart_resume' ); ?>
			<li class="wps-resume-line">
				<span class="wps-line-content"><?php echo esc_html( $shipping_cost_product->data['title'] ); ?></span>
				<span class="wps-line-value"><?php echo esc_html( $shipping_cost_product->data['price_ttc'] ); ?></span>
			</li>
			<li class="wps-resume-line-featured">
				<span class="wps-line-content"><?php esc_html_e( 'Total TTC', 'wpshop' ); ?></span>
				<span class="wps-line-value"><</span>
			</li>
			<?php do_action( 'wps_after_cart_resume' ); ?>
		</ul>
	</div>

	<div data-parent="wps-cart" data-action="wps_update_cart"
		class="update-cart wpeo-button action-input button-disable">
		<?php esc_html_e( 'Update cart', 'wpshop' ); ?>
	</div>

	<?php do_action( 'wps_after_cart_table' ); ?>
</div>
