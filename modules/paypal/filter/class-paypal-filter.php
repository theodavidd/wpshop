<?php
/**
 * Gestion des filtres PayPal.
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
* Gestion des filtres PayPal.
*/
class Paypal_Filter {

	public function __construct() {
		add_filter( 'wps_payment_method_paypal_description', array( $this, 'more_paypal_description' ) );
	}

	public function more_paypal_description( $description ) {
		$paypal_options = Payment_Class::g()->get_payment_option( 'paypal' );

		if ( $paypal_options['use_paypal_sandbox'] ) {
			$description .= __( ' SANDBOX ENABLED. You can use sandbox testing accounts only. See the <a href="https://developer.paypal.com/docs/classic/lifecycle/ug_sandbox/">PayPal Sandbox Testing Guide</a> for more details.', 'wpshop' );
		}

		return $description;
	}
}

new Paypal_Filter();
