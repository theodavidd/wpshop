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
class Doli_Proposals_Class extends \eoxia\Post_Class {

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

		\eoxia\View_Util::exec( 'wpshop', 'doli-proposals', 'list', array(
			'proposals' => $proposals,
		) );
	}

	public function doli_to_wp( $doli_proposal, $wp_proposal ) {
		$wp_proposal->data['external_id']   = (int) $doli_proposal->id;
		$wp_proposal->data['title']         = $doli_proposal->ref;
		$wp_proposal->data['total_ht']      = $doli_proposal->total_ht;
		$wp_proposal->data['total_ttc']     = $doli_proposal->total_ttc;
		$wp_proposal->data['lines']         = $doli_proposal->lines;
		$wp_proposal->data['datec']         = date( 'Y-m-d h:i:s', $doli_proposal->datec );
		$wp_proposal->data['parent_id']     = $third_party_id;
		$wp_proposal->data['status']        = 'publish';

		return Proposals_Class::g()->update( $wp_proposal->data );
	}

	public function update_third_party( $third_party_id ) {
		$proposal_id = Class_Cart_Session::g()->external_data['proposal_id'];

		$proposal = Request_Util::put( 'proposals/' . $proposal_id, array(
			'socid' => $third_party_id,
		) );

		return true;
	}
}

Doli_Proposals_Class::g();
