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

<?php do_action( 'wps_before_checkout_form' ); ?>

<form name="checkout" method="post" class="wps-checkout" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

	<div><?php do_action( 'wps_checkout_billing' ); ?></div>

	<div><?php do_action( 'wps_checkout_shipping' ); ?></div>

	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'wpshop' ); ?></h3>

	<?php do_action( 'wps_checkout_before_order_review' ); ?>

	<div id="order_review" class="wps-checkout-review-order">
		<?php do_action( 'wps_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'wps_checkout_after_order_review' ); ?>
</form>

<?php do_action( 'wps_after_checkout_form' ); ?>
