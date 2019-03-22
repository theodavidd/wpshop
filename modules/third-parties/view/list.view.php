<?php
/**
 * Affichage du listing des tiers dans le backend.
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

<table class="wpeo-table">
	<thead>
		<tr>
			<th><input type="checkbox" /></th>
			<th><?php esc_html_e( 'Society name', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Contacts', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Commercial', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Sync', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $third_parties ) ) :
			foreach ( $third_parties as $third_party ) :
				\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'item', array(
					'third_party' => $third_party,
				) );
			endforeach;
		endif;
		?>
	</tbody>
</table>
