<?php
/**
 * Affichage d'une commande dans le listing de la page des commandes (wps-order)
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
			<li><i class="fas fa-calendar-alt"></i> <?php echo esc_html( $order->data['datec']['rendered']['date_time'] ); ?></li>
		</ul>
		<div class="reference-title">
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $order->data['id'] ) ); ?>"><?php echo esc_html( $order->data['title'] ); ?></a>
		</div>
		<ul class="reference-actions">
			<li><a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $order->data['id'] ) ); ?>"><?php esc_html_e( 'See', 'wpshop' ); ?></a></li>
			<?php if ( ! empty( $order->data['external_id'] ) ) : ?>
				<li><a href="#" target="_blank"><?php esc_html_e( 'See in Dolibarr', 'wpshop' ); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="table-cell table-200">
		<div>
			<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $order->data['tier']->data['id'] . '&action=edit' ) ); ?>">
				<strong><?php echo esc_html( $order->data['tier']->data['title'] ); ?></strong>
			</a>
		</div>
		<div><?php echo esc_html( $order->data['tier']->data['address'] ); ?></div>
		<div><?php echo esc_html( $order->data['tier']->data['zip'] ) . ' ' . esc_html( $order->data['tier']->data['country'] ); ?></div>
		<div><?php echo esc_html( $order->data['tier']->data['phone'] ); ?></div>
	</div>
	<div class="table-cell table-150"><?php echo Payment::g()->make_readable_statut( $order ); ?></div>
	<div class="table-cell table-100"><?php echo esc_html( Payment::g()->get_payment_title( $order->data['payment_method'] ) ); ?></div>
	<div class="table-cell table-100"><strong><?php echo esc_html( number_format( $order->data['total_ttc'], 2, ',', '' ) ); ?>€</strong></div>
	<?php apply_filters( 'wps_order_table_tr', $order ); ?>
	<div class="table-cell table-100">
		<!-- <div class="button-synchro"><i class="fas fa-sync"></i></div>
		<div class="statut statut-green wpeo-tooltip-event" data-direction="left" aria-label="Date de la derniere synchro"></div> -->
	</div>
</div>
