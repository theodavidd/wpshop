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

/**
 * @var mixed $object Object can be Product/Propal/Third_Party.
 */

defined( 'ABSPATH' ) || exit; ?>

<div class="table-cell table-100 wps-sync">
	<div class="button-synchro <?php echo $can_sync ? 'action-attribute' : 'wpeo-modal-event'; ?>"
		data-class="synchro-single"
		<?php // translators: Associate and synchronize object name. ?>
		data-title="<?php printf( __( 'Associate and synchronize %s', 'wpshop' ), $object->data['title'] ); ?>"
		data-action="<?php echo $can_sync ? 'sync_entry' : 'load_associate_modal'; ?>"
		data-type="<?php echo $type; ?>"
		data-id="<?php echo esc_attr( $object->data['id'] ); ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce( $can_sync ? 'sync_entry' : 'load_associate_modal' ) ); ?>"><i class="fas fa-sync"></i></div>

	<div class="statut statut-<?php echo esc_attr( $status_color ); ?> wpeo-tooltip-event" data-direction="left" aria-label="<?php echo esc_html( $message_tooltip ); ?>"></div>
</div>
