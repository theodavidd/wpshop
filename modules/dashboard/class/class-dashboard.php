<?php
/**
 * Les fonctions principales du tableau de bord.
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
 * Dashboard class.
 */
class Dashboard extends \eoxia\Singleton_Util {

	/**
	 * Obligatoire pour Singleton_Util
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Appel la vue "main" du module "dashboard".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		add_meta_box( 'wps-dashboard-sync', __( 'Synchronization', 'wpshop' ), array( $this, 'metabox_sync' ), 'wps-dashboard', 'normal', 'default' );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'main' );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_sync() {
		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-sync' );
	}
}

Dashboard::g();
