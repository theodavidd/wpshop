<?php
/**
 * Affichage des devis dans la page "Mon compte".
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
			<th data-title="Proposal"><?php esc_html_e( 'Proposal', 'wpshop' ); ?></th>
			<th data-title="Date"><?php esc_html_e( 'Date', 'wpshop' ); ?></th>
			<th data-title="Status"><?php esc_html_e( 'Status', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Total', 'wpshop' ); ?></th>
			<th data-title="Total"><?php esc_html_e( 'Actions', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $proposals ) ) :
			foreach ( $proposals as $proposal ) :
				?>
				<tr>
					<th data-title="<?php echo esc_attr( $proposal->data['title'] ); ?>"><?php echo esc_html( $proposal->data['title'] ); ?></th>
					<td data-title="<?php echo esc_attr( $proposal->data['datec']['rendered']['date'] ); ?>"><?php echo esc_html( $proposal->data['datec']['rendered']['date'] ); ?></td>
					<td data-title="N/D">N/D</td>
					<td data-title="<?php echo esc_attr( number_format( $proposal->data['total_ttc'], 2 ) ); ?>€"><?php echo esc_html( number_format( $proposal->data['total_ttc'], 2 ) ); ?>€</td>
					<td data-title="View">
						<?php do_action( 'wps_my_account_proposals_actions', $proposal ); ?>
					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>
	</tbody>
</table>
