<?php
/**
 * Gestion des actions des produits.
 *
 * Ajoutes une page "Product" dans le menu de WordPress.
 *
 * Gestion de la création d'un nouveau produit.
 * Gestion de la mise à jour d'un produit.
 * Gestion de la suppression d'un produit.
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
 * Product Action Class.
 */
class Product_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
		add_action( 'wp_ajax_wps_delete_product', array( $this, 'ajax_delete_product' ) );
	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page( 'wps-order', __( 'Products', 'wpshop' ), __( 'Products', 'wpshop' ), 'manage_options', 'wps-product', array( $this, 'callback_add_menu_page' ) );
		add_submenu_page( 'wps-order', __( 'Products Category', 'wpshop' ), __( 'Products Category', 'wpshop' ), 'manage_options', 'edit-tags.php?taxonomy=wps-product-cat' );
	}

	/**
	 * Appel la vue "main" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		$args = array(
			'post_type'      => 'wps-product',
			'posts_per_page' => -1,
		);

		$count = count( get_posts( $args ) );

		\eoxia\View_Util::exec( 'wpshop', 'products', 'main', array(
			'count' => $count,
		) );
	}

	/**
	 * Met le produit à la corbeille.
	 *
	 * @since 2.0.0
	 */
	public function ajax_delete_product() {
		check_ajax_referer( 'ajax_delete_product' );

		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$product                 = Product::g()->get( array( 'id' => $id ), true );
		$product->data['status'] = 'trash';

		Product::g()->update( $product->data );

		wp_send_json_success();
	}
}

new Product_Action();
