<?php
/**
 * Gestion des actions des produits.
 *
 * Ajoutes une page "Product" dans le menu de WordPress.
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
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ), 0 );
		add_action( 'save_post', array( $this, 'callback_save_post' ), 10, 2 );
	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Products', 'wpshop' ), __( 'Products', 'wpshop' ), 'manage_options', 'wps-product', '', 'dashicons-cart' );
		add_submenu_page( 'wps-product', __( 'Products', 'wpshop' ), __( 'Products', 'wpshop' ), 'manage_options', 'wps-product', array( $this, 'callback_add_menu_page' ) );
		add_submenu_page( 'wps-product', __( 'Products Category', 'wpshop' ), __( 'Products Category', 'wpshop' ), 'manage_options', 'edit-tags.php?taxonomy=wps-product-cat' );
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

	public function callback_save_post( $post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( 'wps-product' !== $post->post_type && 'publish' !== $post->post_status ) {
			return $post_id;
		}

		$product = Product::g()->get( array( 'id' => $post_id ), true );

		if ( empty( $product ) || ( ! empty( $product ) && 0 === $product->data['id'] ) ) {
			return $post_id;
		}

		$product_data = ! empty( $_POST['product_data'] ) ? (array) $_POST['product_data'] : array();
		$product_data['product_downloadable'] = ( ! empty( $product_data['product_downloadable'] ) && 'true' == $product_data['product_downloadable'] ) ? true : false;

		update_post_meta( $post_id, '_product_downloadable', $product_data['product_downloadable'] );
	}
}

new Product_Action();
