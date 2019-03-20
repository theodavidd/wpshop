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

<div data-action="third_party_load_address"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_billing_address' ) ); ?>"
	data-third-party_id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
	class="action-attribute wpeo-button button-square-30" style="float: right;">
	<i class="button-icon fas fa-pen"></i>
</div>

<div class="wpeo-gridlayout grid-2">
	<ul>
		<li>Nom <?php echo $third_party->data['title']; ?></li>
		<li>Adresse <?php echo $third_party->data['address']; ?></li>
		<li>Code postal <?php echo $third_party->data['zip']; ?></li>
	</ul>

	<ul>
		<li>&nbsp;</li>
		<li>Ville <?php echo $third_party->data['town']; ?></li>
		<li>Téléphone <?php echo $third_party->data['phone']; ?></li>
	</ul>
</div>
