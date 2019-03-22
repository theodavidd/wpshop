<?php
/**
 * Les fonctions principales pour les paiements.
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
 * Payement Class.
 */
class Payment extends \eoxia\Singleton_Util {

	/**
	 * Les méthodes de paiement
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $default_options;

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->default_options = array(
			'cheque'          => array(
				'active'      => true,
				'title'       => __( 'Cheque', 'wpshop' ),
				'description' => __( 'Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.', 'wpshop' ),
			),
			'payment_in_shop' => array(
				'active'      => true,
				'title'       => __( 'Payment in shop', 'wpshop' ),
				'description' => __( 'Pay and pick up directly your products at the shop.', 'wpshop' ),
			),
			'paypal'          => array(
				'active'             => true,
				'title'              => __( 'PayPal', 'wpshop' ),
				'description'        => __( 'Accept payments via PayPal using account balance or credit card.', 'wpshop' ),
				'paypal_email'       => '',
				'use_paypal_sandbox' => false,
			),
			'stripe'          => array(
				'active'             => true,
				'title'              => __( 'Stripe', 'wpshop' ),
				'description'        => __( 'Use your credit card to place your order', 'wpshop' ),
				'publish_key'        => '',
				'secret_key'         => '',
				'use_stripe_sandbox' => false,
			),
		);

		$this->default_options = apply_filters( 'wps_payment_methods', $this->default_options );
	}

	/**
	 * Récupères les données d'un méthode de paiement selon $slug.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $slug Le slug de la méthode de paiement.
	 *
	 * @return array        Les données de la méthode de paiement.
	 */
	public function get_payment_option( $slug = '' ) {
		$payment_methods_option = get_option( 'wps_payment_methods', $this->default_options );

		if ( empty( $slug ) || ! isset( $payment_methods_option[ $slug ] ) ) {
			return $payment_methods_option;
		}

		return $payment_methods_option[ $slug ];
	}

	/**
	 * Récupères les données d'un méthode de paiement selon $slug.
	 *
	 * @todo Voir ou c'est appelé
	 *
	 * @since 2.0.0
	 *
	 * @param  string $slug Le slug de la méthode de paiement.
	 *
	 * @return array        Le titre de la méthode de paiement.
	 */
	public function get_payment_title( $slug ) {
		$payment_methods_option = get_option( 'wps_payment_methods', $this->default_options );
		$payment_method         = $payment_methods_option[ $slug ];

		if ( empty( $payment_method ) ) {
			return null;
		}

		return $payment_method['title'];
	}

	/**
	 * Convertis le status vers un message lisible.
	 *
	 * @todo: A voir, a traduire.
	 *
	 * @param  array $object Un tableau contenant un type et la méta billed
	 * ainsi que la méta payment_method.
	 *
	 * @return string Le message
	 */
	public function convert_status( $object ) {
		$statut = '';

		if ( 'wps-order' === $object['type'] ) {
			switch ( $object['payment_method'] ) {
				case 'cheque':
					if ( $object['billed'] ) {
						$statut = 'Payée';
					} else {
						$statut = 'En attente du chèque';
					}
					break;
				case 'Cheque':
					if ( $object['billed'] ) {
						$statut = 'Payée';
					} else {
						$statut = 'En attente du chèque';
					}
					break;
				case 'paypal':
					if ( $object['billed'] ) {
						$statut = 'Payée';
					} elseif ( $object['payment_failed'] ) {
						$statut = 'Paiment échoué.';
					} else {
						$statut = 'En attente du paiement';
					}
					break;
				case 'payment_in_shop':
					if ( $object['billed'] ) {
						$statut = 'Payée';
					} else {
						$statut = 'En attente du paiement.<br />Paiement a régler en boutique';
					}
					break;
				case 'stripe':
					if ( $object['billed'] ) {
						$statut = 'Payée';
					} elseif ( $object['payment_failed'] ) {
						$statut = 'Paiment échoué.';
					} else {
						$statut = 'En attente du paiement';
					}
					break;
				default:
					break;
			}
		} elseif ( 'wps-doli-invoice' === $object['type'] ) {
			if ( $object['paye'] ) {
				$statut = 'Payée';
			} else {
				$statut = 'Impayée';
			}
		}

		return $statut;
	}
}

Payment::g();
