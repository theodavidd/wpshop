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

<p><?php esc_html_e( 'Payment by', 'wpshop' ); ?> <?php echo empty( $order->data['payment_method'] ) ? 'N/D' : Payment_Class::g()->get_payment_title( $order->data['payment_method'] ); ?></p>
<p><?php echo esc_html( Payment_Class::g()->convert_status( $order->data ) ); ?></p>

<div class="wpeo-gridlayout grid-3">
	<div>
		<ul>
			<li><?php esc_html_e( 'Customer', 'wpshop' ); ?> : <a href="<?php echo admin_url( 'admin.php?page=wps-third-party&id=' . $third_party->data['id'] ); ?>" target="_blank"><?php echo $third_party->data['title']; ?></a></li>
		<?php
		if ( ! empty( $invoice->data['payments'] ) ) :
			?><li><?php
			foreach ( $invoice->data['payments'] as $payment ) :
				?>
				<ul>
					<li><?php esc_html_e( 'Payment method', 'wpshop' ); ?> : <?php echo esc_html( $payment->data['payment_type'] ); ?></li>
					<li><?php esc_html_e( 'Payment date', 'wpshop' ); ?> : <?php echo esc_html( $payment->data['date']['rendered']['date_human_readable'] ); ?></li>
					<li><?php esc_html_e( 'Payment reference', 'wpshop' ); ?> : <?php echo esc_html( $payment->data['title'] ); ?></li>
					<li><?php esc_html_e( 'Amount', 'wpshop' ); ?> : <?php echo esc_html( $payment->data['amount'] ); ?>€</li>
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
		<strong><?php esc_html_e( 'Billing', 'wpshop' ); ?></strong>

		<ul>
			<li><?php esc_html_e( 'No billing address', 'wpshop' ); ?></li>
		</ul>
	</div>
	<div>
		<strong><?php esc_html_e( 'Shipment', 'wpshop' ); ?></strong>

		<ul>
			<li><?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : 'N/D'; ?></li>
			<li><?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : 'N/D'; ?></li>
			<li>
				<?php echo ! empty( $third_party->data['zip']) ? $third_party->data['zip'] : 'N/D'; ?>
				<?php echo ! empty( $third_party->data['town']) ? $third_party->data['town'] : 'N/D'; ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Phone number', 'wpshop' ); ?> :</strong>
				<p><?php echo ! empty( $third_party->data['phone']) ? $third_party->data['phone'] : 'N/D'; ?></p>
			</li>
		</ul>
	</div>
</div>
