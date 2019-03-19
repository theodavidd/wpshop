<?php
/**
 * Gestion des actions des contact  avec dolibarr.
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
 * Doli Contact Action Class.
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

	/**
	 * Création d'un contact lors du tunnel de vente
	 *
	 * @since 2.0.0
	 *
	 * @param  Contact_Model $wp_contact Les données du contact.
	 */
	public function checkout_create_contact( $wp_contact ) {
		Doli_Contact::g()->wp_to_doli( $wp_contact, null );
	}

	/**
	 * Supprimes un contact
	 *
	 * @since 2.0.0
	 *
	 * @param  Third_Party_Model $third_party Les données du tier.
	 * @param  Contact_Model     $contact     Les données du contact.
	 */
	public function delete_contact( $third_party, $contact ) {
		$data = array(
			'socid' => -1,
		);

		Request_Util::put( 'contacts/' . $contact->data['external_id'], $data );
	}
}

new Doli_Contact_Action();
