<?php
/**
 * Les fitres relatives au tiers.
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
 * Third Party Filter Class.
 */
class Third_Party_Filter {

	/**
	 * Initialise les filtres liées aux tiers.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-third-party_register_post_type_args', array( $this, 'callback_register_post_type_args' ) );
		add_filter( 'wps_third_party_metaboxes', array( $this, 'add_orders_and_billings_metaboxes' ), 9, 1 );
	}

	/**
	 * Ajoutes des paramètres supplémentaires pour le register post type.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function callback_register_post_type_args() {
		$labels = array(
			'name'               => _x( 'Third parties', 'post type general name', 'wpshop' ),
			'singular_name'      => _x( 'Third party', 'post type singular name', 'wpshop' ),
			'menu_name'          => _x( 'Third parties', 'admin menu', 'wpshop' ),
			'name_admin_bar'     => _x( 'Third party', 'add new on admin bar', 'wpshop' ),
			'add_new'            => _x( 'Add New', 'third party', 'wpshop' ),
			'add_new_item'       => __( 'Add New Third party', 'wpshop' ),
			'new_item'           => __( 'New Third party', 'wpshop' ),
			'edit_item'          => __( 'Edit Third party', 'wpshop' ),
			'view_item'          => __( 'View Third party', 'wpshop' ),
			'all_items'          => __( 'All Third parties', 'wpshop' ),
			'search_items'       => __( 'Search Third parties', 'wpshop' ),
			'parent_item_colon'  => __( 'Parent Third parties:', 'wpshop' ),
			'not_found'          => __( 'No third parties found.', 'wpshop' ),
			'not_found_in_trash' => __( 'No third parties found in Trash.', 'wpshop' ),
		);

		$args['labels']            = $labels;
		$args['supports']          = array( 'title', 'thumbnail' );
		$args['public']            = true;
		$args['has_archive']       = true;
		$args['show_ui']           = true;
		$args['show_in_nav_menus'] = false;
		$args['show_in_menu']      = false;

		return $args;
	}

	public function add_orders_and_billings_metaboxes( $metaboxes ) {
		if ( Settings::g()->dolibarr_is_active() ) {
			$metaboxes['wps-third-party-orders'] = array(
				'callback' => 'metabox_orders',
			);

			$metaboxes['wps-third-party-invoices'] = array(
				'callback' => 'metabox_invoices',
			);
		}

		return $metaboxes;
	}
}

new Third_Party_Filter();
