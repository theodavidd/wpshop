<?php
/**
 * Les fonctions principales du tunnel de vente.
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
class Checkout_Class extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Checkout_Class. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function get_posted_data() {
		$data = array(
			'contact'     => ! empty( $_POST['contact'] ) ? (array) $_POST['contact'] : array(),
			'third_party' => ! empty( $_POST['third_party'] ) ? (array) $_POST['third_party'] : array(),
		);

		return apply_filters( 'wps_checkout_posted_data', $data );
	}
}

Checkout_Class::g();
