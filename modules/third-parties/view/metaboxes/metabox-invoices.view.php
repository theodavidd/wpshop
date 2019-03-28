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

<div class="wps-metabox wps-billing-address view gridw-2">
	<h3 class="metabox-title"><?php esc_html_e( 'Billing' ); ?></h3>

	<div class="wpeo-table table-flex table-4">
		<div class="table-row table-header">
			<div class="table-cell"><?php esc_html_e( 'Billing', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( 'Date', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( '€ TTC', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( 'Status', 'wpshop' ); ?></div>
		</div>

		<?php
		if ( ! empty( $invoices ) ) :
			foreach ( $invoices as $invoice ) :
				?>
				<div class="table-row">
					<div class="table-cell">
						<a href="<?php echo esc_attr( $doli_url . '/compta/facture/card.php?id=' . $invoice->data['external_id'] ); ?>">
							<?php echo esc_html( $invoice->data['title'] ); ?>
						</a>
					</div>
					<div class="table-cell"><?php echo esc_html( $invoice->data['date']['rendered']['date'] ); ?></div>
					<div class="table-cell"><?php echo esc_html( number_format( $invoice->data['total_ttc'], 2, ',', '' ) ); ?>€</div>
					<div class="table-cell"><strong><?php echo Payment::g()->make_readable_statut( $invoice ); ?></strong></div>
				</div>
				<?php
			endforeach;
		endif;
		?>
	</div>
</div>
