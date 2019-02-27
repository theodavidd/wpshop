<?php
/**
 * La vue principale de la page des produits (wps-third-party)
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

<div class="wpeo-gridlayout grid-2">
	<ul>
		<li>Nom <input type="text" name="third_party[title]" value="<?php echo esc_attr( $third_party->data['title'] ); ?>" /></li>
		<li>Adresse <input type="text" name="third_party[address]" value="<?php echo esc_attr( $third_party->data['address'] ); ?>" /></li>
		<li>Code postal <input type="text" name="third_party[zip]" value="<?php echo esc_attr( $third_party->data['zip'] ); ?>" /></li>
	</ul>

	<ul>
		<li>&nbsp;</li>
		<li>Ville <input type="text" name="third_party[town]" value="<?php echo esc_attr( $third_party->data['town'] ); ?>" /></li>
		<li>Téléphone <input type="text" name="third_party[phone]" value="<?php echo esc_attr( $third_party->data['phone'] ); ?>" /></li>
	</ul>
</div>

<div data-parent="inside"
	data-action="third_party_save_address"
	data-third-party_id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
	class="action-input wpeo-button button-square-30">
	<i class="button-icon fas fa-save"></i>
	</div>
