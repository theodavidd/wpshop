<?php
/**
 * Gestion des actions du panier avec dolibarr.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Classes
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Action of cart module.
 */
class Dolibarr_Cart_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'wps_cart_totals_table_after', array( $this, 'callback_after_cart_table' ), 10 );
	}

	public function callback_after_cart_table() {
		$total_price = Class_Cart_Session::g()->total_price;

		include( Template_Util::get_template_part( 'doli_cart', 'cart-totals' ) );
	}
}

new Dolibarr_Cart_Action();
