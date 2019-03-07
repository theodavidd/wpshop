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
 * Action of product module.
 */
class Product_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
		add_action( 'save_post', array( $this, 'callback_save_post' ), 10, 2 );

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

	public function callback_save_post( $post_id, $post ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( 'wps-product' !== $post->post_type ) {
			return $post_id;
		}

		$product = Product_Class::g()->get( array( 'id' => $post_id ), true );

		if ( empty( $product ) || ( ! empty( $product ) && 0 === $product->data['id'] ) ) {
			return $post_id;
		}

		$product_data = ! empty( $_POST['product_data'] ) ? (array) $_POST['product_data'] : array();

		if ( empty( $product_data ) ) {
			return $post_id;
		}

		$product_data['price'] = isset( $product_data['price'] ) ? (float) round( str_replace( ',' , '.', $product_data['price'] ), 2 ) : $product->data['price'];
		update_post_meta( $post_id, '_price', $product_data['price'] );

		$product_data['tva_tx'] = ! empty( $product_data['tva_tx'] ) ? (float) round( str_replace( ',' , '.', $product_data['tva_tx'] ), 2 ) : $product->data['tva_tx'];
		update_post_meta( $post_id, '_tva_tx', $product_data['tva_tx'] );

		$product_data['barcode'] = ! empty( $product_data['barcode'] ) ? sanitize_text_field( $product_data['barcode'] ) : $product->data['barcode'];
		update_post_meta( $post_id, '_barcode', $product_data['barcode'] );

		// Synchronisation Produit
		if ( ! empty( $product->data['external_id'] ) ) {
			$doli_product = Request_Util::put( 'products/' . $product->data['external_id'], array(
				'label'       => $product->data['title'],
				'description' => $product->data['content'],
				'price'       => $product_data['price'],
				'tva_tx'      => $product_data['tva_tx'],
				'barcode'     => $product_data['barcode'],
			) );

			update_post_meta( $post_id, '_price_ttc', $doli_product->price_ttc );
		} else {
			$doli_product_id = Request_Util::post( 'products', array(
				'ref'         => sanitize_title( $product->data['title'] ),
				'label'       => $product->data['title'],
				'description' => $product->data['content'],
				'price'       => $product_data['price'],
				'tva_tx'      => $product_data['tva_tx'],
				'barcode'     => $product_data['barcode'],
				'status'      => 1, // En vente
				'status_buy'  => 1, // En achat
			) );
			update_post_meta( $post_id, '_external_id', $doli_product_id );

			$doli_product = Request_Util::get( 'products/' . $doli_product_id );
			update_post_meta( $post_id, '_price_ttc', $doli_product->price_ttc );
		}
	}

	public function ajax_delete_product() {
		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$product = Product_Class::g()->get( array( 'id' => $id ), true );
		$product->data['status'] = 'trash';
		Product_Class::g()->update( $product->data );

		// Suppression vers dolibarr


		wp_send_json_success();
	}
}

new Product_Action();
