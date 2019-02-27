<?php
/**
 * Gestion des actions de synchronisations des entités avec dolibarr.
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
class Doli_Syncho_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_load_modal_synchro', array( $this, 'load_modal_synchro' ) );
		add_action( 'wp_ajax_load_synchro_modal_single', array( $this, 'load_modal_synchro_single' ) );
		add_action( 'wp_ajax_associate_and_synchronize', array( $this, 'associate_and_synchronize' ) );

		add_action( 'admin_post_synchronization', array( $this, 'sync' ) );
		add_action( 'wp_ajax_sync_third_parties', array( $this, 'sync_third_parties' ) );
		add_action( 'wp_ajax_sync_contacts', array( $this, 'sync_contacts' ) );
		add_action( 'wp_ajax_sync_products', array( $this, 'sync_products' ) );
		add_action( 'wp_ajax_sync_proposals', array( $this, 'sync_proposals' ) );
		add_action( 'wp_ajax_sync_orders', array( $this, 'sync_orders' ) );
		add_action( 'wp_ajax_sync_invoices', array( $this, 'sync_invoices' ) );


	}

	public function load_modal_synchro() {
		$sync_infos = Doli_Synchro::g()->sync_infos;

		if ( ! empty( $sync_infos ) ) {
			foreach ( $sync_infos as &$sync_info ) {
				$sync_info['total_number'] = 0;
				if ( ! empty( $sync_info['endpoint'] ) ) {
					$sync_info['total_number'] = count( Request_Util::get( $sync_info['endpoint'] ) );
				}
			}
		}

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'main', array(
			'sync_infos' => $sync_infos,
		) );
		$view = ob_get_clean();
		wp_send_json_success( array(
			'view' => $view,
	 	) );
	}

	public function load_modal_synchro_single() {
		$wp_id        = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$doli_sync_id = ! empty( $_POST['entry_id'] ) ? (int) $_POST['entry_id'] : get_post_meta( $wp_id, '_external_id', true );
		$view         = '';
		$buttons_view = '';

		if ( empty( $doli_sync_id ) ) {
			$third_parties = Request_Util::get( 'thirdparties?limit=-1' );

			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'single', array(
				'third_parties' => $third_parties,
				'wp_id'         => $wp_id,
			) );
			$view = ob_get_clean();

			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'single-footer' );
			$buttons_view = ob_get_clean();

			wp_send_json_success( array(
				'view'         => $view,
				'buttons_view' => $buttons_view,
		 	) );
		} else {
			$doli_third_party   = Request_Util::get( 'thirdparties/' . $doli_sync_id );
			$wp_third_party     = Third_Party_Class::g()->get( array( 'id' => $wp_id ), true );
			$modified_date_wp   = get_post_modified_time( 'U', false, $wp_id );
			$modified_date_doli = ! empty( $doli_third_party->date_modification ) ? $doli_third_party->date_modification : $doli_third_party->date_creation;

			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'need-to-confirm', array(
				'date_wp'          => $modified_date_wp,
				'date_doli'        => $modified_date_doli,
				'doli_third_party' => $doli_third_party,
				'wp_third_party'   => $wp_third_party,
			) );
			$view = ob_get_clean();

			wp_send_json_success( array(
				'namespace'        => 'wpshop',
				'module'           => 'doliSynchro',
				'callback_success' => 'loadedModalSynchroSingle',
				'view'             => $view,
			) );
		}
	}

	public function associate_and_synchronize() {
		$entry_id = ! empty( $_POST['entry_id'] ) ? (int) $_POST['entry_id'] : 0;
		$wp_id    = ! empty( $_POST['wp_id'] ) ? (int) $_POST['wp_id'] : 0;
		$from       = ! empty( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';

		if ( empty( $entry_id ) || empty( $wp_id ) || empty( $from ) ) {
			wp_send_json_error();
		}

		$post_type = get_post_type( $wp_id );

		switch ( $post_type ) {
			case 'wps-third-party':
				if ( $from == 'dolibarr' ) {
					$doli_third_party = Request_Util::get( 'thirdparties/' . $entry_id );
					$wp_third_party   = Third_Party_Class::g()->get( array( 'id' => $wp_id ), true );

					Doli_Third_Party_Class::g()->doli_to_wp( $doli_third_party, $wp_third_party );
				}

				if ( $from == 'wordpress' ) {
					$wp_third_party   = Third_Party_Class::g()->get( array( 'id' => $wp_id ), true );
					$doli_third_party = Request_Util::get( 'thirdparties/' . $entry_id );

					Doli_Third_Party_Class::g()->wp_to_doli( $wp_third_party, $doli_third_party );
				}
				break;
		}

		update_post_meta( $wp_id, '_last_sync', current_time( 'mysql' ) );

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'doliSynchro',
			'callback_success' => 'associatedAndSynchronized',
			'from'             => $from,
		) );
	}

	public function sync() {
		$id      = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$doli_id = get_post_meta( $id, 'external_id', true );

		$orders = Orders_Class::g()->synchro_by_ids( $doli_id );

		// wp_redirect( admin_url( 'admin.php?page=wps-order&id=' . $id ) );
		exit;
	}

	public function sync_third_parties() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_third_parties = Request_Util::get( 'thirdparties?sortfield=t.rowid&sortorder=ASC&limit=10&page=' . $done_number / 10 );

		if ( ! empty( $doli_third_parties ) ) {
			foreach ( $doli_third_parties as $doli_third_party ) {
				if ( empty( $doli_third_party->id ) ) {
					$wp_third_party = Third_Party_Class::g()->get( array(
						'meta_key'   => '_external_id',
						'meta_value' => (int) $doli_third_party->id,
					), true );
				} else {
					$wp_third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );
				}

				Doli_Third_Party_Class::g()->doli_to_wp( $doli_third_party, $wp_third_party );

				$done_number++;
			}
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 0,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_contacts() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Doli_Contact_Class::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 0,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_products() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Doli_Products_Class::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_proposals() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Doli_Proposals_Class::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_orders() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Orders_Class::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_invoices() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Doli_Invoice::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}
}

new Doli_Syncho_Action();
