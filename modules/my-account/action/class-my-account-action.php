<?php
/**
 * Gestion des actions dans la page mon compte.
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
		add_action( 'init', array( My_Account::g(), 'init_endpoint' ) );

		add_action( 'wps_before_customer_login_form', array( My_Account::g(), 'before_login_form' ) );

		add_action( 'wps_before_checkout_form', array( My_Account::g(), 'checkout_form_login' ) );

		add_action( 'admin_post_wps_login', array( $this, 'handle_login' ) );
		add_action( 'admin_post_nopriv_wps_login', array( $this, 'handle_login' ) );

		add_action( 'wps_account_navigation', array( My_Account::g(), 'display_navigation' ) );
		add_action( 'wps_account_orders', array( My_Account::g(), 'display_orders' ) );
		add_action( 'wps_account_invoices', array( My_Account::g(), 'display_invoices' ) );
		add_action( 'wps_account_download', array( My_Account::g(), 'display_downloads' ) );
		add_action( 'wps_account_quotations', array( My_Account::g(), 'display_quotations' ) );

		add_action( 'wp_ajax_reorder', array( $this, 'do_reorder' ) );

		add_action( 'wps_my_account_proposals_actions', array( $this, 'add_proposal_pdf' ), 10, 1 );
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
		check_admin_referer( 'handle_login' );

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
	 * Repasses la même commande. Remet les mêmes données de la commande dans
	 * le panier.
	 *
	 * @since 2.0.0
	 */
	public function do_reorder() {
		check_ajax_referer( 'do_reorder' );

		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		Cart_Session::g()->destroy();

		$shipping_cost_option     = get_option( 'wps_shipping_cost', Settings::g()->shipping_cost_default_settings );
		$shippint_cost_product_id = ! empty( $shipping_cost_option['shipping_product_id'] ) ? $shipping_cost_option['shipping_product_id'] : 0;

		$order = Doli_Order::g()->get( array( 'id' => $id ), true );

		if ( ! empty( $order->data['lines'] ) ) {
			foreach ( $order->data['lines'] as $element ) {
				$wp_product = Product::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $element['fk_product'],
				), true );

				if ( ! empty( $wp_product ) && $wp_product->data['id'] !== $shippint_cost_product_id ) {
					for ( $i = 0; $i < $element['qty']; ++$i ) {
						Cart::g()->add_to_cart( $wp_product );
					}
				}
			}
		}

		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'myAccount',
			'callback_success' => 'reorderSuccess',
			'redirect_url'     => Pages::g()->get_cart_link(),
		) );
	}

	public function add_proposal_pdf( $proposal ) {
		if ( Settings::g()->dolibarr_is_active() ) {
			include( Template_Util::get_template_part( 'my-account', 'my-account-proposals-devis' ) );
		}
	}
}

new My_Account_Action();
