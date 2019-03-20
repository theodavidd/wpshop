<?php
/**
 * Les fonctions principales du tunnel de vente.
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
 * Checkout Class.
 */
class Checkout extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Checkout. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Récupères les données postées
	 *
	 * @since 2.0.0
	 *
	 * @return array Les données postées filtrés et sécurisés.
	 */
	public function get_posted_data() {
		$data = array(
			'contact'     => ! empty( $_POST['contact'] ) ? (array) $_POST['contact'] : array(),
			'third_party' => ! empty( $_POST['third_party'] ) ? (array) $_POST['third_party'] : array(),
		);

		$data['contact']['firstname']      = ! empty( $_POST['contact']['firstname'] ) ? sanitize_text_field( $_POST['contact']['firstname'] ) : '';
		$data['contact']['lastname']       = ! empty( $_POST['contact']['lastname'] ) ? sanitize_text_field( $_POST['contact']['lastname'] ) : '';
		$data['contact']['phone']          = ! empty( $_POST['contact']['phone'] ) ? sanitize_text_field( $_POST['contact']['phone'] ) : '';
		$data['contact']['email']          = ! empty( $_POST['contact']['email'] ) ? sanitize_email( $_POST['contact']['email'] ) : '';
		$data['contact']['password']       = ! empty( $_POST['contact']['password'] ) ? (string) ( $_POST['contact']['password'] ) : '';
		$data['third_party']['country_id'] = ! empty( $_POST['third_party']['country_id'] ) ? (int) ( $_POST['third_party']['country_id'] ) : '';
		$data['third_party']['address']    = ! empty( $_POST['third_party']['address'] ) ? sanitize_text_field( $_POST['third_party']['address'] ) : '';
		$data['third_party']['zip']        = ! empty( $_POST['third_party']['zip'] ) ? sanitize_text_field( $_POST['third_party']['zip'] ) : '';
		$data['third_party']['town']       = ! empty( $_POST['third_party']['town'] ) ? sanitize_text_field( $_POST['third_party']['town'] ) : '';


		return apply_filters( 'wps_checkout_posted_data', $data );
	}

	/**
	 * Définition du formulaire du tunnel de vente
	 *
	 * @since 2.0.0
	 *
	 * @return array Tableau contenant la définition des champs.
	 */
	private function get_checkout_fields() {
		return array(
			'contact'     => array(
				'firstname' => array(
					'label'    => __( 'First name', 'wpshop' ),
					'required' => false,
				),
				'lastname'  => array(
					'label'    => __( 'Last name', 'wpshop' ),
					'required' => false,
				),
				'phone'     => array(
					'label'    => __( 'Phone', 'wpshop' ),
					'required' => false,
				),
				'email'     => array(
					'label'    => __( 'Email address', 'wpshop' ),
					'required' => true,
				),
				'password'  => array(
					'label'    => __( 'Password', 'wpshop' ),
					'required' => false,
				),
			),
			'third_party' => array(
				'country_id' => array(
					'label'    => __( 'Country', 'wpshop' ),
					'required' => true,
				),
				'address'    => array(
					'label'    => __( 'Street Address', 'wpshop' ),
					'required' => true,
				),
				'zip'        => array(
					'label'    => __( 'Postcode / Zip', 'wpshop' ),
					'required' => true,
				),
				'town'       => array(
					'label'    => __( 'Town / City', 'wpshop' ),
					'required' => true,
				),
			),
		);
	}

	/**
	 * Vérifie les données reçu par le formulaire du tunnel de vente.
	 *
	 * @since 2.0.0
	 *
	 * @param  array    $data   Les données reçu du formulaire.
	 * @param  WP_Error $errors Gestion des erreurs du formulaire.
	 */
	protected function validate_posted_data( &$data, &$errors ) {
		foreach ( $this->get_checkout_fields() as $fieldset_key => $fieldset ) {
			foreach ( $fieldset as $field_key => $field ) {
				if ( $field['required'] && ( '' == $data[ $fieldset_key ][ $field_key ] || '0' == $data[ $fieldset_key ][ $field_key ] ) ) {
					/* translators: Lastname is a required field. */
					$errors->add( 'required-field', apply_filters( 'wps_checkout_required_field_notice', sprintf( __( '%s is a required field.', 'wpshop' ), '<strong>' . esc_html( $field['label'] ) . '</strong>' ), $field['label'] ) );

					$error_field = array(
						'required'    => true,
						'input_class' => $fieldset_key . '-' . $field_key,
					);

					$errors->add_data( $error_field, 'input_' . $fieldset_key . '_' . $field_key );
				}

				if ( ! is_user_logged_in() && 'email' === $field_key && false !== email_exists( $data['contact']['email'] ) ) {
					/* translators: mail@domain.ext is already used. */
					$errors->add( 'email-exists', apply_filters( 'wps_checkout_email_exists_notice', sprintf( __( '%s is already used.', 'wpshop' ), '<strong>' . esc_html( $field['label'] ) . '</strong>' ), $field['label'] ) );
					$error_field = array(
						'email_exists' => true,
						'input_class'  => $fieldset_key . '-' . $field_key,
					);

					$errors->add_data( $error_field, 'input_' . $fieldset_key . '_' . $field_key );
				}
			}
		}
	}

	/**
	 * Appel la méthode pour valider le formulaire.
	 *
	 * @since 2.0.0
	 *
	 * @param  array    $data   Les données reçu du formulaire.
	 * @param  WP_Error $errors Gestion des erreurs du formulaire.
	 */
	public function validate_checkout( &$data, &$errors ) {
		$this->validate_posted_data( $data, $errors );
	}

	/**
	 * Procèdes au paiement
	 *
	 * @since 2.0.0
	 *
	 * @param Order_Model $order Les données de la commande.
	 */
	public function process_order_payment( $order ) {
		$type = ! empty( $_POST['type_payment'] ) ? $_POST['type_payment'] : '';

		switch ( $type ) {
			case 'paypal':
				update_post_meta( $order->data['id'], 'payment_method', 'paypal' );

				$result = Paypal::g()->process_payment( $order );
				Cart_Session::g()->destroy();
				if ( ! empty( $result['url'] ) ) {
					wp_send_json_success( array(
						'namespace'        => 'wpshopFrontend',
						'module'           => 'checkout',
						'callback_success' => 'redirectToPayment',
						'url'              => $result['url'],
					) );
				}
				break;
			case 'cheque':
				update_post_meta( $order->data['id'], 'payment_method', 'cheque' );
				Cart_Session::g()->destroy();
				wp_send_json_success( array(
					'namespace'        => 'wpshopFrontend',
					'module'           => 'checkout',
					'callback_success' => 'redirect',
					'url'              => Pages::g()->get_valid_checkout_link() . '?order_id=' . $order->data['id'],
				) );
				break;
			case 'payment_in_shop':
				update_post_meta( $order->data['id'], 'payment_method', 'payment_in_shop' );
				Cart_Session::g()->destroy();
				wp_send_json_success( array(
					'namespace'        => 'wpshopFrontend',
					'module'           => 'checkout',
					'callback_success' => 'redirect',
					'url'              => Pages::g()->get_valid_checkout_link() . '?order_id=' . $order->data['id'],
				) );
				break;
		}

	}
}

Checkout::g();
