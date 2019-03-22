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
					<td><?php echo esc_html( $proposal->data['datec']['rendered']['date'] ); ?></td>
					<td>
						<ul>
							<?php
							if ( ! empty( $proposal->data['lines'] ) ) :
								foreach ( $proposal->data['lines'] as $line ) :
									?>
									<li>
										<?php
										if ( ! empty( $line['fk_product'] ) ) :
											echo esc_html( $line['libelle'] ); ?> x<?php echo esc_html( $line['qty'] ); ?> - <?php echo esc_html( $line['price'] );
										else:
											echo esc_html( $line['desc'] ); ?> x<?php echo esc_html( $line['qty'] ); ?> - <?php echo esc_html( $line['price'] );

										endif;
										?>€
									</li>
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
						-
					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>
	</tbody>
</table>
