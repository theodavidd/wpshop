<?php
/**
 * Affichage d'une commande dans le listing de la page des commandes (wps-order)
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
	<td><?php echo esc_html( $order->data['id'] ); ?></td>
	<td><?php echo esc_html( $order->data['ref'] ); ?></td>
	<td><?php echo esc_html( $order->data['order_grand_total'] ); ?>€</td>
	<td>
		<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $order->data['id'] . '&action=edit' ) ); ?>" class="wpeo-button button-square-30 button-rounded"><i class="button-icon fas fa-pencil"></i></a>
		<div
			class="action-delete wpeo-button button-square-30 button-rounded"
			data-action=""
			data-message-delete="<?php echo esc_attr_e( 'Êtes-vous sur(e) de vouloir supprimer cette commande ?', 'wpshop' ); ?>"><i class="button-icon fas fa-trash"></i></div>
	</td>
</tr>
