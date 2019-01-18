<?php
/**
 * Les fonctions principales du panier.
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
* Handle order
*/
class Cart_Class extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Cart_Class. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function add_to_cart( $product ) {
		Class_Cart_Session::g()->cart_contents[] = $product->data;

		do_action( 'wps_add_to_cart', $this, $product->data );

		do_action( 'wps_before_calculate_totals', $this );

		do_action( 'wps_calculate_totals', $this );

		do_action( 'wps_after_calculate_totals', $this );

		Class_Cart_Session::g()->update_session();
	}
}

Cart_Class::g();
