<?php
/**
 * Gestion des actions des commandes.
 *
 * Ajoutes une page "Orders" dans le menu de WordPress.
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
 * Action of Order module.
 */
class Orders_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Orders', 'wpshop' ), __( 'Orders', 'wpshop' ), 'manage_options', 'wps-order', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Orders".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		\eoxia\View_Util::exec( 'wpshop', 'orders', 'main' );
	}
}

new Orders_Action();
