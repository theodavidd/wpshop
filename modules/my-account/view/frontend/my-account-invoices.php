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
			<th data-title="Invoices"><?php esc_html_e( 'Invoice', 'wpshop' ); ?></th>
			<th data-title="Date"><?php esc_html_e( 'Date', 'wpshop' ); ?></th>
			<th data-title="Status"><?php esc_html_e( 'Status', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Total HT', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Total TTC', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Actions', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $invoices ) ) :
			foreach ( $invoices as $invoice ) :
				?>
				<tr>
					<td data-title="<?php echo esc_attr( $invoice->data['title'] ); ?>"><?php echo esc_html( $invoice->data['title'] ); ?></td>
					<td data-title="<?php echo esc_attr( $invoice->data['date']['rendered']['date'] ); ?>"><?php echo esc_html( $invoice->data['date']['rendered']['date'] ); ?></td>
					<td data-title="N/D"><?php echo Payment::g()->make_readable_statut( $invoice ); ?></td>
					<td data-title="<?php echo esc_attr( number_format( $invoice->data['total_ht'], 2, ',', '' ) ); ?>€"><?php echo esc_html( number_format( $invoice->data['total_ttc'], 2, ',', '' ) ); ?>€</td>
					<td data-title="<?php echo esc_attr( number_format( $invoice->data['total_ttc'], 2, ',', '' ) ); ?>€"><?php echo esc_html( number_format( $invoice->data['total_ttc'], 2, ',', '' ) ); ?>€</td>
					<td data-title="View">
						<a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_invoice&_wpnonce=' . wp_create_nonce( 'download_invoice' ) . '&avoir=' . $invoice->data['avoir'] . '&order_id=' . $invoice->data['parent_id'] ) ); ?>" class="wpeo-button button-primary">
							<i class="button-icon fas fa-file-download"></i>
						</a>
					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>

	</tbody>
</table>
