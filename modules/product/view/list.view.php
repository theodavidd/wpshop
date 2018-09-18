<?php
/**
 * Affichage du listing de produit dans le backend.
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
			<th><?php esc_html_e( 'Ref', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Photo', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Title', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Price TTC(€)', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Tax Rate', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Manage stocks', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Stocks', 'wpshop' ); ?></th>
			<th><?php esc_html_e( 'Barcode', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $products ) ) :
			foreach ( $products as $product ) :
				\eoxia\View_Util::exec( 'wpshop', 'product', 'item', array(
					'product' => $product,
				) );
			endforeach;
		endif;
		?>
	</tbody>
</table>
