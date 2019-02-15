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

	public function update_third_party( $third_party_id ) {

		$proposal_id = Class_Cart_Session::g()->external_data['proposal_id'];

		$proposal = Request_Util::put( 'proposals/' . $proposal_id, array(
			'socid' => $third_party_id,
		) );

		return true;
	}

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

	public function save( $third_party, $contact ) {
		do_action( 'wps_save_proposal', $third_party, $contact );

		return $this->get( array( 'id' => Class_Cart_Session::g()->external_data['proposal_id'] ), true );
	}

	public function sync( $third_party_id, $proposal_data ) {
		$products = array();

		if ( ! empty( $proposal_data->lines ) ) {
			foreach ( $proposal_data->lines as $line ) {
				$products[] = array(
					'external_id'     => $line->id,
					'ref'             => $line->ref,
					'title'           => $line->product_label,
					'content'         => $line->product_desc,
					'price'           => $line->total_ht,
					'price_ttc'       => $line->total_ttc,
					'tva_tx'          => $line->tva_tx,
					'fk_product_type' => 0, // Type "Produit" ou "Service".
				);
			}
		}

		$proposal_data = array(
			'external_id' => (int) $proposal_data->id,
			'parent_id'   => $third_party_id,
			'title'       => $proposal_data->ref,
			'total_ht'    => $proposal_data->total_ht,
			'total_ttc'   => $proposal_data->total_ttc,
			'products'    => $products,
		);

		return $this->update( $proposal_data );
	}
}

Proposals_Class::g();
