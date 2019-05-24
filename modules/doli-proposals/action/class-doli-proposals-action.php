<?php
/**
 * Gestion des actions des devis venant de dolibarr.
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
 * Doli Porposals Action.
 */
class Doli_Proposals_Action {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_checkout_create_proposal', array( $this, 'checkout_create_proposal' ), 1, 1 );
		add_action( 'wps_checkout_update_proposal', array( $this, 'checkout_update_proposal' ), 1, 1 );

		add_action( 'admin_post_wps_download_proposal', array( $this, 'download_proposal' ) );
	}

	/**
	 * Créer le devis vers dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  Proposal_Model $wp_proposal Les données du devis.
	 */
	public function checkout_create_proposal( $wp_proposal ) {
		if ( Settings::g()->dolibarr_is_active() ) {
			$doli_proposal_id = Doli_Proposals::g()->wp_to_doli( $wp_proposal );

			$doli_proposal = Request_Util::post( 'proposals/' . $doli_proposal_id . '/validate', array(
				'notrigger' => 0,
			) );

			$doli_proposal = Request_Util::post( 'proposals/' . $doli_proposal_id . '/close', array(
				'status'    => 2,
				'notrigger' => 0,
			) );

			Request_Util::put( 'documents/builddoc', array(
				'module_part'   => 'propal',
				'original_file' => $doli_proposal->ref . '/' . $doli_proposal->ref . '.pdf',
			) );

			update_post_meta( $wp_proposal->data['id'], '_external_id', $doli_proposal_id );

			Doli_Proposals::g()->doli_to_wp( $doli_proposal, $wp_proposal );
		}
	}

	/**
	 * Met à jour le devis avec le mode de règlement.
	 *
	 * @since 2.0.0
	 *
	 * @param  Proposal_Model $wp_proposal Les données du devis.
	 */
	public function checkout_update_proposal( $wp_proposal ) {
		if ( Settings::g()->dolibarr_is_active() ) {
			$doli_proposal = Request_Util::put( 'proposals/' . $wp_proposal->data['external_id'], array(
				'mode_reglement_id' => Doli_Payment::g()->convert_to_doli_id( $wp_proposal->data['payment_method'] ),
			) );
		}
	}

	/**
	 * Télécharges le devis.
	 *
	 * @since 2.0.0
	 */
	public function download_proposal() {
		check_admin_referer( 'download_proposal' );


		$proposal_id = ! empty( $_GET['proposal_id'] ) ? (int) $_GET['proposal_id'] : 0;

		if ( ! $proposal_id ) {
			exit;
		}

		$contact     = Contact::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$proposal    = Proposals::g()->get( array( 'id' => $proposal_id ), true );

		if ( ( isset( $third_party->data ) && $proposal->data['parent_id'] !== $third_party->data['id'] ) && ! current_user_can( 'administrator' ) ) {
			exit;
		}


		$proposal_file = Request_Util::get( 'documents/download?module_part=propale&original_file=' . $proposal->data['title'] . '/' . $proposal->data['title'] . '.pdf' );
		$content       = base64_decode( $proposal_file->content );

		header( 'Cache-Control: no-cache' );
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: inline; filename="' . $proposal->data['title'] . '.pdf"' );
		header( 'Content-Length: ' . strlen( $content ) );

		echo $content;

		exit;
	}
}

new Doli_Proposals_Action();
