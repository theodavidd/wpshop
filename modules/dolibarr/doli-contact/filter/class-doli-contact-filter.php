<?php
/**
 * Les filtres des contact de dolibarr.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2019-2020 Eoxia <dev@eoxia.com>.
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
 * Contact Filter class.
 */
class Doli_Contact_Filter {

	/**
	 * Initialise les filtres.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'check_user' ) );

		add_filter( 'manage_users_columns', array( $this, 'sync_column_header' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'sync_column_entry' ), 10, 3 );
	}

	public function check_user() {
		if ( Settings::g()->dolibarr_is_active() ) {
			$response = Doli_Contact::g()->check_connected_to_erp();

			if ( ! $response['status'] ) {
				$link = Template_Util::get_template_part( 'dolibarr/doli-contact', 'user-alert' );
				include $link;
			}
		}
	}

	public function sync_column_header( $columns ) {
		$columns['sync'] = __( 'Sync', 'wpshop' );

		return $columns;
	}

	public function sync_column_entry( $val, $column_name, $user_id ) {
		if ( $column_name == 'sync' ) {
			$object = Contact::g()->get( array( 'id' => $user_id ), true );

			ob_start();
			echo '<div class="wpeo-wrap">';
			Doli_Sync::g()->display_sync_status( $object, 'wps-user', false );
			echo '</div>';
			$val = ob_get_clean();
		}

		return $val;
	}
}

new Doli_Contact_Filter();
