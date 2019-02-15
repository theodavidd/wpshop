<?php
/**
 * Les filtres relatives aux proposals.
 *
 * @author Eoxia <corentin-settelen@hotmail.com>
 * @since 2.0.0
 * @version 2.0.0
 * @copyright 2018 Eoxia
 * @package wpshop
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Les filtres relatives aux proposals.
 */
class Third_Party_Filter {

	/**
	 * Initialise les filtres liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-third-party_register_post_type_args', array( $this, 'callback_register_post_type_args' ) );
	}

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
			'not_found_in_trash' => __( 'No third parties found in Trash.', 'wpshop' )
		);

		$args['labels']               = $labels;
		$args['supports']             = array( 'title', 'thumbnail' );
		$args['public']               = true;
		$args['has_archive']          = true;
		$args['show_ui']              = true;
		$args['show_in_nav_menus']    = false;
		$args['show_in_menu']         = false;

		return $args;
	}
}

new Third_Party_Filter();
