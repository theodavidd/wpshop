<?php
/**
 * Gestion des actions de synchronisations des entités avec dolibarr.
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
 * Doli Synchro Action Class.
 */
class Doli_Sync_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_load_modal_synchro', array( $this, 'load_modal_synchro' ) );

		add_action( 'wp_ajax_sync', array( $this, 'sync' ) );
		add_action( 'wps_sync_payments_before', array( $this, 'sync_payments' ), 10, 2 );

		add_action( 'wp_ajax_sync_entry', array( $this, 'sync_entry' ) );

		add_action( 'wps_listing_table_header_end', array( $this, 'add_sync_header' ) );
		add_action( 'wps_listing_table_end', array( $this, 'add_sync_item' ), 10, 2 );

		add_action( 'wp_ajax_check_sync_status', array( $this, 'check_sync_status' ) );
	}

	/**
	 * Charges la modal de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function load_modal_synchro() {
		check_ajax_referer( 'load_modal_synchro' );

		$sync_action = ! empty( $_POST['sync'] ) ? sanitize_text_field( $_POST['sync'] ) : '';
		$sync_infos  = Doli_Sync::g()->sync_infos;

		if ( empty( $sync_action ) ) {
			$sync_action = array( 'third-parties', 'contacts', 'products', 'proposals', 'orders', 'invoices', 'payments' );
		} else {
			$sync_action = explode( ',', $sync_action );
		}

		if ( ! empty( $sync_infos ) ) {
			foreach ( $sync_infos as $key => &$sync_info ) {
				if ( ! in_array( $key, $sync_action, true ) ) {
					unset ( $sync_infos[ $key ] );
					continue;
				}

				$sync_info['last']         = false;
				$sync_info['total_number'] = 0;
				$sync_info['page']         = 0;
				$sync_info                 = Doli_Sync::g()->count_entries( $sync_info );

				if ( end( $sync_action ) == $key ) {
					$sync_info['last'] = true;
				}
			}
		}

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'doli-sync', 'main', array(
			'sync_infos' => $sync_infos,
		) );
		$view = ob_get_clean();

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'doli-sync', 'modal-sync-button' );
		$buttons_view = ob_get_clean();
		wp_send_json_success( array(
			'view'         => $view,
			'buttons_view' => $buttons_view,
		) );
	}

	/**
	 * Synchornise.
	 *
	 * @todo: Use Doli_Sync::g()->get_sync_infos
	 *
	 * @since 2.0.0
	 */
	public function sync() {
		check_ajax_referer( 'sync' );

		$done           = false;
		$updateComplete = false;
		$done_number    = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number   = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;
		$type           = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$last           = ( ! empty( $_POST['last'] ) && '1' == $_POST['last'] ) ? true : false;

		$route      = '';
		$wp_class   = '\wpshop\\';
		$doli_class = '\wpshop\\';
		$doli_type  = '';

		switch ( $type ) {
			case 'third-parties':
				$route       = 'thirdparties';
				$wp_class   .= 'Third_Party';
				$doli_class .= 'Doli_Third_Parties';
				$doli_type   = 'third_party';
				break;
			case 'contacts':
				$route       = 'contacts';
				$wp_class   .= 'Contact';
				$doli_class .= 'Doli_Contact';
				$doli_type   = 'contact';
				break;
			case 'products':
				$route       = 'wpshop/productsonweb';
				$wp_class   .= 'Product';
				$doli_class .= 'Doli_Products';
				$doli_type   = 'product';
				break;
			case 'proposals':
				$route       = 'proposals';
				$wp_class   .= 'Proposals';
				$doli_class .= 'Doli_Proposals';
				$doli_type   = 'propal';
				break;
			default:
				break;
		}

		$doli_entries = Request_Util::get( $route . '?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Sync::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Sync::g()->limit_entries_by_request );

		if ( ! empty( $doli_entries ) ) {
			foreach ( $doli_entries as $doli_entry ) {

				// translators: Try to sync %s.
				\eoxia\LOG_Util::log( sprintf( 'Try to sync %s', json_encode( $doli_entry ) ), 'wpshop2' );
				$wp_entry = $wp_class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_entry->id,
				), true );

				if ( empty( $wp_entry ) ) {
					$wp_entry = $wp_class::g()->get( array( 'schema' => true ), true );
				}

				if ( ! is_array( $wp_entry ) ) {
					$wp_entry = array( $wp_entry );
				}

				if ( ! empty( $wp_entry ) ) {
					foreach ( $wp_entry as $entry ) {
						do_action( 'wps_sync_' . $type . '_before', $doli_entry, $entry );
					}
				}

				// @todo: >> passsage en do_action.
				$wp_entry = $wp_class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_entry->id,
				), true );

				if ( empty( $wp_entry ) ) {
					$wp_entry = $wp_class::g()->get( array( 'schema' => true ), true );
				}

				if ( ! is_array( $wp_entry ) ) {
					$wp_entry = array( $wp_entry );
				}

				if ( ! empty( $wp_entry ) ) {
					foreach ( $wp_entry as $entry ) {
						$doli_class::g()->doli_to_wp( $doli_entry, $entry );

						$doli_object = Request_Util::post( 'wpshop/object', array(
							'wp_id'   => $entry->data['id'],
							'doli_id' => $doli_entry->id,
							'type'    => $doli_type,
						) );

//							update_post_meta( $entry->data['id'], '_date_last_synchro', $doli_object->last_sync_date );
					}
				}

				// translators: Sync done for the entry {json_data}.
				\eoxia\LOG_Util::log( sprintf( 'Sync done for the entry %s', json_encode( $doli_entry ) ), 'wpshop2' );

				$done_number++;
				do_action( 'wps_sync_' . $type . '_after', $doli_entry );
			}
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done        = true;

			if ( $last ) {
				$updateComplete = true;
			}
		}

		wp_send_json_success( array(
			'updateComplete'     => $updateComplete,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 0,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	/**
	 * Synchronise une entrée.
	 *
	 * @since 2.0.0
	 */
	public function sync_entry() {
		check_ajax_referer( 'sync_entry' );

		$wp_id    = ! empty( $_POST['wp_id'] ) ? (int) $_POST['wp_id'] : 0;
		$entry_id = ! empty( $_POST['entry_id'] ) ? (int) $_POST['entry_id'] : 0;
		$route    = ! empty( $_POST['route'] ) ? sanitize_text_field( $_POST['route'] ) : '';
		$type     = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$type     = str_replace( '_Class', '', str_replace( '/', '\\', '/' . $type ) );
		$wp_type  = ! empty( $_POST['wp_type'] ) ? str_replace( '\\\\', '\\', sanitize_text_field( $_POST['wp_type'] ) ) : '';
		$modal    = ( ! empty( $_POST['modal'] ) && '1' == $_POST['modal'] ) ? true : false;

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		if ( $type == '\wpshop\Doli_Products' ) {
			$doli_entry = Request_Util::get( 'wpshop/products?id=' . $entry_id );
		} else {
			$doli_entry = Request_Util::get( $route . '/' . $entry_id );
		}

		$wp_entry = $wp_type::g()->get( array( 'id' => $wp_id ), true );

		$to_type = '';

		$notices = array(
			'errors'   => array(),
			'messages' => array(),
		);

		switch ( $route ) {
			case 'thirdparties':
				$url = admin_url( 'admin.php?page=wps-third-party' );

				$notices['messages'] = Third_Party::g()->dessociate_contact( $wp_entry );
				$wp_entry            = Doli_Third_Parties::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );

				$to_type = 'third_party';
				break;
			case 'products':
				$url      = admin_url( 'admin.php?page=wps-product' );
				$wp_entry = Doli_Products::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );
				$to_type  = 'product';

				break;
			case 'orders':
				$url = admin_url( 'admin.php?page=wps-order' );

				$wp_entry = Doli_Order::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );

				$wp_entry->data['external_id'] = $entry_id;
				$wp_entry                      = Doli_Order::g()->wp_to_doli( $wp_entry, $doli_entry, true, $notices );

				$to_type = 'order';
				break;
			case 'proposals':
				$url = admin_url( 'admin.php?page=wps-proposal' );

				$wp_entry               = Doli_Proposals::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );
				$wp_entry->data['tier'] = null;

				if ( ! empty( $wp_entry->data['parent_id'] ) ) {
					$wp_entry->data['tier'] = Third_Party::g()->get( array( 'id' => $wp_entry->data['parent_id'] ), true );
				}

				$to_type = 'propal';
				break;
			case 'invoices':
				$url = admin_url( 'admin.php?page=wps-invoice' );

				$wp_entry = Doli_Invoice::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );

				$to_type = 'invoice';
				break;
			default:
				break;
		}

		$data = array(
			'doli_id' => $entry_id,
			'wp_id'   => $wp_id,
			'type'    => $to_type,
		);

		Request_Util::post( 'wpshop/object', $data );

		ob_start();
		if ( $modal ) {
			\eoxia\View_Util::exec( 'wpshop', 'doli-associate', 'modal-associate-result', array(
				'notice' => $notices,
				'url'    => $url,
			) );
		} else {
			$wp_type::g()->display_item( $wp_entry, $dolibarr_option['dolibarr_url'] );
		}

		wp_send_json_success( array(
			'id'               => $wp_id,
			'sync_status'      => Doli_Sync::g()->check_status( $wp_id ),
			'namespace'        => 'wpshop',
			'module'           => 'doliSync',
			'callback_success' => 'syncEntrySuccess',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Appel la vue pour ajouter "Synchro" dans le header du listing.
	 *
	 * @since 2.0.0
	 */
	public function add_sync_header( $type ) {
		if ( in_array( $type, array( 'products', 'thirdparties', 'proposals' ) ) && Settings::g()->dolibarr_is_active() ) {
			\eoxia\View_Util::exec( 'wpshop', 'doli-sync', 'sync-header' );
		}
	}

	/**
	 * Prépares les données pour l'état de synchronisation de l'entité.
	 * et appel la vue sync-item.
	 *
	 * @param   mixed   $object  Peut être Order, Product ou Tier.
	 * @param   string  $route   La route pour l'api dolibarr.
	 * @param           $doli_class
	 * @param           $wp_class
	 * @param   string  $mode    Peut être view ou edit.
	 *
	 * @since 2.0.0
	 *
	 */
	public function add_sync_item( $object, $type ) {
		if ( Settings::g()->dolibarr_is_active() && in_array( $type, array( 'products', 'thirdparties', 'proposals' ) ) ) {
			Doli_Sync::g()->display_sync_status( $object, $type, false );
		}
	}

	/**
	 * @todo: nonce
	 * @return [type] [description]
	 */
	public function check_sync_status() {
		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$type = ! empty( $_POST['type'] ) ? sanitize_text_field ( $_POST['type'] ) : '';

		if ( empty( $id ) && ! in_array( $type, array( 'products', 'thirdparties', 'proposals' ) ) ) {
			wp_send_json_error();
		}

		$sync_info = Doli_Sync::g()->get_sync_infos( $type );

		$object = $sync_info['wp_class']::g()->get( array( 'id' => $id ), true );
		Doli_Sync::g()->display_sync_status( $object, $type );
		wp_send_json_success( array(
			'view' => ob_get_clean(),
			'id'   => $id,
		) );
	}
}

new Doli_Sync_Action();
