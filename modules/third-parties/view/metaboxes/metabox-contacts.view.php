<?php
/**
 * La vue affichant les contacts d'un tier dans la page single d'un tier.
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

<div class="wps-metabox wps-billing-address view gridw-3">
	<h3 class="metabox-title"><?php esc_html_e( 'Contacts', 'wpshop' ); ?></h3>

	<div class="wpeo-table table-flex table-5">
		<div class="table-row table-header">
			<div class="table-cell"><?php esc_html_e( 'Name', 'wpshop' ); ?></div>
			<div class="table-cell"><?php esc_html_e( 'Firstname', 'wpshop' ); ?></div>
			<div class="table-cell"><i class="fas fa-envelope"></i></div>
			<div class="table-cell"><i class="fas fa-phone"></i></div>
			<div class="table-cell"></div>
		</div>

		<?php
		if ( ! empty( $contacts ) ) :
			foreach ( $contacts as $contact ) :
				\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts-item', array(
					'third_party_id' => $third_party->data['id'],
					'contact'        => $contact,
				) );
			endforeach;
		endif;
		// \eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts-edit', array(
		// 	'third_party_id' => $third_party->data['id'],
		// 	'contact'        => Contact::g()->get( array( 'schema' => true ), true ),
		// ) );
		?>
	</div>
	<!-- <div>
		<div class="wpeo-autocomplete search-contact" data-action="third_party_search_contact" data-nonce="<?php echo esc_attr( wp_create_nonce( 'search_contact' ) ); ?>">
			<label class="autocomplete-label" for="search-contact">
				<i class="autocomplete-icon-before fas fa-search"></i>
				<input id="search-contact" autocomplete="off" placeholder="Recherche..." class="autocomplete-search-input" type="text" />
				<span class="autocomplete-icon-after"><i class="fas fa-times"></i></span>
			</label>

			<ul class="autocomplete-search-list"></ul>

			<div data-parent-id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
				data-action="third_party_save_contact"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'save_and_associate_contact' ) ); ?>"
				class="action-attribute button-associate-contact wpeo-button button-main">
				<i class="button-icon fas fa-plus"></i>
			</div>
		</div>

		<span>Ou</span>

		<div class="wpeo-button button-main add-contact">
			<i class="button-icon fas fa-plus"></i>
			<span>Ajouter un utilisateur</span>
		</div>
	</div> -->
</div>
