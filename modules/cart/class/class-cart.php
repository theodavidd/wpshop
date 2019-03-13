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
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
* Handle order
*/
class Cart_Class extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Cart_Class. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function add_to_cart( $product ) {
		$data = array_merge(
			array( 'qty' => 1 ),
			$product->data
		);

		$index = -1;

		if ( ! empty( Class_Cart_Session::g()->cart_contents ) ) {
			foreach ( Class_Cart_Session::g()->cart_contents as $key => $line ) {
				if ( $line['id'] == $product->data['id'] ) {
					$data['qty'] = $line['qty'] + 1;
					$index = $key;
					break;
				}
			}
		}

		if ( $index == -1 ) {
			Class_Cart_Session::g()->cart_contents[] = $data;
		} else {
			Class_Cart_Session::g()->cart_contents[ $index ] = $data;
		}

		do_action( 'wps_add_to_cart', $this, $data );

		do_action( 'wps_before_calculate_totals', $this );

		do_action( 'wps_calculate_totals', $this );

		do_action( 'wps_after_calculate_totals', $this );

		Class_Cart_Session::g()->update_session();
	}

	public function update_cart( $product ) {
		if ( ! empty( Class_Cart_Session::g()->cart_contents ) ) {
			foreach ( Class_Cart_Session::g()->cart_contents as $key => &$line ) {
				if ( $line['id'] == $product['id'] ) {
					$line['qty'] = $product['qty'];
				}
			}
		}

		do_action( 'wps_update_cart', $this );

		do_action( 'wps_before_calculate_totals', $this );

		do_action( 'wps_calculate_totals', $this );

		do_action( 'wps_after_calculate_totals', $this );

		Class_Cart_Session::g()->update_session();
	}

	public function delete_product( $key ) {
		array_splice( Class_Cart_Session::g()->cart_contents, $key, 1 );

		do_action( 'wps_delete_to_cart', $this, $key );

		do_action( 'wps_before_calculate_totals', $this );

		do_action( 'wps_calculate_totals', $this );

		do_action( 'wps_after_calculate_totals', $this );


		Class_Cart_Session::g()->update_session();
	}
}

Cart_Class::g();
