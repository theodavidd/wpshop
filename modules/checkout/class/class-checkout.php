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
* Handle order
*/
class Checkout_Class extends \eoxia\Singleton_Util {

	/**
	 * Constructeur pour la classe Checkout_Class. Charge les options et les actions.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	public function get_posted_data() {
		$data = array(
			'contact'     => ! empty( $_POST['contact'] ) ? (array) $_POST['contact'] : array(),
			'third_party' => ! empty( $_POST['third_party'] ) ? (array) $_POST['third_party'] : array(),
		);

		return apply_filters( 'wps_checkout_posted_data', $data );
	}

	private function get_checkout_fields() {
		return array(
			'contact' => array(
				'firstname' => array(
					'label'    => __( 'First name', 'wpshop' ),
					'required' => false,
				),
				'lastname' => array(
					'label'    => __( 'Last name', 'wpshop' ),
					'required' => false,
				),
				'phone' => array(
					'label'    => __( 'Phone', 'wpshop' ),
					'required' => false,
				),
				'email' => array(
					'label'    => __( 'Email address', 'wpshop' ),
					'required' => true,
				),
				'password' => array(
					'label'    => __( 'Password', 'wpshop' ),
					'required' => false,
				),
			),
			'third_party' => array(
				'country' => array(
					'label'    => __( 'Country', 'wpshop' ),
					'required' => false,
				),
				'address' => array(
					'label'    => __( 'Street Address', 'wpshop' ),
					'required' => false,
				),
				'zip'     => array(
					'label'    => __( 'Postcode / Zip', 'wpshop' ),
					'required' => false,
				),
				'state' => array(
					'label'    => __( 'Town / City', 'wpshop' ),
					'required' => false,
				),
			)
		);
	}

	protected function validate_posted_data( &$data, &$errors ) {
		foreach ( $this->get_checkout_fields() as $fieldset_key => $fieldset ) {
			foreach ( $fieldset as $field_key => $field ) {
				if ( $field['required'] && '' === $data[ $fieldset_key ][ $field_key ] ) {
					$errors->add( 'required-field', apply_filters( 'wps_checkout_required_field_notice', sprintf( __( '%s is a required field.', 'wpshop' ), '<strong>' . esc_html( $field['label'] ) . '</strong>' ), $field['label'] ) );

					$error_field = array(
						'required'    => true,
						'input_class' => $fieldset_key . '-' . $field_key,
					);

					$errors->add_data( $error_field, 'input_' . $fieldset_key . '_' . $field_key );
				}

				if ( 'email' === $field_key && false !== email_exists( $data['contact']['email'] ) ) {
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

	public function validate_checkout( &$data, &$errors ) {
		$this->validate_posted_data( $data, $errors );
	}

}

Checkout_Class::g();
