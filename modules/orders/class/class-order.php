<?php
/**
 * Les fonctions principales des devis.
 *
 * Le controlleur du modèle order_Model.
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
* Handle order
*/
class Order_Class extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Order_Model';

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
	 * Récupères la liste des devis et appel la vue "list" du module "order".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$orders = $this->get();

		\eoxia\View_Util::exec( 'wpshop', 'orders', 'list', array(
			'orders' => $orders,
		) );
	}

	public function save( $third_party, $contact ) {
		do_action( 'wps_save_order', $third_party, $contact );
	}

	public function sync( $third_party_id, $proposal_data ) {
		return Order_Class::g()->update( array(
			'external_id'       => (int) $proposal_data->id,
			'parent_id'         => $third_party_id,
			'ref'               => $proposal_data->ref,
			'order_date'        => $proposal_data->datec,
			'order_currency'    => 'EUR',
			'order_grand_total' => $proposal_data->total_ht,
			'order_total_ttc'   => $proposal_data->total_ttc,
		) );
	}
}

Order_Class::g();
