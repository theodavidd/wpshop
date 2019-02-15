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
class Proposals_Filter {

	/**
	 * Initialise les filtres liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-proposal_register_post_type_args', array( $this, 'callback_register_post_type_args' ) );

		add_filter( 'wps_proposal_table_th', array( $this, 'add_proposal_status_header' ) );
		add_filter( 'wps_proposal_table_tr', array( $this, 'add_proposal_status_body' ), 10, 1 );
	}

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
			'not_found_in_trash' => __( 'No products found in Trash.', 'wpshop' )
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

	public function add_proposal_status_header() {
		echo '<th>' . __( 'Status', 'wpshop' ) . '</th>';
	}

	public function add_proposal_status_body( $order ) {
		switch ( $order->data['status'] ) {
			case 'wps-completed':
				echo '<td>' . __( 'Completed', 'wpshop' ) . '</td>';
				break;
			default:
				echo '<td>' . __( 'Pending payment', 'wpshop' ) . '</td>';
				break;
		}
	}

}

new Proposals_Filter();
