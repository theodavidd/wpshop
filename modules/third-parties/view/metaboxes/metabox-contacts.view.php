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

<table class="wpeo-table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Email</th>
			<th>Téléphone</th>
			<th></th>
		</tr>
	</thead>

	<tbody>
		<?php
		if ( ! empty( $contacts ) ) :
			foreach ( $contacts as $contact ) :
				\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts-item', array(
					'third_party_id' => $third_party->data['id'],
					'contact'        => $contact,
				) );
			endforeach;
		endif;
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts-edit', array(
			'third_party_id' => $third_party->data['id'],
			'contact'        => Contact_Class::g()->get( array( 'schema' => true ), true ),
		) );
		?>
	</tbody>
</table>

<div>
	<div class="wpeo-autocomplete search-contact" data-action="third_party_search_contact">
		<label class="autocomplete-label" for="search-contact">
			<i class="autocomplete-icon-before far fa-search"></i>
			<input id="search-contact" autocomplete="off" placeholder="Recherche..." class="autocomplete-search-input" type="text" />
			<span class="autocomplete-icon-after"><i class="far fa-times"></i></span>
		</label>

		<ul class="autocomplete-search-list"></ul>

		<div data-third-party_id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
			data-action="third_party_associate_contact"
			class="action-attribute button-associate-contact wpeo-button button-main">
			<i class="button-icon fas fa-plus"></i>
		</div>
	</div>

	<span>Ou</span>

	<div class="wpeo-button button-main add-contact">
		<i class="button-icon fas fa-plus"></i>
		<span>Ajouter un utilisateur</span>
	</div>
</div>
