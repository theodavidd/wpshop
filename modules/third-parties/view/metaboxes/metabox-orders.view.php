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
			<th>Commande</th>
			<th>Date</th>
			<th>Contenu  HT</th>
			<th>Status</th>
			<th>Paiement</th>
			<th>Montant TTC</th>
			<th>Facture</th>
		</tr>
	</thead>

	<tbody>
		<?php
		if ( ! empty( $orders ) ) :
			foreach ( $orders as $order ) :
				?>
				<tr>
					<td>
						<a target="_blank" href="<?php echo admin_url( 'admin.php?page=wps-order&id=' . $order->data['id'] ); ?>">#<?php echo esc_html( $order->data['title'] ); ?></a>
					</td>
					<td><?php echo esc_html( $order->data['datec']['rendered']['date_time'] ); ?></td>
					<td>
						<ul>
							<?php
							if ( ! empty( $order->data['lines'] ) ) :
								foreach ( $order->data['lines'] as $line ) :
									?>
									<li><?php echo esc_html( $line['libelle'] ); ?> x<?php echo esc_html( $line['qty'] ); ?> - <?php echo esc_html( $line['price'] ); ?>€</li>
									<?php
								endforeach;
							endif;
							?>
						</ul>
					</td>
					<td><?php echo esc_html( Payment_Class::g()->convert_status( $order->data ) ); ?></td>
					<td><?php echo esc_html( $order->data['payment_method'] ); ?></td>
					<td><?php echo esc_html( $order->data['total_ttc'] ); ?>€</td>
					<td>
						<?php
						if ( ! empty( $order->data['invoice'] ) ) :
							?><a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_invoice&order_id=' . $order->data['id'] ) ); ?>"><i class="fas fa-file-download"></i></a><?php
						else:
							?>
							-
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
