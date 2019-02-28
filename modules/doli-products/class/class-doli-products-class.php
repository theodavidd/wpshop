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
		$product->data['external_id']     = $doli_product->id;
		$product->data['ref']             = $doli_product->ref;
		$product->data['title']           = $doli_product->label;
		$product->data['content']         = $doli_product->description;
		$product->data['price']           = $doli_product->price;
		$product->data['price_ttc']       = $doli_product->price_ttc;
		$product->data['tva_tx']          = $doli_product->tva_tx;
		$product->data['barcode']         = $doli_product->barcode;
		$product->data['fk_product_type'] = 0; // Type "Produit" ou "Service".
		$product->data['volume']          = $doli_product->volume;
		$product->data['length']          = $doli_product->length;
		$product->data['width']           = $doli_product->width;
		$product->data['height']          = $doli_product->height;
		$product->data['weight']          = $doli_product->weight;
		$product->data['status']          = 'publish';

		Product_Class::g()->update( $product->data );
	}
}

Doli_Products_Class::g();
