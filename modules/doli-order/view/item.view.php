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
	<td><?php echo esc_html( $proposal->data['id'] ); ?></td>
	<td><?php echo esc_html( $proposal->data['external_id'] ); ?></td>
	<td><?php echo esc_html( $proposal->data['title'] ); ?></td>
	<td><?php echo esc_html( $proposal->data['status'] ); ?></td>
	<td><?php echo esc_html( $proposal->data['total_ttc'] ); ?>€</td>
	<?php apply_filters( 'wps_order_table_tr', $proposal ); ?>
	<td>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $proposal->data['id'] ) ); ?>" class="wpeo-button button-square-30 button-rounded"><i class="button-icon fas fa-pencil"></i></a>
	</td>
</tr>
