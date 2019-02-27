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

	/**
	 * Synchronisation de dolibarr vers WP
	 *
	 * @since 2.0.0
	 */
	public function doli_to_wp( $doli_third_party, $wp_third_party ) {
		$wp_third_party->data['external_id']   = (int) $doli_third_party->id;
		$wp_third_party->data['title']         = $doli_third_party->name;
		$wp_third_party->data['address']       = $doli_third_party->address;
		$wp_third_party->data['town']          = $doli_third_party->town;
		$wp_third_party->data['zip']           = $doli_third_party->zip;
		$wp_third_party->data['state']         = $doli_third_party->state;
		$wp_third_party->data['country']       = $doli_third_party->country;
		$wp_third_party->data['phone']         = $doli_third_party->phone;
		$wp_third_party->data['email']         = $doli_third_party->email;
		if ( ! empty( $doli_third_party->date_modification ) ) {
			$wp_third_party->data['date_modified'] = date( 'Y-m-d H:i:s', $doli_third_party->date_modification );
		}
		$wp_third_party->data['status']        = 'publish';

		Third_Party_Class::g()->update( $wp_third_party->data );
	}

	public function wp_to_doli( $wp_third_party, $doli_third_party ) {
		// Force le changement pour mêttre à jour la date de modification si les données n'ont pas changé.
		//  Dolibarr ne met pas à jour la date de modification si aucune donnée n'est changé.
		Request_Util::put( 'thirdparties/' . $doli_third_party->id, array(
			'name'    => 'tmp',
			'country' => $wp_third_party->data['country'],
			'address' => $wp_third_party->data['address'],
			'zip'     => $wp_third_party->data['zip'],
			'state'   => $wp_third_party->data['state'],
		) );

		Request_Util::put( 'thirdparties/' . $doli_third_party->id, array(
			'name'    => $wp_third_party->data['title'],
			'country' => $wp_third_party->data['country'],
			'address' => $wp_third_party->data['address'],
			'zip'     => $wp_third_party->data['zip'],
			'state'   => $wp_third_party->data['state'],
		) );
	}

	public function get_wp_id_by_doli_id( $doli_id ) {
		$third_party = Third_Party_Class::g()->get( array(
			'meta_key'   => '_external_id',
			'meta_value' => $doli_third_party->id,
		), true ); // WPCS: slow query ok.

		return $third_party->data['id'];
	}
}

Doli_Third_Party_Class::g();
