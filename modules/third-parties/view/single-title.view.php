<?php
/**
 * La vue pour l'affichage du titre d'un tier.
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

defined( 'ABSPATH' ) || exit;

echo esc_html( $third_party->data['title'] );

?>
<!-- <div data-action="third_party_load_title_edit"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_title_edit' ) ); ?>"
	data-post-id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
	class="action-attribute wps-edit-title wpeo-button button-square-30 button-rounded">
	<i class="button-icon fas fa-pen"></i></div> -->
