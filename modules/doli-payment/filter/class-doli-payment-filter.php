<?php
/**
 * Les filtres principaux des paiements dolibarr.
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
* Handle product
*/
class Doli_Payment_Filter {
	public function __construct() {
		add_filter( 'wps_payment_methods', array( $this, 'add_payment_details' ), 10, 1 );
	}

	public function add_payment_details( $payment_methods ) {
		$payment_methods['paypal']['doli_type'] = 'paypal';
		$payment_methods['cheque']['doli_type'] = 'CHQ';

		return $payment_methods;
	}
}

new Doli_Payment_Filter();
