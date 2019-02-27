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

<tr>
	<td><?php echo esc_html( $contact->data['id'] ); ?></td>
	<td><?php echo esc_html( $contact->data['lastname'] ); ?></td>
	<td><?php echo esc_html( $contact->data['firstname'] ); ?></td>
	<td><?php echo esc_html( $contact->data['email'] ); ?></td>
	<td><?php echo esc_html( $contact->data['phone'] ); ?></td>
	<td>
		<div data-action="third_party_load_contact"
			data-third-party_id="<?php echo esc_attr( $third_party_id ); ?>"
			data-contact-id="<?php echo esc_attr( $contact->data['id'] ); ?>"
			class="action-attribute wpeo-button button-square-30">
			<i class="button-icon fas fa-pen"></i>
		</div>

		<div data-action="third_party_delete_contact"
			data-third-party_id="<?php echo esc_attr( $third_party_id ); ?>"
			data-contact-id="<?php echo esc_attr( $contact->data['id'] ); ?>"
			class="action-attribute wpeo-button button-square-30">
			<i class="button-icon fas fa-trash"></i>
		</div>
	</td>
</tr>
