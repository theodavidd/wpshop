<?php
/**
 * Gestion des actions des contacts.
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
 * Contact Action Class.
 */
class Contact_Action {

	/**
	 * Le constructeur
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_third_party_search_contact', array( $this, 'search_contact' ) );
		add_action( 'wp_ajax_third_party_associate_contact', array( $this, 'associate_contact' ) );
		add_action( 'wp_ajax_third_party_save_contact', array( $this, 'save_and_associate_contact' ) );
		add_action( 'wp_ajax_third_party_load_contact', array( $this, 'load_contact' ) );
		add_action( 'wp_ajax_third_party_delete_contact', array( $this, 'delete_contact' ) );
	}

	/**
	 * Recherches un contact dans la base de donnée de WP.
	 *
	 * @since 2.0.0
	 */
	public function search_contact() {
		check_ajax_referer( 'search_contact' );

		$term = ! empty( $_POST['term'] ) ? sanitize_text_field( $_POST['term'] ) : '';

		if ( empty( $term ) ) {
			wp_send_json_error();
		}

		$contacts = Contact::g()->get( array(
			'search'         => '*' . $term . '*',
			'search_columns' => array(
				'user_login',
				'user_nicename',
				'user_email',
			),
		) );

		ob_start();
		foreach ( $contacts as $contact ) :
			?>
			<li data-id="<?php echo esc_attr( $contact->data['id'] ); ?>" data-result="<?php echo esc_html( $contact->data['firstname'] . ' ' . $contact->data['lastname'] ); ?>" class="autocomplete-result">
				<div class="autocomplete-result-container">
					<span class="autocomplete-result-title"><?php echo esc_html( $contact->data['firstname'] . ' ' . $contact->data['lastname'] ); ?></span>
					<span class="autocomplete-result-subtitle"><?php echo esc_html( $contact->data['email'] ); ?></span>
				</div>
			</li>
			<?php
		endforeach;
		wp_send_json_success( array(
			'view' => ob_get_clean(),
		) );
	}

	/**
	 * Associe un contact au tier
	 *
	 * @since 2.0.0
	 */
	public function associate_contact() {
		check_ajax_referer( 'associate_contact' );

		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$contact_id     = ! empty( $_POST['contact_id'] ) ? (int) $_POST['contact_id'] : 0;

		if ( empty( $contact_id ) || empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );
		$contact     = Contact::g()->get( array( 'id' => $contact_id ), true );

		if ( ! in_array( $contact->data['id'], $third_party->data['contact_ids'] ) ) {
			$third_party->data['contact_ids'][] = $contact->data['id'];
			$contact->data['third_party']       = $third_party->data['external_id'];

			$third_party = Third_Party::g()->update( $third_party->data );
			Contact::g()->update( $contact->data );

			do_action( 'wps_saved_and_associated_contact', $third_party, $contact, false );
		}

		ob_start();
		$contacts = array();

		if ( ! empty( $third_party->data['contact_ids'] ) ) {
			$contacts = Contact::g()->get( array( 'include' => $third_party->data['contact_ids'] ) );
		}
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts', array(
			'third_party' => $third_party,
			'contacts'    => $contacts,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'associatedContactSuccess',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Enregistres et associes un contact au tier
	 *
	 * @todo: Merger avec associate contact
	 *
	 * @since 2.0.0
	 */
	public function save_and_associate_contact() {
		check_ajax_referer( 'save_and_associate_contact' );

		$third_party_id = ! empty( $_POST['parent_id'] ) ? (int) $_POST['parent_id'] : 0;
		$contact        = ! empty( $_POST['contact'] ) ? (array) $_POST['contact'] : array();
		$contact['id']  = ! empty( $_POST['contact']['id'] ) ? (int) $_POST['contact']['id'] : 0;

		if ( empty( $third_party_id ) || empty( $contact['email'] ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );

		if ( empty( $contact['id'] ) ) {
			$email                = explode( '@', $contact['email'] );
			$contact['login']     = $email[0];
			$contact['user_pass'] = wp_generate_password();
		}

		$contact       = apply_filters( 'wps_save_and_associate_contact', $contact, $third_party );
		$saved_contact = Contact::g()->update( $contact );

		if ( empty( $contact['id'] ) ) {
			$third_party->data['contact_ids'][] = $saved_contact->data['id'];
		}

		$third_party = Third_Party::g()->update( $third_party->data );

		do_action( 'wps_saved_and_associated_contact', $third_party, $saved_contact, empty( $contact['id'] ) ? true : false );

		ob_start();
		$contacts = array();

		if ( ! empty( $third_party->data['contact_ids'] ) ) {
			$contacts = Contact::g()->get( array( 'include' => $third_party->data['contact_ids'] ) );
		}
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts', array(
			'third_party' => $third_party,
			'contacts'    => $contacts,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'savedContact',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Charges un contact et appel la vue edit.
	 *
	 * @since 2.0.0
	 */
	public function load_contact() {
		check_ajax_referer( 'load_contact' );

		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$contact_id     = ! empty( $_POST['contact_id'] ) ? (int) $_POST['contact_id'] : 0;

		if ( empty( $contact_id ) || empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$contact = Contact::g()->get( array( 'id' => $contact_id ), true );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts-edit', array(
			'third_party_id' => $third_party_id,
			'contact'        => $contact,
		) );

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'loaddedContactSuccess',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Supprimes un contact
	 *
	 * @since 2.0.0
	 */
	public function delete_contact() {
		check_ajax_referer( 'delete_contact' );

		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$contact_id     = ! empty( $_POST['contact_id'] ) ? (int) $_POST['contact_id'] : 0;

		if ( empty( $contact_id ) || empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );

		$index = array_search( $contact_id, $third_party->data['contact_ids'], true );

		if ( false !== $index ) {
			array_splice( $third_party->data['contact_ids'], $index, 1 );

			$contact = Contact::g()->get( array( 'id' => $contact_id ), true );

			$contact->data['third_party_id'] = -1;

			Third_Party::g()->update( $third_party->data );
			Contact::g()->update( $contact->data );

			do_action( 'wps_deleted_contact', $third_party, $contact );
		}

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'deletedContactSuccess',
		) );
	}
}

new Contact_Action();
