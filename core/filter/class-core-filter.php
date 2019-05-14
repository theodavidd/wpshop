<?php
/**
 * Classe gérant les filtres principales de WPshop.
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
 * Main filters of wpshop.
 */
class Core_Filter {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'custom_menu_order', array( $this, 'order_menu' ), 10, 1 );
		add_filter( 'menu_order', array( $this, 'order_menu' ), 10, 1 );
	}

	/**
	 * Réorganises le menu du backadmin de WordPress.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $order_menu Le menu.
	 *
	 * @return array             Le menu réorgéanisé.
	 */
	public function order_menu( $order_menu ) {
		if ( ! $order_menu || empty( $order_menu ) ) {
			return true;
		}

		$key         = array_search( 'edit-comments.php', $order_menu, true );
		$key_product = array_search( 'wps-product', $order_menu, true );

		if ( false !== $key ) {
			array_splice( $order_menu, $key, 0, array( 'wps-product' ) );
			array_splice( $order_menu, $key_product + 1, 1 );
		}

		$key_wpshop = array_search( 'wpshop', $order_menu, true );

		if ( false !== $key ) {
			array_splice( $order_menu, $key, 0, array( 'wpshop' ) );
			array_splice( $order_menu, $key_wpshop + 1, 1 );
		}

		return $order_menu;
	}

}

new Core_Filter();
