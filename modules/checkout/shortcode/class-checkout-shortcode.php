<?php
/**
 * Gestion shortcode du tunnel de vente.
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
* Handle order
*/
class Checkout_Shortcode extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Class_Checkout_Shortcode. Ajoutes les
	 * shortcodes pour le tunnel de vente.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function callback_init() {
		add_shortcode( 'wps_checkout', array( $this, 'callback_checkout' ) );
		add_shortcode( 'wps_valid_checkout', array( $this, 'callback_valid_checkout' ) );
		add_shortcode( 'wps_valid_proposal', array( $this, 'callback_valid_proposal' ) );
	}

	public function callback_checkout( $param ) {
		if ( ! is_admin() ) {
			$step     = $param['step'];
			$proposal = null;

			if ( isset( Class_Cart_Session::g()->external_data['proposal_id'] ) ) {
				$proposal = Proposals_Class::g()->get( array( 'id' =>  Class_Cart_Session::g()->external_data['proposal_id'] ), true );
			}

			$current_user = wp_get_current_user();

			$third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );
			$contact     = Third_Party_Class::g()->get( array( 'schema' => true ), true );

			if ( $current_user->ID != 0 ) {
				$contact = Contact_Class::g()->get( array(
					'search' => $current_user->user_email,
					'number' => 1,
				), true );

				$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
			}


			include( Template_Util::get_template_part( 'checkout', 'form-checkout-step-' . $step ) );
		}
	}

	public function callback_valid_checkout() {
		if ( ! is_admin() ) {
			$order_id = ! empty( $_GET['order_id'] ) ? (int) $_GET['order_id'] : 0;
	 		$order    = Orders_Class::g()->get( array( 'id' => $order_id ), true );

			$tva_lines = array();

			if ( ! empty( $order->data['lines'] ) ) {
				foreach ( $order->data['lines'] as $line ) {
					if ( empty( $tva_lines[ $line['tva_tx'] ] ) ) {
						$tva_lines[ $line['tva_tx'] ] = 0;
					}

					$tva_lines[ $line['tva_tx'] ] += $line['total_tva'];
				}
			}

			if ( ! empty( $order_id ) ) {
				include( Template_Util::get_template_part( 'checkout', 'valid-checkout' ) );
			}
		}
	}

	public function callback_valid_proposal() {
		if ( ! is_admin() ) {
			$proposal_id = ! empty( $_GET['proposal_id'] ) ? (int) $_GET['proposal_id'] : 0;
	 		$proposal    = Proposals_Class::g()->get( array( 'id' => $proposal_id ), true );

			if ( ! empty( $proposal_id ) ) {
				include( Template_Util::get_template_part( 'checkout', 'valid-proposal' ) );
			}
		}
	}
}

Checkout_Shortcode::g();
