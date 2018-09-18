<?php
/**
 * Affichage d'un produit dans le listing  de la page des produits (wps-product)
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

<tr>
	<td><input type="checkbox" /></td>
	<td><?php echo esc_html( $product->data['ref'] ); ?></td>
	<td>Photo</td>
	<td><?php echo esc_html( $product->data['title'] ); ?></td>
	<td><?php echo esc_html( $product->data['price'] ); ?></td>
	<td><?php echo esc_html( $product->data['tva_tx'] ); ?></td>
	<td>no</td>
	<td><?php echo esc_html( $product->data['stock'] ); ?></td>
	<td><?php echo esc_html( $product->data['barcode'] ); ?></td>
</tr>
