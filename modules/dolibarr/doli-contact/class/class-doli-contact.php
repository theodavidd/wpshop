<?php
/**
 * Les fonctions principales des contact avec dolibarr.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
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
 * Doli Contact Class.
 */
class Doli_Contact extends \eoxia\Singleton_Util {

	/**
	 * Constructeur
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Synchronise les contact de dolibarr vers WP.
	 *
	 * @since 2.0.0
	 *
	 * @param  stdClass      $doli_contact Les données provenant de Dolibarr.
	 * @param  Contact_Model $wp_contact   Les données de WP.
	 * @param  boolean       $save         True pour enregister le contact en
	 * base de donnée. Sinon false pour seulement récupérer un objet remplis.
	 *
	 * @return boolean|Contact_Model       Les données du contact.
	 */
	public function doli_to_wp( $doli_contact, $wp_contact, $save = true ) {
		$wp_third_party = null;

		if ( ! empty( $doli_contact->socid ) ) {
			$wp_third_party = Third_Party::g()->get( array(
				'meta_key'   => '_external_id',
				'meta_value' => (int) $doli_contact->socid,
			), true );
		}

		$wp_contact->data['external_id']  = (int) $doli_contact->id;
		$wp_contact->data['login']        = sanitize_title( $doli_contact->email );
		$wp_contact->data['firstname']    = $doli_contact->firstname;
		$wp_contact->data['lastname']     = $doli_contact->lastname;
		$wp_contact->data['displayname']  = $doli_contact->lastname;
		$wp_contact->data['phone']        = $doli_contact->phone_pro;
		$wp_contact->data['phone_mobile'] = $doli_contact->phone_mobile;
		$wp_contact->data['email']        = $doli_contact->email;

		if ( ! empty( $wp_third_party ) ) {
			$wp_contact->data['third_party_id'] = $wp_third_party->data['id'];
		}

		if ( $save ) {
			// @todo: Vérifier cette condition.
			if ( 0 === $wp_contact->data['id'] && false !== email_exists( $wp_contact->data['email'] ) ) {
				\eoxia\LOG_Util::log( sprintf( 'Contact: doli_to_wp can\'t create %s email already exist', json_encode( $wp_contact->data ) ), 'wpshop2' );
				return false;
			}

			if ( empty( $wp_contact->data['id'] ) ) {
				$wp_contact->data['password'] = wp_generate_password();
			}

			$contact_saved = Contact::g()->update( $wp_contact->data );

			if ( is_wp_error( $contact_saved ) ) {
				// translators: Contact: doli_to_wp error when update or create contact {json_data}.
				\eoxia\LOG_Util::log( sprintf( 'Contact: doli_to_wp error when update or create contact: %s', json_encode( $contact_saved ) ), 'wpshop2' );
				return false;
			}

			$data_sha = array();

			$data_sha['doli_id']      = (int) $doli_contact->id;
			$data_sha['wp_id']        = (int) $wp_contact->data['id'];
			$data_sha['lastname']     = $wp_contact->data['lastname'];
			$data_sha['phone']        = $wp_contact->data['phone'];
			$data_sha['phone_mobile'] = $wp_contact->data['phone_mobile'];
			$data_sha['email']        = $wp_contact->data['email'];

			update_user_meta( $wp_contact->data['id'], '_sync_sha_256', hash( 'sha256', implode( ',', $data_sha ) ) );

			if ( ! empty( $wp_third_party ) ) {
				$wp_third_party->data['contact_ids'][] = $contact_saved->data['id'];
				Third_Party::g()->update( $wp_third_party->data );
			}
		}

		return $wp_contact;
	}

	/**
	 * Synchronise WP vers Dolibarr
	 *
	 * @since 2.0.0
	 *
	 * @param  Contact_Model $wp_contact   Les données du contact provenant de WP.
	 * @param  stdClass      $doli_contact Les données du contact dolibarr.
	 *
	 * @return integer                     L'ID de dolibarr.
	 */
	public function wp_to_doli( $wp_contact ) {
		$third_party = Third_Party::g()->get( array(
			'id' => $wp_contact->data['third_party_id'],
		), true );

		$data = array(
			'lastname'  => $wp_contact->data['lastname'],
			'firstname' => $wp_contact->data['firstname'],
			'email'     => $wp_contact->data['email'],
			'phone_pro' => $wp_contact->data['phone'],
			'socid'     => $third_party->data['external_id'],
		);

		if ( ! empty( $wp_contact->data['external_id'] ) ) {
			$contact = Request_Util::put( 'contact/' . $wp_contact->data['external_id'], $data );
		} else {
			$contact_id                      = Request_Util::post( 'contact', $data );
			$wp_contact->data['external_id'] = (int) $contact_id;

			Contact::g()->update( $wp_contact->data );

		}

		return $wp_contact->data['external_id'];
	}

