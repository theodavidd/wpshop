<?php
/**
 * Affichage les produits téléchargables dans la page "Mon compte"
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

defined( 'ABSPATH' ) || exit;

?>

<table class="wpeo-table">
	<thead>
		<tr>
			<th data-title="Product"><?php esc_html_e( 'Product', 'wpshop' ); ?></th>
			<th data-title="Download"><?php esc_html_e( 'Download', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $products_downloadable ) ) :
			foreach ( $products_downloadable as $product_downloadable ) :
				?>
				<tr>
					<td data-title="test"><?php echo esc_html( $product_downloadable->data['title'] ); ?></td>
					<td>
						<a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_product&_wpnonce=' . wp_create_nonce( 'download_product' ) . '&product_id=' . $product_downloadable->data['id'] ) ); ?>" class="wpeo-button button-primary">
							<i class="button-icon fas fa-file-download"></i>
						</a>
					</td>

				</tr>
				<?php
			endforeach;
		endif;
		?>

	</tbody>
</table>
