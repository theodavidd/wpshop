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

<div class="table-cell table-100 wps-sync" data-id="<?php echo $object->data['id']; ?>" data-associate="<?php echo ! empty( $object->data['external_id'] ) ? "true" : "false"; ?>">
	<?php
	if ( 'view' === $mode ) :
		?>
		<div class="button-synchro <?php echo ! empty( $object->data['external_id'] ) ? 'action-attribute' : 'wpeo-modal-event'; ?>"
			data-class="synchro-single"
			<?php // translators: Associate and synchronize object name. ?>
			data-title="<?php printf( __( 'Associate and synchronize %s', 'wpshop' ), $object->data['title'] ); ?>"
			data-action="<?php echo ! empty( $object->data['external_id'] ) ? 'sync_entry' : 'load_associate_modal'; ?>"
			data-nonce="<?php echo esc_attr( wp_create_nonce( ! empty( $object->data['external_id'] ) ? 'sync_entry' : 'load_associate_modal' ) ); ?>"
			data-type="<?php echo esc_attr( $doli_class ); ?>"
			data-wp-type="<?php echo esc_attr( $wp_class ); ?>"
			data-entry-id="<?php echo $object->data['external_id']; ?>"
			data-wp-id="<?php echo $object->data['id']; ?>"
			data-route="<?php echo esc_attr( $route ); ?>"
			data-from="dolibarr"><i class="fas fa-sync"></i></div>
		<?php
	else :
		?>
		<div class="wpeo-button button-synchro button-disable button-event button-radius-1 wpeo-tooltip-event" data-direction="left" aria-label="<?php echo esc_html( $message_tooltip ); ?>"><i class="fas fa-sync"></i></div>
		<?php
	endif;
	?>
	<div class="statut statut-<?php echo empty( $object->data['external_id'] ) ? 'red' : 'grey'; ?> wpeo-tooltip-event" data-direction="left" aria-label="<?php echo esc_html( $message_tooltip ); ?>"></div>
</div>
