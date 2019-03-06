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

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<p>Paiement par <?php echo empty( $order->data['payment_method'] ) ? 'N/D' : Payment_Class::g()->get_payment_title( $order->data['payment_method'] ); ?></p>
<p><?php echo esc_html( Payment_Class::g()->convert_status( $order->data ) ); ?></p>

<div class="wpeo-gridlayout grid-3">
	<div>
		<ul>
			<li>Customer: <a href="<?php echo admin_url( 'admin.php?page=wps-third-party&id=' . $third_party->data['id'] ); ?>" target="_blank"><?php echo $third_party->data['title']; ?></a></li>
		<?php
		if ( ! empty( $invoice->data['payments'] ) ) :
			?><li><?php
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
			?> </li><?php
		endif;

		if ( ! empty( $link_invoice ) ) :
			?><li><a href="<?php echo esc_url( $link_invoice ); ?>" target="_blank"><?php esc_html_e( 'View invoice', 'wpshop' ); ?></a></li><?php
		endif;
		?>
	</div>
	<div>
		<strong>Facturation</strong>

		<ul>
			<li>Aucune adresse de facturation</li>
		</ul>
	</div>
	<div>
		<strong>Expédition</strong>

		<ul>
			<li><?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : 'N/D'; ?></li>
			<li><?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : 'N/D'; ?></li>
			<li>
				<?php echo ! empty( $third_party->data['zip']) ? $third_party->data['zip'] : 'N/D'; ?>
				<?php echo ! empty( $third_party->data['town']) ? $third_party->data['town'] : 'N/D'; ?>
			</li>
			<li>
				<strong>Téléphone:</strong>
				<p><?php echo ! empty( $third_party->data['phone']) ? $third_party->data['phone'] : 'N/D'; ?></p>
			</li>
		</ul>
	</div>
</div>
