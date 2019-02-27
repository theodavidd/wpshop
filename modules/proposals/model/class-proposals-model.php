<?php
/**
 * Classe définisant le modèle d'un proposal WPSHOP.
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
 * Class proposal model.
 */
class Proposals_Model extends \eoxia\Post_Model {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param Order_Class $object     Les données de l'objet.
	 * @param string        $req_method La méthode de la requête.
	 */
	public function __construct( $object, $req_method = null ) {

		$this->schema['external_id'] = array(
			'type'        => 'integer',
			'meta_type'   => 'single',
			'field'       => 'external_id',
			'since'       => '2.0.0',
			'description' => 'L\'ID du customer (dolibarr). Relation avec dolibarr.',
		);

		$this->schema['external_status'] = array(
			'type'        => 'integer',
			'meta_type'   => 'single',
			'field'       => 'external_status',
			'since'       => '2.0.0',
			'description' => 'Le status de la proposition commerciale dans dolibarr. Relation avec dolibarr.',
		);

		$this->schema['datec'] = array(
			'type'        => 'wpeo_date',
			'meta_type'   => 'single',
			'field'       => 'datec',
			'since'       => '2.0.0',
			'description' => 'Date de création de la commande. Relation avec dolibarr',
			'context'     => array( 'GET' ),
		);

		$this->schema['total_ht'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => 'total_ht',
			'since'       => '2.0.0',
			'description' => '',
		);

		$this->schema['total_ttc'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => 'total_ttc',
			'default'     => 0.00000000,
			'since'       => '2.0.0',
			'description' => 'Prix total de la commande, toutes taxes comprises (float). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['lines'] = array(
			'type'      => 'array',
			'meta_type' => 'single',
			'field'     => '_lines',
			'default'   => array(),
		);

		$this->schema['payment_method'] = array(
			'type'      => 'string',
			'meta_type' => 'single',
			'field'     => 'payment_method',
			'default'   => '',
		);

		parent::__construct( $object, $req_method );
	}
}
