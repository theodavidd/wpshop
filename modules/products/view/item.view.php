<?php
/**
 * Affichage d'un produit dans le listing  de la page des produits (wps-product)
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

<tr>
	<td><input type="checkbox" /></td>
	<td><?php echo esc_html( $product->data['id'] ); ?></td>
	<td><?php echo esc_html( $product->data['external_id'] ); ?></td>
	<td><?php echo get_the_post_thumbnail( $product->data['id'], array( 80, 80 ) ); ?></td>
	<td><?php echo esc_html( $product->data['title'] ); ?></td>
	<td><?php echo esc_html( $product->data['price'] ); ?></td>
	<td><?php echo esc_html( $product->data['tva_tx'] ); ?></td>
	<td><?php echo esc_html( $product->data['price_ttc'] ); ?></td>
	<td><?php echo esc_html( $product->data['barcode'] ); ?></td>
	<td>
		<a href="<?php echo esc_attr( get_post_permalink( $product->data['id'] ) ); ?>" class="wpeo-button button-square-30 button-rounded"><i class="button-icon fas fa-eye"></i></a>
		<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $product->data['id'] . '&action=edit' ) ); ?>" class="wpeo-button button-square-30 button-rounded"><i class="button-icon fas fa-pencil"></i></a>
		<div class="wpeo-button button-square-30 button-rounded"><i class="button-icon fas fa-copy"></i></div>
		<div
			class="action-delete wpeo-button button-square-30 button-rounded"
			data-action="wps_delete_product"
			data-message-delete="<?php echo esc_attr_e( 'Êtes-vous sur de vouloir supprimer ce produit ?', 'wpshop' ); ?>"><i class="button-icon fas fa-trash"></i></div>
	</td>
</tr>
