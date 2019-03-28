<?php
/**
 * Les fonctions principales pour les status de dolibarr.
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
 * Doli Statut Class.
 */
class Doli_Statut extends \eoxia\Singleton_Util {

	public $status = array();

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->status = array(
			'wps-proposal' => array(
				'draft'         => array(
					'text'  => __( 'Draft', 'wpshop' ),
					'class' => 'status-grey',
				),
				'publish'       => array(
					'text' => __( 'Validate', 'wpshop' ),
					'class' => 'status-orange',
				),
				'wps-accepted'  => array(
					'text' => __( 'Signed', 'wpshop' ),
					'class' => 'status-orange',
				),
				'wps-refused'   => array(
					'text' => __( 'Not signed', 'wpshop' ),
					'class' => 'status-red',
				),
				'wps-billed'    => array(
					'text' => __( 'Billed', 'wpshop' ),
					'class' => 'status-green',
				),
				'wps-delivered' => array(
					'text' => __( 'Delivered', 'wpshop' ),
					'class' => 'status-green',
				),
			),
			'wps-order' => array(
				'draft'         => array(
					'text' => __( 'Draft', 'wpshop' ),
					'class' => 'status-grey',
				),
				'publish'       => array(
					'text' => __( 'Validate', 'wpshop' ),
					'class' => 'status-orange',
				),
				'wps-accepted'  => array(
					'text' => __( 'Signed', 'wpshop' ),
					'class' => 'status-orange',
				),
				'wps-refused'   => array(
					'text' => __( 'Not signed', 'wpshop' ),
					'class' => 'status-red',
				),
				'wps-billed'    => array(
					'text' => __( 'Billed', 'wpshop' ),
					'class' => 'status-green',
				),
				'wps-delivered' => array(
					'text' => __( 'Delivered', 'wpshop' ),
					'class' => 'status-green',
				),
			),
			'wps-doli-invoice' => array(
				'draft'         => array(
					'text' => __( 'Draft', 'wpshop' ),
					'class' => 'status-grey',
				),
				'publish'       => array(
					'text' => __( 'Not paid', 'wpshop' ),
					'class' => 'status-orange',
				),
				'wps-billed'    => array(
					'text' => __( 'Billed', 'wpshop' ),
					'class' => 'status-green',
				),
				'wps-abandoned' => array(
					'text' => __( 'Abandoned', 'wpshop' ),
					'class' => 'status-red',
				),
			)
		);
	}

	/**
	 * Affiches un status lisible selon le status de l'objet
	 *
	 * @since 2.0.0
	 *
	 * @param  mixed $object Les données de l'objet.
	 *
	 * @return string        Le status lisible.
	 */
	public function display_status( $object ) {
		$status = null;

		if ( $this->status[ $object->data['type'] ][ $object->data['status'] ] ) {
			$status = $this->status[ $object->data['type'] ][ $object->data['status'] ];
		}

		if ( $status ) {
			\eoxia\View_Util::exec( 'wpshop', 'doli-statut', 'item', array(
				'text' => $status['text'],
				'class' => $status['class'],
			) );
		}
	}
}

Doli_Statut::g();
