<?php
/**
 * La vue du tableau récapitulatif du panier.
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
			<div data-parent="wps-cart" data-action="wps_update_cart"
				class="wpeo-util-hidden update-cart wpeo-button action-input">
				<?php esc_html_e( 'Update cart', 'wpshop' ); ?>
			</div>
		</div>


		<div>
			<?php Cart::g()->display_cart_resume( $total_price_no_shipping, $tva_amount, $total_price_ttc, $shipping_cost ); ?>

			<?php do_action( 'wps_after_cart_resume' ); ?>
		</div>
	</div>

	<?php do_action( 'wps_after_cart_table' ); ?>
</div>
