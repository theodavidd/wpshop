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
		$wp_proposal->data['external_id'] = (int) $doli_proposal->id;
		$wp_proposal->data['title']       = $doli_proposal->ref;
		$wp_proposal->data['total_ht']    = $doli_proposal->total_ht;
		$wp_proposal->data['total_ttc']   = $doli_proposal->total_ttc;
		$wp_proposal->data['lines']       = $doli_proposal->lines;
		$wp_proposal->data['datec']       = date( 'Y-m-d h:i:s', $doli_proposal->datec );
		$wp_proposal->data['parent_id']   = Doli_Third_Party_Class::g()->get_wp_id_by_doli_id( $doli_proposal->socid );
		$wp_proposal->data['status']      = 'publish';

		Proposals_Class::g()->update( $wp_proposal->data );
	}

	public function wp_to_doli( $wp_proposal ) {
		$third_party_doli_id = get_post_meta( $wp_proposal->data['parent_id'], '_external_id', true );
		$doli_proposal_id = 0;

		if ( ! empty( $wp_proposal->data['external_id'] ) ) {
			$doli_proposal_id = $wp_proposal->data['external_id'];
		} else {

			$doli_proposal_id = Request_Util::post( 'proposals', array(
				'socid'             => $third_party_doli_id,
				'date'              => current_time( 'timestamp' ),
				'mode_reglement_id' => ! empty( $wp_proposal->data['payment_method'] ) ? Doli_Payment::g()->convert_to_doli_id( $wp_proposal->data['payment_method'] ) : '',
			) );

			if ( ! empty( $wp_proposal->data['lines'] ) ) {
				foreach ( $wp_proposal->data['lines'] as $content ) {
					$proposal = Request_Util::post( 'proposals/' . $doli_proposal_id . '/lines', array(
						'desc'                    => $content['content'],
						'fk_product'              => $content['external_id'],
						'product_type'            => 1,
						'qty'                     => $content['qty'],
						'tva_tx'                  => $content['tva_tx'],
						'subprice'                => $content['price'],
						'remice_percent'          => 0,
						'rang'                    => 1,
						'total_ht'                => $content['price'],
						'total_tva'               => 0,
						'total_ttc'               => $content['price_ttc'],
						'product_label'           => $content['title'],
						'multicurrency_code'      => 'EUR',
						'multicurrency_subprice'  => $content['price'],
						'multicurrency_total_ht'  => $content['price'],
						'multicurrency_total_tva' => 0,
						'multicurrency_total_ttc' => $content['price_ttc'],
					) );
				}
			}

		}

		return $doli_proposal_id;
	}
}

Doli_Proposals_Class::g();
