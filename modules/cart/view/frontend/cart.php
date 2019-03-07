<?php
/**
 * La vue principale de la page des produits (wps-product)
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

<table class="wpeo-table">
	<thead>
		<tr>
			<th></th>
			<th data-title="<?php esc_html_e( 'Product name', 'wpshop' ); ?>"><?php esc_html_e( 'Product name', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'VAT', 'wpshop' ); ?>"><?php esc_html_e( 'VAT', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'P.U. HT', 'wpshop' ); ?>"><?php esc_html_e( 'P.U HT', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'Quantity', 'wpshop' ); ?>"><?php esc_html_e( 'Quantity', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'Total HT', 'wpshop' ); ?>"><?php esc_html_e( 'Total HT', 'wpshop' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $cart_contents ) ) :
			foreach ( $cart_contents as $key => $cart_item ) :
				?>
				<tr>
					<td><?php echo get_the_post_thumbnail( $cart_item['id'], array( 80, 80 ) ); ?></td>
					<td><a href="<?php echo esc_url( get_permalink( $cart_item['id'] ) ); ?>"><?php esc_html_e( $cart_item['title'] ); ?></a></td>
					<td><?php esc_html_e( number_format( $cart_item['tva_tx'], 2 , ',', '' ) ); ?>%</td>
					<td><?php esc_html_e( number_format( $cart_item['price'], 2, ',', '' ) ); ?>€</td>
					<td><?php esc_html_e( $cart_item['qty'] ); ?></td>
					<td><?php esc_html_e( number_format( $cart_item['price'] * $cart_item['qty'], 2, ',', '' ) ); ?>€</td>
					<td>
						<a href="#" class="action-attribute" data-action="delete_product_from_cart" data-key="<?php echo esc_attr( $key ); ?>">
							<i class="fas fa-trash"></i>
						</a>
					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>
	</tbody>
</table>

<?php do_action( 'wps_after_cart_table' ); ?>
