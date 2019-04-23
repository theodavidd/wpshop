<?php
/**
 * Gestion des actions des produits avec dolibarr.
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
 * Doli Products Action Class.
 */
class Doli_Products_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'save_post', array( $this, 'callback_save_post' ), 20, 2 );
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
		$product_data['price'] = isset( $product_data['price'] ) ? (float) round( str_replace( ',' , '.', $product_data['price'] ), 2 ) : $product->data['price'];
		$product_data['tva_tx'] = ! empty( $product_data['tva_tx'] ) ? (float) round( str_replace( ',' , '.', $product_data['tva_tx'] ), 2 ) : $product->data['tva_tx'];

		// Synchronisation Produit
		if ( ! empty( $product->data['external_id'] ) ) {
			$doli_product = Request_Util::put( 'wpshopapi/update/product/' . $product->data['external_id'], array(
				'label'       => $product->data['title'],
				'description' => $product->data['content'],
				'price'       => $product_data['price'],
				'tva_tx'      => $product_data['tva_tx'],
				'fk_product'  => $product->data['external_id'],
				'wp_product'  => $product->data['id'],
			) );

			update_post_meta( $post_id, '_price', $doli_product->price );
			update_post_meta( $post_id, '_tva_tx', $doli_product->tva_tx );
			update_post_meta( $post_id, '_price_ttc', $doli_product->price_ttc );
			update_post_meta( $post_id, '_date_last_synchro', date( 'Y-m-d H:i:s', $doli_product->last_sync_date ) );

		} else {
			$data = array(
				'ref'         => sanitize_title( $product->data['title'] ),
				'label'       => $product->data['title'],
				'description' => $product->data['content'],
				'price'       => $product_data['price'],
				'tva_tx'      => $product_data['tva_tx'],
				'status'      => 1, // En vente
				'status_buy'  => 1, // En achat
				'wp_product'  => $product->data['id'],
			);

			$doli_product = Request_Util::post( 'wpshopapi/associate/product', $data );

			update_post_meta( $post_id, '_price', $doli_product->price );
			update_post_meta( $post_id, '_tva_tx', $doli_product->tva_tx );
			update_post_meta( $post_id, '_price_ttc', $doli_product->price_ttc );
			update_post_meta( $post_id, '_date_last_synchro', date( 'Y-m-d H:i:s', $doli_product->last_sync_date ) );
			update_post_meta( $post_id, '_external_id', $doli_product->id );

		}
	}
}

new Doli_Products_Action();
