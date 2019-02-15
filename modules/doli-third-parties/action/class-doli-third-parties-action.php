<?php
/**
 * Gestion des actions des tiers avec dolibarr.
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
 * Action of Third Party module.
 */
class Doli_Third_Party_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'wps_save_third_party', array( Doli_Third_Party_Class::g(), 'save' ), 10, 2 );
		add_filter( 'wps_update_third_party', array( Doli_Third_Party_Class::g(), 'update' ), 10, 2 );

		add_action( 'wps_saved_billing_address', array( $this, 'update_billing_address' ) );
	}

	public function update_billing_address( $third_party ) {
		$third_party_id = Request_Util::put( 'thirdparties/ ' . $third_party->data['external_id'], array(
			'name'    => $third_party->data['title'],
			'address' => $third_party->data['address'],
			'town'    => $third_party->data['town'],
			'zip'     => $third_party->data['zip'],
			'email'   => $third_party->data['email'],
		) );
	}

}

new Doli_Third_Party_Action();
