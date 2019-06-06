<?php
/**
 * Les fonctions principales du panier.
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
 * Cart Class.
 */
class Cart extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Cart. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Ajout d'un produit dans le panier
	 *
	 * @since 2.0.0
	 *
	 * @param Product_Model $product Les données du produit.
	 */
	public function add_to_cart( $product ) {
		$data = array_merge(
			array( 'qty' => 1 ),
			$product->data
		);

		$index = -1;

		if ( ! empty( Cart_Session::g()->cart_contents ) ) {
			foreach ( Cart_Session::g()->cart_contents as $key => $line ) {
				if ( $line['id'] === $product->data['id'] ) {
					$data['qty'] = $line['qty'] + 1;
					$index       = $key;
					break;
				}
			}
		}

		$can_add = apply_filters( 'wps_add_to_cart_product', true, $product );

		if ( $can_add ) {
			if ( -1 === $index ) {
				Cart_Session::g()->add_product( $data );
			} else {
				Cart_Session::g()->update_product( $index, $data );
			}
		}

		do_action( 'wps_add_to_cart' );
		do_action( 'wps_before_calculate_totals' );
		do_action( 'wps_calculate_totals' );
		do_action( 'wps_after_calculate_totals' );

		return $can_add;
	}

	/**
	 * Met à jour le contenu du panier
	 *
	 * @since 2.0.0
	 *
	 * @param  Product_Model $product Les données du produit.
	 */
	public function update_cart( $product ) {
		if ( ! empty( Cart_Session::g()->cart_contents ) ) {
			foreach ( Cart_Session::g()->cart_contents as $key => $line ) {
				if ( (int) $line['id'] === (int) $product['id'] ) {
					$line['qty'] = $product['qty'];
					Cart_Session::g()->update_product( $key, $line );
				}
			}
		}

		do_action( 'wps_update_cart' );
		do_action( 'wps_before_calculate_totals' );
		do_action( 'wps_calculate_totals' );
		do_action( 'wps_after_calculate_totals' );
	}

	/**
	 * Supprimes un produit du panier.
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $key La clé du produit dans le tableau.
	 */
	public function delete_product( $key ) {
		Cart_Session::g()->remove_product_by_key( $key );

		do_action( 'wps_delete_to_cart', $key );
		do_action( 'wps_before_calculate_totals' );
		do_action( 'wps_calculate_totals' );
		do_action( 'wps_after_calculate_totals' );
	}

	public function display_cart_resume( $total_price_no_shipping, $tva_amount, $total_price_ttc, $shipping_cost ) {
		$shipping_cost_option  = get_option( 'wps_shipping_cost', Settings::g()->shipping_cost_default_settings );

		$shipping_cost_product = Product::g()->get( array( 'id' => $shipping_cost_option['shipping_product_id'] ), true );

		include( Template_Util::get_template_part( 'cart', 'cart-resume' ) );
	}

	public function check_stock( $order ) {
		$stock_statut = array(
			'is_valid' => true,
			'errors'   => array(),
		);

		if ( ! empty( Cart_Session::g()->cart_contents ) ) {
			foreach ( Cart_Session::g()->cart_contents as $product ) {
				if ( ! $product['manage_stock'] ) {
					continue;
				}

				if ( $product['stock'] < $product['qty'] ) {
					$stock_statut['is_valid'] = false;
					$stock_statut['errors'][] = __( sprintf( '%s is sold out.', $product['title'] ), 'wpshop' );
				}
			}
		}

		return $stock_statut;
	}
}

Cart::g();
