<?php
/**
 * Gestion des filtres du panier.
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
 * Cart Filter Class.
 */
class Cart_Filter {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'wp_nav_menu_objects', array( $this, 'nav_menu_add_search' ), 10, 2 );
	}

	/**
	 * Ajoutes le nombre de produit dans le panier dans le menu "Panier".
	 *
	 * @since 2.0.0
	 *
	 * @param array $items Les items du menu.
	 * @param array $args  Arguments supplémentaires.
	 *
	 * @return array       Les items du menu avec le bouton "Panier" modifié.
	 */
	public function nav_menu_add_search( $items, $args ) {

		if ( ! empty( $items ) ) {
			foreach ( $items as &$item ) {
				if ( Pages::g()->get_cart_link() === $item->url ) {
					$item->classes[] = 'cart-button';
					$qty   = Cart_Session::g()->qty;

					if ( ! empty( $qty ) ) {
						$item->title .= ' <span class="qty">(<span class="qty-value">' . $qty . '</span>)</span>';
					} else {
						$item->title .= ' <span class="qty"></span>';
					}
				}
			}
		}

		return $items;
	}
}

new Cart_Filter();
