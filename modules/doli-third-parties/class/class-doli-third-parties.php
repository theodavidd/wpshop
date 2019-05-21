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
	 *
	 * @since 2.0.0
	 */
	public function doli_to_wp( $doli_third_party, $wp_third_party ) {
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

		if ( ! empty( $doli_third_party->date_modification ) ) {
			$wp_third_party->data['date_modified'] = date( 'Y-m-d H:i:s', $doli_third_party->date_modification );
		}

		$wp_third_party->data['date_last_synchro'] = current_time( 'mysql');

		Third_Party::g()->update( $wp_third_party->data );
	}

	/**
	 * Synchronisation de WP vers dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  Third_Party_Model $wp_third_party   Les données du tier de WP.
	 * @param  stdClass          $doli_third_party Les données du tier venant
	 * de dolibarr.
	 */
	public function wp_to_doli( $wp_third_party, $doli_third_party ) {
		$data = array(
			'name'       => $wp_third_party->data['title'],
			'country'    => $wp_third_party->data['country'],
			'country_id' => $wp_third_party->data['country_id'],
			'address'    => $wp_third_party->data['address'],
			'zip'        => $wp_third_party->data['zip'],
			'state'      => $wp_third_party->data['state'],
		);

		if ( ! empty( $wp_third_party->data['external_id'] ) ) {
			Request_Util::put( 'thirdparties/' . $doli_third_party->id, $data );
		} else {
			$doli_third_party_id                 = Request_Util::post( 'thirdparties', $data );
			$wp_third_party->data['external_id'] = $doli_third_party_id;

			Third_Party::g()->update( $wp_third_party->data );
		}
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
