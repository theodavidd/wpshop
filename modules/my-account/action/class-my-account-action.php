<?php
/**
 * Gestion des actions des commandes.
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
 * My Account Action Class.
 */
class My_Account_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( My_Account_Shortcode::g(), 'init_shortcode' ) );
		add_action( 'init', array( My_Account_Class::g(), 'init_endpoint' ) );

		add_action( 'wps_before_customer_login_form', array( My_Account_Class::g(), 'before_login_form' ) );

		add_action( 'wps_before_checkout_form', array( My_Account_Class::g(), 'checkout_form_login' ) );

		add_action( 'admin_post_wps_login', array( $this, 'handle_login' ) );
		add_action( 'admin_post_nopriv_wps_login', array( $this, 'handle_login' ) );

		add_action( 'wps_account_navigation', array( My_Account_Class::g(), 'display_navigation' ) );
		add_action( 'wps_account_orders', array( My_Account_Class::g(), 'display_orders' ) );
		add_action( 'wps_account_proposals', array( My_Account_Class::g(), 'display_proposals' ) );

		add_action( 'wp_ajax_load_modal_resume_order', array( $this, 'load_modal_resume_order' ) );
		add_action( 'wp_ajax_reorder', array( $this, 'do_reorder' ) );
	}

	/**
	 * Gestion de la connexion, si les identifiants sont correctes, rediriges
	 * à la page indiqué dans $_POST['page'].
	 *
	 * Si les identifiants sont incorrectes, redigires à la page indiquée dans
	 * $_POST['page'] avec une erreur.
	 *
	 * @use wp_signon
	 *
	 * @since 2.0.0
	 */
	public function handle_login() {
		$page = ! empty( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : 'my-account';

		if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) ) {
			wp_redirect( site_url( $page ) );
			exit;
		}

		$user = wp_signon( array(
			'user_login'    => $_POST['username'],
			'user_password' => $_POST['password'],
		), is_ssl() );

		if ( is_wp_error( $user ) ) {
			set_transient( 'login_error_' . $_COOKIE['PHPSESSID'], __( 'Your username or password is incorrect.', 'wpshop' ), 30 );

			wp_redirect( $page );
		} else {
			wp_redirect( $page );
			exit;
		}
	}

	/**
	 * Charges la modal résumant la commande
	 *
	 * @since 2.0.0
	 */
	public function load_modal_resume_order() {
		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$contact     = Contact_Class::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );

		$order = Orders_Class::g()->get( array( 'id' => $id ), true );

		if ( ( isset( $third_party->data ) && $order->data['parent_id'] != $third_party->data['id'] ) && ! current_user_can( 'administrator' ) ) {
			exit;
		}

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'my-account', 'frontend/order-modal', array(
			'order' => $order,
		) );
		wp_send_json_success( array(
			'view'         => ob_get_clean(),
			'buttons_view' => '<div class="wpeo-button button-main modal-close"><span>' . __( 'Close', 'wpshop' ) . '</span></div>',
		) );
	}

	/**
	 * Repasses la même commande. Remet les mêmes données de la commande dans
	 * le panier.
	 *
	 * @since 2.0.0
	 */
	public function do_reorder() {
		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		Class_Cart_Session::g()->destroy();

		$order = Orders_Class::g()->get( array( 'id' => $id ), true );

		if ( ! empty( $order->data['lines'] ) ) {
			foreach ( $order->data['lines'] as $element ) {
				$wp_product = Product_Class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $element['fk_product'],
				), true );

				if ( ! empty( $wp_product ) ) {
					for ( $i = 0; $i < $element['qty']; ++$i ) {
						Cart_Class::g()->add_to_cart( $wp_product );
					}
				}
			}
		}

		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'myAccount',
			'callback_success' => 'reorderSuccess',
			'redirect_url'     => Pages_Class::g()->get_cart_link(),
		) );
	}
}

new My_Account_Action();
