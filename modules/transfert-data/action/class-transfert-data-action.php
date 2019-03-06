<?php
/**
 * Gestion des actions des tiers.
 *
 * Ajoutes une page "Tiers" dans le menu de WordPress.
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
 * Action of Third Party module.
 */
class Transfert_Data_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
		add_action( 'wp_ajax_wps_transfert_data', array( $this, 'callback_transfert_data' ) );
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		// add_submenu_page( 'wps-order', __( 'Transfert data', 'wpshop' ), __( 'Transfert data', 'wpshop' ), 'manage_options', 'wps-transfert-data', array( $this, 'callback_add_menu_page' ) );

	}

	/**
	 * Appel la vue "main" du module "Third Party".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		$number_customers = count( get_posts( array(
			'post_type'      => 'wpshop_customers',
			'posts_per_page' => -1,
			'post_status'    => 'draft',
		) ) );

		\eoxia\View_Util::exec( 'wpshop', 'transfert-data', 'main', array(
			'number_customers' => $number_customers,
		) );
	}

	public function callback_transfert_data() {
		define( 'SAVEQUERIES', true );
		global $wpdb;

		$number_customers = ! empty( $_POST['number_customers'] ) ? (int) $_POST['number_customers'] : 0;
		$index            = ! empty( $_POST['index'] ) ? (int) $_POST['index'] : 0;
		$index_error      = ! empty( $_POST['index_error'] ) ? (int) $_POST['index_error'] : 0;
		$key_query        = ! empty( $_POST['key_query'] ) ? (int) $_POST['key_query'] : 1;
		$done             = false;
		$output           = '';
		$errors           = '';

		$results = new \WP_Query( array(
			'posts_per_page' => 20,
			'post_type'      => 'wpshop_customers',
			'offset'         => $index,
			'post_status'    => 'draft',
		) );

		$customers = $results->posts;

		if ( ! empty( $customers ) ) {
			foreach ( $customers as $customer ) {
				$results = new \WP_Query( array(
					'meta_key'       => 'old_id',
					'meta_value'     => $customer->ID,
					'post_type'      => 'wps-third-party',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
				) );

				if ( ! empty( $results->posts ) ) {
					$new_customer = $results->posts[0];
				}

				$data = array(
					'post_title'    => $customer->post_title,
					'post_date'     => $customer->post_date,
					'post_modified' => $customer->post_modified,
					'post_type'     => 'wps-third-party',
					'post_status'   => 'publish',
				);


				if ( ! empty( $new_customer ) ) {
					$data['ID'] = $new_customer->ID;
				}

				$post_id = wp_insert_post( $data );

				$old_id = get_post_meta( $post_id, 'old_id', true );

				if ( empty( $old_id ) ) {
					add_post_meta( $post_id, 'old_id', $customer->ID );
				}

				$contact_ids = update_post_meta( $post_id, '_contact_ids', array() );

				$results = new \WP_Query( array(
					'post_type'      => 'wpshop_address',
					'post_status'    => 'draft',
					'posts_per_page' => 1,
					'post_parent'    => $customer->ID,
				) );

				if ( ! empty( $results->posts ) ) {
					$address      = $results->posts[0];
					$address_meta = get_post_meta( $address->ID, '_wpshop_address_metadata', true );

					update_post_meta( $post_id, '_address', $address_meta['address'] );
					update_post_meta( $post_id, '_zip', $address_meta['postcode'] );
					update_post_meta( $post_id, '_town', $address_meta['city'] );
					update_post_meta( $post_id, '_country', $address_meta['country'] );
				}

				$users_id = get_post_meta( $customer->ID, '_wpscrm_associated_user', true );

				if ( empty( $users_id ) ) {
					$users_id = array();
				}

				if ( ! in_array( $customer->post_author, $users_id, true ) ) {
					$users_id = array_merge( $users_id, array( $customer->post_author ) );
				}

				if ( ! empty( $users_id ) ) {
					$users = get_users( array(
						'include' => $users_id,
					) );

					if ( ! empty( $users ) ) {
						foreach ( $users as $user ) {
							$phone = get_user_meta( $user->ID, 't_l_phone__1684463909', true );

							if ( empty( $phone ) ) {
								$phone = get_user_meta( $user->ID, 'wps_phone', true );
							}

							update_user_meta( $user->ID, '_phone', $phone );

							$third_party_id = get_user_meta( $user->ID, '_third_party_id', true );

							if ( empty( $third_party_id ) ) {
								update_user_meta( $user->ID, '_third_party_id', $post_id );

								$contact_ids = json_decode( get_post_meta( $post_id, '_contact_ids', true ) );

								if ( empty( $contact_ids ) ) {
									$contact_ids = array();
								}

								$contact_ids[] = $user->ID;

								update_post_meta( $post_id, '_contact_ids', json_encode( $contact_ids ) );
							} else {
								$errors .= '<li><strong>' . $index_error . '</strong> ' . __( 'Contact #' . $user->ID . ' is already affected to another third party #' . $third_party_id, 'wpshop' ) . '</li>';
								$index_error++;
							}
						}
					}
				}

				$index += 1;

			}
		}

		if ( ! empty( $wpdb->queries ) ) {
			foreach ( $wpdb->queries as $key => $element ) {
				$output .= '<li><strong>' . ($key + $key_query) . '</strong> ' . $element[0] . '</li>';

			}
		}

		$key_query = $key + $key_query;


		if ( $index >= $number_customers ) {
			$done = true;

			$output .= '<li>Done</li>';
		}

		wp_send_json_success( array(
			'done'        => $done,
			'index'       => $index,
			'index_error' => $index_error,
			'key_query'   => $key_query,
			'output'      => $output,
			'errors'      => $errors,
		) );
	}
}

new Transfert_Data_Action();
