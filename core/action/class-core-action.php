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
		add_action( 'init', array( $this, 'callback_register_session' ), 1 );
		add_action( 'init', array( $this, 'callback_language' ) );

		add_action( 'wp_head', array( $this, 'define_ajax_url' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'callback_admin_enqueue_scripts' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'callback_enqueue_scripts' ), 11 );

		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
	}

	/**
	 * Enregistres la session et enlève l'admin bar
	 *
	 * @since 2.0.0
	 */
	public function callback_register_session() {
		if ( ! session_id() ) {
			session_start();
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			show_admin_bar( false );
		}
	}

	/**
	 * Charges le fichier de traduction.
	 *
	 * @since 2.0.0
	 */
	public function callback_language() {
		$i18n_loaded = load_plugin_textdomain( 'wpshop', false, PLUGIN_WPSHOP_DIR . '/core/asset/language/' );
	}

	/**
	 * Ajoutes ajaxurl
	 *
	 * @todo A vérifier
	 * @since 2.0.0
	 */
	public function define_ajax_url() {}

	/**
	 * Init backend style and script
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_enqueue_scripts() {
		wp_dequeue_script( 'wpeo-assets-datepicker-js' );
		wp_dequeue_style( 'wpeo-assets-datepicker' );

		wp_enqueue_style( 'wpshop-style', PLUGIN_WPSHOP_URL . 'core/asset/css/style.css', array(), \eoxia\Config_Util::$init['wpshop']->version );
		wp_enqueue_script( 'wpshop-backend-script', PLUGIN_WPSHOP_URL . 'core/asset/js/backend.min.js', array( 'jquery', 'jquery-form' ), \eoxia\Config_Util::$init['wpshop']->version );
	}

	/**
	 * Init backend style and script
	 *
	 * @since 2.0.0
	 */
	public function callback_enqueue_scripts() {
		wp_dequeue_script( 'wpeo-assets-datepicker-js' );
		wp_dequeue_style( 'wpeo-assets-datepicker' );
		wp_enqueue_style( 'wpshop-style', PLUGIN_WPSHOP_URL . 'core/asset/css/style.css', array(), \eoxia\Config_Util::$init['wpshop']->version );
		wp_enqueue_script( 'wpshop-frontend-script', PLUGIN_WPSHOP_URL . 'core/asset/js/frontend.min.js', array(), \eoxia\Config_Util::$init['wpshop']->version );
	}

	/**
	 * Ajoutes le menu principal de WPShop.
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'WPShop', 'wpshop' ), __( 'WPShop', 'wpshop' ), 'manage_options', 'wps-order' );
	}
}

new Core_Action();
