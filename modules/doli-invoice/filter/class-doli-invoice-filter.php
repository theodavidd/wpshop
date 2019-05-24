<?php
/**
 * Les filtres relatives aux factures de dolibarr.
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
 * Doli Invoice Filter Class.
 */
class Doli_Invoice_Filter {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eo_model_wps-invoice_register_post_type_args', array( $this, 'callback_register_post_type_args' ) );
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
			'name'               => _x( 'Invoices', 'post type general name', 'wpshop' ),
			'singular_name'      => _x( 'Invoice', 'post type singular name', 'wpshop' ),
			'menu_name'          => _x( 'Invoices', 'admin menu', 'wpshop' ),
			'name_admin_bar'     => _x( 'Invoice', 'add new on admin bar', 'wpshop' ),
			'add_new'            => _x( 'Add New', 'invoice', 'wpshop' ),
			'add_new_item'       => __( 'Add New Invoice', 'wpshop' ),
			'new_item'           => __( 'New Invoice', 'wpshop' ),
			'edit_item'          => __( 'Edit Invoice', 'wpshop' ),
			'view_item'          => __( 'View Invoice', 'wpshop' ),
			'all_items'          => __( 'All Invoices', 'wpshop' ),
			'search_items'       => __( 'Search Invoices', 'wpshop' ),
			'parent_item_colon'  => __( 'Parent Invoices:', 'wpshop' ),
			'not_found'          => __( 'No invoices found.', 'wpshop' ),
			'not_found_in_trash' => __( 'No invoices found in Trash.', 'wpshop' ),
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

new Doli_Invoice_Filter();
