<?php
/**
 * Classe gérant les actions principales de WPshop.
 *
 * Elle ajoute les styles et scripts JS principaux pour le bon fonctionnement de WPshop.
 * Elle ajoute également les textes de traductions (fichiers .mo)
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
 * Main actions of wpshop.
 */
class Core_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'callback_admin_enqueue_scripts' ), 11 );
	}

	/**
	 * Init style and script
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_enqueue_scripts() {
		wp_enqueue_style( 'wpshop-backend-style', PLUGIN_WPSHOP_URL . 'core/asset/css/style.css', array(), \eoxia\Config_Util::$init['wpshop']->version );
		wp_enqueue_script( 'wpshop-backend-script', PLUGIN_WPSHOP_URL . 'core/asset/js/backend.min.js', array(), \eoxia\Config_Util::$init['wpshop']->version );
	}
}

new Core_Action();
