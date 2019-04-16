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

		add_action( 'wp_ajax_sync_third_parties', array( $this, 'sync_third_parties' ) );
		add_action( 'wp_ajax_sync_contacts', array( $this, 'sync_contacts' ) );
		add_action( 'wp_ajax_sync_products', array( $this, 'sync_products' ) );
		add_action( 'wp_ajax_sync_proposals', array( $this, 'sync_proposals' ) );
		add_action( 'wp_ajax_sync_orders', array( $this, 'sync_orders' ) );
		add_action( 'wp_ajax_sync_invoices', array( $this, 'sync_invoices' ) );
		add_action( 'wp_ajax_sync_payments', array( $this, 'sync_payments' ) );

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
		$sync_infos = Doli_Synchro::g()->sync_infos;

		if ( ! empty( $sync_infos ) ) {
			foreach ( $sync_infos as &$sync_info ) {
				$sync_info['total_number'] = 0;
				if ( ! empty( $sync_info['endpoint'] ) ) {
					$tmp = Request_Util::get( $sync_info['endpoint'] );

					if ( $tmp ) {
						$sync_info['total_number'] = count( $tmp );
					}
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

		if ( empty( $doli_sync_id ) ) {
			$third_parties = Request_Util::get( $route . '?limit=-1' );

			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'single', array(
				'third_parties' => $third_parties,
				'wp_id'         => $wp_id,
				'route'         => $route,
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
				'wp_entity'   => $wp_entity
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
	public function sync_third_parties() {
		check_ajax_referer( 'sync_third_parties' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_third_parties = Request_Util::get( 'thirdparties?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_third_parties ) ) {
			foreach ( $doli_third_parties as $doli_third_party ) {
				$wp_third_party = Third_Party::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_third_party->id,
				), true );

				if ( empty( $wp_third_party ) ) {
					$wp_third_party = Third_Party::g()->get( array( 'schema' => true ), true );
				}

				Doli_Third_Parties::g()->doli_to_wp( $doli_third_party, $wp_third_party );

				$done_number++;
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
	 * Synchornise les contacts.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
	 */
	public function sync_contacts() {
		check_ajax_referer( 'sync_contacts' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_contacts = Request_Util::get( 'contacts?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_contacts ) ) {
			foreach ( $doli_contacts as $doli_contact ) {
				$wp_contact = Contact::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_contact->id,
				), true );

				if ( empty( $wp_contact ) ) {
					$wp_contact = Contact::g()->get( array( 'schema' => true ), true );
				}

				Doli_Contact::g()->doli_to_wp( $doli_contact, $wp_contact );

				$done_number++;
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
	 * Synchornise les produits.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
	 */
	public function sync_products() {
		check_ajax_referer( 'sync_products' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_products = Request_Util::get( 'products?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_products ) ) {
			foreach ( $doli_products as $doli_product ) {
				$wp_product = Product::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_product->id,
				), true );

				if ( empty( $wp_product ) ) {
					$wp_product = Product::g()->get( array( 'schema' => true ), true );
				}

				Doli_Products::g()->doli_to_wp( $doli_product, $wp_product );

				$done_number++;
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
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	/**
	 * Synchornise les devis.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
	 */
	public function sync_proposals() {
		check_ajax_referer( 'sync_proposals' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_proposals = Request_Util::get( 'proposals?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_proposals ) ) {
			foreach ( $doli_proposals as $doli_proposal ) {
				$wp_proposal = Proposals::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_proposal->id,
				), true );

				if ( empty( $wp_proposal ) ) {
					$wp_proposal = Proposals::g()->get( array( 'schema' => true ), true );
				}

				Doli_Proposals::g()->doli_to_wp( $doli_proposal, $wp_proposal );

				$done_number++;
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
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	/**
	 * Synchornise les commandes.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
	 */
	public function sync_orders() {
		check_ajax_referer( 'sync_orders' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_orders = Request_Util::get( 'orders?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_orders ) ) {
			foreach ( $doli_orders as $doli_order ) {
				$wp_order = Doli_Order::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_order->id,
				), true );

				if ( empty( $wp_order ) ) {
					$wp_order = Doli_Order::g()->get( array( 'schema' => true ), true );
				}

				Doli_Order::g()->doli_to_wp( $doli_order, $wp_order );

				$done_number++;
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
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	/**
	 * Synchornise les factures.
	 *
	 * @todo: Faire qu'une seul fonction
	 *
	 * @since 2.0.0
	 */
	public function sync_invoices() {
		check_ajax_referer( 'sync_invoices' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$doli_invoices = Request_Util::get( 'invoices?sortfield=t.rowid&sortorder=ASC&limit=' . Doli_Synchro::g()->limit_entries_by_request . '&page=' . $done_number / Doli_Synchro::g()->limit_entries_by_request );

		if ( ! empty( $doli_invoices ) ) {
			foreach ( $doli_invoices as $doli_invoice ) {
				$wp_invoice = Doli_Invoice::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_invoice->id,
				), true );

				if ( empty( $wp_invoice ) ) {
					$wp_invoice = Doli_Invoice::g()->get( array( 'schema' => true ), true );
				}

				Doli_Invoice::g()->doli_to_wp( $doli_invoice, $wp_invoice );

				$done_number++;
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
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
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
	public function sync_payments() {
		check_ajax_referer( 'sync_payments' );

		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$invoices = Doli_Invoice::g()->get();

		if ( ! empty( $invoices ) ) {
			foreach ( $invoices as $invoice ) {
				$doli_payments = Request_Util::get( 'invoices/' . $invoice->data['external_id'] . '/payments' );

				if ( ! empty( $doli_payments ) ) {
					foreach ( $doli_payments as $doli_payment ) {
						$wp_payment = Doli_Payment::g()->get( array( 'title' => $doli_payment->ref ), true );

						if ( empty( $wp_payment ) ) {
							$wp_payment = Doli_Payment::g()->get( array( 'schema' => true ), true );
						}

						Doli_Payment::g()->doli_to_wp( $invoice->data['id'], $doli_payment, $wp_payment );
					}
				}

				$done_number++;
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
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	/**
	 * Appel la vue pour ajouter "Synchro" dans le header du listing.
	 *
	 * @since 2.0.0
	 */
	public function add_sync_header() {
		\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'sync-header' );
	}

	/**
	 * Prépares les données pour l'état de synchronisation de l'entité.
	 * et appel la vue sync-item.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $object Peut être Order, Product ou Tier.
	 */
	public function add_sync_item( $object, $route ) {
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

new Doli_Synchro_Action();
