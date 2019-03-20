<?php
/**
 * Affichage d'une commande dans le listing de la page des commandes (wps-order)
 *
 * @todo Proposal au lieu de devis
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
	<td><?php echo esc_html( $order->data['external_id'] ); ?></td>
	<td><?php echo esc_html( $order->data['datec']['rendered']['date_time'] ); ?></td>
	<td><a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $order->data['id'] ) ); ?>"><?php echo esc_html( $order->data['title'] ); ?></a></td>
	<td>
		<?php
		if ( ! empty( $order->data['tier'] ) ) :
			?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-third-party&id=' . $order->data['tier']->data['id'] ) ); ?>"><?php echo $order->data['tier']->data['title']; ?></a>
			<?php
		else :
			?>
			N/D
			<?php
		endif;
		?>
	</td>
	<td><?php echo Payment::g()->convert_status( $order->data ); ?></td>
	<td><?php echo esc_html( number_format( $order->data['total_ttc'], 2, ',', '' ) ); ?>€</td>
	<?php apply_filters( 'wps_order_table_tr', $order ); ?>
	<td>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-order&id=' . $order->data['id'] ) ); ?>" class="wpeo-button button-square-30 button-rounded"><i class="button-icon fas fa-pencil-alt"></i></a>
	</td>
</tr>
