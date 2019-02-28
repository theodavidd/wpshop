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

	public function doli_to_wp( $doli_contact, $wp_contact ) {
		$wp_third_party = null;

		if ( ! empty( $doli_contact->socid ) ) {
			$wp_third_party = Third_Party_Class::g()->get( array(
				'meta_key'   => '_external_id',
				'meta_value' => (int) $doli_contact->socid,
			), true );
		}

		$wp_contact->data['external_id'] = (int) $doli_contact->id;
		$wp_contact->data['login']       = sanitize_title( $doli_contact->email );
		$wp_contact->data['firstname']   = $doli_contact->firstname;
		$wp_contact->data['lastname']    = $doli_contact->lastname;
		$wp_contact->data['phone']       = $doli_contact->phone_pro;
		$wp_contact->data['email']       = $doli_contact->email;

		if ( false !== email_exists( $wp_contact->data['email'] ) ) {
			return false;
		}

		if ( $wp_third_party != null ) {
			$wp_contact->data['third_party_id'] = $wp_third_party->data['id'];
		}

		if ( empty( $wp_contact->data['id'] ) ) {
			$wp_contact->data['password'] = wp_generate_password();
		}

		$contact_saved = Contact_Class::g()->update( $wp_contact->data );

		if ( is_wp_error( $contact_saved ) ) {
			return false;
		}

		if ( $wp_third_party != null ) {
			$wp_third_party->data['contact_ids'][] = $contact_saved->data['id'];
			Third_Party_Class::g()->update( $wp_third_party->data );
		}
	}

	public function wp_to_doli( $wp_contact, $doli_contact ) {
	}
}

Doli_Contact_Class::g();
