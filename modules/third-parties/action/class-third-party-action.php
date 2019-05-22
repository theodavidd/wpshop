<?php
/**
 * Gestion des actions des tiers.
 *
 * Ajoutes une page "Tiers" dans le menu de WordPress.
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
 * Third Party Action Class.
 */
class Third_Party_Action {

	/**
	 * Définition des metabox sur la page.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $metaboxes = null;

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ), 11 );

		add_action( 'wp_ajax_third_party_load_title_edit', array( $this, 'load_title_edit' ) );
		add_action( 'admin_post_third_party_save_title', array( $this, 'save_third' ) );
		add_action( 'wp_ajax_third_party_save_title', array( $this, 'save_third' ) );

		add_action( 'wp_ajax_third_party_load_address', array( $this, 'load_billing_address' ) );
		add_action( 'wp_ajax_third_party_save_address', array( $this, 'save_billing_address' ) );

		$this->metaboxes = array(
			'wps-third-party-billing'  => array(
				// 'title'    => __( 'Billing address', 'wpshop' ),
				'callback' => array( $this, 'metabox_billing_address' ),
			),
			'wps-third-party-contacts' => array(
				// 'title'    => __( 'Contacts', 'wpshop' ),
				'callback' => array( $this, 'metabox_contacts' ),
			),
			'wps-third-party-propal'   => array(
				// 'title'    => __( 'Proposals', 'wpshop' ),
				'callback' => array( $this, 'metabox_proposals' ),
			),
			'wps-third-party-orders'   => array(
				// 'title'    => __( 'Orders', 'wpshop' ),
				'callback' => array( $this, 'metabox_orders' ),
			),
			'wps-third-party-invoices' => array(
				// 'title'    => __( 'Invoices', 'wpshop' ),
				'callback' => array( $this, 'metabox_invoices' ),
			),
		);
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page(
			'wpshop',
			__( 'Third Parties', 'wpshop' ),
			__( 'Third Parties', 'wpshop' ),
			'manage_options',
			'wps-third-party',
			array( $this, 'callback_add_menu_page' )
		);
	}

	/**
	 * Appel la vue "main" du module "Third Party".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		if ( isset( $_GET['id'] ) ) {
			$third_party  = Third_Party::g()->get( array( 'id' => $_GET['id'] ), true );
			$args_metabox = array(
				'third_party' => $third_party,
				'id'          => $_GET['id'],
			);

			if ( ! empty( $this->metaboxes ) ) {
				foreach ( $this->metaboxes as $key => $metabox ) {
					add_action( 'wps_third_party', $metabox['callback'], 10, 1 );
				}
			}

			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single', array( 'third_party' => $third_party ) );
		} else {
			$s     = ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			$count = Third_Party::g()->search( $s, array(), true );

			$number_page  = ceil( $count / 25 );
			$current_page = isset( $_GET['current_page'] ) ? $_GET['current_page'] : 1;

			$base_url = admin_url( 'admin.php?page=wps-third-party' );

			$begin_url = $base_url . '&current_page=1';
			$end_url   = $base_url . '&current_page=' . $number_page;

			$prev_url = $base_url . '&current_page=' . ( $current_page - 1 );
			$next_url = $base_url . '&current_page=' . ( $current_page + 1 );

			if ( ! empty( $_GET['s'] ) ) {
				$begin_url .= '&s=' . $_GET['s'];
				$end_url   .= '&s=' . $_GET['s'];
				$prev_url  .= '&s=' . $_GET['s'];
				$next_url  .= '&s=' . $_GET['s'];
			}

			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'main', array(
				'number_page'  => $number_page,
				'current_page' => $current_page,
				'count'        => $count,
				'begin_url'    => $begin_url,
				'end_url'      => $end_url,
				'prev_url'     => $prev_url,
				'next_url'     => $next_url,
			) );
		}
	}

	/**
	 * Appel la vue de la metabox des adresses.
	 *
	 * @param Third_Party $third_party Les données du tiers.
	 *
	 * @since 2.0.0
	 */
	public function metabox_billing_address( $third_party ) {
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address', array(
			'third_party' => $third_party,
		) );
	}

	/**
	 * Appel la vue de la metabox des contacts.
	 *
	 * @param Third_Party $third_party Les données du tiers.
	 *
	 * @since 2.0.0
	 */
	public function metabox_contacts( $third_party ) {
		$contacts = array();

		if ( ! empty( $third_party->data['contact_ids'] ) ) {
			$contacts = Contact::g()->get( array( 'include' => $third_party->data['contact_ids'] ) );
		}
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts', array(
			'third_party' => $third_party,
			'contacts'    => $contacts,
		) );
	}

	/**
	 * Appel la vue de la metabox des devis.
	 *
	 * @param Third_Party $third_party Les données du tiers.
	 *
	 * @since 2.0.0
	 */
	public function metabox_proposals( $third_party ) {
		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		$proposals = Proposals::g()->get( array( 'post_parent' => $third_party->data['id'] ) );

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-proposals', array(
			'doli_url'  => $dolibarr_option['dolibarr_url'],
			'proposals' => $proposals,
		) );
	}

	/**
	 * Appel la vue de la metabox des commandes.
	 *
	 * @param Third_Party $third_party Les données du tiers.
	 *
	 * @since 2.0.0
	 */
	public function metabox_orders( $third_party ) {
		$orders = array();

		if ( Settings::g()->dolibarr_is_active() ) {
			$orders = Doli_Order::g()->get( array( 'post_parent' => $third_party->data['id'] ) );

			if ( ! empty( $orders ) ) {
				foreach ( $orders as &$order ) {
					$order->data['invoice'] = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
				}
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-orders', array(
			'orders' => $orders,
		) );
	}

	/**
	 * Appel la vue de la metabox des factures.
	 *
	 * @param Third_Party $third_party Les données du tiers.
	 *
	 * @since 2.0.0
	 */
	public function metabox_invoices( $third_party ) {
		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		$invoices = array();

		if ( Settings::g()->dolibarr_is_active() ) {
			$invoices = Doli_Invoice::g()->get( array(
				'meta_key'   => '_third_party_id',
				'meta_value' => $third_party->data['id'],
			) );

			if ( ! empty( $invoices ) ) {
				foreach ( $invoices as &$invoice ) {
					$invoice->data['order'] = Doli_Order::g()->get( array( 'id' => $invoice->data['parent_id'] ), true );
				}
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-invoices', array(
			'doli_url' => $dolibarr_option['dolibarr_url'],
			'invoices' => $invoices,
		) );
	}

	/**
	 * Renvoies la vue d'édition d'un titre.
	 *
	 * @since 2.0.0
	 */
	public function load_title_edit() {
		check_ajax_referer( 'load_title_edit' );

		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : -1;

		if ( -1 === $post_id ) {
			exit;
		}

		$third_party = Third_Party::g()->get( array( 'id' => $post_id ), true );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single-title-edit', array(
			'third_party' => $third_party,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'loaddedTitleEdit',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Enregistres le titre du tier.
	 *
	 * @since 2.0.0
	 */
	public function save_third() {
		check_ajax_referer( 'save_third' );

		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : -1;
		$title   = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

		if ( -1 === $post_id ) {
			exit;
		}

		$third_party = Third_Party::g()->get( array( 'id' => $post_id ), true );

		$third_party->data['id'] = $post_id;

		if ( empty( $post_id ) ) {
			$third_party->data['status'] = 'publish';
		}
		$third_party->data['title'] = $title;

		$third_party = Third_Party::g()->update( $third_party->data );

		$external_id = do_action( 'wps_saved_third_party', $third_party->data );

		$third_party->data['external_id'] = $external_id;
		$third_party                      = Third_Party::g()->update( $third_party->data );

		if ( wp_doing_ajax() ) {
			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single-title', array(
				'third_party' => $third_party,
			) );
			wp_send_json_success( array(
				'namespace'        => 'wpshop',
				'module'           => 'thirdParties',
				'callback_success' => 'savedThird',
				'view'             => ob_get_clean(),
			) );
		} else {
			wp_redirect( admin_url( 'admin.php?page=wps-third-party&id=' . $third_party->data['id'] ) );
			exit;
		}
	}

	/**
	 * Charges la vue pour éditer l'adresse du tier.
	 *
	 * @since 2.0.0
	 */
	public function load_billing_address() {
		check_ajax_referer( 'load_billing_address' );

		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;

		if ( empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address-edit', array(
			'third_party' => $third_party,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'loaddedBillingAddressSuccess',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Met à jour l'adresse du tier
	 *
	 * @todo: Merger avec save third
	 * @since 2.0.0
	 */
	public function save_billing_address() {
		check_ajax_referer( 'save_billing_address' );

		$third_party_id   = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$third_party_form = ! empty( $_POST['third_party'] ) ? (array) $_POST['third_party'] : array();

		if ( empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );

		$third_party->data['title']   = $third_party_form['title'];
		$third_party->data['address'] = $third_party_form['address'];
		$third_party->data['zip']     = $third_party_form['zip'];
		$third_party->data['town']    = $third_party_form['town'];
		$third_party->data['phone']   = $third_party_form['phone'];

		$third_party = Third_Party::g()->update( $third_party->data );

		do_action( 'wps_saved_billing_address', $third_party );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address', array(
			'third_party' => $third_party,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'savedBillingAddressSuccess',
			'view'             => ob_get_clean(),
		) );
	}
}

new Third_Party_Action();
