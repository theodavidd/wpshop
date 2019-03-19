<?php
/**
 * Classe principale de My Account.
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
 * My Account Class.
 */
class My_Account extends \eoxia\Singleton_Util {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}


	/**
	 * Ajoutes la route orders.
	 *
	 * @since 2.0.0
	 */
	public function init_endpoint() {
		add_rewrite_endpoint( 'orders', EP_ALL );
	}

	/**
	 * Ajoutes le titre de la page de login.
	 *
	 * @since 2.0.0
	 *
	 * @todo: Mal placé ? Pas trop compréhensible.
	 */
	public function before_login_form() {
		global $post;

		if ( Pages::g()->get_slug_link_shop_page( $post->ID ) == 'my-account' ) {
			include( Template_Util::get_template_part( 'my-account', 'login-title' ) );
		}
	}

	/**
	 * Appel la vue pour afficher le formulaire de login dans la page de
	 * paiement.
	 *
	 * @since 2.0.0
	 *
	 * @todo: Mal placé ?
	 */
	public function checkout_form_login() {
		if ( ! is_user_logged_in() ) {
			include( Template_Util::get_template_part( 'my-account', 'checkout-login' ) );
		}
	}

	/**
	 * Affiches le menu de navigation
	 *
	 * @since 2.0.0
	 *
	 * @param  string $tab Le slug de l'onglet actuel.
	 */
	public function display_navigation( $tab ) {
		include( Template_Util::get_template_part( 'my-account', 'my-account-navigation' ) );
	}

	/**
	 * Affiches les commandes liées au tier.
	 *
	 * @since 2.0.0
	 */
	public function display_orders() {
		$contact     = Contact::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$orders      = Doli_Order::g()->get( array( 'post_parent' => $third_party->data['id'] ) );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as &$order ) {
				$order->data['invoice'] = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
			}
		}

		unset( $order );

		include( Template_Util::get_template_part( 'my-account', 'my-account-orders' ) );
	}

	/**
	 * Affiches les devis liés au tiers.
	 *
	 * @since 2.0.0
	 */
	public function display_proposals() {
		$contact     = Contact::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$proposals   = Proposals::g()->get( array( 'post_parent' => $third_party->data['id'] ) );

		include( Template_Util::get_template_part( 'my-account', 'my-account-proposals' ) );
	}
}

My_Account::g();
