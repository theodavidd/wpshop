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

<tr class="row">
	<td><input type="text" name="contact[firstname]" value="<?php echo esc_attr( $contact->data['firstname'] ); ?>" /></td>
	<td><input type="text" name="contact[lastname]" value="<?php echo esc_attr( $contact->data['lastname'] ); ?>" /></td>
	<td><input type="text" name="contact[email]" value="<?php echo esc_attr( $contact->data['email'] ); ?>" /></td>
	<td><input type="text" name="contact[phone]" value="<?php echo esc_attr( $contact->data['phone'] ); ?>" /></td>
	<td>
		<input type="hidden" name="contact[id]" value="<?php echo esc_attr( $contact->data['id'] ); ?>" />
		<div data-parent="row"
			data-parent-id="<?php echo esc_attr( $third_party_id ); ?>"
			data-action="third_party_save_contact"
			class="action-attribute wpeo-button button-square-30">
			<i class="button-icon fas <?php echo ! empty( $contact->data['id'] ) ? 'fa-save' : 'fa-plus'; ?>"></i>
		</div>
	</td>
</tr>
