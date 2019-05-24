<?php
/**
 * Le contenu de la modal de synchronisation.
 *
 * @todo: template pas OK
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<div class="wpeo-modal need-to-confirm modal-active">

	<div class="modal-container" style="max-width: 900px; max-height: 800px;">

		<!-- Entête -->
		<div class="modal-header">
			<h2 class="modal-title">Synchroniser depuis WordPress ou depuis Dolibarr ?</h2>
		</div>


		<!-- Corps -->
		<div class="modal-content">

			<div class="wpeo-gridlayout grid-2">
				<div class="choose wp">
					<h2>WordPress</h2>

					<p>Dernière modification le <?php echo date( 'd/m/Y H:i:s', $date_wp ); ?></p>

					<ul>
						<li><strong><?php esc_html_e( 'Tier name', 'wpshop' ); ?></strong>: <?php echo ! empty( $wp_entity->data['title'] ) ? esc_html( $wp_entity->data['title'] ) : 'Non définie'; ?></li>
						<li><strong>Adresse</strong>: <?php echo ! empty( $wp_entity->data['address'] ) ? esc_html( $wp_entity->data['address'] ) : 'Non définie'; ?></li>
						<li><strong>Code postal</strong>: <?php echo ! empty( $wp_entity->data['zip'] ) ? esc_html( $wp_entity->data['zip'] ) : 'Non définie'; ?></li>
						<li><strong>Ville</strong>: <?php echo ! empty( $wp_entity->data['town'] ) ? esc_html( $wp_entity->data['town'] ) : 'Non définie'; ?></li>
						<li><strong>Pays</strong>: <?php echo ! empty( $wp_entity->data['country'] ) ? esc_html( $wp_entity->data['country'] ) : 'Non définie'; ?></li>
						<li><strong>Numéro de téléphone</strong>: <?php echo ! empty( $wp_entity->data['phone'] ) ? esc_html( $wp_entity->data['phone'] ) : 'Non définie'; ?></li>
					</ul>

					<h3>Contacts</h3>

					<div class="wpeo-table table-flex table-5">

						<div class="table-row table-header">
							<div class="table-cell"><?php esc_html_e( 'Name', 'wpshop' ); ?></div>
							<div class="table-cell"><?php esc_html_e( 'Firstname', 'wpshop' ); ?></div>
							<div class="table-cell"><i class="fas fa-envelope"></i></div>
							<div class="table-cell"><i class="fas fa-phone"></i></div>
						</div>

						<?php
						if ( ! empty( $wp_entity->data['contacts'] ) ) :
							foreach ( $wp_entity->data['contacts'] as $contact ) :
								?>
								<div class="table-row">
									<div class="table-cell"><?php echo esc_html( $contact->data['lastname'] ); ?></div>
									<div class="table-cell"><?php echo esc_html( $contact->data['firstname'] ); ?></div>
									<div class="table-cell"><?php echo esc_html( $contact->data['email'] ); ?></div>
									<div class="table-cell"><?php echo esc_html( $contact->data['phone'] ); ?></div>
								</div>
								<?php
							endforeach;
						endif;
						?>
					</div>

					<div class="action-attribute wpeo-button button-main"
						style="text-align: center;display: block;margin: auto;width: 50%;"
						data-action="associate_and_synchronize"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'associate_and_synchronize' ) ); ?>"
						data-entry-id="<?php echo $doli_entity->id; ?>"
						data-wp-id="<?php echo $wp_entity->data['id']; ?>"
						data-from="wp">
						<span>Choisir WordPress</span>
					</div>
				</div>

				<div class="choose dolibarr">
					<h3>Dolibarr</h3>

					<p>Dernière modification le <?php echo date( 'd/m/Y H:i:s', $date_doli ); ?></p>

					<ul>
						<li><strong><?php esc_html_e( 'Tier name', 'wpshop' ); ?></strong>: <?php echo ! empty( $doli_entity->name ) ? esc_html( $doli_entity->name ) : 'Non définie'; ?></li>
						<li><strong>Adresse</strong>: <?php echo ! empty( $doli_entity->address ) ? esc_html( $doli_entity->address ) : 'Non définie'; ?></li>
						<li><strong>Code postal</strong>: <?php echo ! empty( $doli_entity->zip ) ? esc_html( $doli_entity->zip ) : 'Non définie'; ?></li>
						<li><strong>Ville</strong>: <?php echo ! empty( $doli_entity->town ) ? esc_html( $doli_entity->town ) : 'Non définie'; ?></li>
						<li><strong>Pays</strong>: <?php echo ! empty( $doli_entity->country ) ? esc_html( $doli_entity->country ) : 'Non définie'; ?></li>
						<li><strong>Numéro de téléphone</strong>: <?php echo ! empty( $doli_entity->phone ) ? esc_html( $doli_entity->phone ) : 'Non définie'; ?></li>
					</ul>

					<h3>Contacts</h3>

					<div class="wpeo-table table-flex table-5">

						<div class="table-row table-header">
							<div class="table-cell"><?php esc_html_e( 'Name', 'wpshop' ); ?></div>
							<div class="table-cell"><?php esc_html_e( 'Firstname', 'wpshop' ); ?></div>
							<div class="table-cell"><i class="fas fa-envelope"></i></div>
							<div class="table-cell"><i class="fas fa-phone"></i></div>
						</div>

						<?php
						if ( ! empty( $doli_entity->contacts ) ) :
							foreach ( $doli_entity->contacts as $contact ) :
								?>
								<div class="table-row">
									<div class="table-cell"><?php echo esc_html( $contact->lastname ); ?></div>
									<div class="table-cell"><?php echo esc_html( $contact->firstname ); ?></div>
									<div class="table-cell"><?php echo esc_html( $contact->email ); ?></div>
									<div class="table-cell"><?php echo esc_html( $contact->phone_pro ); ?></div>
								</div>
								<?php
							endforeach;
						else:
							?>
							<div class="table-row">No contact</div>
							<?php
						endif;
						?>
					</div>

					<div class="action-attribute wpeo-button button-main"
						style="text-align: center;display: block;margin: auto;width: 50%;"
						data-action="associate_and_synchronize"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'associate_and_synchronize' ) ); ?>"
						data-entry-id="<?php echo $doli_entity->id; ?>"
						data-wp-id="<?php echo $wp_entity->data['id']; ?>"
						data-from="dolibarr"
						wpeo-before-cb="wpshop/doliSynchro/goSync">
						<span>Choisir Dolibarr</span>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<div class="wpeo-button modal-close button-light">
				<span>Annuler</span>
			</div>
		</div>

	</div>
</div>
