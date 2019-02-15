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

<table class="wpeo-table">
	<thead>
		<tr>
			<th>Product</th>
			<th>Price</th>
			<th>Qty</th>
			<th>Total</th>
		</tr>
	</thead>

	<tbody>
		<?php
		if ( ! empty( $order->data['lines'] ) ) :
			foreach ( $order->data['lines'] as $line ) :
				?>
				<tr>
					<td><?php echo $line['libelle']; ?></td>
					<td><?php echo number_format( $line['price'], 2); ?>€</td>
					<td>x 1</td>
					<td><?php echo number_format( $line['price'], 2 ); ?>€</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>

		<tr>
			<td></td>
			<td></td>
			<td>Total:</td>
			<td><?php echo number_format( $order->data['total_ttc'], 2 ); ?>€</td>
		</tr>
	</tbody>
</table>
