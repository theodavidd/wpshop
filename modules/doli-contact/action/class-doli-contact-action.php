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
		add_action( 'wps_saved_and_associated_contact', array( $this, 'syncho_save_contact' ), 10, 3 );
		add_action( 'wps_deleted_contact', array( $this, 'delete_contact' ), 10, 2 );
	}

	public function syncho_save_contact( $third_party, $contact, $new = true ) {
		$data = array(
			'lastname'  => $contact->data['lastname'],
			'firstname' => $contact->data['firstname'],
			'email'     => $contact->data['email'],
			'phone_pro' => $contact->data['phone'],
			'socid'     => $third_party->data['external_id'],
		);

		if ( $new ) {
			$contact_id = Request_Util::post( 'contacts', $data );

			$contact->data['external_id'] = $contact_id;

			Contact_Class::g()->update( $contact->data );
		} else {
			Request_Util::put( 'contacts/' . $contact->data['external_id'], $data );
		}
	}

	public function delete_contact( $third_party, $contact ) {
		$data = array(
			'socid' => -1,
		);

		Request_Util::put( 'contacts/' . $contact->data['external_id'], $data );
	}
}

new Doli_Contact_Action();
