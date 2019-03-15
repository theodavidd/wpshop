<?php
/**
 * Classe définisant le modèle d'un produit WPshop.
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
 * Product Model Class.
 */
class Product_Model extends \eoxia\Post_Model {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param Product_Class $object     Les données de l'objet.
	 * @param string        $req_method La méthode de la requête.
	 */
	public function __construct( $object, $req_method = null ) {
		$this->schema['external_id'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_external_id',
			'since'       => '2.0.0',
			'description' => 'L\'ID provenant de dolibarr',
		);

		$this->schema['ref'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_ref',
			'since'       => '2.0.0',
			'description' => 'La référence du produit (varchar(128)). Ne peut être NULL. Clé unique. Aucune valeur par défaut. Relation avec dolibarr.',
		);

		$this->schema['price'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_price',
			'default'     => 0.00000000,
			'since'       => '2.0.0',
			'description' => 'Le prix HT du produit (double(24,8)). Peut être NULL. Valeur par défaut 0.00000000',
		);

		$this->schema['price_ttc'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_price_ttc',
			'default'     => 0.00000000,
			'since'       => '2.0.0',
			'description' => 'Le prix TTC du produit (double(24,8)). Peut être NULL. Valeur par défaut 0.00000000',
		);

		$this->schema['tva_tx'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_tva_tx',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'Le taux de TVA du produit (double(6,3)). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['barcode'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_barcode',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'Le code-barres du produit (varchar(180)). Peut être NULL. Valeur par défaut NULL.',
		);

		// A verifier.
		$this->schema['fk_product_type'] = array(
			'type'        => 'integer',
			'meta_type'   => 'single',
			'field'       => '_fk_product_type',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'Le type du produit (int(11)). Peut être NULL. Valeur par défaut 0. Valeur attendu: 0 (Produit) ou 1 (Service).',
		);

		$this->schema['volume'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_volume',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'Le volume en m³ (float). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['length'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_length',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'La longueur en m (float). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['width'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_width',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'La largeur en m (float). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['height'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_height',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'La hauteur en m (float). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['weight'] = array(
			'type'        => 'float',
			'meta_type'   => 'single',
			'field'       => '_weight',
			'default'     => null,
			'since'       => '2.0.0',
			'description' => 'Le poids en Kg (float). Peut être NULL. Valeur par défaut NULL.',
		);

		parent::__construct( $object, $req_method );
	}
}
