<?php
/**
 * Les filtres relatives aux commandes de dolibarr.
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
 * Doli Order Filter Class.
 */
class Doli_Order_Filter {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-order_register_post_type_args', array( $this, 'callback_register_post_type_args' ) );
	}

	/**
	 * Ajoutes des données supplémentaires pour le register_post_type.
	 *
	 * @since 2.0.0
	 *
	 * @return array Les données supplementaires.
	 */
	public function callback_register_post_type_args() {
		$labels = array(
			'name'               => _x( 'Orders', 'post type general name', 'wpshop' ),
			'singular_name'      => _x( 'Order', 'post type singular name', 'wpshop' ),
			'menu_name'          => _x( 'Orders', 'admin menu', 'wpshop' ),
			'name_admin_bar'     => _x( 'Order', 'add new on admin bar', 'wpshop' ),
			'add_new'            => _x( 'Add New', 'product', 'wpshop' ),
			'add_new_item'       => __( 'Add New Order', 'wpshop' ),
			'new_item'           => __( 'New Order', 'wpshop' ),
			'edit_item'          => __( 'Edit Order', 'wpshop' ),
			'view_item'          => __( 'View Order', 'wpshop' ),
			'all_items'          => __( 'All Orders', 'wpshop' ),
			'search_items'       => __( 'Search Orders', 'wpshop' ),
			'parent_item_colon'  => __( 'Parent Orders:', 'wpshop' ),
			'not_found'          => __( 'No products found.', 'wpshop' ),
			'not_found_in_trash' => __( 'No products found in Trash.', 'wpshop' ),
		);

		$args['labels']            = $labels;
		$args['supports']          = array( 'title' );
		$args['public']            = true;
		$args['has_archive']       = true;
		$args['show_ui']           = true;
		$args['show_in_nav_menus'] = false;
		$args['show_in_menu']      = false;

		return $args;
	}
}

new Doli_Order_Filter();
