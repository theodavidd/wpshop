<?php
/**
 * Les fonctions principales des produits avec dolibarr.
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
 * Doli Product Class.
 */
class Doli_Products extends \eoxia\Singleton_Util {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Synchronise de Dolibarr vers WP.
	 *
	 * @since 2.0.0
	 *
	 * @param  stdClass      $doli_product Les données du produit venant de
	 * dolibarr.
	 * @param  Product_Model $wp_product   Les données du produit de WP.
	 * @param  boolean       $save         Enregistres les données sinon
	 * renvoies l'objet remplit sans l'enregistrer en base de donnée.
	 * @param  array         $notices      Gestion des erreurs et
	 * informations de l'évolution de la méthode.
	 *
	 * @return Product_Model Les données du produit.
	 */
	public function doli_to_wp( $doli_product, $wp_product, $save = true, &$notices = array(
		'errors'   => array(),
		'messages' => array(),
	) ) {
		if ( is_object( $wp_product ) ) {
			$wp_product->data['external_id']       = (int) $doli_product->id;
			$wp_product->data['ref']               = $doli_product->ref;
			$wp_product->data['title']             = $doli_product->label;
			$wp_product->data['content']           = $doli_product->description;
			$wp_product->data['price']             = $doli_product->price;
			$wp_product->data['price_ttc']         = $doli_product->price_ttc;
			$wp_product->data['tva_tx']            = $doli_product->tva_tx;
			$wp_product->data['barcode']           = $doli_product->barcode;
			$wp_product->data['fk_product_type']   = 0; // Type "Produit" ou "Service".
			$wp_product->data['volume']            = $doli_product->volume;
			$wp_product->data['length']            = $doli_product->length;
			$wp_product->data['width']             = $doli_product->width;
			$wp_product->data['height']            = $doli_product->height;
			$wp_product->data['weight']            = $doli_product->weight;
			$wp_product->data['status']            = 'publish';
			$wp_product->data['date_last_synchro'] = ! empty( $doli_product->last_sync_date ) ? $doli_product->last_sync_date : current_time( 'mysql' );

			if ( $save ) {
				remove_all_actions( 'save_post' );
				$wp_product = Product::g()->update( $wp_product->data );

				// translators: Erase data for the product <strong>dolibarr</strong> data.
				$notices['messages'][] = sprintf( __( 'Erase data for the product <strong>%s</strong> with the <strong>dolibarr</strong> data', 'wpshop' ), $wp_product->data['title'] );

				update_post_meta( $wp_product->data['id'], '_external_id', (int) $doli_product->id );
				add_action( 'save_post', array( Doli_Products_Action::g(), 'callback_save_post' ), 20, 2 );
				Product::g()->update( $wp_product->data );
			}

			return $wp_product;
		}
	}

	/**
	 * Appel la route update de dolibarr pour modifier le produit sur dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  Product_Model $wp_product   Les données du produit sur WordPress.
	 * @param  Object        $doli_product Les données du produit sur dolibarr.
	 * @param  boolean       $save         Enregistres les données sinon
	 * renvoies l'objet remplit sans l'enregistrer en base de donnée.
	 * @param  array         $notices      Gestion des erreurs et
	 * informations de l'évolution de la méthode.
	 *
	 * @return Product_Model Les données du produit.
	 */
	public function wp_to_doli( $wp_product, $doli_product, $save = true, &$notices = array(
		'errors'   => array(),
		'messages' => array(),
	) ) {
		$doli_product = Request_Util::put( 'wpshopapi/update/product/' . $wp_product->data['external_id'], array(
			'label'       => $wp_product->data['title'],
			'description' => $wp_product->data['content'],
			'price'       => ! empty( $wp_product->data['price'] ) ? $wp_product->data['price'] : 0,
			'tva_tx'      => ! empty( $wp_product->data['tva_tx'] ) ? $wp_product->data['tva_tx'] : 0,
			'fk_product'  => (int) $wp_product->data['external_id'],
			'wp_product'  => (int) $wp_product->data['id'],
		) );

		update_post_meta( $wp_product->data['id'], '_price', $doli_product->price );
		update_post_meta( $wp_product->data['id'], '_tva_tx', $doli_product->tva_tx );
		update_post_meta( $wp_product->data['id'], '_price_ttc', $doli_product->price_ttc );
		update_post_meta( $wp_product->data['id'], '_date_last_synchro', date( 'Y-m-d H:i:s', $doli_product->last_sync_date ) );
		update_post_meta( $wp_product->data['id'], '_external_id', $wp_product->data['external_id'] );

		// translators: Erase data for the product <strong>Produit A</strong> with the <strong>WordPress</strong> data.
		$notices['messages'][] = sprintf( __( 'Erase data for the product <strong>%s</strong> with the <strong>WordPress</strong> data', 'wpshop' ), $doli_product->label );

		return $wp_product;
	}
}

Doli_Products::g();
