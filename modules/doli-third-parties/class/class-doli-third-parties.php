<?php
/**
 * Les fonctions principales des tiers avec dolibarr.
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
 * Doli Third Parties class.
 */
class Doli_Third_Parties extends \eoxia\Singleton_Util {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Synchronisation de dolibarr vers WP
	 *
	 * @param stdClass          $doli_third_party Les données du tier
	 * venant de dolibarr.
	 * @param Third_party_Model $wp_third_party   Les données du tier de WP.
	 * @param boolean           $save             Enregistres les données sinon
	 * renvoies l'objet remplit sans l'enregistrer en base de donnée.
	 * @param array             $notices          Gestion des erreurs et
	 * informations de l'évolution de la méthode.
	 *
	 * @since 2.0.0
	 */
	public function doli_to_wp( $doli_third_party, $wp_third_party, $save = true, &$notices = array(
		'errors'   => array(),
		'messages' => array(),
	) ) {
		$wp_third_party->data['external_id'] = (int) $doli_third_party->id;
		$wp_third_party->data['title']       = $doli_third_party->name;
		$wp_third_party->data['address']     = $doli_third_party->address;
		$wp_third_party->data['town']        = $doli_third_party->town;
		$wp_third_party->data['zip']         = $doli_third_party->zip;
		$wp_third_party->data['state']       = $doli_third_party->state;
		$wp_third_party->data['country']     = $doli_third_party->country;
		$wp_third_party->data['phone']       = $doli_third_party->phone;
		$wp_third_party->data['email']       = $doli_third_party->email;
		$wp_third_party->data['status']      = 'publish';

		if ( $save ) {
			if ( ! empty( $doli_third_party->date_modification ) ) {
				$wp_third_party->data['date_modified'] = date( 'Y-m-d H:i:s', $doli_third_party->date_modification );
			}

			$wp_third_party->data['date_last_synchro'] = current_time( 'mysql');
			Third_Party::g()->update( $wp_third_party->data );

			// translators: Erase data for the third party <strong>Eoxia</strong> with the <strong>dolibarr</strong> data.
			$notices['messages'][] = sprintf( __( 'Erase data for the third party <strong>%s</strong> with the <strong>dolibarr</strong> data', 'wpshop' ), $wp_third_party->data['title'] );
		}

		$doli_third_party->contacts = Request_Util::get( 'contacts?sortfield=t.rowid&sortorder=ASC&limit=-1&thirdparty_ids=' . $doli_third_party->id );

		if ( ! empty( $doli_third_party->contacts ) ) {
			foreach ( $doli_third_party->contacts as $doli_contact ) {
				if ( ! empty( $doli_contact->email ) ) {

					// Gestion contact déjà existant.
					$wp_contact = Contact::g()->get( array(
						'search' => $doli_contact->email,
					), true );

					// translators: Try to add contact <strong>Test</strong> to the third party <strong>Eoxia</strong>.
					$notices['messages'][] = sprintf( __( 'Try to add contact <strong>%1$s</strong> to the third party <strong>%2$s</strong>', 'wpshop' ), $wp_contact->data['email'], $wp_third_party->data['title'] );

					if ( ! empty( $wp_contact ) ) {
						// Est-ce qu'il a une société ?
						if ( ! empty( $wp_contact->data['third_party_id'] ) && $wp_contact->data['third_party_id'] !== $wp_third_party->data['id'] ) {

							// translators: The contact <strong>test</strong> is already associated to another third party.
							$notices['errors'][] = sprintf( __( 'The contact <strong>%s</strong> is already associated to another third party', 'wpshop' ), $wp_contact->data['email'] );
						} else {
							// On le met à jour et on l'affecte à la société.
							Doli_Contact::g()->doli_to_wp( $doli_contact, $wp_contact );

							// translators: Erase data for the contact <strong>test</strong> with the <strong>dolibarr</strong> data and affect to <strong>Eoxia</strong>.
							$notices['messages'][] = sprintf( __( 'Erase data for the contact <strong>%1$s</strong> with the <strong>dolibarr</strong> data and affect to <strong>%2$s</strong>', 'wpshop' ), $wp_contact->data['email'], $wp_third_party->data['title'] );
						}
					} else {
						$wp_contact = Contact::g()->get( array( 'schema' => true ), true );
						// On le créer et on l'affecte à la société.
						Doli_Contact::g()->doli_to_wp( $doli_contact, $wp_contact );

						// translators: Erase data for the contact <strong>test</strong> with the <strong>dolibarr</strong> data and affect to <strong>Eoxia</strong>.
						$notices['messages'][] = sprintf( __( 'Erase data for the contact <strong>%1$s</strong> with the <strong>dolibarr</strong>data and affect to <strong>%2$s</strong>', 'wpshop' ), $wp_contact->data['email'], $wp_third_party->data['title'] );
					}
				}
			}
		}

		return $wp_third_party;
	}

	/**
	 * Synchronisation de WP vers dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param Third_Party_Model $wp_third_party   Les données du tier de WP.
	 * @param stdClass          $doli_third_party Les données du tier venant
	 * de dolibarr.
	 * @param boolean           $save             Enregistres les données sinon
	 * renvoies l'objet remplit sans l'enregistrer en base de donnée.
	 * @param array             $notices          Gestion des erreurs et
	 * informations de l'évolution de la méthode.
	 */
	public function wp_to_doli( $wp_third_party, $doli_third_party, $save = true, &$notices = array(
		'errors'   => array(),
		'messages' => array(),
	) ) {
		$data = array(
			'name'       => $wp_third_party->data['title'],
			'country'    => $wp_third_party->data['country'],
			'country_id' => $wp_third_party->data['country_id'],
			'address'    => $wp_third_party->data['address'],
			'zip'        => $wp_third_party->data['zip'],
			'state'      => $wp_third_party->data['state'],
			'phone'      => $wp_third_party->data['phone'],
			'town'       => $wp_third_party->data['town'],
		);

		if ( ! empty( $wp_third_party->data['external_id'] ) ) {
			$doli_third_party = Request_Util::put( 'thirdparties/' . $wp_third_party->data['external_id'], $data );

			// translators: Erase data for the third party <strong>Eoxia</strong> with the <strong>WordPress</strong> data.
			$notices['messages'][] = sprintf( __( 'Erase data for the third party <strong>%s</strong> with the <strong>WordPress</strong> data', 'wpshop' ), $doli_third_party->name );
		} else {
			$doli_third_party_id                 = Request_Util::post( 'thirdparties', $data );
			$wp_third_party->data['external_id'] = $doli_third_party_id;
		}

		$wp_third_party = Third_Party::g()->update( $wp_third_party->data );
		return $wp_third_party;
	}

	/**
	 * Récupères l'ID de WP depuis l'ID de dolibarr
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $doli_id L'ID du tier venant de dolibarr.
	 *
	 * @return integer          L'ID WP du tier.
	 */
	public function get_wp_id_by_doli_id( $doli_id ) {
		$third_party = Third_Party::g()->get( array(
			'meta_key'   => '_external_id',
			'meta_value' => (int) $doli_id,
		), true ); // WPCS: slow query ok.

		return $third_party->data['id'];
	}
}

Doli_Third_Parties::g();
