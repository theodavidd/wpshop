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

				<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-third-party&id=' . $wp_third_party->data['id'] ) ); ?>" class="wpeo-button button-main">
					<span>Rafraichir la page</span>
				</a>
			</div>

		</div>

		<!-- Entête -->
		<div class="modal-header">
			<h2 class="modal-title">Synchroniser depuis WordPress ou depuis Dolibarr ?</h2>
		</div>

		<?php
		$date_time_wp   = new \DateTime( date( 'Y-m-d H:i:s', $date_wp ) );
		$date_time_doli = new \DateTime( date( 'Y-m-d H:i:s', $date_doli ) );

		?>
		<h3>
			<?php
			if ( $date_wp > $date_doli ) :
				$interval = $date_time_wp->diff( $date_time_doli );
				?>
				Les données du tier sont plus récentes de<strong style="font-weight: 700">
				<span>
					<?php
					if ( $interval->format( '%a' ) != 0 ) :
						echo $interval->format( '%a jour(s) et ' );
					endif;

					if ( $interval->format( '%h' ) != 0 ) :
						echo $interval->format( '%hh' );
					endif;

					echo $interval->format( '%imin' );
					?>
					</strong> sur
					<strong style="font-weight: 700">WordPress</strong>.
				</span>
				<?php
			elseif ( $date_wp < $date_doli ) :
				$interval = $date_time_doli->diff( $date_time_wp );
				?>
				Les données du tier sont plus récentes de<strong style="font-weight: 700">
				<span>
					<?php
					if ( $interval->format( '%a' ) != 0 ) :
						echo $interval->format( '%a jour(s) et ' );
					endif;

					if ( $interval->format( '%h' ) != 0 ) :
						echo $interval->format( '%hh' );
					endif;

					echo $interval->format( '%imin' );
					?>
					</strong> sur <strong style="font-weight: 700">Dolibarr</strong>.
				</span>
				<?php
			else :
				?>
				Les données du tier sont identiques sur WordPress et Dolibarr
				<?php
			endif;
			?>
		</h3>

		<!-- Corps -->
		<div class="modal-content">

			<div class="wpeo-gridlayout grid-2">
				<div class="choose wp">
					<h3>WordPress</h3>

					<p>Dernière modification le <?php echo date( 'd/m/Y H:i:s', $date_wp ); ?></p>

					<ul>
						<li><strong>Nom</strong>: <?php echo ! empty( $wp_third_party->data['title'] ) ? esc_html( $wp_third_party->data['title'] ) : 'Non définie'; ?></li>
						<li><strong>Adresse</strong>: <?php echo ! empty( $wp_third_party->data['address'] ) ? esc_html( $wp_third_party->data['address'] ) : 'Non définie'; ?></li>
						<li><strong>Code postal</strong>: <?php echo ! empty( $wp_third_party->data['zip'] ) ? esc_html( $wp_third_party->data['zip'] ) : 'Non définie'; ?></li>
						<li><strong>Ville</strong>: <?php echo ! empty( $wp_third_party->data['town'] ) ? esc_html( $wp_third_party->data['town'] ) : 'Non définie'; ?></li>
						<li><strong>Téléphone</strong>: <?php echo ! empty( $wp_third_party->data['phone'] ) ? esc_html( $wp_third_party->data['phone'] ) : 'Non définie'; ?></li>
						<li>
							<div class="action-attribute wpeo-button button-main"
								style="text-align: center;display: block;margin: auto;width: 50%;"
								data-action="associate_and_synchronize"
								data-entry-id="<?php echo $doli_third_party->id; ?>"
								data-wp-id="<?php echo $wp_third_party->data['id']; ?>"
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
						<li><strong>Nom</strong>: <?php echo ! empty( $doli_third_party->name ) ? esc_html( $doli_third_party->name ) : 'Non définie'; ?></li>
						<li><strong>Adresse</strong>: <?php echo ! empty( $doli_third_party->address ) ? esc_html( $doli_third_party->address ) : 'Non définie'; ?></li>
						<li><strong>Code postal</strong>: <?php echo ! empty( $doli_third_party->zip ) ? esc_html( $doli_third_party->zip ) : 'Non définie'; ?></li>
						<li><strong>Ville</strong>: <?php echo ! empty( $doli_third_party->town ) ? esc_html( $doli_third_party->town ) : 'Non définie'; ?></li>
						<li><strong>Téléphone</strong>: <?php echo ! empty( $doli_third_party->phone ) ? esc_html( $doli_third_party->phone ) : 'Non définie'; ?></li>
						<li>
							<div class="action-attribute wpeo-button button-main"
								style="text-align: center;display: block;margin: auto;width: 50%;"
								data-action="associate_and_synchronize"
								data-entry-id="<?php echo $doli_third_party->id; ?>"
								data-wp-id="<?php echo $wp_third_party->data['id']; ?>"
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
