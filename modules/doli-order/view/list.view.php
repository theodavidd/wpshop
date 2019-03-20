<?php
/**
 * Affichage du listing des commandes dans le backend.
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
			<th><?php esc_html_e( 'WP ID', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Dolibarr ID', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Date', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Ref', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Tier', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Statut', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Price', 'wpshop' ); ?></th>
			<?php echo apply_filters( 'wps_order_table_th', '' ); ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $orders ) ) :
			foreach ( $orders as $order ) :
				\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'item', array(
					'order' => $order,
				) );
			endforeach;
		endif;
		?>
	</tbody>
</table>
