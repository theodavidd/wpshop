<?php
/**
 * Les actions relatives aux proposals.
 *
 * @author Eoxia <corentin-settelen@hotmail.com>
 * @since 2.0.0
 * @version 2.0.0
 * @copyright 2018 Eoxia
 * @package wpshop
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Les actions relatives aux proposals.
 */
class Doli_Proposals_Action {

	/**
	 * Initialise les actions liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_synchro_proposals', array( $this, 'synchro_proposals' ) );
		add_action( 'wps_save_proposal', array( $this, 'callback_wps_save_proposal' ), 1, 2 );

		add_action( 'admin_post_wps_download_proposal', array( $this, 'download_proposal' ) );
	}

	public function synchro_proposals() {
		$doli_proposals = Request_Util::get( 'proposals' );

		if ( ! empty( $doli_proposals ) ) {
			foreach ( $doli_proposals as $doli_proposal ) {
				// Vérifie l'existence du produit en base de donnée.
				$proposal = Doli_Proposals_Class::g()->get( array(
					'meta_key'   => 'external_id',
					'meta_value' => $doli_proposal->id,
				), true );

				if ( empty( $proposal ) ) {
					$proposal = Doli_Proposals_Class::g()->get( array( 'schema' => true ), true );
				}

				$proposal->data['external_id'] = (int) $doli_proposal->id;
				$proposal->data['title']       = $doli_proposal->ref;
				$proposal->data['total_ht']    = $doli_proposal->total_ht;
				$proposal->data['total_ttc']   = $doli_proposal->total_ttc;
				$proposal->data['status']      = 'publish';

				Doli_Proposals_Class::g()->update( $proposal->data );

				do_action( 'wps_synchro_proposal', $proposal->data, $doli_proposal );

			}
		}

		wp_send_json_success( $doli_proposals );
	}

	public function callback_wps_save_proposal( $third_party, $contact ) {
		$proposal_id = Request_Util::post( 'proposals', array(
			'socid'             => $third_party->data['external_id'],
			'date'              => current_time( 'timestamp' ),
			'mode_reglement_id' => Doli_Payment::g()->convert_to_doli_id( $_POST['type_payment'] ),
		) );

		if ( ! empty( Class_Cart_Session::g()->cart_contents ) ) {
			foreach ( Class_Cart_Session::g()->cart_contents as $content ) {
				$proposal = Request_Util::post( 'proposals/' . $proposal_id . '/lines', array(
					'desc'                    => $content['content'],
					'fk_product'              => $content['external_id'],
					'product_type'            => 1,
					'qty'                     => $content['qty'],
					'tva_tx'                  => 0,
					'subprice'                => $content['price'],
					'remice_percent'          => 0,
					'rang'                    => 1,
					'total_ht'                => $content['price'],
					'total_tva'               => 0,
					'total_ttc'               => $content['price_ttc'],
					'product_label'           => $content['title'],
					'multicurrency_code'      => 'EUR',
					'multicurrency_subprice'  => $content['price'],
					'multicurrency_total_ht'  => $content['price'],
					'multicurrency_total_tva' => 0,
					'multicurrency_total_ttc' => $content['price_ttc'],
				) );

			}
		}

		$proposal = Request_Util::post( 'proposals/' . $proposal_id . '/validate', array(
			'notrigger' => 0,
		) );

		Request_Util::put( 'documents/builddoc', array(
			'module_part'   => 'propal',
			'original_file' => $proposal->ref . '/' . $proposal->ref . '.pdf'
		) );

		$proposal = Doli_Proposals_Class::g()->sync( $third_party->data['id'], $proposal );

		Class_Cart_Session::g()->add_external_data( 'proposal_id', $proposal->data['id'] );
		Class_Cart_Session::g()->update_session();
	}

	public function download_proposal() {
		$proposal_id = ! empty( $_GET['proposal_id'] ) ? (int) $_GET['proposal_id'] : 0;

		if ( ! $proposal_id ) {
			exit;
		}

		$contact     = Contact_Class::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$proposal    = Proposals_Class::g()->get( array( 'id' => $proposal_id ), true );

		if ( ( isset( $third_party->data ) && $proposal->data['parent_id'] != $third_party->data['id'] ) && ! current_user_can( 'administrator' ) )
			exit;

		$proposal_file = Request_Util::get( 'documents/download?module_part=propale&original_file=' . $proposal->data['title'] . '/' . $proposal->data['title'] . '.pdf' );
		$content = base64_decode( $proposal_file->content );

		header( 'Cache-Control: no-cache' );
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: inline; filename="' . $proposal->data['title'] . '.pdf"' );
		header( 'Content-Length: ' . strlen( $content ) );

		echo $content;

		exit;
	}
}

new Doli_Proposals_Action();
