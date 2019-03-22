<?php
/**
 * Affichage d'une facture dans le listing de la page des commandes (wps-invoice)
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

<div class="table-row">
	<div class="table-cell table-25"><input type="checkbox" class="check"/></div>
	<div class="table-cell table-full">
		<ul class="reference-id">
			<li><i class="fas fa-calendar-alt"></i> <?php echo esc_html( $invoice->data['date']['rendered']['date_time'] ); ?></li>
		</ul>
		<div class="reference-title">
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-invoice&id=' . $invoice->data['id'] ) ); ?>"><?php echo esc_html( $invoice->data['title'] ); ?></a>
		</div>
		<ul class="reference-actions">
			<li><a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-invoice&id=' . $invoice->data['id'] ) ); ?>"><?php esc_html_e( 'See', 'wpshop' ); ?></a></li>
			<?php if ( ! empty( $invoice->data['external_id'] ) ) : ?>
				<li><a href="#" target="_blank"><?php esc_html_e( 'See in Dolibarr', 'wpshop' ); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="table-cell table-full">
		<div class="reference-title">
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $invoice->data['order']->data['id'] ) ); ?>"><?php echo esc_html( $invoice->data['order']->data['title'] ); ?></a>
		</div>
		<ul class="reference-actions">
			<li><a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $invoice->data['order']->data['id'] ) ); ?>"><?php esc_html_e( 'See', 'wpshop' ); ?></a></li>
			<?php if ( ! empty( $invoice->data['order']->data['external_id'] ) ) : ?>
				<li><a href="#" target="_blank"><?php esc_html_e( 'See in Dolibarr', 'wpshop' ); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="table-cell table-200">
		<?php
		if ( ! empty( $invoice->data['tier'] ) ) :
			?>
			<div><strong><?php echo esc_html( $invoice->data['tier']->data['title'] ); ?></strong></div>
			<div><?php echo esc_html( $invoice->data['tier']->data['address'] ); ?></div>
			<div><?php echo esc_html( $invoice->data['tier']->data['zip'] ) . ' ' . esc_html( $invoice->data['tier']->data['country'] ); ?></div>
			<div><?php echo esc_html( $invoice->data['tier']->data['phone'] ); ?></div>
			<?php
		endif;
		?>
	</div>
	<div class="table-cell table-150"><?php echo Payment::g()->convert_status( $invoice->data ); ?></div>
	<div class="table-cell table-100"><?php echo esc_html( $invoice->data['payment_method'] ); ?></div>
	<div class="table-cell table-100"><strong><?php echo esc_html( number_format( $invoice->data['total_ttc'], 2, ',', '' ) ); ?>€</strong></div>
	<?php apply_filters( 'wps_order_table_tr', $invoice ); ?>
	<div class="table-cell table-100">
		<!-- <div class="button-synchro"><i class="fas fa-sync"></i></div>
		<div class="statut statut-green wpeo-tooltip-event" data-direction="left" aria-label="Date de la derniere synchro"></div> -->
	</div>
</div>
