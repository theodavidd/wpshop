<?php
/**
 * Le formulaire pour créer son adresse de livraison
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

Merci. Votre commande a été reçue.

<ul>
	<li>Numéro de commande: <strong><?php echo esc_html( $order->data['title'] ); ?></strong></li>
	<li>Date: <strong><?php echo esc_html( $order->data['date_commande']['rendered']['date'] ); ?></strong></li>
	<li>Total: <strong><?php echo esc_html( number_format( $order->data['total_ttc'], 2 ) ); ?>€</strong></li>
	<li>Moyen de paiement: <strong><?php echo esc_html( $order->data['payment_method'] ); ?></strong></li>
</ul>

<h2>Détails de la commande</h2>

<table class="wpeo-table">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'wpshop' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if ( ! empty( $order->data['lines'] ) ) :
				foreach ( $order->data['lines'] as $line ) :
					?>
					<tr>
						<td class="product-name"><?php echo $line['libelle'] ?> x <?php echo $line['qty']; ?></td>
						<td class="product-total"><?php echo number_format( $line['price'] * $line['qty'], 2 ); ?>€</td>
					</tr>
					<?php
				endforeach;
			endif;
		?>
		<tr>
			<td>Total</td>
			<td><?php echo number_format( $order->data['total_ttc'], 2 ); ?>€</td>
		</tr>
	</tbody>
</table>

<a href="<?php echo Pages_Class::g()->get_account_link(); ?>orders/" class="wpeo-button button-main">
	<span>Voir mes commandes</span>
</a>