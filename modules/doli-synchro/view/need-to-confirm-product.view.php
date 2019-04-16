<?php
/**
 * Le contenu de la modal de synchronisation.
 *
 * @todo: template pas OK
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

<div class="wpeo-modal need-to-confirm modal-active">

	<div class="modal-container">

		<div class="mask" style="display: none;">
			<div class="content">
				<h3>Synchronisation terminée</h3>

				<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-third-party&id=' . $wp_entity->data['id'] ) ); ?>" class="wpeo-button button-main">
					<span>Rafraichir la page</span>
				</a>
			</div>

		</div>

		<!-- Entête -->
		<div class="modal-header">
			<h2 class="modal-title">Synchroniser depuis WordPress ou depuis Dolibarr ?</h2>
		</div>


		<!-- Corps -->
		<div class="modal-content">

			<div class="wpeo-gridlayout grid-2">
				<div class="choose wp">
					<h3>WordPress</h3>

					<p>Dernière modification le <?php echo date( 'd/m/Y H:i:s', $date_wp ); ?></p>

					<ul>
						<li><strong><?php esc_html_e( 'Label', 'wpshop' ); ?></strong>: <?php echo ! empty( $wp_entity->data['title'] ) ? esc_html( $wp_entity->data['title'] ) : 'Non définie'; ?></li>
						<li><strong>Price HT(€)</strong>: <?php echo ! empty( $wp_entity->data['price'] ) ? esc_html( number_format( $wp_entity->data['price'], 2, ',', '' ) ) : 'Non définie'; ?></li>
						<li><strong>VAT Rate</strong>: <?php echo ! empty( $wp_entity->data['tva_tx'] ) ? esc_html( number_format( $wp_entity->data['tva_tx'], 2, ',', '' ) ) : 'Non définie'; ?></li>
						<li><strong>Price TTC(€)</strong>: <?php echo ! empty( $wp_entity->data['price_ttc'] ) ? esc_html( number_format( $wp_entity->data['price_ttc'], 2, ',', '' ) ) : 'Non définie'; ?></li>
						<li>
							<div class="action-attribute wpeo-button button-main"
								style="text-align: center;display: block;margin: auto;width: 50%;"
								data-action="associate_and_synchronize"
								data-nonce="<?php echo esc_attr( wp_create_nonce( 'associate_and_synchronize' ) ); ?>"
								data-entry-id="<?php echo $doli_entity->id; ?>"
								data-wp-id="<?php echo $wp_entity->data['id']; ?>"
								data-from="wordpress">
								<span>Choisir WordPress</span>
							</div>
						</li>
					</ul>
				</div>

				<div class="choose dolibarr">
					<h3>Dolibarr</h3>

					<p>Dernière modification le <?php echo date( 'd/m/Y H:i:s', $date_doli ); ?></p>

					<ul>
						<li><strong><?php esc_html_e( 'Label', 'wpshop' ); ?></strong>: <?php echo ! empty( $doli_entity->label ) ? esc_html( $doli_entity->label ) : 'Non définie'; ?></li>
						<li><strong>Price HT(€)</strong>: <?php echo ! empty( $doli_entity->price ) ? esc_html( number_format( $doli_entity->price, 2, ',', '' ) ) : 'Non définie'; ?></li>
						<li><strong>VAT Rate</strong>: <?php echo ! empty( $doli_entity->tva_tx ) ? esc_html( number_format( $doli_entity->tva_tx, 2, ',', '' ) ) : 'Non définie'; ?></li>
						<li><strong>Price TTC(€)</strong>: <?php echo ! empty( $doli_entity->price_ttc ) ? esc_html( number_format( $doli_entity->price_ttc, 2, ',', '' ) ) : 'Non définie'; ?></li>
						<li>
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
						</li>
					</ul>
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
