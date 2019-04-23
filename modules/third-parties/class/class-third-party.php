<?php
/**
 * Les fonctions principales des tiers.
 *
 * Le controlleur du modèle Third_Party_Model.
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
 * Third Party class.
 */
class Third_Party extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Third_Party_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-third-party';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'third-party';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'third-party';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	/**
	 * Récupères la liste des produits et appel la vue "list" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$current_page = isset( $_GET['current_page'] ) ? $_GET['current_page'] : 1;

		$args = array(
			'orderby'        => 'ID',
			'offset'         => ( $current_page - 1 ) * 25,
			'posts_per_page' => 25,
		);

		if ( ! empty( $_GET['s'] ) ) {
			$args['s'] = $_GET['s'];
		}

		$third_parties = $this->get( $args );

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'list', array(
			'third_parties' => $third_parties,
			'doli_url'      => $dolibarr_option['dolibarr_url'],
		) );
	}

	/**
	 * Affiches les trois dernières actions commerciales du tier.
	 *
	 * @since 2.0.0
	 *
	 * @param  Third_Party $third_party Les données du tier.
	 */
	public function display_commercial( $third_party ) {
		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		$order = Doli_Order::g()->get( array(
			'post_parent'    => $third_party['id'],
			'posts_per_page' => 1,
		), true );

		$propal = Proposals::g()->get( array(
			'post_parent'    => $third_party['id'],
			'posts_per_page' => 1,
		), true );

		$invoice = Doli_Invoice::g()->get( array(
			'meta_key'       => '_third_party_id',
			'meta_value'     => $third_party['id'],
			'posts_per_page' => 1,
		), true );

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'commercial', array(
			'doli_url' => $dolibarr_option['dolibarr_url'],
			'order'    => $order,
			'propal'   => $propal,
			'invoice'  => $invoice,
		) );
	}
}

Third_Party::g();
