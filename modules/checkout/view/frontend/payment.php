<?php
/**
 * Les méthodes de paiement et le bouton pour payer.
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

<div id="payment" class="wps-checkout-payment">
	<ul>
		<?php
		if ( ! empty( $payment_methods ) ) :
			foreach ( $payment_methods as $key => $payment_method ) :
				$checked = '';
				if ( $key == 'cheque' ) :
					$checked = 'checked';
				endif;
				?>
				<li>
					<div class="form-field-inline">
						<input type="radio" id="radio-<?php echo esc_attr( $key ); ?>" class="form-field" name="type_payment" <?php echo esc_attr( $checked ); ?> value="<?php echo $key; ?>">
						<label for="radio-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $payment_method['title'] ); ?></label>
					</div>

					<?php
					if ( ! empty( $payment_method['description'] ) ) :
						?></p><?php echo apply_filters( 'wps_payment_method_' . $key . '_description', nl2br( $payment_method['description'] ) ); ?></p><?php
					endif;
					?>
				</li>
				<?php
			endforeach;
		endif;
		?>
	</ul>

	<?php // include( Template_Util::get_template_part( 'checkout', 'terms' ) ); ?>

	<?php do_action( 'wps_review_order_before_submit' ); ?>

	<input type="hidden" name="action" value="wps_place_order" />
	<!--<a class="action-input wpeo-button" data-type="proposal" data-parent="wps-checkout-step-2"><?php esc_html_e( 'Quotation', 'wpshop' ); ?></a>-->
	<a class="action-input wpeo-button" data-type="order" data-parent="wps-checkout-step-2"><?php esc_html_e( 'Place order', 'wpshop' ); ?></a>

	<?php do_action( 'wps_review_order_after_submit' ); ?>
</div>
