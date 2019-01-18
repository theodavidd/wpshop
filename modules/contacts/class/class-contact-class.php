<?php
/**
 * Les fonctions principales des contacts.
 *
 * Le controlleur du modèle Contact_Model.
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
 * Contact class.
 */
class Contact_Class extends \eoxia\User_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Contact_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-contact';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'contact';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'contact';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	public function display( $third_party ) {
		$contacts = array();

		if ( ! empty( $third_party->data['contact_ids'] ) ) {
			$contacts = $this->get( array(
				'include' => $third_party->data['contact_ids'],
			) );
		}

		\eoxia\View_Util::exec( 'wpshop', 'contacts', 'list', array(
			'contacts' => $contacts,
		) );
	}

	public function synchro_contact( $third_party ) {
		$contact_ids = array();

		$request = wp_remote_get( 'http://127.0.0.1/dolibarr/api/index.php/contacts?thirdparty_ids=' . $third_party->data['external_id'], array(
			'headers' => array(
				'Content-type' => 'application/json',
				'DOLAPIKEY'    => 'JaTmW3kZu2X5oD491hTfY9Wbp9oY4Ag1',
			),
		) );

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body );

		if ( isset( $data->error ) ) {
			return $contact_ids;
		}

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

				$contact->data['external_id'] = (int) $doli_contact->id;
				$contact->data['login']       = $doli_contact->user_login;
				$contact->data['firstname']   = $doli_contact->firstname;
				$contact->data['lastname']    = $doli_contact->lastname;
				$contact->data['phone']       = $doli_contact->phone_pro;

				if ( empty( $contact->data['id'] ) ) {
					$contact->data['password'] = wp_generate_password();
				}

				$contact       = Contact_Class::g()->update( $contact->data );
				$contact_ids[] = $contact->data['id'];
			}
		}

		return $contact_ids;
	}

	public function save( $data ) {
		$contact = Contact_Class::g()->get( array( 'schema' => true ), true );

		$contact->data['login']     = sanitize_user( current( explode( '@', $data['contact']['email'] ) ), true );
		$contact->data['email']     = $data['contact']['email'];
		$contact->data['firstname'] = $data['contact']['firstname'];
		$contact->data['lastname']  = $data['contact']['lastname'];
		$contact->data['phone']     = $data['contact']['phone'];
		$contact->data['password']  = wp_generate_password();

		$contact = Contact_Class::g()->update( $contact->data );
		return $contact;
	}
}

Contact_Class::g();
