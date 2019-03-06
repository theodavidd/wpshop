<?php
/**
 * Gestion des proposals.
 *
 * @author Eoxia <dev@eoxia.com>
 * @since 2.0.0
 * @version 2.0.0
 * @copyright 2018 Eoxia
 * @package wpshop
 */

namespace wpshop;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gestion des Proposals CRUD.
 */
class Proposals_Class extends \eoxia\Post_Class {

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

	public function save( $third_party, $contact ) {
		do_action( 'wps_save_proposal', $third_party, $contact );

		return $this->get( array( 'id' => Class_Cart_Session::g()->external_data['proposal_id'] ), true );
	}

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

Proposals_Class::g();
