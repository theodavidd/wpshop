<?php
/**
 * Gestion des actions des tiers.
 *
 * Ajoutes une page "Tiers" dans le menu de WordPress.
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
 * Action of Third Party module.
 */
class Third_Party_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'wp_ajax_synchro_third_parties', array( $this, 'ajax_synchro_third_parties' ) );
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Third Parties', 'wpshop' ), __( 'Third Parties', 'wpshop' ), 'manage_options', 'wps-third-party', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Third Party".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		\eoxia\View_Util::exec( 'wpshop', 'third-party', 'main' );
	}

	/**
	 * Synchronisation des produits avec dolibarr.
	 *
	 * @since 2.0.0
	 */
	public function ajax_synchro_third_parties() {
		$request = wp_remote_get( 'http://127.0.0.1/dolibarr/api/index.php/thirdparties', array(
			'headers' => array(
				'Content-type' => 'application/json',
				'DOLAPIKEY'    => 'JaTmW3kZu2X5oD491hTfY9Wbp9oY4Ag1',
			),
		) );

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body );

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
				$third_party->data['zip']              = $doli_third_party->zip;
				$third_party->data['state']            = $doli_third_party->state;
				$third_party->data['country']          = $doli_third_party->country;

				$third_party = Third_Party_Class::g()->update( $third_party->data );
				$contact_ids = Contact_Class::g()->synchro_contact( $third_party );

				$third_party->data['contact_ids'] = $contact_ids;
				Third_Party_Class::g()->update( $third_party->data );

			}
		}

		wp_send_json_success( $data );
	}
}

new Third_Party_Action();
