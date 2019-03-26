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

<div class="wps-metabox wps-billing-address view gridw-2">
	<h3 class="metabox-title"><?php esc_html_e( 'Proposals' ); ?></h3>

	<div class="wpeo-table table-flex table-4">
		<div class="table-row table-header">
			<div class="table-cell"><?php esc_html_e( 'Proposal', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( 'Date', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( '€ TTC', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( 'Status', 'wpshop' ); ?></div>
		</div>

		<?php
		if ( ! empty( $proposals ) ) :
<<<<<<< HEAD
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
											?>
											<span><?php echo esc_html( $line['libelle'] ); ?></span>
											<span>x</span>
											<span><?php echo esc_html( $line['qty'] ); ?></span>
											<span>-</span>
											<span><?php echo esc_html( $line['price'] ); ?></span>
											<?php
										else :
											?>
											<span> <?php echo esc_html( $line['desc'] ); ?></span>
											<span>x</span>
											<span><?php echo esc_html( $line['qty'] ); ?></span>
											<span>-</span>
											<span><?php echo esc_html( $line['price'] ); ?></span>
											<?php
										endif;
										?>
										<span>€</span>
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
=======
			foreach ( $proposals as $proposal ) : ?>
				<div class="table-row">
					<div class="table-cell">
						<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-proposal&id=' . $proposal->data['id'] ) ); ?>">
							<?php echo esc_html( $proposal->data['title'] ); ?>
						</a>
					</div>
					<div class="table-cell"><?php echo esc_html( $proposal->data['datec']['rendered']['date'] ); ?></div>
					<div class="table-cell"><?php echo esc_html( number_format( $proposal->data['total_ttc'], 2, ',', '' ) ); ?>€</div>
					<div class="table-cell"><strong><?php echo Payment::g()->make_readable_statut( $proposal ); ?></strong></div>
				</div> <?php
>>>>>>> b222688d19ab40a2ee19839eca0f84eb1201eaef
			endforeach;
		endif;
		?>
	</div>
</div>
