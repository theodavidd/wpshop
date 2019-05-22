<?php
/**
 * Affichage d'un devis dans le listing de la page des devis (wps-proposals)
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
	<div class="table-cell table-full">
		<ul class="reference-id">
			<li><i class="fas fa-calendar-alt"></i> <?php echo esc_html( $proposal->data['datec']['rendered']['date_time'] ); ?></li>
		</ul>
		<div class="reference-title">
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-proposal&id=' . $proposal->data['id'] ) ); ?>"><?php echo esc_html( $proposal->data['title'] ); ?></a>
		</div>
		<ul class="reference-actions">
			<li><a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-proposal&id=' . $proposal->data['id'] ) ); ?>"><?php esc_html_e( 'See', 'wpshop' ); ?></a></li>
			<?php if ( ! empty( $proposal->data['external_id'] ) ) : ?>
				<li><a href="<?php echo esc_attr( $doli_url ); ?>comm/propal/card.php?id=<?php echo $proposal->data['external_id']; ?>" target="_blank"><?php esc_html_e( 'See in Dolibarr', 'wpshop' ); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="table-cell table-200">
		<div><strong><?php echo esc_html( $proposal->data['tier']->data['title'] ); ?></strong></div>
		<div><?php echo esc_html( $proposal->data['tier']->data['address'] ); ?></div>
		<div><?php echo esc_html( $proposal->data['tier']->data['zip'] ) . ' ' . esc_html( $proposal->data['tier']->data['country'] ); ?></div>
		<div><?php echo ! empty( $proposal->data['tier']->data['phone'] ) ? esc_html( $proposal->data['tier']->data['phone'] ) : ''; ?></div>
	</div>
	<div class="table-cell table-150"><?php echo Payment::g()->make_readable_statut( $proposal ); ?></div>
	<div class="table-cell table-100"><?php echo esc_html( Payment::g()->get_payment_title( $proposal->data['payment_method'] ) ); ?></div>
	<div class="table-cell table-100"><strong><?php echo esc_html( number_format( $proposal->data['total_ttc'], 2, ',', '' ) ); ?>€</strong></div>
	<?php apply_filters( 'wps_proposal_table_tr', $proposal ); ?>
	<?php do_action( 'wps_listing_table_end', $proposal, 'proposals' ); ?>
</div>
