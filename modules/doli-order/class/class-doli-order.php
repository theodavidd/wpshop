<?php
/**
 * Fonction principales pour les commandes avec DolibarR.
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
 * Doli Order Class.
 */
class Doli_Order extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Doli_Order_Model';

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

	/**
	 * Nom du post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $post_type_name = 'Orders';

	/**
	 * Récupères la liste des devis et appel la vue "list" du module "order".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$orders = $this->get( array(
			'orderby'  => 'meta_value',
			'meta_key' => 'datec',
		) );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as &$element ) {
				$element->data['tier'] = Third_Party::g()->get( array( 'id' => $element->data['parent_id'] ), true );
			}
		}

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		\eoxia\View_Util::exec( 'wpshop', 'doli-order', 'list', array(
			'orders'   => $orders,
			'doli_url' => $dolibarr_option['dolibarr_url'],
		) );
	}

	/**
	 * Synchronisation depuis Dolibarr vers WP.
	 *
	 * @since 2.0.0
	 *
	 * @param  stdClass    $doli_order Les données venant de dolibarr.
	 * @param  Order_Model $wp_order   Les données de WP.
	 *
	 * @return Order_Model             Les données de WP avec ceux de dolibarr.
	 */
	public function doli_to_wp( $doli_order, $wp_order ) {
		if ( is_object( $wp_order ) ) {
			$wp_order->data['external_id']    = (int) $doli_order->id;
			$wp_order->data['title']          = $doli_order->ref;
			$wp_order->data['total_ht']       = $doli_order->total_ht;
			$wp_order->data['total_ttc']      = $doli_order->total_ttc;
			$wp_order->data['billed']         = (int) $doli_order->billed;
			$wp_order->data['lines']          = $doli_order->lines;
			$wp_order->data['date_commande']  = date( 'Y-m-d H:i:s', $doli_order->date_commande );
			$wp_order->data['datec']          = date( 'Y-m-d H:i:s', $doli_order->date_creation );
			$wp_order->data['parent_id']      = Doli_Third_Parties::g()->get_wp_id_by_doli_id( $doli_order->socid );
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

					if ( $wp_order->data['billed'] ) {
						$status = 'wps-billed';
					}
					break;
				default:
					$status = 'publish';
					break;
			}

			$wp_order->data['status']            = $status;
			$wp_order->data['date_last_synchro'] = current_time( 'mysql');

			return Doli_Order::g()->update( $wp_order->data );
		}
	}

	/**
	 * Récupères l'ID WP selon l'ID de dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $doli_id L'ID de dolibarr.
	 * @return integer          L'ID de WP.
	 */
	public function get_wp_id_by_doli_id( $doli_id ) {
		$order = Doli_Order::g()->get( array(
			'meta_key'   => '_external_id',
			'meta_value' => $doli_id,
		), true );

		return $order->data['id'];
	}
}

Doli_Order::g();
