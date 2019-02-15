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
class Doli_Contact_Class extends \eoxia\Singleton_Util {

	protected function construct() {}

	public function save( $third_party_id, $posted_data  ) {
		if ( empty( $posted_data['contact']['lastname'] ) ) {
			$email = explode( '@', $posted_data['contact']['email'] );

			$posted_data['contact']['lastname'] = $email[0];
		}

		$contact_id = Request_Util::post( 'contacts', array(
			'lastname'  => $posted_data['contact']['lastname'],
			'firstname' => $posted_data['contact']['firstname'],
			'email'     => $posted_data['contact']['email'],
			'phone_pro' => $posted_data['contact']['phone'],
			'socid'     => $third_party_id,
		) );

		return $contact_id;
	}

	public function update( $third_party_id, $posted_data ) {
		if ( ! empty( $posted_data['contact']['external_id'] ) ) {

			if ( empty( $posted_data['contact']['lastname'] ) ) {
				$email = explode( '@', $posted_data['contact']['email'] );

				$posted_data['contact']['lastname'] = $email[0];
			}

			Request_Util::put( 'contact/' . $posted_data['contact']['external_id'], array(
				'lastname'  => $posted_data['contact']['lastname'],
				'firstname' => $posted_data['contact']['firstname'],
				'email'     => $posted_data['contact']['email'],
				'phone_pro' => $posted_data['contact']['phone'],
			) );
		}
	}

	public function synchro( $index, $limit ) {
		$contact_ids = array();

		$data = Request_Util::get( 'contacts?sortfield=t.rowid&sortorder=ASC&limit=' . $limit . '&page=' . ( $index / $limit ) );

		if ( ! empty( $data ) ) {
			foreach ( $data as $doli_contact ) {
				// Vérifie l'existence du contact en base de donnée.
				$contact = Contact_Class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => $doli_contact->id,
				), true ); // WPCS: slow query ok.

				if ( empty( $contact ) ) {
					$contact = Contact_Class::g()->get( array( 'schema' => true ), true );
				}

				$contact->data['external_id']    = (int) $doli_contact->id;
				$contact->data['third_party_id'] = (int) $doli_contact->socid;
				$contact->data['login']          = $doli_contact->socname;
				$contact->data['firstname']      = $doli_contact->firstname;
				$contact->data['lastname']       = $doli_contact->lastname;
				$contact->data['phone']          = $doli_contact->phone_pro;
				$contact->data['email']          = $doli_contact->email;

				if ( empty( $contact->data['id'] ) ) {
					$contact->data['password'] = wp_generate_password();
				}

				$contact_saved = Contact_Class::g()->update( $contact->data );

				$third_party = Third_Party_Class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => $contact_saved->data['third_party_id'],
				), true );
				$third_party->data['contact_ids'][] = $contact_saved->data['id'];
				Third_Party_Class::g()->update( $third_party->data );
			}
		}

		// // Supprimes les contacts qui ne sont plus présent dans dolibarr
		// if ( ! empty( $third_party->data['contact_ids'] ) ) {
		// 	foreach ( $third_party->data['contact_ids'] as $index => $contact_id ) {
		// 		if ( ! in_array( $contact_id, $contact_ids ) && ! empty( $contact_id ) ) {
		// 			array_splice( $third_party->data['contact_ids'], $index, 1 );
		//
		// 			$contact                = Contact_Class::g()->get( array( 'id' => $contact_id ), true );
		// 			$contact->data['socid'] = -1;
		// 			Contact_Class::g()->update( $contact->data );
		//
		// 		}
		// 	}
		// }
		//
		// Third_Party_Class::g()->update( $third_party->data );

		return true;
	}
}

Doli_Contact_Class::g();
