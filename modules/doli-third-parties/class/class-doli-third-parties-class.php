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

		if ( empty( $third_party_id ) || empty( $contact_id ) ) {
			return false;
		}

		return $this->sync( 0, 0, $third_party_id, $contact_id );
	}

	public function update( $posted_data, $third_party_data ) {
		Request_Util::put( 'thirdparties/' . $posted_data['third_party']['external_id'], array(
			'name'    => $posted_data['third_party']['title'],
			'country' => $posted_data['third_party']['country'],
			'address' => $posted_data['third_party']['address'],
			'zip'     => $posted_data['third_party']['zip'],
			'state'   => $posted_data['third_party']['state'],
			'email'   => $posted_data['contact']['email'],
		) );

		Doli_Contact_Class::g()->update( $posted_data['third_party']['id'], $posted_data );

		return $this->sync( $posted_data['third_party']['id'], $posted_data['contact']['id'], $posted_data['third_party']['external_id'], $posted_data['contact']['external_id'] );
	}

	private function sync( $third_party_wp_id, $contact_wp_id, $third_party_id, $contact_id ) {
		$third_party_data = Request_Util::get( 'thirdparties/' . $third_party_id );
		$third_party_wp   = Third_Party_Class::g()->sync( $third_party_wp_id, $third_party_id, $third_party_data );

		$contact_data = Request_Util::get( 'thirdparties/' . $third_party_id );
		$contact_wp   = Contact_Class::g()->sync( $contact_wp_id, $third_party_wp->data['id'], $contact_id, $contact_data );

		if ( ! in_array( $contact_wp->data['id'], (array) $third_party_wp->data['contact_ids'], true ) ) {
			$third_party_wp->data['contact_ids'] = array( $contact_wp->data['id'] );
			Third_Party_Class::g()->update( $third_party_wp->data );
		}

		return array(
			'third_party' => $third_party_wp,
			'contact'     => $contact_wp,
		);
	}

	/**
	 * Synchronisation des tiers de dolibarr.
	 *
	 * @since 2.0.0
	 */
	public function synchro( $index, $limit ) {
		$data = Request_Util::get( 'thirdparties?sortfield=t.rowid&sortorder=ASC&limit=' . $limit . '&page=' . ( $index / $limit ) );

		if ( ! empty( $data ) ) {
			foreach ( $data as $doli_third_party ) {
				// Vérifie l'existence du tier en base de donnée.
				$third_party = Third_Party_Class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => $doli_third_party->id,
				), true ); // WPCS: slow query ok.

				if ( empty( $third_party ) ) {
					$third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );
				}

				$third_party->data['external_id']      = (int) $doli_third_party->id;
				$third_party->data['title']            = $doli_third_party->name;
				$third_party->data['forme_juridique']  = $doli_third_party->forme_juridique;
				$third_party->data['code_fournisseur'] = $doli_third_party->code_fournisseur;
				$third_party->data['address']          = $doli_third_party->address;
				$third_party->data['town']             = $doli_third_party->town;
				$third_party->data['zip']              = $doli_third_party->zip;
				$third_party->data['state']            = $doli_third_party->state;
				$third_party->data['country']          = $doli_third_party->country;
				$third_party->data['phone']            = $doli_third_party->phone;
				$third_party->data['email']            = $doli_third_party->email;
				$third_party->data['status']           = 'publish';

				$third_party = Third_Party_Class::g()->update( $third_party->data );
				//$contact_ids = Contact_Class::g()->synchro_contact( $third_party );

				//$third_party->data['contact_ids'] = $contact_ids;
				//Third_Party_Class::g()->update( $third_party->data );

			}
		}

		return true;
	}
}

Doli_Third_Party_Class::g();
