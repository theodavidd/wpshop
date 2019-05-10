<?php
/**
 * Gestion des actions de Stripe.
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
 * Stripe Action Class.
 */
class Stripe_Action {
	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'callback_enqueue_scripts' ), 9 );

		add_action( 'wps_setting_payment_method_stripe', array( $this, 'callback_setting_payment_method' ), 10, 0 );
		add_action( 'admin_post_wps_update_method_payment_stripe', array( $this, 'update_method_payment_stripe' ) );

		add_action( 'wps_gateway_stripe', array( $this, 'callback_wps_gateway_stripe' ), 10, 1 );
	}

	/**
	 * Inclus le JS de stripe.
	 *
	 * @since 2.0.0
	 *
	 * @todo: Inclusions que si on est sur la page de paieemnt.
	 */
	public function callback_enqueue_scripts() {
		wp_enqueue_script( 'wpshop-stripe', 'https://js.stripe.com/v3/', array(), \eoxia\Config_Util::$init['wpshop']->version );
	}

	/**
	 * Ajoutes la page pour configurer le paiement Stripe.
	 *
	 * @since 2.0.0
	 */
	public function callback_setting_payment_method() {
		$stripe_options = Payment::g()->get_payment_option( 'stripe' );
		\eoxia\View_Util::exec( 'wpshop', 'stripe', 'form-setting', array(
			'stripe_options' => $stripe_options,
		) );
	}

	/**
	 * Enregistres les configurations de Stripe en base de donnée.
	 *
	 * @since 2.0.0
	 */
	public function update_method_payment_stripe() {
		check_admin_referer( 'update_method_payment_stripe' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$title              = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description        = ! empty( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
		$publish_key        = ! empty( $_POST['publish_key'] ) ? sanitize_text_field( $_POST['publish_key'] ) : '';
		$secret_key         = ! empty( $_POST['secret_key'] ) ? sanitize_text_field( $_POST['secret_key'] ) : '';
		$use_stripe_sandbox = ( isset( $_POST['use_stripe_sandbox'] ) && 'on' === $_POST['use_stripe_sandbox'] ) ? true : false;

		$payment_methods_option = get_option( 'wps_payment_methods', Payment::g()->default_options );

		$payment_methods_option['stripe']['title']              = $title;
		$payment_methods_option['stripe']['description']        = $description;
		$payment_methods_option['stripe']['publish_key']        = $publish_key;
		$payment_methods_option['stripe']['secret_key']         = $secret_key;
		$payment_methods_option['stripe']['use_stripe_sandbox'] = $use_stripe_sandbox;

		update_option( 'wps_payment_methods', $payment_methods_option );

		set_transient( 'updated_wpshop_option_' . get_current_user_id(), __( 'Your settings have been saved.', 'wpshop' ), 30 );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab=payment_method&section=stripe' ) );
	}

	/**
	 * Déclenches l'action pour compléter le paiement.
	 *
	 * @since 2.0.0
	 *
	 * @param array $param Donnée reçu par Stripe.
	 */
	public function callback_wps_gateway_stripe( $param ) {
		if ( 'order.payment_failed' === $param['type'] ) {
			do_action( 'wps_payment_failed', $param );
		} else {
			do_action( 'wps_payment_complete', $param );
		}
	}
}

new Stripe_Action();
