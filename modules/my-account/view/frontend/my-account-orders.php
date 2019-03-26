<?php
/**
 * Affichage des commandes dans la page "Mon compte"
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

defined( 'ABSPATH' ) || exit;

?>

<table class="wpeo-table">
	<thead>
		<tr>
			<th data-title="Order"><?php esc_html_e( 'Order', 'wpshop' ); ?></th>
			<th data-title="Date"><?php esc_html_e( 'Date', 'wpshop' ); ?></th>
			<th data-title="Status"><?php esc_html_e( 'Status', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Total HT', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Total TTC', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Actions', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $orders ) ) :
			foreach ( $orders as $order ) :
				?>
				<tr>
					<td data-title="<?php echo esc_attr( $order->data['title'] ); ?>"><?php echo esc_html( $order->data['title'] ); ?></td>
					<td data-title="<?php echo esc_attr( $order->data['date_commande']['rendered']['date'] ); ?>"><?php echo esc_html( $order->data['date_commande']['rendered']['date'] ); ?></td>
					<td data-title="N/D"><?php echo Payment::g()->make_readable_statut( $order ); ?></td>
					<td data-title="<?php echo esc_attr( number_format( $order->data['total_ht'], 2, ',', '' ) ); ?>€"><?php echo esc_html( number_format( $order->data['total_ttc'], 2, ',', '' ) ); ?>€</td>
					<td data-title="<?php echo esc_attr( number_format( $order->data['total_ttc'], 2, ',', '' ) ); ?>€"><?php echo esc_html( number_format( $order->data['total_ttc'], 2, ',', '' ) ); ?>€</td>
					<td data-title="View">
						<?php
						if ( ! empty( $order->data['invoice'] ) ) :
							?>
							<a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_invoice&_wpnonce=' . wp_create_nonce( 'download_invoice' ) . '&order_id=' . $order->data['id'] ) ); ?>" class="wpeo-button button-primary">
							<i class="button-icon fas fa-file-download"></i>
							</a>
							<?php
						endif;
						?>

						<div data-action="reorder"
							data-nonce="<?php echo esc_attr( wp_create_nonce( 'do_reorder' ) ); ?>"
							data-id="<?php echo esc_attr( $order->data['id'] ); ?>"
							class="action-attribute wpeo-button button-primary">
						<span><?php esc_html_e( 'Reorder', 'wpshop' ); ?></span>
					</div>

					<div class="wpeo-button button-primary wpeo-modal-event"
					data-id="<?php echo esc_attr( $order->data['id'] ); ?>"
					data-title="Commande <?php echo esc_attr( $order->data['title'] ); ?>"
					data-action="load_modal_resume_order">
						<span><?php esc_html_e( 'Resume', 'wpshop' ); ?></span>
					</div>

					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>

	</tbody>
</table>
