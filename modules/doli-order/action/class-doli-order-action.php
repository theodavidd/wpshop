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
class Doli_Order_Action {

	/**
	 * Initialise les actions liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'callback_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'wps_checkout_create_order', array( $this, 'create_order' ), 10, 2 );
		add_action( 'wp_ajax_synchro_orders', array( $this, 'synchro_orders' ) );
	}

	public function callback_admin_init() {
		remove_post_type_support( 'wps-order', 'title' );
		remove_post_type_support( 'wps-order', 'editor' );
		remove_post_type_support( 'wps-order', 'excerpt' );

		register_post_status( 'wps-delivered', array(
			'label'                     => _x( 'Delivered', 'Order status', 'wpshop' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', 'wpshop' ),
		) );

		register_post_status( 'wps-canceled', array(
			'label'                     => _x( 'Canceled', 'Order status', 'wpshop' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'Canceled <span class="count">(%s)</span>', 'Canceled <span class="count">(%s)</span>', 'wpshop' ),
		) );
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page( 'wps-order', __( 'Orders', 'wpshop' ), __( 'Orders', 'wpshop' ), 'manage_options', 'wps-order', array( $this, 'callback_add_menu_page' ) );
	}

	public function callback_add_menu_page() {
		if ( isset( $_GET['id'] ) ) {
			$order  = Orders_Class::g()->get( array( 'id' => $_GET['id'] ), true );
			$args_metabox = array(
				'order' => $order,
				'id'    => $_GET['id'],
			);

			add_meta_box( 'wps-order-customer', __( 'Order details #' . $order->data['title'], 'wpshop' ), array( $this, 'callback_meta_box' ), 'wps-order', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-order-products',  __( 'Products', 'wpshop' ), array( $this, 'callback_products' ), 'wps-order', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-order-submit', __( 'Order actions', 'wpshop'), array( $this, 'callback_order_action' ), 'wps-order', 'normal', 'default', $args_metabox );

			\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'single', array(
				'order' => $order
			) );
		} else {
			\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'main' );
		}
	}

	public function callback_meta_box( $post, $callback_args ) {
		$order        = $callback_args['args']['order'];
		$invoice      = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
		$third_party  = Third_Party_Class::g()->get( array( 'id' => $order->data['parent_id'] ), true );
		$link_invoice = '';

		if ( ! empty( $invoice ) ) {
			$invoice->data['payments'] = array();
			$invoice->data['payments'] = Doli_Payment::g()->get( array( 'post_parent' => $invoice->data['id'] ) );
			$link_invoice = admin_url( 'admin-post.php?action=wps_download_invoice&order_id=' . $order->data['id'] );
		}


		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'metabox-order-details', array(
			'order'        => $order,
			'third_party'  => $third_party,
			'invoice'      => $invoice,
			'link_invoice' => $link_invoice,
		) );
	}

	public function callback_products( $post, $callback_args ) {
		$order = $callback_args['args']['order'];

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'metabox-orders', array(
			'order' => $order,
		) );
	}

	public function callback_order_action( $post, $callback_args ) {
		$order = $callback_args['args']['order'];

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'metabox-action', array(
			'order' => $order,
		) );
	}

	public function create_order( $proposal, $data ) {
		$order = Request_Util::post( 'orders/createfromproposal/' . $proposal->data['external_id'] );
		$order = Request_Util::post( 'orders/' . $order->id . '/validate' );

		Emails_Class::g()->send_mail( null, 'wps_email_new_order', array(
			'order'       => $order,
			'third_party' => $data['third_party']->data,
		) );

		Emails_Class::g()->send_mail( $data['third_party']->data['email'], 'wps_email_customer_processing_order', array(
			'order'       => $order,
			'third_party' => $data['third_party']->data
		) );

		return Orders_Class::g()->sync( $order, $proposal->data['parent_id'] );
	}

	public function synchro_orders() {
		$doli_orders = Request_Util::get( 'orders' );

		if ( ! empty( $doli_orders ) ) {
			foreach ( $doli_orders as $doli_order ) {
				// Vérifie l'existence du produit en base de donnée.
				$order = Orders_Class::g()->get( array(
					'meta_key'   => 'external_id',
					'meta_value' => $doli_order->id,
				), true );

				if ( empty( $order ) ) {
					$order = Orders_Class::g()->get( array( 'schema' => true ), true );
				}

				$order->data['external_id']   = (int) $doli_order->id;
				$order->data['title']         = $doli_order->ref;
				$order->data['total_ht']      = $doli_order->total_ht;
				$order->data['total_ttc']     = $doli_order->total_ttc;
				$order->data['lines']         = $doli_order->lines;
				$order->data['date_commande'] = date( 'Y-m-d h:i:s', $doli_order->date_commande );
				$order->data['status']        = 'publish';
				$order->data['parent_id']     = Third_Party_Class::g()->get_id_or_sync( $doli_order->socid );

				Orders_Class::g()->update( $order->data );

				do_action( 'wps_synchro_order', $order->data, $doli_order );

			}
		}

		wp_send_json_success( $doli_orders );
	}
}

new Doli_Order_Action();
