<?php
/**
 * Gestion des actions des réglages.
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
 * Action of product module.
 */
class Dashboard_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
		add_action( 'load-toplevel_page_wps-third-party', array( $this, 'callback_load' ) );
	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Dashboard', 'wpshop' ), __( 'Dashboard', 'wpshop' ), 'manage_options', 'wps-dashboard', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		add_meta_box( 'wps-dashboard-sync',  __( 'Synchronization', 'wpshop' ), array( $this, 'metabox_sync' ), 'wps-dashboard', 'normal', 'default' );
		add_meta_box( 'wps-dashboard-stats',  __( 'Stats', 'wpshop' ), array( $this, 'metabox_stats' ), 'wps-dashboard', 'normal', 'default' );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'main' );
	}

	public function callback_load() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	public function metabox_sync() {
		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-sync' );
	}

	public function metabox_stats() {
		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-stats' );
	}
}

new Dashboard_Action();
