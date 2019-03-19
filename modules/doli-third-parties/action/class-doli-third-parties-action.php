<?php
/**
 * Gestion des actions des tiers avec dolibarr.
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
 * Doli Third Parties Action Class.
 */
class Doli_Third_Parties_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_checkout_create_third_party', array( $this, 'checkout_create_third_party' ) );
		add_action( 'wps_saved_third_party', array( $this, 'save_third_party' ) );
		add_action( 'wps_saved_billing_address', array( $this, 'update_billing_address' ) );
	}

	/**
	 * Lors du tennel de vente, créer un tier vers dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  Third_Party_Model $wp_third_party Les données du tier depuis WP.
	 */
	public function checkout_create_third_party( $wp_third_party ) {
		Doli_Third_Parties::g()->wp_to_doli( $wp_third_party, null );
	}

	/**
	 * Créer ou met à jour un tier.
	 *
	 * @since 2.0.0
	 *
	 * @todo: Vérifier l'utilité
	 *
	 * @param  Third_Party_Model $third_party Les données du tier venant de WP.
	 *
	 * @return integer           ID du tier venant de Dolibarr.
	 */
	public function save_third_party( $third_party ) {
		$third_party_id = $third_party['external_id'];

		if ( ! empty( $third_party['external_id'] ) ) {
			Request_Util::put( 'thirdparties/' . $third_party['external_id'], array(
				'name' => $third_party['title'],
			) );
		} else {
			$third_party_id = Request_Util::post( 'thirdparties', array(
				'name' => $third_party['title'],
			) );
		}
		return $third_party_id;
	}

	/**
	 * Met à jour l'adresse de livraison d'un tier dans dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  Third_Party_Model $third_party Les données du tier venant de WP.
	 */
	public function update_billing_address( $third_party ) {
		$third_party_id = Request_Util::put( 'thirdparties/ ' . $third_party->data['external_id'], array(
			'name'    => $third_party->data['title'],
			'address' => $third_party->data['address'],
			'town'    => $third_party->data['town'],
			'zip'     => $third_party->data['zip'],
			'email'   => $third_party->data['email'],
		) );
	}
}

new Doli_Third_Parties_Action();