	/**
	 * Récupères l'ID de WP depuis l'ID de dolibarr
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $doli_id L'ID du contact venant de dolibarr.
	 *
	 * @return integer          L'ID WP du contact.
	 */
	public function get_wp_id_by_doli_id( $doli_id ) {
		$users = get_users( array(
			'meta_key'   => '_external_id',
			'meta_value' => (int) $doli_id,
		) );

		$user = isset ( $users[0] ) ? $users[0] : null;

		if ( ! $user ) {
			return null;
		}

		return $user->ID;
	}

	/**
	 * @todo: Comment.
	 *
	 * @return array
	 *@since 2.0.0
	 *
	 */
	public function check_connected_to_erp() {
		if ( ! is_user_logged_in() ) {
			return array(
				'status' => true,
				'status_code' => '0x0',
				'status_message' => 'User not log',
			);
		}

		$contact = Contact::g()->get( array( 'id' => get_current_user_id() ), true );

		if ( ! $contact ) {
			return array(
				'status' => false,
				'status_code' => '0x5',
				'status_message' => 'User not found',
			);
		}

		if ( empty( $contact->data['external_id'] ) ) {
			return array(
				'status' => false,
				'status_code' => '0x6',
				'status_message' => 'User not external_id',
			);
		}

		if ( empty( $contact->data['sync_sha_256'] ) ) {
			return array(
				'status' => false,
				'status_code' => '0x7',
				'status_message' => 'Contact SHA256 cannot be empty',
			);
		}

		if ( empty( $contact->data['third_party_id'] ) ) {
			return array(
				'status' => false,
				'status_code' => '0x8',
				'status_message' => 'User not connected to third party',
			);
		}

		$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );

		if ( ! $third_party ) {
			return array(
				'status' => false,
				'status_code' => '0x9',
				'status_message' => 'Third Party not found',
			);
		}

		if ( empty( $third_party->data['external_id'] ) ) {
			return array(
				'status' => false,
				'status_code' => '0x10',
				'status_message' => 'Third Party not external id',
			);
		}

		// @todo: If contact_ids is empty, alert.

		if ( empty( $third_party->data['sync_sha_256'] ) ) {
			return array(
				'status' => false,
				'status_code' => '0x11',
				'status_message' => 'Third Party SHA256 cannot be empty',
			);
		}

		$data = array(
			'wp_contact_id'       => $contact->data['id'],
			'doli_contact_id'     => $contact->data['external_id'],
			'sha_contact'         => $contact->data['sync_sha_256'],
			'wp_third_party_id'   => $third_party->data['id'],
			'doli_third_party_id' => $third_party->data['external_id'],
			'sha_third_party'     => $third_party->data['sync_sha_256'],
		);

		$api_url = 'wpshop/getUserStatus?' . http_build_query( $data );

		$response_user = Request_Util::g()->get( $api_url );

		if ( ! $response_user || ! isset( $response_user->status_code ) ) {
			return array(
				'status' => false,
				'status_code' => '0x11',
				'status_message' => 'Check User Status: No response from Dolibarr.',
			);
		}

		if ( $response_user->status_code != '0x0' ) {
			return array(
				'status' => false,
				'status_code' => $response_user->status_code,
				'status_message' => $response_user->status_message,
			);
		} else {
			return array(
				'status' => true,
				'status_code' => '0x0',
			);
		}


		//  test data consistence
		/*$response_user = Doli_Sync::g()->check_status( $contact->data['id'], 'wps-user' );

		if ( ! $response_user->status ) {
			return array(
				'status' => false,
				'status_code' => '0x8',
				'status_message' => 'User no connector on Dolibarr',
			);
		}*/
	}
}

Doli_Contact::g();
