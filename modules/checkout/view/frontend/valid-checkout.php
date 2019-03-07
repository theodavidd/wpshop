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

Merci. Votre commande a été reçue.

<ul>
	<li>Numéro de commande: <strong><?php echo esc_html( $order->data['title'] ); ?></strong></li>
	<li>Date: <strong><?php echo esc_html( $order->data['date_commande']['rendered']['date'] ); ?></strong></li>
	<li>Total: <strong><?php echo esc_html( number_format( $order->data['total_ttc'], 2 ) ); ?>€</strong></li>
	<li>Moyen de paiement: <strong><?php echo esc_html( $order->data['payment_method'] ); ?></strong></li>
</ul>

<h2>Détails de la commande</h2>

<table class="wpeo-table">
	<thead>
		<tr>
			<th></th>
			<th data-title="<?php esc_html_e( 'Product name', 'wpshop' ); ?>"><?php esc_html_e( 'Product name', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'TVA', 'wpshop' ); ?>"><?php esc_html_e( 'TVA', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'P.U. HT', 'wpshop' ); ?>"><?php esc_html_e( 'P.U HT', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'Quantity', 'wpshop' ); ?>"><?php esc_html_e( 'Quantity', 'wpshop' ); ?></th>
			<th data-title="<?php esc_html_e( 'Total HT', 'wpshop' ); ?>"><?php esc_html_e( 'Total HT', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if ( ! empty( $order->data['lines'] ) ) :
				foreach ( $order->data['lines'] as $line ) :
					?>
					<tr>
						<td><?php echo get_the_post_thumbnail( $line['id'], array( 80, 80 ) ); ?></td>
						<td><a href="<?php echo esc_url( get_permalink( $line['id'] ) ); ?>"><?php esc_html_e( $line['libelle'] ); ?></a></td>
						<td><?php esc_html_e( number_format( $line['tva_tx'], 2 , ',', '' ) ); ?>%</td>
						<td><?php esc_html_e( number_format( $line['price'], 2, ',', '' ) ); ?>€</td>
						<td><?php esc_html_e( $line['qty'] ); ?></td>
						<td><?php esc_html_e( number_format( $line['price'] * $line['qty'], 2, ',', '' ) ); ?>€</td>
					</tr>
					<?php
				endforeach;
			endif;
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5"><strong>Total HT</strong></td>
			<td><?php echo number_format( $order->data['total_ht'], 2, ',', '' ); ?>€</td>
		</tr>
		<?php
		if ( ! empty( $tva_lines ) ) :
			foreach ( $tva_lines as $key => $tva_line ) :
				?>
				<tr>
					<td colspan="5"><strong>Total TVA <?php echo number_format( $key, 2, ',', '' ); ?>%</strong></td>
					<td><?php echo number_format( $tva_line, 2, ',', '' ); ?>€</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>

		<tr>
			<td colspan="5"><strong>Total TTC</strong></td>
			<td><strong><?php echo number_format( $order->data['total_ttc'], 2, ',', '' ); ?>€</strong></td>
		</tr>
	</tfoot>
</table>

<a href="<?php echo Pages_Class::g()->get_account_link(); ?>orders/" class="wpeo-button button-main">
	<span>Voir mes commandes</span>
</a>
