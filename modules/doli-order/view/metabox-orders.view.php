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
			<th>Product name</th>
			<th>TVA</th>
			<th>P.U HT</th>
			<th>Quantity</th>
			<th>Total HT</th>
		</tr>
	</thead>

	<tbody>
		<?php
		if ( ! empty( $order->data['lines'] ) ) :
			foreach ( $order->data['lines'] as $line ) :
				?>
				<tr>
					<td><?php echo $line['libelle']; ?></td>
					<td><?php echo number_format( $line['tva_tx'], 2, ',', '' ); ?>%</td>
					<td><?php echo number_format( $line['price'], 2, ',', '' ); ?>€</td>
					<td><?php echo $line['qty']; ?></td>
					<td><?php echo number_format( $line['price'], 2, ',', '' ); ?>€</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>

		<tr>
			<td colspan="3"></td>
			<td>Total HT</td>
			<td><?php echo number_format( $order->data['total_ht'], 2, ',', '' ); ?>€</td>
		</tr>
		<?php
		if ( ! empty( $tva_lines ) ) :
			foreach ( $tva_lines as $key => $tva_line ) :
				?>
				<tr>
					<td colspan="3"></td>
					<td>Total TVA <?php echo number_format( $key, 2, ',', '' ); ?>%</td>
					<td><?php echo number_format( $tva_line, 2, ',', '' ); ?>€</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>

		<tr>
			<td colspan="3"></td>
			<td>Total TTC</td>
			<td><?php echo number_format( $order->data['total_ttc'], 2, ',', '' ); ?>€</td>
		</tr>
	</tbody>
</table>
