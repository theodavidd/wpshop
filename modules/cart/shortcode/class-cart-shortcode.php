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
 * Cart Shortcode Class.
 */
class Cart_Shortcode extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Class_Cart_Shortcode. Ajoutes les
	 * shortcodes pour le tunnel de vente.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Initialise le shortcode
	 *
	 * @since 2.0.0
	 */
	public function callback_init() {
		add_shortcode( 'wps_cart', array( $this, 'callback_cart' ) );
	}

	/**
	 * Affichage de la vue du shortcode
	 *
	 * @since 2.0.0
	 */
	public function callback_cart() {
		$cart_contents = Cart_Session::g()->cart_contents;

		$shipping_cost_option  = get_option( 'wps_shipping_cost', Settings::g()->shipping_cost_default_settings );
		$shipping_cost_product = Product::g()->get( array( 'id' => $shipping_cost_option['shipping_product_id'] ), true );
		if ( ! empty( $cart_contents ) ) {
			include( Template_Util::get_template_part( 'cart', 'cart' ) );
		} else {
			include( Template_Util::get_template_part( 'cart', 'empty-cart' ) );
		}
	}
}

Cart_Shortcode::g();
