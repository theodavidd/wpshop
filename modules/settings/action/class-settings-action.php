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
class Settings_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
		add_action( 'admin_post_wps_load_settings_tab', array( $this, 'callback_load_tab' ) );

		add_action( 'admin_post_wps_update_general_settings', array( $this, 'callback_update_general_settings' ) );
		add_action( 'admin_post_wps_update_pages_settings', array( $this, 'callback_update_pages_settings' ) );
		add_action( 'admin_post_wps_update_email', array( $this, 'callback_update_email' ) );
	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page( 'wps-order', __( 'Settings', 'wpshop' ), __( 'Settings', 'wpshop' ), 'manage_options', 'wps-settings', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		$tab     = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		$section = ! empty( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'main', array(
			'tab'     => $tab,
			'section' => $section,
		) );
	}

	public function callback_load_tab() {
		$tab     = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		$section = ! empty( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

		$url = 'admin.php?page=wps-settings&tab= ' . $tab;

		if ( ! empty( $section ) ) {
			$url .= '&section=' . $section;
		}

		wp_redirect( admin_url( $url ) );
	}

	public function callback_update_general_settings() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_die();
		}

		$tab             = ! empty( $_POST['tab'] ) ? sanitize_text_field( $_POST['tab'] ) : 'general';
		$dolibarr_url    = ! empty( $_POST['dolibarr_url'] ) ? sanitize_text_field( $_POST['dolibarr_url' ] ) : '';
		$dolibarr_secret = ! empty( $_POST['dolibarr_secret'] ) ? sanitize_text_field( $_POST['dolibarr_secret' ] ) : '';
		$shop_email = ! empty( $_POST['shop_email'] ) ? sanitize_text_field( $_POST['shop_email' ] ) : '';

		$dolibarr_option = get_option( 'wps_dolibarr', Settings_Class::g()->default_settings );

		$dolibarr_option['dolibarr_url']    = $dolibarr_url;
		$dolibarr_option['dolibarr_secret'] = $dolibarr_secret;
		$dolibarr_option['shop_email']      = $shop_email;

		update_option( 'wps_dolibarr', $dolibarr_option );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab= ' . $tab ) );
	}

	public function callback_update_pages_settings() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_die();
		}

		$tab                        = ! empty( $_POST['tab'] ) ? sanitize_text_field( $_POST['tab'] ) : 'general';
		$wps_page_shop_id           = ! empty( $_POST['wps_page_shop_id'] ) ? (int) $_POST['wps_page_shop_id'] : 0;
		$wps_page_cart_id           = ! empty( $_POST['wps_page_cart_id'] ) ? (int) $_POST['wps_page_cart_id'] : 0;
		$wps_page_checkout_id       = ! empty( $_POST['wps_page_checkout_id'] ) ? (int) $_POST['wps_page_checkout_id'] : 0;
		$wps_page_my_account_id     = ! empty( $_POST['wps_page_my_account_id'] ) ? (int) $_POST['wps_page_my_account_id'] : 0;
		$wps_page_valid_checkout_id = ! empty( $_POST['wps_page_valid_checkout_id'] ) ? (int) $_POST['wps_page_valid_checkout_id'] : 0;
		$wps_page_valid_proposal_id = ! empty( $_POST['wps_page_valid_proposal_id'] ) ? (int) $_POST['wps_page_valid_proposal_id'] : 0;

		$page_ids_options = get_option( 'wps_page_ids', Pages_Class::g()->default_options );

		$page_ids_options['shop_id']           = $wps_page_shop_id;
		$page_ids_options['cart_id']           = $wps_page_cart_id;
		$page_ids_options['checkout_id']       = $wps_page_checkout_id;
		$page_ids_options['my_account_id']     = $wps_page_my_account_id;
		$page_ids_options['valid_checkout_id'] = $wps_page_valid_checkout_id;
		$page_ids_options['valid_proposal_id'] = $wps_page_valid_proposal_id;

		update_option( 'wps_page_ids', $page_ids_options );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab= ' . $tab ) );
	}

	public function callback_update_email() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_die();
		}

		$tab     = ! empty( $_POST['tab'] ) ? sanitize_text_field( $_POST['tab'] ) : 'general';
		$content = ! empty( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';
		$section = ! empty( $_POST['section'] ) ? sanitize_text_field( $_POST['section'] ) : '';

		$email = Emails_Class::g()->emails[ $section ];
		$path_file = Emails_Class::g()->get_path( $email['filename_template'] );

		$f = fopen( $path_file, 'w+' );

		if ( false !== $f ) {
			fwrite( $f, $content );
			fclose( $f );
		}

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab= ' . $tab . '&section=' . $section ) );
	}
}

new Settings_Action();
