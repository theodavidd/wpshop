<?php
/**
 * La vue affichant les devis d'un tier dans la page single d'un tier.
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
			<th><?php esc_html_e( 'Proposal', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Date', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Contenu', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Status', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Paiement', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Montant', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Facture', 'wpshop' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		if ( ! empty( $proposals ) ) :
			foreach ( $proposals as $proposal ) :
				?>
				<tr>
					<td>#<?php echo esc_html( $proposal->data['id'] ); ?></td>
					<td><?php echo esc_html( $proposal->data['date_commande']['rendered']['date'] ); ?></td>
					<td>
						<ul>
							<?php
							if ( ! empty( $proposal->data['lines'] ) ) :
								foreach ( $proposal->data['lines'] as $line ) :
									?>
									<li><?php echo esc_html( $line['libelle'] ); ?> - <?php echo esc_html( $line['price'] ); ?>€</li>
									<?php
								endforeach;
							endif;
							?>
						</ul>
					</td>
					<td>Non validée</td>
					<td>-</td>
					<td><?php echo esc_html( $proposal->data['total_ttc'] ); ?>€</td>
					<td>
						<?php
						if ( ! empty( $proposal->data['invoice'] ) ) :
							?>
								<a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_invoice&order_id=' . $proposal->data['id'] ) ); ?>"><i class="fas fa-file-download"></i></a>
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
