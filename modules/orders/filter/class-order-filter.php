<?php
/**
 * Gestion des filtres des produits.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Filters
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Filters of product module.
 */
class Order_Filter {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-order_register_post_type_args', array( $this, 'callback_register_post_type_args' ) );
	}

	/**
	 * Permet d'ajouter l'argument public à true pour le register_post_type de EOModel.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $args Les arguments pour le register_post_type.
	 * @return array       Les arguments pour le register_post_type avec public à true.
	 */
	public function callback_register_post_type_args( $args ) {
		$args['public']               = true;
		$args['has_archive']          = true;
		$args['show_ui']              = true;
		$args['show_in_nav_menus']    = false;
		$args['show_in_menu']         = false;
		$args['show_in_rest']         = true;
		return $args;
	}
}

new Order_Filter();
