<?php
/**
 * Les fonctions principales des tiers avec dolibarr.
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
 * Third Party class.
 */
class Doli_Third_Party_Class extends \eoxia\Singleton_Util {

	protected function construct() {}

	public function save( $posted_data, $third_party_data ) {
		if ( empty( $posted_data['third_party']['title'] ) ) {
			$email = explode( '@', $posted_data['contact']['email'] );

			$posted_data['third_party']['title'] = $email[0];
		}

		$third_party_id = Request_Util::post( 'thirdparties', array(
			'name'    => $posted_data['third_party']['title'],
			'country' => $posted_data['third_party']['country'],
			'address' => $posted_data['third_party']['address'],
			'zip'     => $posted_data['third_party']['zip'],
			'state'   => $posted_data['third_party']['state'],
			'email'   => $posted_data['contact']['email'],
			'client'  => 1,
		) );

		$contact_id = Doli_Contact_Class::g()->save( $third_party_id, $posted_data );

		$update_proposal_status = Proposals_Class::g()->update_third_party( $third_party_id );

		if ( empty( $third_party_id ) || empty( $contact_id ) || ! $update_proposal_status ) {
			return false;
		}

		return $this->sync( $third_party_id, $contact_id );
	}

	private function sync( $third_party_id, $contact_id ) {

		$third_party_data = Request_Util::get( 'thirdparties/' . $third_party_id );
		$third_party_wp   = Third_Party_Class::g()->sync( $third_party_id, $third_party_data );

		$contact_data = Request_Util::get( 'thirdparties/' . $third_party_id );
		$contact_wp   = Contact_Class::g()->sync( $contact_id, $contact_data );

		$third_party_wp->data['contact_ids'] = array( $contact_wp->data['id'] );
		Third_Party_Class::g()->update( $third_party_wp->data );

		return array(
			'third_party' => $third_party_wp,
			'contact'     => $contact_wp,
		);
	}
}

Doli_Third_Party_Class::g();
