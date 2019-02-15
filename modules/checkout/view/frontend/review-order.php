<?php
/**
 * Le formulaire pour créer son adresse de livraison
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

<table class="wpeo-table wps-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'wpshop' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			do_action( 'wps_review_order_before_cart_contents' );

			if ( ! empty( $cart_contents ) ) :
				foreach ( $cart_contents as $cart_item ) :
					?>
					<tr>
						<td class="product-name"><?php echo $cart_item['title'] ?> x <?php echo $cart_item['qty']; ?></td>
						<td class="product-total"><?php echo number_format( $cart_item['price'] * $cart_item['qty'], 2 ); ?>€</td>
					</tr>
					<?php
				endforeach;
			endif;

			?>
			<tr>
				<td>Total</td>
				<td><?php echo number_format( Class_Cart_Session::g()->total_price_ttc, 2 ); ?>€</td>
			</tr>
			<?php

			do_action( 'wps_review_order_after_cart_contents' );
		?>
	</tbody>
</table>
