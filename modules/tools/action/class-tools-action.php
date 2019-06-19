<?php
/**
 * Gestion des actions des outils.
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
 * Settings Action Class.
 */
class Tools_Action {

	private $destination_directory;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ), 13 );

		add_action( 'admin_post_wps_load_tools_tab', array( $this, 'callback_load_tab' ) );

		add_action( 'wp_ajax_import_third_party', array( $this, 'import_third_party' ) );

		$wp_upload_dir               = wp_upload_dir();
		$this->destination_directory = $wp_upload_dir['basedir'] . '/wpshop/tmp/';
		wp_mkdir_p( $this->destination_directory );
	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page( 'wpshop', __( 'Tools', 'wpshop' ), __( 'Tools', 'wpshop' ), 'manage_options', 'wps-tools', array( $this, 'callback_add_menu_page' ) );
	}

	public function callback_add_menu_page() {
		$tab     = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		$section = ! empty( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

		\eoxia\View_Util::exec( 'wpshop', 'tools', 'main', array(
			'tab'       => $tab,
			'section'   => $section,
		) );
	}

	/**
	 * Redirige vers le bon onglet dans la page option;
	 *
	 * @since 2.0.0
	 */
	public function callback_load_tab() {
		check_admin_referer( 'callback_load_tab' );

		$tab     = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		$section = ! empty( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

		$url = 'admin.php?page=wps-tools&tab= ' . $tab;

		if ( ! empty( $section ) ) {
			$url .= '&section=' . $section;
		}

		wp_redirect( admin_url( $url ) );
	}

	public function import_third_party() {
		ini_set( 'memory_limit', -1 );

		$path_to_json  = ! empty( $_POST['path_to_json'] ) ? stripslashes( $_POST['path_to_json'] ) : '';
		$index_element = ! empty( $_POST['index_element'] ) ? $_POST['index_element'] : 0;
		$count_element = ! empty( $_POST['count_element'] ) ? $_POST['count_element'] : 0;
		$end           = false;

		if ( empty( $path_to_json ) ) {
			if ( empty( $_FILES ) || empty( $_FILES['file'] ) ) {
				wp_send_json_error();
			}

			$path_to_json = $this->destination_directory . $_FILES['file']['name'];

			$statut = move_uploaded_file( $_FILES['file']['tmp_name'], $path_to_json );

			$count_element = count( file( $path_to_json ) );
		} else {
			$content = file( $path_to_json );

			$content = array_splice( $content, $index_element, 200 );

			if ( ! empty( $content ) ) {
				foreach ( $content as $line ) {
					$line = explode( ',', $line );

					if ( ! empty( $line ) ) {
						foreach ( $line as &$v ) {
							$v = preg_replace('/^"(.*)"/','$1', $v);
						}
					}

					$third_party = Third_Party::g()->get( array( 'title' => $line[0] ), true );

					if ( empty( $third_party ) ) {
						$third_party_data = array(
							'post_title' => $line[0],
						);

						$third_party = Third_Party::g()->update( $third_party_data );
					}

					$contact_data = array(
						'email'          => $line[1],
						'firstname'      => ! empty( $line[2] ) ? $line[2] : '',
						'lastname'       => ! empty( $line[3] ) ? $line[3] : '',
						'phone'          => ! empty( $line[4] ) ? $line[4] : '',
						'login'          => $line[1],
						'password'       => wp_generate_password(),
						'third_party_id' => $third_party->data['id'],
					);

					$contact = Contact::g()->update( $contact_data );

					if ( ! isset( $contact->errors ) ) {
						$third_party->data['contact_ids'][] = $contact->data['id'];

						Third_Party::g()->update( $third_party->data );
					}

					$index_element++;
				}
			}

		}

		if ( $index_element >= $count_element ) {
			$end = true;
		}

		wp_send_json_success( array(
			'end'           => $end,
			'index_element' => $index_element,
			'count_element' => $count_element,
			'path_to_json'  => $path_to_json,
		) );
	}
}

new Tools_Action();
