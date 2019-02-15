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
class Doli_Synchro extends \eoxia\Singleton_Util {
	public $sync_infos = array();

	protected function construct() {
		$this->sync_infos = array(
			'third-parties' => array(
				'title'    => __( 'Third parties', 'wpshop' ),
				'action'   => 'sync_third_parties',
				'endpoint' => 'thirdparties',
			),
			'contacts' => array(
				'title'    => __( 'Contacts', 'wpshop' ),
				'action'   => 'sync_contacts',
				'endpoint' => 'contacts',
			),
			'products' => array(
				'title'     => __( 'Products', 'wpshop' ),
				'action'    => 'sync_products',
				'endpoints' => 'products',
			),
			'orders' => array(
				'title'     => __( 'Orders', 'wpshop' ),
				'action'    => 'sync_orders',
				'endpoints' => 'orders',
			),
		);
	}
}

Doli_Synchro::g();
