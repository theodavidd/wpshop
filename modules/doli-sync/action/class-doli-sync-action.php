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
		add_action( 'wps_listing_table_end', array( $this, 'add_sync_item' ), 10, 5 );
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

		switch ( $type ) {
			case 'third-parties':
				$route       = 'thirdparties';
				$wp_class   .= 'Third_Party';
				$doli_class .= 'Doli_Third_Parties';
				break;
			case 'contacts':
				$route       = 'contacts';
				$wp_class   .= 'Contact';
				$doli_class .= 'Doli_Contact';
				break;
			case 'products':
				$route       = 'wpshopapi/product/get/web';
				$wp_class   .= 'Product';
				$doli_class .= 'Doli_Products';
				break;
			case 'proposals':
				$route       = 'proposals';
				$wp_class   .= 'Proposals';
				$doli_class .= 'Doli_Proposals';
				break;
			case 'orders':
				$route       = 'orders';
				$wp_class   .= 'Doli_Order';
				$doli_class .= 'Doli_Order';
				break;
			case 'invoices':
				$route       = 'invoices';
				$wp_class   .= 'Doli_Invoice';
				$doli_class .= 'Doli_Invoice';
				break;
			case 'payments':
				$route       = 'invoices';
				$wp_class   .= 'Doli_Invoice';
				$doli_class .= 'Doli_Invoice';
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

				do_action( 'wps_sync_' . $type . '_before', $doli_entry, $wp_entry );

				if ( 'payments' !== $type ) {
					$wp_entry = $wp_class::g()->get( array(
						'meta_key'   => '_external_id',
						'meta_value' => (int) $doli_entry->id,
					), true );

					if ( empty( $wp_entry ) ) {
						$wp_entry = $wp_class::g()->get( array( 'schema' => true ), true );
					}

					$doli_class::g()->doli_to_wp( $doli_entry, $wp_entry );
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
	 * Synchronise les paiements.
	 *
	 * @since 2.0.0
	 *
	 * @param Doli_Invoice_Model $doli_invoice Les données de la facture venant de dolibarr.
	 * @param Doli_Invoice_Model $wp_invoice   Les données de la facture vanant de WP.
	 */
	public function sync_payments( $doli_invoice, $wp_invoice ) {
		$doli_payments = Request_Util::get( 'invoices/' . $wp_invoice->data['external_id'] . '/payments' );

		if ( ! empty( $doli_payments ) ) {
			foreach ( $doli_payments as $doli_payment ) {
				$wp_payment = Doli_Payment::g()->get( array( 'title' => $doli_payment->ref ), true );

				if ( empty( $wp_payment ) ) {
					$wp_payment = Doli_Payment::g()->get( array( 'schema' => true ), true );
				}

				Doli_Payment::g()->doli_to_wp( $wp_invoice->data['id'], $doli_payment, $wp_payment );
			}
		}
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
		$from     = ! empty( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$modal    = ( ! empty( $_POST['modal'] ) && '1' == $_POST['modal'] ) ? true : false;

		$wp_entry         = $wp_type::g()->get( array( 'id' => $wp_id ), true );
		$doli_entry       = Request_Util::get( $route . '/' . $entry_id );
		$doli_to_wp_entry = $wp_type::g()->get( array( 'schema' => true ), true );

		$wp_entry   = $wp_type::g()->get( array( 'id' => $wp_id ), true );
		$doli_entry = Request_Util::get( $route . '/' . $entry_id );

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		switch ( $route ) {
			case 'thirdparties':
				$url = admin_url( 'admin.php?page=wps-third-party' );

				if ( 'dolibarr' === $from ) {
					$notices = array(
						'errors'   => array(),
						'messages' => array(),
					);

					$notices['messages'] = Third_Party::g()->dessociate_contact( $wp_entry );
					$wp_entry            = Doli_Third_Parties::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );
				} elseif ( 'wordpress' === $from ) {
					$wp_entry->data['external_id'] = $entry_id;
					$wp_entry                      = Doli_Third_Parties::g()->wp_to_doli( $wp_entry, $doli_entry, true, $notices );
				}
				break;
			case 'products':
				$url = admin_url( 'admin.php?page=wps-product' );

				$notices = array(
					'errors'   => array(),
					'messages' => array(),
				);

				if ( 'dolibarr' === $from ) {
					$wp_entry = Doli_Products::g()->doli_to_wp( $doli_entry, $wp_entry, true, $notices );
				} else {
					$wp_entry->data['external_id'] = $entry_id;
					$wp_entry                      = Doli_Products::g()->wp_to_doli( $wp_entry, $doli_entry, true, $notices );
				}
				break;
			default:
				break;
		}

		$last_sync = current_time( 'mysql' );
		update_post_meta( $wp_entry->data['id'], '_last_sync', $last_sync );

		$wp_entry->data['date_last_synchro'] = array(
			'rendered' => \eoxia\Date_Util::g()->fill_date( $last_sync ),
			'raw'      => $wp_entry->data['date_last_synchro'],
		);

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
	public function add_sync_header() {
		if ( Settings::g()->dolibarr_is_active() ) {
			\eoxia\View_Util::exec( 'wpshop', 'doli-sync', 'sync-header' );
		}
	}

	/**
	 * Prépares les données pour l'état de synchronisation de l'entité.
	 * et appel la vue sync-item.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed  $object Peut être Order, Product ou Tier.
	 * @param string $route  La route pour l'api dolibarr.
	 * @param string $mode   Peut être view ou edit.
	 */
	public function add_sync_item( $object, $route, $doli_class, $wp_class, $mode = 'view' ) {
		if ( Settings::g()->dolibarr_is_active() ) {
			$class           = '';
			$message_tooltip = '';

			if ( 'view' === $mode ) {
				if ( empty( $object->data['external_id'] ) ) {
					$class           = 'red';
					$message_tooltip = __( 'No associated to an ERP Entity', 'wpshop' );
				} else {
					$class = 'green';
					// translators: Last synchronisation on 03/04/2019 12:00.
					$message_tooltip = sprintf( __( 'Last synchronisation on %s', 'wpshop'), $object->data['date_last_synchro']['rendered']['date_time'] );
				}
			} else {
				$class = 'grey';

				$message_tooltip = __( 'Not available in quick release', 'wpshop' );
			}

			\eoxia\View_Util::exec( 'wpshop', 'doli-sync', 'sync-item', array(
				'object'          => $object,
				'class'           => $class,
				'route'           => $route,
				'message_tooltip' => $message_tooltip,
				'mode'            => $mode,
				'doli_class'      => $doli_class,
				'wp_class'        => $wp_class,
			) );
		}
	}
}

new Doli_Sync_Action();
