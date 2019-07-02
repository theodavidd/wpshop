<?php
/**
 * Gestion API.
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
 * API Action Class.
 */
class API_Action {

	/**
	 * Constructeur
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_check_cap', array( $this, 'check_cap' ), 1, 2 );

		add_action( 'rest_api_init', array( $this, 'callback_rest_api_init' ) );
		add_action( 'init', array( $this, 'init_endpoint' ) );
		add_action( 'template_include', array( $this, 'change_template' ) );

		add_action( 'show_user_profile', array( $this, 'callback_edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'callback_edit_user_profile' ) );

		add_action( 'wp_ajax_generate_api_key', array( $this, 'generate_api_key' ) );
	}

	public function check_cap( $cap, $request ) {
		$headers = $request->get_headers();

		if ( empty( $headers['wpapikey'] ) ) {
			return false;
		}

		$wp_api_key = $headers['wpapikey'];

		$user = API::g()->get_user_by_token( $wp_api_key[0] );

		if ( empty( $user ) ) {
			return false;
		}

		wp_set_current_user( $user->ID );

		return true;
	}

	/**
	 * Ajoutes la route pour PayPal.
	 *
	 * @since 2.0.0
	 */
	public function callback_rest_api_init() {
		register_rest_route( 'wpshop/v2', '/statut', array(
			'methods'  => array( 'GET' ),
			'callback' => array( $this, 'check_statut' ),
			'permission_callback' => function( $request ){
				return \eoxia\Rest_Class::g()->check_cap( 'get', $request );
			}
		) );

		register_rest_route( 'wpshop/v2', '/wps_gateway_paypal', array(
			'methods'  => array( 'GET', 'POST' ),
			'callback' => array( $this, 'callback_wps_gateway_paypal' ),
		) );

		register_rest_route( 'wpshop/v2', '/wps_gateway_stripe', array(
			'methods'  => array( 'GET', 'POST' ),
			'callback' => array( $this, 'callback_wps_gateway_stripe' ),
		) );

		register_rest_route( 'wpshop/v2', '/product/search', array(
			'methods'  => array( 'GET' ),
			'callback' => array( $this, 'callback_search' ),
		) );
	}

	/**
	 * Ajoutes la route si oauth2 n'est pas activé.
	 *
	 * @since 2.0.0
	 */
	public function init_endpoint() {
		if ( ! DEFINED( 'WPOAUTH_VERSION' ) ) {
			add_rewrite_endpoint( 'oauth/authorize', EP_ALL );

			if ( ! get_option( 'plugin_permalinks_flushed' ) ) {
				flush_rewrite_rules( false );
				update_option( 'plugin_permalinks_flushed', 1 );
			}
		}
	}

	public function change_template( $template ) {
		if ( ! DEFINED( 'WPOAUTH_VERSION' ) && get_query_var( 'oauth/authorize', false ) !== false ) {
			exit( __( 'Please, active WP OAuth Server plugin', 'wpshop' ) );
		}

		return $template;
	}

	public function check_statut( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_REST_Response( false);
		}

		return new \WP_REST_Response( true );
	}

	/**
	 * Gestion de la route Paypal.
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Request $request L'objet contenant les informations de la
	 * requête.
	 */
	public function callback_wps_gateway_paypal( $request ) {
		$data = $request->get_body_params();

		// translators: Paypal Gateway data: {json_data}.
		\eoxia\LOG_Util::log( sprintf( 'Paypal Gateway data: %s', json_encode( $data ) ), 'wpshop2' );

		$txn_id = get_post_meta( $data['custom'], 'payment_txn_id', true );

		if ( $txn_id !== $data['txn_id'] ) {
			update_post_meta( $data['custom'], 'payment_data', $data );
			update_post_meta( $data['custom'], 'payment_txn_id', $data['txn_id'] );
			update_post_meta( $data['custom'], 'payment_method', 'paypal' );

			do_action( 'wps_gateway_paypal', $data );
		}
	}

	/**
	 * Gestion de la route Stripe.
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Request $request L'objet contenant les informations de la
	 * requête.
	 */
	public function callback_wps_gateway_stripe( $request ) {
		$param = json_decode( $request->get_body(), true );

		// translators: Stripe Gateway data: {json_data}.
		\eoxia\LOG_Util::log( sprintf( 'Stripe Gateway data: %s', json_encode( $param ) ), 'wpshop2' );

		$order = Doli_Order::g()->get( array(
			'meta_key'     => '_external_data',
			'meta_compare' => 'LIKE',
			'meta_value'   => $param['data']['object']['id'],
		), true );

		if ( ! empty( $order ) ) {
			update_post_meta( $order->data['id'], 'payment_data', $param );
			update_post_meta( $order->data['id'], 'payment_txn_id', $param['data']['object']['id'] );
			update_post_meta( $order->data['id'], 'payment_method', 'stripe' );

			$param['custom'] = $order->data['id'];

			do_action( 'wps_gateway_stripe', $param );
		}
	}

	public function callback_search( $request ) {
		$param = $request->get_params();
		$products = Product::g()->get( array( 's' => $param['s'] ) );

		$response_products = array();

		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$response_products[] = $product->data;
			}
		}

		$response = new \WP_REST_Response( $response_products );
		return $response;
	}


	/**
	 * Ajoute les champs spécifiques à note de frais dans le compte utilisateur.
	 *
	 * @param WP_User $user L'objet contenant la définition complète de l'utilisateur.
	 */
	public function callback_edit_user_profile( $user ) {
		$token = get_user_meta( $user->ID, '_wpshop_api_key', true );

		\eoxia\View_Util::exec( 'wpshop', 'api', 'field-api', array(
			'id'    => $user->ID,
			'token' => $token,
		) );
	}

	public function generate_api_key() {
		check_ajax_referer( 'generate_api_key' );

		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$token = API::g()->generate_token();
		update_user_meta( $id, '_wpshop_api_key', $token );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'api', 'field-api', array(
			'id'    => $id,
			'token' => $token,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'API',
			'callback_success' => 'generatedAPIKey',
			'view'             => ob_get_clean(),
		) );
	}
}

new API_Action();
