<?php
/**
 * Gestion des devis avec Dolibarr.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
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
 * Doli Proposals Class.
 */
class Doli_Proposals extends \eoxia\Singleton_Util {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Synchronise Dolibarr vers WPshop.
	 *
	 * @since 2.0.0
	 *
	 * @param  stdClass       $doli_proposal Les données de dolibarr.
	 *
	 * @param  Proposal_Model $wp_proposal   Les données de WP.
	 */
	public function doli_to_wp( $doli_proposal, $wp_proposal ) {
		if ( is_object( $wp_proposal ) ) {
			$wp_proposal->data['external_id']    = (int) $doli_proposal->id;
			$wp_proposal->data['title']          = $doli_proposal->ref;
			$wp_proposal->data['total_ht']       = $doli_proposal->total_ht;
			$wp_proposal->data['total_ttc']      = $doli_proposal->total_ttc;
			$wp_proposal->data['billed']         = 0;

			// @todo: Les trois valeurs ci-dessous ne corresponde pas au valeur de Dolibarr elle sont adapté pour répondre au besoin de WPshop.
			// @todo: Détailler les besoins de WPshop et enlever ce fonctionnement pour le coup.
			$wp_proposal->data['datec']          = date( 'Y-m-d H:i:s', $doli_proposal->datec );
			$wp_proposal->data['parent_id']      = Doli_Third_Parties::g()->get_wp_id_by_doli_id( $doli_proposal->socid );
			$wp_proposal->data['payment_method'] = ( null === $doli_proposal->mode_reglement_code ) ? $wp_proposal->data['payment_method'] : Doli_Payment::g()->convert_to_wp( $doli_proposal->mode_reglement_code );

			$wp_proposal->data['lines'] = null;

			if ( ! empty( $doli_proposal->lines ) ) {
				$wp_proposal->data['lines'] = array();
				foreach ( $doli_proposal->lines as $line ) {
					$line_data = array(
						'fk_proposal' => $doli_proposal->id,
						'fk_product'  => $line->fk_product,
						'qty'         => $line->qty,
						'total_tva'   => $line->total_tva,
						'total_ht'    => $line->total_ht,
						'total_ttc'   => $line->total_ttc,
						'libelle'     => ! empty( $line->libelle ) ? $line->libelle : $line->desc,
						'tva_tx'      => $line->tva_tx,
						'subprice'    => $line->price,
						'rowid'       => $line->rowid,
					);

					$wp_proposal->data['lines'][] = $line_data;
				}
			}

			// @todo: Ajouter une ENUMERATION pour mieux comprendre les chiffres dans ce switch.
			switch ( $doli_proposal->statut ) {
				case -1:
					$status = 'wps-canceled';
					break;
				case 0:
					$status = 'draft';
					break;
				case 1:
					$status = 'publish';
					break;
				case 2:
					$status = 'wps-accepted';
					break;
				case 3:
					$status = 'wps-refused';
					break;
				case 4:
					$status                      = 'publish';
					$wp_proposal->data['billed'] = 1;
					break;
				default:
					$status = 'publish';
					break;
			}

			$wp_proposal->data['status'] = $status;
			$wp_proposal                 = Proposals::g()->update( $wp_proposal->data );

			// Generate SHA
			$data_sha = array();

			$data_sha['doli_id']   = (int) $doli_proposal->id;
			$data_sha['wp_id']     = $wp_proposal->data['id'];
			$data_sha['ref']       = $wp_proposal->data['title'];
			$data_sha['total_ht']  = $wp_proposal->data['total_ht'];
			$data_sha['total_ttc'] = $wp_proposal->data['total_ttc'];

			// @todo: Ajouter les lignes pour le SHA256.
			update_post_meta( $wp_proposal->data['id'], '_sync_sha_256', hash( 'sha256', implode( ',', $data_sha ) ) );

			return $wp_proposal;
		}
	}

	/**
	 * Synchronise WPshop vers Dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  Proposal_Model $wp_proposal Les données venant de WP.
	 *
	 * @return integer                     L'ID du devis venant de Dolibarr.
	 */
	public function wp_to_doli( $wp_proposal ) {
		$third_party_doli_id = get_post_meta( $wp_proposal->data['parent_id'], '_external_id', true );
		$doli_proposal_id    = 0;

		if ( ! empty( $wp_proposal->data['external_id'] ) ) {
			$doli_proposal_id = $wp_proposal->data['external_id'];
		} else {
			$proposal_data = array(
				'socid'             => $third_party_doli_id,
				'date'              => current_time( 'timestamp' ),
				'type'              => 'propal',
				'wp_id'             => $wp_proposal->data['id'],
			);

			if ( ! empty( $wp_proposal->data['payment_method'] ) ) {
				$proposal_data['mode_reglement_id'] = Doli_Payment::g()->convert_to_doli_id( $wp_proposal->data['payment_method'] );
			}

			\eoxia\LOG_Util::log( sprintf( 'Dolibarr call POST /wpshop/object with data %s', json_encode( $proposal_data ) ), 'wpshop2' );
			$doli_proposal = Request_Util::post( 'wpshop/object', $proposal_data );

			\eoxia\LOG_Util::log( sprintf( 'Checkout: Create proposal on dolibarr. Return response %s', json_encode( $doli_proposal ) ), 'wpshop2' );
			$doli_proposal_id = $doli_proposal->id;

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

		update_post_meta( $wp_proposal->data['id'], '_external_id', $doli_proposal_id );

		return $doli_proposal_id;
	}
}

Doli_Proposals::g();
