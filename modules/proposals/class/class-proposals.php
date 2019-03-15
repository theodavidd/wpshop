<?php
/**
 * Classe gérant les devis.
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
 * Proposals Class.
 */
class Proposals extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Proposals_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-proposal';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'proposal';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'proposal';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	/**
	 * Le nom du post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $post_type_name = 'Proposals';

	/**
	 * Récupères la liste des devis et appel la vue "list" du module "order".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$proposals = $this->get();

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'list', array(
			'proposals' => $proposals,
		) );
	}

	/**
	 * Récupères la dernière ref des devis.
	 *
	 * @since 2.0.0
	 *
	 * @return string La référence.
	 */
	public function get_last_ref() {
		global $wpdb;

		$last_ref = $wpdb->get_var( "
			SELECT meta_value FROM $wpdb->postmeta AS PM
				JOIN $wpdb->posts AS P ON PM.post_id=P.ID

			WHERE PM.meta_key='_ref'
				AND P.post_type='wps-proposal'
		" );

		return $last_ref;
	}
}

Proposals::g();
