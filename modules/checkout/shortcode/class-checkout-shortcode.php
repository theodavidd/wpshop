<?php
/**
 * Gestion shortcode du tunnel de vente.
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
class Checkout_Shortcode extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Class_Checkout_Shortcode. Ajoutes les
	 * shortcodes pour le tunnel de vente.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function callback_init() {
		add_shortcode( 'wps_checkout', array( $this, 'callback_checkout' ) );
	}


	public function callback_checkout() {
		if ( ! is_admin() ) {
			include( Template_Util::get_template_part( 'checkout', 'form-checkout' ) );
		}
	}
}

Checkout_Shortcode::g();