<?php
/**
 * Gestion des actions de la page "État"
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

if ( ! function_exists( 'plugins_api' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
}

/**
 * Product Action Class.
 */
class Status_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ), 13 );
	}

	/**
	 * Initialise la page "État".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page( 'wpshop', __( 'Status', 'wpshop' ), __( 'Status', 'wpshop' ), 'manage_options', 'wps-status', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Status".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		global $wpdb;

		$curl_version      = curl_version();
		$mysql_version     = $wpdb->db_version();
		$wpshop_db_version = get_option( 'wpshop2_database_version', false );
		$db_prefix         = $wpdb->prefix;
		$plugins           = get_plugins();

		if ( ! empty( $plugins ) ) {
			foreach( $plugins as $key => &$plugin ) {
				$key = preg_replace( '/\/.*\.php/', '', $key );

				$args = array(
					'slug' => $key,
					'fields' => array(
						'version'           => true,
						'short_description' => false,
						'ratings'           => false,
						'rating'            => false,
						'tested'            => false,
						'downloaded'        => false,
						'downloadlink'      => false,
						'last_updated'      =>  false,
						'added'             => false,
						'tags'              => false,
						'compatibility'     => false,
						'homepage'          => false,
						'donate_link'       => false,
						'contributors'      => false,
					),
				);

				$call_api = plugins_api( 'plugin_information', $args );
				if ( ! isset( $call_api->version ) ) {
					$plugin['Uptodate'] = true;
					continue;
				}

				$plugin['Uptodate'] = version_compare( $call_api->version, $plugin['Version'], '>' ) ? false : true;
			}
		}

		unset ( $plugin );

		$page_ids_options = get_option( 'wps_page_ids', Pages::g()->default_options );

		\eoxia\View_Util::exec( 'wpshop', 'status', 'main', array(
			'curl_version'      => $curl_version,
			'mysql_version'     => $mysql_version,
			'wpshop_db_version' => $wpshop_db_version,
			'db_prefix'         => $db_prefix,
			'plugins'           => $plugins,
			'page_ids_options'  => $page_ids_options,
		) );
	}

}

new Status_Action();
