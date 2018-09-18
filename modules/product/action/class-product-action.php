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

		add_action( 'wp_ajax_synchro', array( $this, 'ajax_synchro' ) );
	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Products', 'wpshop' ), __( 'Products', 'wpshop' ), 'manage_options', 'wps-product', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		\eoxia\View_Util::exec( 'wpshop', 'product', 'main' );
	}

	/**
	 * Synchronisation des produits avec dolibarr.
	 *
	 * @since 2.0.0
	 */
	public function ajax_synchro() {
		$request = wp_remote_get( 'http://127.0.0.1/dolibarr/api/index.php/products', array(
			'headers' => array(
				'Content-type' => 'application/json',
				'DOLAPIKEY'    => 'JaTmW3kZu2X5oD491hTfY9Wbp9oY4Ag1',
			),
		) );

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body );

		if ( ! empty( $data ) ) {
			foreach ( $data as $doli_product ) {
				// Vérifie l'existence du produit en base de donnée.
				$product = Product_Class::g()->get( array(
					'meta_key'   => '_ref',
					'meta_value' => $doli_product->ref,
				), true );

				if ( empty( $product ) ) {
					$product = Product_Class::g()->get( array( 'schema' => true ), true );
				}

				$product->data['ref']             = $doli_product->ref;
				$product->data['title']           = $doli_product->label;
				$product->data['content']         = $doli_product->description;
				$product->data['price']           = $doli_product->price;
				$product->data['price_ttc']       = $doli_product->price_ttc;
				$product->data['tva_tx']          = $doli_product->tva_tx;
				$product->data['barcode']         = $doli_product->barcode;
				$product->data['stock']           = $doli_product->stock_reel;
				$product->data['fk_product_type'] = 0; // Type "Produit" ou "Service".
				$product->data['volume']          = $doli_product->volume;
				$product->data['length']          = $doli_product->length;
				$product->data['width']           = $doli_product->width;
				$product->data['height']          = $doli_product->height;
				$product->data['weight']          = $doli_product->weight;

				Product_Class::g()->update( $product->data );
			}
		}

		wp_send_json_success( $data );
	}
}

new Product_Action();
