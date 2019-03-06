<?php
/**
 * Les fonctions principales des tiers avec dolibarr.
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
 * Third Party class.
 */
class Doli_Products_Class extends \eoxia\Singleton_Util {

	protected function construct() {}

	public function doli_to_wp( $doli_product, $wp_product ) {
		$wp_product->data['external_id']     = $doli_product->id;
		$wp_product->data['ref']             = $doli_product->ref;
		$wp_product->data['title']           = $doli_product->label;
		$wp_product->data['content']         = $doli_product->description;
		$wp_product->data['price']           = $doli_product->price;
		$wp_product->data['price_ttc']       = $doli_product->price_ttc;
		$wp_product->data['tva_tx']          = $doli_product->tva_tx;
		$wp_product->data['barcode']         = $doli_product->barcode;
		$wp_product->data['fk_product_type'] = 0; // Type "Produit" ou "Service".
		$wp_product->data['volume']          = $doli_product->volume;
		$wp_product->data['length']          = $doli_product->length;
		$wp_product->data['width']           = $doli_product->width;
		$wp_product->data['height']          = $doli_product->height;
		$wp_product->data['weight']          = $doli_product->weight;
		$wp_product->data['status']          = 'publish';

		Product_Class::g()->update( $wp_product->data );
	}
}

Doli_Products_Class::g();
