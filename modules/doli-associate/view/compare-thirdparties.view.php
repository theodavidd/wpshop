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

<div class="wpeo-gridlayout grid-2">
	<?php
	if ( ! empty( $entries ) ) :
		foreach ( $entries as $key => $entry ) :
			?>
			<div class="choose <?php echo esc_attr( $key ); ?>">
				<h2><?php echo $entry['title']; ?>

				<p><?php printf( __( 'Last update the %s', 'wpshop' ), $entry['data']['date']['rendered']['date_time'] ); ?></p>

				<ul>
					<li><strong><?php esc_html_e( 'Tier name', 'wpshop' ); ?></strong>: <?php echo ! empty( $entry['data']['title'] ) ? esc_html( $entry['data']['title'] ) : 'Non définie'; ?></li>
					<li><strong>Adresse</strong>: <?php echo ! empty( $entry['data']['address'] ) ? esc_html( $entry['data']['address'] ) : 'Non définie'; ?></li>
					<li><strong>Code postal</strong>: <?php echo ! empty( $entry['data']['zip'] ) ? esc_html( $entry['data']['zip'] ) : 'Non définie'; ?></li>
					<li><strong>Ville</strong>: <?php echo ! empty( $entry['data']['town'] ) ? esc_html( $entry['data']['town'] ) : 'Non définie'; ?></li>
					<li><strong>Pays</strong>: <?php echo ! empty( $entry['data']['country'] ) ? esc_html( $entry['data']['country'] ) : 'Non définie'; ?></li>
					<li><strong>Numéro de téléphone</strong>: <?php echo ! empty( $entry['data']['phone'] ) ? esc_html( $entry['data']['phone'] ) : 'Non définie'; ?></li>
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
					if ( ! empty( $entry['data']['contacts'] ) ) :
						foreach ( $entry['data']['contacts'] as $contact ) :
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
					data-action="sync_entry"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'sync_entry' ) ); ?>"
					data-type="<?php echo esc_attr( 'wpshop/Third_Party_Class' ); ?>"
					data-entry-id="<?php echo $entries['dolibarr']['id'] ?>"
					data-wp-id="<?php echo $entries['wordpress']['id']; ?>"
					data-route="<?php echo esc_attr( $route ); ?>"
					data-from="<?php echo esc_attr( $key ); ?>"
					data-modal="1">
					<span><?php printf( __( 'Choose %s', 'wpshop' ), $entry['title'] ); ?></span>
				</div>
			</div>
			<?php
		endforeach;
	endif;
	?>
</div>
