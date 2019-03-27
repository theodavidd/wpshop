<?php
/**
 * Les fonctions principales des contacts avec dolibarr.
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
	 * Synchronise les contacts de dolibarr vers WP.
	 *
	 * @since 2.0.0
	 *
	 * @param  stdClass      $doli_contact Les données provenant de Dolibarr.
	 * @param  Contact_Model $wp_contact   Les données de WP.
	 */
	public function doli_to_wp( $doli_contact, $wp_contact ) {
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
		$wp_contact->data['phone']        = $doli_contact->phone_pro;
		$wp_contact->data['phone_mobile'] = $doli_contact->phone_mobile;
		$wp_contact->data['email']        = $doli_contact->email;

		if ( 0 === $wp_contact->data['id'] && false !== email_exists( $wp_contact->data['email'] ) ) {
			return false;
		}

		if ( null !== $wp_third_party ) {
			$wp_contact->data['third_party_id'] = $wp_third_party->data['id'];
		}

		if ( empty( $wp_contact->data['id'] ) ) {
			$wp_contact->data['password'] = wp_generate_password();
		}

		$contact_saved = Contact::g()->update( $wp_contact->data );

		if ( is_wp_error( $contact_saved ) ) {
			return false;
		}

		if ( null !== $wp_third_party ) {
			$wp_third_party->data['contact_ids'][] = $contact_saved->data['id'];
			Third_Party::g()->update( $wp_third_party->data );
		}
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
	public function wp_to_doli( $wp_contact, $doli_contact ) {
		$third_party = Third_Party::g()->get( array(
			'id' => $wp_contact->data['third_party_id'],
		), true );

		$contact_id = Request_Util::post( 'contacts', array(
			'lastname'  => $wp_contact->data['lastname'],
			'firstname' => $wp_contact->data['firstname'],
			'email'     => $wp_contact->data['email'],
			'phone_pro' => $wp_contact->data['phone'],
			'socid'     => $third_party->data['external_id'],
		) );

		return $contact_id;
	}
}

Doli_Contact::g();
