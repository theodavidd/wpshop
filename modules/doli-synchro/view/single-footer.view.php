<?php
/**
 * Le footer de la modal de synchronisation.
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


<div class="wpeo-button button-light modal-close">
	<span>Annuler</span>
</div>

<div class="wpeo-button button-main action-input"
	data-action="load_synchro_modal_single"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_modal_synchro_single' ) ); ?>"
	data-parent="wpeo-modal">
	<span>Associer et synchroniser</span>
</div>
