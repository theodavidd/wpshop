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
class Cart_Filter {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'wp_nav_menu_objects', array( $this, 'nav_menu_add_search' ), 10, 2 );
	}

	public function nav_menu_add_search( $items, $args ) {

		if ( ! empty( $items ) ) {
			foreach ( $items as &$item ) {
				if ( $item->url == Pages_Class::g()->get_cart_link() ) {
					$item->classes[] = 'cart-button';
					$qty           = 0;
					$cart_contents = Class_Cart_Session::g()->cart_contents;

					if ( ! empty( $cart_contents ) ) {
						foreach ( $cart_contents as $content ) {
							$qty += $content['qty'];
						}
					}

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
