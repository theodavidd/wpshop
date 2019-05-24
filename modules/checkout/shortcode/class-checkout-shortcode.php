<?php
/**
 * Gestion shortcode du tunnel de vente.
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
 * Checkout Shortcode Class.
 */
class Checkout_Shortcode extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Class_Checkout_Shortcode. Ajoutes les
	 * shortcodes pour le tunnel de vente.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Initialise les shortcodes.
	 *
	 * @since 2.0.0
	 */
	public function callback_init() {
		add_shortcode( 'wps_checkout', array( $this, 'callback_checkout' ) );
		add_shortcode( 'wps_valid', array( $this, 'callback_valid' ) );
	}

	/**
	 * Affichage du tunnel de vente
	 *
	 * @since 2.0.
	 *
	 * @param  array $param Les paramètres du shortcode.
	 */
	public function callback_checkout( $param ) {
		if ( ! is_admin() ) {
			$current_user = wp_get_current_user();

			$third_party = Third_Party::g()->get( array( 'schema' => true ), true );
			$contact     = Third_Party::g()->get( array( 'schema' => true ), true );

			if ( 0 !== $current_user->ID ) {
				$contact = Contact::g()->get( array(
					'search' => $current_user->user_email,
					'number' => 1,
				), true );

				$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
			}

			$total_price_no_shipping = Cart_Session::g()->total_price_no_shipping;
			$tva_amount              = Cart_Session::g()->tva_amount;
			$total_price_ttc         = Cart_Session::g()->total_price_ttc;
			$shipping_cost           = Cart_Session::g()->shipping_cost;

			include( Template_Util::get_template_part( 'checkout', 'form-checkout' ) );
		}
	}

	/**
	 * Affichage la validation de la commande
	 *
	 * @since 2.0.0
	 */
	public function callback_valid( $param ) {
		if ( ! is_admin() ) {
			$object      = null;
			$text        = '';
			$button_text = '';

			if ( 'proposal' === $param['type'] ) {
				$object      = Proposals::g()->get( array( 'id' => $param['id'] ), true );
				$title       = __( 'quotation', 'wpshop' );
				$button_text = __( 'See my quotations', 'wpshop' );
			} else if ( 'order' === $param['type'] ) {
				$object = Doli_Order::g()->get( array( 'id' => $param['id'] ), true );
				$title       = __( 'order', 'wpshop' );
				$button_text = __( 'See my orders', 'wpshop' );
			}

			if ( null !== $object ) {
				$total_price_no_shipping = $object->data['total_price_no_shipping'];
				$tva_amount              = $object->data['tva_amount'];
				$total_price_ttc         = $object->data['total_ttc'];
				$shipping_cost           = $object->data['shipping_cost'];

				include( Template_Util::get_template_part( 'checkout', 'valid-checkout' ) );
			}
		}
	}

	/**
	 * Affichage de la validation du devis
	 *
	 * @since 2.0.0
	 */
	public function callback_valid_proposal() {
		if ( ! is_admin() ) {
			$proposal_id = ! empty( $_GET['proposal_id'] ) ? (int) $_GET['proposal_id'] : 0;
			$proposal    = Proposals::g()->get( array( 'id' => $proposal_id ), true );

			if ( ! empty( $proposal_id ) ) {
				include( Template_Util::get_template_part( 'checkout', 'valid-proposal' ) );
			}
		}
	}
}

Checkout_Shortcode::g();
