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
class Proposals_Action {

	/**
	 * Initialise les actions liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'add_meta_boxes', array( $this, 'callback_add_meta_boxes' ) );

		add_action( 'wp_ajax_synchro_proposals', array( $this, 'synchro_proposals' ) );

		add_action( 'wps_save_proposal', array( $this, 'callback_wps_save_proposal' ), 1, 2 );
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Proposals', 'wpshop' ), __( 'Proposals', 'wpshop' ), 'manage_options', 'wps-proposal', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Orders".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		\eoxia\View_Util::exec( 'wpshop', 'doli-proposals', 'main' );
	}

	public function callback_add_meta_boxes() {
		add_meta_box( 'wps-proposals-customer', __( 'Proposals details number 1', 'wpshop' ), array( $this, 'callback_meta_box' ), 'wps-proposal' );
	}

	public function callback_meta_box( $post ) {
		$order                     = Proposals_Class::g()->get( array( 'id' => $post->ID ), true );
		$invoice                   = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
		$invoice->data['payments'] = Doli_Payment::g()->get( array( 'post_parent' => $invoice->data['id'] ) );

		\eoxia\View_Util::exec( 'wpshop', 'doli-proposals', 'order-details', array(
			'order'   => $order,
			'invoice' => $invoice,
		) );
	}

	public function synchro_proposals() {
		$doli_proposals = Request_Util::get( 'proposals' );

		if ( ! empty( $doli_proposals ) ) {
			foreach ( $doli_proposals as $doli_proposal ) {
				// Vérifie l'existence du produit en base de donnée.
				$proposal = Proposals_Class::g()->get( array(
					'meta_key'   => 'external_id',
					'meta_value' => $doli_proposal->id,
				), true );

				if ( empty( $proposal ) ) {
					$proposal = Proposals_Class::g()->get( array( 'schema' => true ), true );
				}

				$proposal->data['external_id'] = (int) $doli_proposal->id;
				$proposal->data['title']       = $doli_proposal->ref;
				$proposal->data['total_ht']    = $doli_proposal->total_ht;
				$proposal->data['total_ttc']   = $doli_proposal->total_ttc;
				$proposal->data['status']      = 'publish';

				Proposals_Class::g()->update( $proposal->data );

				do_action( 'wps_synchro_proposal', $proposal->data, $doli_proposal );

			}
		}

		wp_send_json_success( $doli_proposals );
	}

	public function callback_wps_save_proposal( $third_party, $contact ) {
		$proposal_id = Request_Util::post( 'proposals', array(
			'socid'             => $third_party->data['external_id'],
			'date'              => current_time( 'timestamp' ),
			'mode_reglement_id' => Doli_Payment::g()->convert_to_doli_id( $_POST['type'] ),
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

		$proposal = Proposals_Class::g()->sync( $third_party->data['id'], $proposal );

		Class_Cart_Session::g()->add_external_data( 'proposal_id', $proposal->data['id'] );
		Class_Cart_Session::g()->update_session();
	}
}

new Proposals_Action();
