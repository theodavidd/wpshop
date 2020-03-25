<?php
/**
 * Les fonctions principales des synchronisations.
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
 *  Doli Synchro Class.
 */
class Doli_Sync extends \eoxia\Singleton_Util {

	/**
	 * Tableau contenant les synchronisations à effectuer.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $sync_infos = array();

	/**
	 * Limites de synchronisation par requête.
	 *
	 * @since 2.0.0
	 *
	 * @var integer
	 */
	public $limit_entries_by_request = 50;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->sync_infos = array(
			'third-parties' => array(
				'title'      => __( 'Third parties', 'wpshop' ),
				'action'     => 'sync_third_parties',
				'nonce'      => 'sync_third_parties',
				'endpoint'   => 'thirdparties',
				'wp_class'   => '\wpshop\\Third_Parties',
				'doli_class' => '\wpshop\\Doli_Third_Parties',
				'doli_type'  => 'third_party',
			),
			'contacts'      => array(
				'title'      => __( 'Contacts', 'wpshop' ),
				'action'     => 'sync_contacts',
				'nonce'      => 'sync_contacts',
				'endpoint'   => 'contacts',
				'wp_class'   => '\wpshop\\Contact',
				'doli_class' => '\wpshop\\Doli_Contact',
				'doli_type'  => 'contact',
			),
			'products'      => array(
				'title'      => __( 'Products', 'wpshop' ),
				'action'     => 'sync_products',
				'nonce'      => 'sync_products',
				'endpoint'   => 'wpshop/productsonweb',
				'wp_class'   => '\wpshop\\Product',
				'doli_class' => '\wpshop\\Doli_Products',
				'doli_type'  => 'product',
			),
			'proposals'     => array(
				'title'      => __( 'Proposals', 'wpshop' ),
				'action'     => 'sync_proposals',
				'nonce'      => 'sync_proposals',
				'endpoint'   => 'proposals',
				'wp_class'   => '\wpshop\\Proposals',
				'doli_class' => '\wpshop\\Doli_Proposals',
				'doli_type'  => 'propal',
			),
		);
	}

	/**
	 * Get sync info by type.
	 *
	 * @param $type
	 *
	 * @todo: Mal nommé
	 *
	 * @return array
	 */
	public function get_sync_infos( $type ) {
		return $this->sync_infos[ $type ];
	}

	/**
	 * Compte le nombre d'entrée.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $sync_info Les informations de synchro.
	 *
	 * @return array            Les informations de synchro avec le nombre
	 * total d'élement en plus.
	 */
	public function count_entries( $sync_info ) {
		if ( ! empty( $sync_info['endpoint'] ) ) {
			$args = array(
				'limit' => $this->limit_entries_by_request,
				'page'  => $sync_info['page'],
			);

			$args = implode( '&', array_map( function( $v, $k ) {
				return $k . '=' . $v;
			}, $args, array_keys( $args ) ) );

			$tmp = Request_Util::get( $sync_info['endpoint'] . '?' . $args );

			if ( $tmp ) {
				$count                      = count( $tmp );
				$sync_info['total_number'] += count( $tmp );

				if ( $count >= $this->limit_entries_by_request ) {
					$sync_info['page']++;
					$sync_info = $this->count_entries( $sync_info );
				}
			}
		}

		return $sync_info;
	}

	/**
	 * Associes et synchronises les données d'une entité.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $from     De quel coté la synchro ? WordPress ou Dolibarr.
	 * @param  integer $wp_id    L'ID de l'entitée sur WordPress.
	 * @param  integer $entry_id L'ID de l'entitée sur Dolibarr.
	 *
	 * @return array             Les informations de la société.
	 */
	public function associate_and_synchronize( $from, $wp_id, $entry_id ) {
		$post_type = get_post_type( $wp_id );

		$wp_error = new \WP_Error();
		$messages = array();

		switch ( $post_type ) {
			case 'wps-third-party':
				if ( 'dolibarr' === $from ) {
					$doli_third_party = Request_Util::get( 'thirdparties/' . $entry_id );
					$wp_third_party   = Third_Party::g()->get( array( 'id' => $wp_id ), true );

					Doli_Third_Parties::g()->doli_to_wp( $doli_third_party, $wp_third_party );

					// translators: Erase date for the third party <strong>Eoxia</strong> with the <strong>dolibarr</strong> data.
					$messages[] = sprintf( __( 'Erase data for the third party <strong>%s</strong> with the <strong>dolibarr</strong> data', 'wpshop' ), $wp_third_party->data['title'] );

					$messages = array_merge( $messages, Third_Party::g()->dessociate_contact( $wp_third_party ) );
				}

				$wp_object = $wp_third_party;
				break;
			case 'wps-product':
				if ( 'dolibarr' === $from ) {
					$doli_product = Request_Util::get( 'products/' . $entry_id );
					$wp_product   = Product::g()->get( array( 'id' => $wp_id ), true );

					$wp_product = Doli_Products::g()->doli_to_wp( $doli_product, $wp_product );

					Request_Util::post( 'wpshop/object', array(
						'wp_id'   => $wp_id,
						'doli_id' => $entry_id,
					) );
				}

				$wp_object = $wp_product;
				break;
			case 'wps-proposal':
				if ( 'dolibarr' === $from ) {
					$doli_proposal = Request_Util::get( 'proposals/' . $entry_id );
					$wp_proposal   = Proposals::g()->get( array( 'id' => $wp_id ), true );

					Doli_Proposals::g()->doli_to_wp( $doli_proposal, $wp_proposal );
				}

				$wp_object = $wp_proposal;
				break;
			default:
				break;
		}

		return array(
			'messages'  => $messages,
			'wp_error'  => $wp_error,
			'wp_object' => $wp_object,
		);
	}

	/**
	 * Vérifie la SHA256 entre une entité WPShop et une entité Dolibarr.
	 *
	 * @todo: Expliquer to_type
	 *
	 * @since 2.0.0
	 *
	 * @param integer $id L'ID de l'entité WP.
	 */
	public function check_status( $id ) {
		$external_id = get_post_meta( $id, '_external_id', true );
		$sha_256     = get_post_meta( $id, '_sync_sha_256', true );
		$to_type     = '';

		switch ( get_post_type( $id ) ) {
			case Product::g()->get_type():
				$to_type = 'product';
				break;
			case Doli_Order::g()->get_type():
				$to_type = 'order';
				break;
			case Proposals::g()->get_type():
				$to_type = 'propal';
				break;
			case Third_Party::g()->get_type():
				$to_type = 'third_party';
				break;
		}

		$data = array(
			'doli_id' => $external_id,
			'wp_id'   => $id,
			'type'    => $to_type,
			'sha256'  => $sha_256,
		);

		$url      = 'wpshop/object/status?' . http_build_query( $data );
		$response = Request_Util::get( $url );

		return $response;
	}

	public function display_sync_status( $object, $type, $load_erp_status = true ) {
		$status_color    = 'grey';
		$can_sync        = false;
		$message_tooltip = __( 'Looking for sync status', 'wpshop' );

		if ( $load_erp_status ) {
			if ( empty( $object->data['external_id'] ) ) {
				$status_color           = 'red';
				$message_tooltip = __( 'No associated to an ERP Entity', 'wpshop' );
			} else {
				$response = Doli_Sync::g()->check_status( $object->data['id'] );

				if ( ! $response->status ) {
					$status_color = 'red';
				} else {
					// @todo: Do Const for status_code.
					switch ( $response->status_code ) {
						case '0x0':
							$status_color = 'green';
							$can_sync = true;
							break;
						case '0x1':
							$status_color = 'red';
							break;
						case '0x2':
							$status_color = 'orange';
							$can_sync = true;
							break;
					}
				}

				$message_tooltip = $response->status_message;
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'doli-sync', 'sync-item', array(
			'object'          => $object,
			'type'            => $type,
			'status_color'    => $status_color,
			'message_tooltip' => $message_tooltip,
			'can_sync'        => $can_sync,
		) );
	}

//	public function
}

Doli_Sync::g();
