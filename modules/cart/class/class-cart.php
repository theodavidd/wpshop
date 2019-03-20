<?php
/**
 * Les fonctions principales du panier.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Classes
 *
 * @since     2.0.0
 *
 * @todo: Enlevez les cart session de partout
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
				if ( $line['id'] == $product->data['id'] ) {
					$data['qty'] = $line['qty'] + 1;
					$index       = $key;
					break;
				}
			}
		}

		if ( -1 === $index ) {
			Cart_Session::g()->cart_contents[] = $data;
		} else {
			Cart_Session::g()->cart_contents[ $index ] = $data;
		}

		Cart_Session::g()->update_session();

		do_action( 'wps_add_to_cart' );

		Cart_Session::g()->update_session();

		do_action( 'wps_before_calculate_totals' );

		Cart_Session::g()->update_session();

		do_action( 'wps_calculate_totals' );

		Cart_Session::g()->update_session();

		do_action( 'wps_after_calculate_totals' );

		Cart_Session::g()->update_session();
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
			foreach ( Cart_Session::g()->cart_contents as $key => &$line ) {
				if ( $line['id'] == $product['id'] ) {
					$line['qty'] = $product['qty'];
				}
			}
		}

		Cart_Session::g()->update_session();

		do_action( 'wps_update_cart' );

		Cart_Session::g()->update_session();

		do_action( 'wps_before_calculate_totals' );

		Cart_Session::g()->update_session();

		do_action( 'wps_calculate_totals' );

		Cart_Session::g()->update_session();

		do_action( 'wps_after_calculate_totals' );

		Cart_Session::g()->update_session();
	}

	/**
	 * Supprimes un produit du panier.
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $key La clé du produit dans le tableau.
	 */
	public function delete_product( $key ) {
		array_splice( Cart_Session::g()->cart_contents, $key, 1 );

		Cart_Session::g()->update_session();

		do_action( 'wps_delete_to_cart', $key );

		Cart_Session::g()->update_session();

		do_action( 'wps_before_calculate_totals' );

		Cart_Session::g()->update_session();

		do_action( 'wps_calculate_totals' );

		Cart_Session::g()->update_session();

		do_action( 'wps_after_calculate_totals' );

		Cart_Session::g()->update_session();
	}
}

Cart::g();
