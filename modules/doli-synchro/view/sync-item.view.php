<?php
/**
 * Ajoutes l'état de synchronisation d'une entité dans les listing.
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

<div class="table-cell table-100 wps-sync">
	<div class="button-synchro <?php echo ! empty( $object->data['external_id'] ) ? 'action-attribute' : 'wpeo-modal-event'; ?>"
		data-class="synchro-single"
		data-action="<?php echo ! empty( $object->data['external_id'] ) ? 'associate_and_synchronize' : 'load_modal_synchro_single'; ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce( ! empty( $object->data['external_id'] ) ? 'associate_and_synchronize' : 'load_modal_synchro_single' ) ); ?>"
		data-entry-id="<?php echo $object->data['external_id']; ?>"
		data-wp-id="<?php echo $object->data['id']; ?>"
		data-from="dolibarr"><i class="fas fa-sync"></i></div>
	<div class="statut statut-<?php echo esc_attr( $class ); ?> wpeo-tooltip-event" data-direction="left" aria-label="<?php echo esc_html( $message_tooltip ); ?>"></div>
</div>
