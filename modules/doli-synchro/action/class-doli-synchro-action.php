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
 * Doli Synchro Action Class.
 */
class Doli_Synchro_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_load_modal_synchro', array( $this, 'load_modal_synchro' ) );
		add_action( 'wp_ajax_load_modal_synchro_single', array( $this, 'load_modal_synchro_single' ) );
		add_action( 'wp_ajax_associate_and_synchronize', array( $this, 'associate_and_synchronize' ) );

		add_action( 'wp_ajax_sync', array( $this, 'sync' ) );
		add_action( 'wps_sync_payments_before', array( $this, 'sync_payments' ), 10, 2 );

		add_action( 'wps_listing_table_header_end', array( $this, 'add_sync_header' ) );
		add_action( 'wps_listing_table_end', array( $this, 'add_sync_item' ), 10, 2 );
	}

	/**
	 * Charges la modal de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function load_modal_synchro() {
		check_ajax_referer( 'load_modal_synchro' );

		$sync_action = ! empty( $_POST['sync'] ) ? sanitize_text_field( $_POST['sync'] ) : '';
		$sync_infos  = Doli_Synchro::g()->sync_infos;

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

				$sync_info['total_number'] = 0;
				$sync_info['page']         = 0;
				$sync_info                 = Doli_Synchro::g()->count_entries( $sync_info );
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

	/**
	 * Charges la modal de synchronisation pour un seul élément.
	 *
	 * @since 2.0.0
	 */
	public function load_modal_synchro_single() {
		check_ajax_referer( 'load_modal_synchro_single' );

		$wp_id        = ! empty( $_POST['wp_id'] ) ? (int) $_POST['wp_id'] : 0;
		$doli_sync_id = ! empty( $_POST['entry_id'] ) ? (int) $_POST['entry_id'] : get_post_meta( $wp_id, '_external_id', true );
		$route        = ! empty( $_POST['route'] ) ? sanitize_text_field( $_POST['route'] ) : '';
		$view         = '';
		$buttons_view = '';
		$text         = '';

		if ( empty( $doli_sync_id ) ) {
			switch ( $route ) {
				case 'third-parties':
					$text = __( 'third party', 'wpshop' );
					break;
				case 'contacts':
					$text = __( 'contact', 'wpshop' );
					break;
				case 'products':
				 	$text = __( 'product', 'wpshop' );
					break;
				case 'proposals':
					$text = __( 'proposal', 'wpshop' );
					break;
				case 'orders':
					$text = __( 'order', 'wpshop' );
					break;
				case 'invoices':
					$text = __( 'invoice', 'wpshop' );
					break;
				default:
					break;
			}

			$entries = Request_Util::get( $route . '?limit=-1' );

			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'single', array(
				'entries' => $entries,
				'wp_id'   => $wp_id,
				'route'   => $route,
				'text'    => $text,
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
			$doli_entity = Request_Util::get( $route . '/' . $doli_sync_id );
			switch ( $route ) {
				case 'products':
					$wp_entity = Product::g()->get( array( 'id' => $wp_id ), true );
					$url       = admin_url( 'admin.php?page=wps-product' );
					break;
				default:
					break;
			}

			$modified_date_wp   = strtotime( $wp_entity->data['date']['raw'] );
			$modified_date_doli = ! empty( $doli_entity->date_modification ) ? $doli_entity->date_modification : strtotime( $doli_entity->date_creation );

			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'need-to-confirm-product', array(
				'date_wp'     => $modified_date_wp,
				'date_doli'   => $modified_date_doli,
				'doli_entity' => $doli_entity,
				'wp_entity'   => $wp_entity,
				'url'         => $url,
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

	/**
	 * Associe et synchronise l'élément.
	 *
	 * @todo: dolibarr wp en dur
	 *
	 * @since 2.0.0
	 */
	public function associate_and_synchronize() {
		check_ajax_referer( 'associate_and_synchronize' );

		$entry_id = ! empty( $_POST['entry_id'] ) ? (int) $_POST['entry_id'] : 0;
		$wp_id    = ! empty( $_POST['wp_id'] ) ? (int) $_POST['wp_id'] : 0;
		$from     = ! empty( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';

		if ( empty( $entry_id ) || empty( $wp_id ) || empty( $from ) ) {
			wp_send_json_error();
		}

		Doli_Synchro::g()->associate_and_synchronize( $from, $wp_id, $entry_id );

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'doliSynchro',
			'callback_success' => 'associatedAndSynchronized',
			'from'             => $from,
		) );
	}

	/**
	 * Synchornise les tiers.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
	 */
	public function sync() {
		check_ajax_referer( 'sync' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;
		$type         = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

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

		$doli_entries = Request_Util::get( $route . '?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_entries ) ) {
			foreach ( $doli_entries as $doli_entry ) {
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

				$done_number++;
				do_action( 'wps_sync_' . $type . '_after', $doli_entry );
			}
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done        = true;
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

	/**
	 * Synchornise les paiements.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
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
	 * Appel la vue pour ajouter "Synchro" dans le header du listing.
	 *
	 * @since 2.0.0
	 */
	public function add_sync_header() {
		if ( Settings::g()->dolibarr_is_active() ) {
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'sync-header' );
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
	 */
	public function add_sync_item( $object, $route ) {
		if ( Settings::g()->dolibarr_is_active() ) {
			$class           = '';
			$message_tooltip = '';

			if ( empty( $object->data['external_id'] ) ) {
				$class           = 'red';
				$message_tooltip = __( 'No associated to an ERP Entity', 'wpshop' );
			} else {
				$class = 'green';
				// translators: Last synchronisation on 03/04/2019 12:00.
				$message_tooltip = sprintf( __( 'Last synchronisation on %s', 'wpshop'), $object->data['date_last_synchro']['rendered']['date_time'] );
			}

			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'sync-item', array(
				'object'          => $object,
				'class'           => $class,
				'route'           => $route,
				'message_tooltip' => $message_tooltip,
			) );
		}
	}
}

new Doli_Synchro_Action();
