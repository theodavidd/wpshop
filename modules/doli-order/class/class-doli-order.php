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
		$proposals = $this->get( array(
			'orderby'  => 'meta_value',
			'meta_key' => 'date_commande'
		));

		if ( ! empty( $proposals ) ) {
			foreach ( $proposals as &$element ) {
				$element->data['tier']  = Third_Party_Class::g()->get( array( 'id' => $element->data['parent_id'] ), true );
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'list', array(
			'proposals' => $proposals,
		) );
	}

	public function doli_to_wp( $doli_order, $wp_order ) {
		$wp_order->data['external_id']    = (int) $doli_order->id;
		$wp_order->data['title']          = $doli_order->ref;
		$wp_order->data['total_ht']       = $doli_order->total_ht;
		$wp_order->data['total_ttc']      = $doli_order->total_ttc;
		$wp_order->data['billed']         = (int) $doli_order->billed;
		$wp_order->data['lines']          = $doli_order->lines;
		$wp_order->data['date_commande']  = date( 'Y-m-d H:i:s', $doli_order->date_commande );
		$wp_order->data['datec']          = date( 'Y-m-d H:i:s', $doli_order->date_creation );
		$wp_order->data['parent_id']      = Doli_Third_Party_Class::g()->get_wp_id_by_doli_id( $doli_order->socid );
		$wp_order->data['payment_method'] = Doli_Payment::g()->convert_to_wp( $doli_order->mode_reglement_code );

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

		$wp_order->data['status'] = $status;

		return Orders_Class::g()->update( $wp_order->data );
	}

	public function get_wp_id_by_doli_id( $doli_id ) {
		$order = Orders_Class::g()->get( array(
			'meta_key'   => 'external_id',
			'meta_value' => $doli_id,
		), true );


		if ( is_array( $order ) ) {
			var_dump( $doli_id );
			echo '<pre>'; print_r( $order ); echo '</pre>';
		}

		return $order->data['id'];
	}
}

Orders_Class::g();
