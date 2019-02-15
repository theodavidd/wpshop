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
class Third_Party_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
		add_action( 'load-toplevel_page_wps-third-party', array( $this, 'callback_load' ) );

		add_action( 'wp_ajax_third_party_load_address', array( $this, 'load_billing_address' ) );
		add_action( 'wp_ajax_third_party_save_address', array( $this, 'save_billing_address' ) );

		add_action( 'wp_ajax_third_party_search_contact', array( $this, 'search_contact' ) );
		add_action( 'wp_ajax_third_party_associate_contact', array( $this, 'associate_contact' ) );
		add_action( 'wp_ajax_third_party_save_contact', array( $this, 'save_and_associate_contact' ) );
		add_action( 'wp_ajax_third_party_load_contact', array( $this, 'load_contact' ) );
		add_action( 'wp_ajax_third_party_delete_contact', array( $this, 'delete_contact' ) );

		add_action( 'wp_ajax_synchro_third_parties', array( $this, 'ajax_synchro_third_parties' ) );

	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Third Parties', 'wpshop' ), __( 'Third Parties', 'wpshop' ), 'manage_options', 'wps-third-party', array( $this, 'callback_add_menu_page' ) );

	}

	public function callback_load() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	/**
	 * Appel la vue "main" du module "Third Party".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		if ( ! empty( $_GET['id'] ) ) {
			$third_party  = Third_Party_Class::g()->get( array( 'id' => $_GET['id'] ), true );
			$args_metabox = array(
				'third_party' => $third_party,
				'id'          => $_GET['id'],
			);

			add_meta_box( 'wps-third-party-billing',  __( 'Billing address', 'wpshop' ), array( $this, 'metabox_billing_address' ), 'wps-third-party', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-third-party-contacts',  __( 'Contacts', 'wpshop' ), array( $this, 'metabox_contacts' ), 'wps-third-party', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-third-party-orders',  __( 'Orders', 'wpshop' ), array( $this, 'metabox_orders' ), 'wps-third-party', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-third-party-informations',  __( 'Informations', 'wpshop' ), array( $this, 'metabox_informations' ), 'wps-third-party', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-third-party-activity',  __( 'History of activities', 'wpshop' ), array( $this, 'metabox_activity' ), 'wps-third-party', 'normal', 'default', $args_metabox );

			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single', array(
				'third_party' => $third_party
			) );
		} else {
			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'main' );
		}
	}

	public function metabox_billing_address( $post, $callback_args ) {
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address', array(
			'third_party' => $callback_args['args']['third_party'],
		) );
	}

	public function metabox_contacts( $post, $callback_args ) {
		$contacts = Contact_Class::g()->get( array( 'include' => $callback_args['args']['third_party']->data['contact_ids'] ) );
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts', array(
			'third_party' => $callback_args['args']['third_party'],
			'contacts'    => $contacts,
		) );
	}

	public function metabox_orders( $post, $callback_args ) {
		$orders = Orders_Class::g()->get( array( 'post_parent' => $callback_args['args']['id'] ) );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as &$order ) {
				$order->data['invoice'] = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-orders', array(
			'orders' => $orders,
		) );
	}

	public function metabox_informations( $post, $callback_args ) {
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-informations' );
	}

	public function metabox_activity( $post, $callback_args ) {
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-activities' );
	}

	public function load_billing_address() {
		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;

		if ( empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party_Class::g()->get( array( 'id' => $third_party_id ), true );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address-edit', array(
			'third_party' => $third_party,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'loaddedBillingAddressSuccess',
			'view'             => ob_get_clean(),
		) );
	}

	public function save_billing_address() {
		$third_party_id   = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$third_party_form = ! empty( $_POST['third_party'] ) ? (array) $_POST['third_party'] : array();

		if ( empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party_Class::g()->get( array( 'id' => $third_party_id ), true );

		$third_party->data['title']   = $third_party_form['title'];
		$third_party->data['address'] = $third_party_form['address'];
		$third_party->data['zip']     = $third_party_form['zip'];
		$third_party->data['email']   = $third_party_form['email'];
		$third_party->data['town']    = $third_party_form['town'];
		$third_party->data['phone']   = $third_party_form['phone'];

		$third_party = Third_Party_Class::g()->update( $third_party->data );

		do_action( 'wps_saved_billing_address', $third_party );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address', array(
			'third_party' => $third_party,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'savedBillingAddressSuccess',
			'view'             => ob_get_clean(),
		) );
	}

	public function search_contact() {
		$term = ! empty( $_POST['term'] ) ? sanitize_text_field( $_POST['term'] ) : '';

		if ( empty( $term ) ) {
			wp_send_json_error();
		}

		$contacts = Contact_Class::g()->get( array(
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

	public function associate_contact() {
		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$contact_id     = ! empty( $_POST['contact_id'] ) ? (int) $_POST['contact_id'] : 0;

		if ( empty( $contact_id ) || empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party_Class::g()->get( array( 'id' => $third_party_id ), true );
		$contact     = Contact_Class::g()->get( array( 'id' => $contact_id ), true );

		if ( ! in_array( $contact->data['id'], $third_party->data['contact_ids'] ) ) {
			$third_party->data['contact_ids'][] = $contact->data['id'];
			$contact->data['third_party']       = $third_party->data['external_id'];

			Third_Party_Class::g()->update( $third_party->data );
			Contact_Class::g()->update( $contact->data );

			do_action( 'wps_saved_and_associated_contact', $third_party, $contact, false );
		}

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'associatedContactSuccess',
			'view'             => ob_get_clean(),
		) );
	}

	public function save_and_associate_contact() {
		$third_party_id = ! empty( $_POST['parent_id'] ) ? (int) $_POST['parent_id'] : 0;
		$contact        = ! empty( $_POST['contact'] ) ? (array) $_POST['contact'] : array();
		$contact['id']  = ! empty( $_POST['contact']['id'] ) ? (int) $_POST['contact']['id'] : 0;

		if ( empty( $third_party_id ) || empty( $contact['email'] ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party_Class::g()->get( array( 'id' => $third_party_id ), true );

		if ( empty( $contact['id'] ) ) {
			$email                = explode( '@', $contact['email'] );
			$contact['login']     = $email[0];
			$contact['user_pass'] = wp_generate_password();
		}

		$contact       = apply_filters( 'wps_save_and_associate_contact', $contact, $third_party );
		$saved_contact = Contact_Class::g()->update( $contact );

		if ( empty( $contact['id'] ) ) {
			$third_party->data['contact_ids'][] = $saved_contact->data['id'];
		}

		Third_Party_Class::g()->update( $third_party->data );

		do_action( 'wps_saved_and_associated_contact', $third_party, $saved_contact, empty( $contact['id'] ) ? true : false );

		wp_send_json_success();
	}

	public function load_contact() {
		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$contact_id     = ! empty( $_POST['contact_id'] ) ? (int) $_POST['contact_id'] : 0;

		if ( empty( $contact_id ) || empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$contact = Contact_Class::g()->get( array( 'id' => $contact_id ), true );

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

	public function delete_contact() {
		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$contact_id     = ! empty( $_POST['contact_id'] ) ? (int) $_POST['contact_id'] : 0;

		if ( empty( $contact_id ) || empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party_Class::g()->get( array( 'id' => $third_party_id ), true );

		$index = array_search( $contact_id, $third_party->data['contact_ids'], true );

		if ( $index !== FALSE ) {
			array_splice( $third_party->data['contact_ids'], $index, 1 );

			$contact = Contact_Class::g()->get( array( 'id' => $contact_id ), true );

			$contact->data['third_party_id'] = -1;

			Third_Party_Class::g()->update( $third_party->data );
			Contact_Class::g()->update( $contact->data );

			do_action( 'wps_deleted_contact', $third_party, $contact );
		}


		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'deletedContactSuccess',
		) );
	}

	/**
	 * Synchronisation des produits avec dolibarr.
	 *
	 * @since 2.0.0
	 */
	public function ajax_synchro_third_parties() {
		$data = Request_Util::get( 'thirdparties' );

		if ( ! empty( $data ) ) {
			foreach ( $data as $doli_third_party ) {
				// Vérifie l'existence du tier en base de donnée.
				$third_party = Third_Party_Class::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => $doli_third_party->id,
				), true ); // WPCS: slow query ok.

				if ( empty( $third_party ) ) {
					$third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );
				}

				$third_party->data['external_id']      = (int) $doli_third_party->id;
				$third_party->data['title']            = $doli_third_party->name;
				$third_party->data['forme_juridique']  = $doli_third_party->forme_juridique;
				$third_party->data['code_fournisseur'] = $doli_third_party->code_fournisseur;
				$third_party->data['address']          = $doli_third_party->address;
				$third_party->data['town']             = $doli_third_party->town;
				$third_party->data['zip']              = $doli_third_party->zip;
				$third_party->data['state']            = $doli_third_party->state;
				$third_party->data['country']          = $doli_third_party->country;
				$third_party->data['phone']            = $doli_third_party->phone;
				$third_party->data['email']            = $doli_third_party->email;
				$third_party->data['status']           = 'publish';

				$third_party = Third_Party_Class::g()->update( $third_party->data );
				$contact_ids = Contact_Class::g()->synchro_contact( $third_party );

				$third_party->data['contact_ids'] = $contact_ids;
				Third_Party_Class::g()->update( $third_party->data );

			}
		}

		wp_send_json_success( $data );
	}
}

new Third_Party_Action();
