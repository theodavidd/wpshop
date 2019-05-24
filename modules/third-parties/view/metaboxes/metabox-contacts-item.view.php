<?php
/**
 * La vue principale de la page des produits (wps-third-party)
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

<div class="table-row">
	<div class="table-cell"><?php echo esc_html( $contact->data['lastname'] ); ?></div>
	<div class="table-cell"><?php echo esc_html( $contact->data['firstname'] ); ?></div>
	<div class="table-cell"><?php echo esc_html( $contact->data['email'] ); ?></div>
	<div class="table-cell"><?php echo esc_html( $contact->data['phone'] ); ?></div>
	<div class="table-cell table-end">
		<!-- <div data-action="third_party_load_contact"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_contact' ) ); ?>"
			data-third-party_id="<?php echo esc_attr( $third_party_id ); ?>"
			data-contact-id="<?php echo esc_attr( $contact->data['id'] ); ?>"
			class="action-attribute table-button-edit wpeo-button button-size-small button-square-30 button-transparent">
			<i class="button-icon fas fa-pen"></i>
		</div>

		<div data-action="third_party_delete_contact"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_contact' ) ); ?>"
			data-third-party_id="<?php echo esc_attr( $third_party_id ); ?>"
			data-contact-id="<?php echo esc_attr( $contact->data['id'] ); ?>"
			class="action-attribute table-button-delete wpeo-button button-size-small button-square-30 button-transparent">
			<i class="button-icon fas fa-trash"></i>
		</div> -->
	</div>
</div>
