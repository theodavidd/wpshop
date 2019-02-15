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
			<th data-title="<?php esc_html_e( 'Price', 'wpshop' ); ?>"><?php esc_html_e( 'Price', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'Quantity', 'wpshop' ); ?>"><?php esc_html_e( 'Quantity', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'Total', 'wpshop' ); ?>"><?php esc_html_e( 'Total', 'wpshop' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $cart_contents ) ) :
			foreach ( $cart_contents as $cart_item ) :
				?>
				<tr>
					<td>A</td>
					<td><?php esc_html_e( $cart_item['title'] ); ?></td>
					<td><?php esc_html_e( number_format( $cart_item['price'], 2 ) ); ?>€</td>
					<td><?php esc_html_e( $cart_item['qty'] ); ?></td>
					<td><?php esc_html_e( number_format( $cart_item['price'] * $cart_item['qty'], 2 ) ); ?>€</td>
					<td>B</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>
	</tbody>
</table>

<?php do_action( 'wps_after_cart_table' ); ?>
