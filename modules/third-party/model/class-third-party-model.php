<?php
/**
 * Classe définisant le modèle d'un tier.
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
 * Class Third Party model.
 */
class Third_Party_Model extends \eoxia\Post_Model {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param Third_Party_Class $object     Les données de l'objet.
	 * @param string            $req_method La méthode de la requête.
	 */
	public function __construct( $object, $req_method = null ) {
		$this->schema['external_id'] = array(
			'type'        => 'integer',
			'meta_type'   => 'single',
			'field'       => '_external_id',
			'since'       => '2.0.0',
			'description' => 'L\'ID provenant de dolibarr',
		);

		$this->schema['forme_juridique'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_forme_juridique',
			'default'     => NULL,
			'since'       => '2.0.0',
			'description' => 'La forme juridique du tier (varchar(255)). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['code_fournisseur'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_code_fournisseur',
			'default'     => NULL,
			'since'       => '2.0.0',
			'description' => 'Le code fournisseur du tier (varchar(24)). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['address'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_address',
			'default'     => NULL,
			'since'       => '2.0.0',
			'description' => 'L\'adresse d\'un tier (varchar(255)). Peut être NULL. Valeur par défaut NULL.',
		);

		$this->schema['zip'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_zip',
			'default'     => NULL,
			'since'       => '2.0.0',
			'description' => 'Le code postal d\'un tier (varchar(25)). Peut être NULL. Valeur par défault NULL.',
		);

		$this->schema['town'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_town',
			'default'     => NULL,
			'since'       => '2.0.0',
			'description' => 'La ville d\'un tier (varchar(50)). Peut être NULL. Valeur par défault NULL.',
		);

		$this->schema['state'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_state',
			'default'     => NULL,
			'since'       => '2.0.0',
			'description' => 'Le département d\'un tier (varchar(50)). Peut être NULL. Valeur par défault NULL.',
		);

		$this->schema['country'] = array(
			'type'        => 'string',
			'meta_type'   => 'single',
			'field'       => '_country',
			'since'       => '2.0.0',
			'description' => 'Le pays d\'un tier (varchar(50)). Ne peut être NULL. Aucune valeur par défault.',
		);

		$this->schema['contact_ids'] = array(
			'type'        => 'array',
			'array_type'  => 'integer',
			'meta_type'   => 'single',
			'field'       => '_contact_ids',
			'since'       => '2.0.0',
			'description' => 'Association des contacts. Attends un tableau d\'ids des contacts. Aucune valeur par défaut.'

		);

		parent::__construct( $object, $req_method );
	}
}
