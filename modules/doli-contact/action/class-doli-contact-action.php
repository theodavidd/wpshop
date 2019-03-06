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
class Doli_Contact_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_checkout_create_contact', array( $this, 'checkout_create_contact' ) );

		add_action( 'wps_deleted_contact', array( $this, 'delete_contact' ), 10, 2 );
	}

	public function checkout_create_contact( $wp_contact ) {
		Doli_Contact_Class::g()->wp_to_doli( $wp_contact, null );
	}

	public function delete_contact( $third_party, $contact ) {
		$data = array(
			'socid' => -1,
		);

		Request_Util::put( 'contacts/' . $contact->data['external_id'], $data );
	}
}

new Doli_Contact_Action();
