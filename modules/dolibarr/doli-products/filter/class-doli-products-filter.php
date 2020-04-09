<?php
/**
 * Gestion des filtres des produits Dolibarr.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2020 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Filters
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Dolibarr Product Filter Class.
 */
class Doli_Product_Filter {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-product_after_get', array( $this, 'auto_sync' ), 10, 2 );
	}

	public function auto_sync( $object, $args_cb ) {
		if ( empty( $object->data['external_id'] ) ) {
			return $object;
		}

		$status = Doli_Sync::g()->check_status( $object->data['id'], $object->data['type'] );

		if ($status['status_code'] != '0x3') {
			return $object;
		}

		$doli_product = Request_Util::get( 'products/' . $object->data['external_id'] );
		$object       = Doli_Products::g()->doli_to_wp( $doli_product, $object );
		echo '<pre>';
		print_r($doli_product);
		echo '</pre>';
		exit;

		return $object;
	}

}

new Doli_Product_Filter();
