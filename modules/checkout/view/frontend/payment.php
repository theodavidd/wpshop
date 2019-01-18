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
		<li>
			<div class="form-field-inline">
				<input type="radio" id="radio1" class="form-field" name="type" checked value="radio1">
				<label for="radio1">Check payments</label>
			</div>

		</div>Please send a check to Store Name, Store Street, Store Town, Store State / Country, Store Postcode.</div>
		</li>
	</ul>

	<?php include( Template_Util::get_template_part( 'checkout', 'terms' ) ); ?>

	<?php do_action( 'wps_review_order_before_submit' ); ?>

	<input type="hidden" name="action" value="wps_place_order" />
	<a class="action-input wpeo-button" data-parent="wps-checkout"><?php esc_html_e( 'Place order', 'wpshop' ); ?></a>

	<?php do_action( 'wps_review_order_after_submit' ); ?>
</div>
