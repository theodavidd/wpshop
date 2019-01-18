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
class Class_Cart_Session extends \eoxia\Singleton_Util {

	protected $cart;

	/**
	 * Constructeur pour la classe Class_Cart_Session. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct( $cart ) {
		$this->cart = $cart;
	}
}

Class_Cart_Session::g();
