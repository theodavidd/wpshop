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

<div class="cart">
	<table class="wpeo-table">
		<thead>
			<tr>
				<th></th>
				<th data-title="<?php esc_html_e( 'Product name', 'wpshop' ); ?>"><?php esc_html_e( 'Product name', 'wpshop' ); ?></th>
				<th data-title="<?php esc_html_e( 'VAT', 'wpshop' ); ?>"><?php esc_html_e( 'VAT', 'wpshop' ); ?></th>
				<th data-title="<?php esc_html_e( 'P.U. HT', 'wpshop' ); ?>"><?php esc_html_e( 'P.U HT', 'wpshop' ); ?></th>
				<th style="width: 60px;" data-title="<?php esc_html_e( 'Quantity', 'wpshop' ); ?>"><?php esc_html_e( 'Quantity', 'wpshop' ); ?></th>
				<th data-title="<?php esc_html_e( 'Total HT', 'wpshop' ); ?>"><?php esc_html_e( 'Total HT', 'wpshop' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( ! empty( $cart_contents ) ) :
				foreach ( $cart_contents as $key => $cart_item ) :
					?>
					<input type="hidden" name="products[<?php echo esc_attr( $key ); ?>][id]" value="<?php echo esc_attr( $cart_item['id'] ); ?>" />
					<tr>
						<td><?php echo get_the_post_thumbnail( $cart_item['id'], array( 80, 80 ) ); ?></td>
						<td><a href="<?php echo esc_url( get_permalink( $cart_item['id'] ) ); ?>"><?php echo esc_html( $cart_item['title'] ); ?></a></td>
						<td><?php echo esc_html( number_format( $cart_item['tva_tx'], 2, ',', '' ) ); ?>%</td>
						<td><?php echo esc_html( number_format( $cart_item['price'], 2, ',', '' ) ); ?>€</td>
						<td style="width: 60px;">
							<?php
							if ( $shipping_cost_option['shipping_product_id'] === $cart_item['id'] ) :
								?>
								-
								<?php
							else :
								?>
								<input style="width: 60px;" class="cart-qty" type="number" name="products[<?php echo esc_attr( $key ); ?>][qty]" value="<?php echo esc_html( $cart_item['qty'] ); ?>" />
								<?php
							endif;
							?>
						</td>
						<td><?php echo esc_html( number_format( $cart_item['price'] * $cart_item['qty'], 2, ',', '' ) ); ?>€</td>
						<td>
							<?php
							if ( $shipping_cost_option['shipping_product_id'] !== $cart_item['id'] ) :
								?>
								<a href="#" class="action-attribute" data-action="delete_product_from_cart" data-key="<?php echo esc_attr( $key ); ?>">
									<i class="fas fa-trash"></i>
								</a>
								<?php
							endif;
							?>
						</td>
					</tr>
					<?php
				endforeach;
			endif;
			?>
		</tbody>
	</table>

	<div data-parent="cart" data-action="wps_update_cart"
		class="update-cart wpeo-button action-input button-disable"
		wpeo-before-cb="wpshopFrontend/cart/makeLoadOnAllCart">
		<?php esc_html_e( 'Update cart', 'wpshop' ); ?>
	</div>
</div>


<?php do_action( 'wps_after_cart_table' ); ?>
