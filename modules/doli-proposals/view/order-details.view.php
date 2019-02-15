<?php
/**
 * Affichage des données de la commande
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

if ( ! empty( $invoice->data['payments'] ) ) :
	foreach ( $invoice->data['payments'] as $payment ) :
		?>
		<ul>
			<li>Type de paiement : <?php echo esc_html( $payment->data['payment_type'] ); ?></li>
			<li>Date de paiement : <?php echo esc_html( $payment->data['date']['rendered']['date_human_readable'] ); ?></li>
			<li>Référence du paiement : <?php echo esc_html( $payment->data['title'] ); ?></li>
			<li>Montant : <?php echo esc_html( $payment->data['amount'] ); ?>€</li>
		</ul>

		<?php
	endforeach;
endif;
?>
