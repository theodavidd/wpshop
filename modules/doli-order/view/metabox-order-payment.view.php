<?php
/**
 * Affichage des détails de paiement la commande
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

<div class="wps-metabox wps-order-payment">
	<h3 class="metabox-title"><?php esc_html_e( 'Payments', 'wpshop' ); ?></h3>

	<?php
	if ( ! empty( $invoice->data['payments'] ) ) :
		?>
		<li>
			<?php
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
			?>
		</li>
		<?php
	endif;
	?>

</div>
