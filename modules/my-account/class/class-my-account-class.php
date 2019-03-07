<?php
/**
 * Gestion des actions des commandes.
 *
 * Ajoutes une page "Orders" dans le menu de WordPress.
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
 * Action of Order module.
 */
class My_Account_Class extends \eoxia\Singleton_Util {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function init_endpoint() {
		add_rewrite_endpoint( 'orders', EP_ALL );
	}

	public function before_login_form() {
		global $post;

		if ( Pages_Class::g()->get_slug_by_page_id( $post->ID ) == 'my_account_id' ) {
			include( Template_Util::get_template_part( 'my-account', 'login-title' ) );
		}
	}

	public function checkout_form_login() {
		if ( ! is_user_logged_in() ) {
			include( Template_Util::get_template_part( 'my-account', 'checkout-login' ) );
		}
	}

	public function display_navigation( $tab ) {
		include( Template_Util::get_template_part( 'my-account', 'my-account-navigation' ) );
	}

	public function display_orders() {
		$contact     = Contact_Class::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$orders      = Orders_Class::g()->get( array( 'post_parent' => $third_party->data['id'] ) );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as &$order ) {
				$order->data['invoice'] = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
			}
		}

		unset( $order );

		include( Template_Util::get_template_part( 'my-account', 'my-account-orders' ) );
	}

	public function display_proposals() {
		$contact     = Contact_Class::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$proposals   = Proposals_Class::g()->get( array( 'post_parent' => $third_party->data['id'] ) );

		include( Template_Util::get_template_part( 'my-account', 'my-account-proposals' ) );
	}
}

My_Account_Class::g();
