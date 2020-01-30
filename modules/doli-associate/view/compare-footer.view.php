<?php
/**
 * Le footer de la modal de comparaison.
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

<div class="action-attribute wpeo-button button-main"
     data-action="sync_entry"
     data-nonce="<?php echo esc_attr( wp_create_nonce( 'sync_entry' ) ); ?>"
     data-type="<?php echo $doli_type; ?>"
     data-wp-type="<?php echo esc_attr( $wp_type ); ?>"
     data-entry-id="<?php echo esc_attr( $entry_id ); ?>"
     data-wp-id="<?php echo esc_attr( $wp_id ); ?>"
     data-route="<?php echo esc_attr( $route ); ?>"
     data-modal="1">
	<?php // translators: Choose WordPress. ?>
	<span><?php printf( __( 'Associate and Sync', 'wpshop' ) ); ?></span>
</div>

<div class="wpeo-button button-light modal-close">
	<span><?php esc_html_e( 'Back', 'wpshop' ); ?></span>
</div>
