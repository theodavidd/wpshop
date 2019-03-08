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

		add_action( 'wps_checkout_create_order', array( $this, 'create_order' ), 10, 1 );
		add_action( 'wps_payment_complete', array( $this, 'set_to_billed' ), 30, 1 );
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
			// add_meta_box( 'wps-order-submit', __( 'Order actions', 'wpshop'), array( $this, 'callback_order_action' ), 'wps-order', 'normal', 'default', $args_metabox );

			\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'single', array(
				'order'       => $order,
				'third_party' => $third_party,
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

		$tva_lines = array();

		if ( ! empty( $order->data['lines'] ) ) {
			foreach ( $order->data['lines'] as $line ) {
				if ( empty( $tva_lines[ $line['tva_tx'] ] ) ) {
					$tva_lines[ $line['tva_tx'] ] = 0;
				}

				$tva_lines[ $line['tva_tx'] ] += $line['total_tva'];
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'metabox-orders', array(
			'order'     => $order,
			'tva_lines' => $tva_lines,
		) );
	}

	public function callback_order_action( $post, $callback_args ) {
		$order = $callback_args['args']['order'];

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'metabox-action', array(
			'order' => $order,
		) );
	}

	public function create_order( $wp_proposal ) {
		$third_party      = Third_Party_Class::g()->get( array( 'id' => $wp_proposal->data['parent_id'] ), true );
		$doli_proposal_id = get_post_meta( $wp_proposal->data['id'], '_external_id', true );

		$doli_order = Request_Util::post( 'orders/createfromproposal/' . $doli_proposal_id );
		$doli_order = Request_Util::post( 'orders/' . $doli_order->id . '/validate' );

		Emails_Class::g()->send_mail( null, 'wps_email_new_order', array(
			'order'       => $doli_order,
			'third_party' => $third_party->data,
		) );

		$current_user = wp_get_current_user();

		Emails_Class::g()->send_mail( $current_user->user_email, 'wps_email_customer_processing_order', array(
			'order'       => $doli_order,
			'third_party' => $third_party->data
		) );

		$wp_order = Orders_Class::g()->get( array( 'schema' => true ), true );

		return Orders_Class::g()->doli_to_wp( $doli_order, $wp_order );
	}

	public function set_to_billed( $data ) {
		$wp_order   = Orders_Class::g()->get( array( 'id' => (int) $data['custom'] ), true );

		$doli_order = Request_Util::post( 'orders/' . $wp_order->data['external_id'] . '/setinvoiced' );

		Orders_Class::g()->doli_to_wp( $doli_order, $wp_order );
	}
}

new Doli_Order_Action();
