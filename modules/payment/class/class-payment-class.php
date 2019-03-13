<?php
/**
 * Les fonctions principales des produits.
 *
 * Le controlleur du modèle Product_Model.
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
* Handle product
*/
class Payment_Class extends \eoxia\Singleton_Util {
	public $default_options;

	protected function construct() {
		$this->default_options = array(
			'cheque' => array(
				'active'      => true,
				'title'       => __( 'Payment by cheque', 'wpshop' ),
				'description' => __( 'Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.', 'wpshop' ),
			),
			'payment_in_shop' => array(
				'active'      => true,
				'title'       => __( 'Payment in shop', 'wpshop' ),
				'description' => __( 'Pay and pick up directly your products at the shop.', 'wpshop' ),
			),
			'paypal' => array(
				'active'             => true,
				'title'              => __( 'PayPal', 'wpshop' ),
				'description'        => __( 'Accept payments via PayPal using account balance or credit card.', 'wpshop' ),
				'paypal_email'       => '',
				'use_paypal_sandbox' => false,
			),
		);

		$this->default_options = apply_filters( 'wps_payment_methods', $this->default_options );
	}

	public function get_payment_option( $slug = '' ) {
		$payment_methods_option = get_option( 'wps_payment_methods', $this->default_options );

		if ( empty( $slug ) || ! isset( $payment_methods_option[ $slug ] ) ) {
			return $payment_methods_option;
		}

		return $payment_methods_option[ $slug ];
	}

	public function get_payment_title( $slug ) {
		$payment_methods_option = get_option( 'wps_payment_methods', $this->default_options );
		$payment_method         = $payment_methods_option[ $slug ];

		if ( empty( $payment_method ) ) {
			return null;
		}

		return $payment_method['title'];
	}

	public function convert_status( $object ) {
		$statut = '';

		if ( $object['type'] == 'wps-order' ) {
			switch ( $object['payment_method'] ) {
				case 'cheque':
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
				default:
					break;
			}
		} else if ( $object['type'] == 'wps-doli-invoice' ) {
			if ( $object['paye'] ) {
				$statut = 'Payée';
			} else {
				$statut = 'Impayée';
			}
		}

		return $statut;
	}
}

Payment_Class::g();
