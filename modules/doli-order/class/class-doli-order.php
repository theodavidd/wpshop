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
class Orders_Class extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Orders_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-order';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'order';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'order';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	protected $post_type_name = 'Orders';

	/**
	 * Récupères la liste des devis et appel la vue "list" du module "order".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$proposals = $this->get();

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'list', array(
			'proposals' => $proposals,
		) );
	}

	public function sync( $order_data, $third_party_id ) {
		$order_data = array(
			'external_id'   => (int) $order_data->id,
			'parent_id'     => $third_party_id,
			'title'         => $order_data->ref,
			'date_commande' => date( 'Y-m-d h:i:s', $order_data->date_commande ),
			'total_ht'      => $order_data->total_ht,
			'total_ttc'     => $order_data->total_ttc,
			'lines'         => $order_data->lines,
		);

		return $this->update( $order_data );
	}

	public function synchro( $index, $limit ) {
		$doli_orders = Request_Util::get( 'orders?sortfield=t.rowid&sortorder=ASC&limit=' . $limit . '&page=' . ( $index / $limit ) );

		if ( ! empty( $doli_orders ) ) {
			foreach ( $doli_orders as $doli_order ) {
				$this->synchro_by_ids( $doli_order->id );
			}
		}

		return true;
	}

	public function synchro_by_ids( $ids ) {
		// Forces en tableau.
		$ids = ! is_array( $ids ) ? array( $ids ) : $ids;

		$sync_orders = array();

		if ( ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				$doli_order = Request_Util::get( 'orders/' . $id );

				$order = Orders_Class::g()->get( array(
					'meta_key'   => 'external_id',
					'meta_value' => $doli_order->id,
				), true );

				if ( empty( $order ) ) {
					$order = Orders_Class::g()->get( array( 'schema' => true ), true );
				}

				$order->data['external_id']   = (int) $doli_order->id;
				$order->data['title']         = $doli_order->ref;
				$order->data['total_ht']      = $doli_order->total_ht;
				$order->data['total_ttc']     = $doli_order->total_ttc;
				$order->data['billed']        = (int) $doli_order->billed;
				$order->data['lines']         = $doli_order->lines;
				$order->data['date_commande'] = date( 'Y-m-d h:i:s', $doli_order->date_commande );
				$order->data['parent_id']     = Third_Party_Class::g()->get_id_or_sync( $doli_order->socid );


				$status = '';

				switch ( $doli_order->statut ) {
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
						break;
					case 3:
						$status = 'wps-delivered';
						break;
					default:
						$status = 'publish';
						break;
				}

				$order->data['status']        = $status;

				$sync_orders[] = Orders_Class::g()->update( $order->data );
			}
		}

		return $sync_orders;
	}

	public function get_id_by_doli_id( $doli_id ) {
		$order = Orders_Class::g()->get( array(
			'meta_key'   => 'external_id',
			'meta_value' => $doli_id,
		), true );

		return $order->data['id'];
	}
}

Orders_Class::g();
