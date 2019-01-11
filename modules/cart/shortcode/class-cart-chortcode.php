<?php
/**
 * Gestion shortcode du panier.
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
class Cart_Shortcode extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Class_Cart_Shortcode. Ajoutes les
	 * shortcodes pour le tunnel de vente.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function callback_init() {
		add_shortcode( 'wps_cart', array( $this, 'callback_cart' ) );
	}

	public function callback_cart() {
		if ( ! is_admin() ) {
			$cart = Cart_Class::g()->get_cart();

			include( Template_Util::get_template_part( 'cart', 'cart' ) );
		}
	}
}

Cart_Shortcode::g();
