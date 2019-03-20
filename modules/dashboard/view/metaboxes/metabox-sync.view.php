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

<div class="wpeo-button button-main wpeo-modal-event"
	data-action="load_modal_synchro"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_modal_synchro' ) ); ?>"
	data-class="modal-sync"
	data-title="<?php echo esc_attr_e( 'Data synchronization', 'wpshop' ); ?>">
	<span><?php esc_html_e( 'Synchronization', 'wpshop' ); ?></span>
</div>
