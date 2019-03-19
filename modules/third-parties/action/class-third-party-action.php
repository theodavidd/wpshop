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
 * Third Party Action Class.
 */
class Third_Party_Action {

	/**
	 * Définition des metabox sur la page.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $metaboxes = null;

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'load-wpshop_page_wps-third-party', array( $this, 'callback_load' ) );

		add_action( 'wp_ajax_third_party_load_title_edit', array( $this, 'load_title_edit' ) );
		add_action( 'admin_post_third_party_save_title', array( $this, 'save_third' ) );
		add_action( 'wp_ajax_third_party_save_title', array( $this, 'save_third' ) );

		add_action( 'wp_ajax_third_party_load_address', array( $this, 'load_billing_address' ) );
		add_action( 'wp_ajax_third_party_save_address', array( $this, 'save_billing_address' ) );

		add_action( 'wp_ajax_third_party_search_contact', array( $this, 'search_contact' ) );
		add_action( 'wp_ajax_third_party_associate_contact', array( $this, 'associate_contact' ) );
		add_action( 'wp_ajax_third_party_save_contact', array( $this, 'save_and_associate_contact' ) );
		add_action( 'wp_ajax_third_party_load_contact', array( $this, 'load_contact' ) );
		add_action( 'wp_ajax_third_party_delete_contact', array( $this, 'delete_contact' ) );

		$this->metaboxes = array(
			'wps-third-party-billing'  => array(
				'title'    => __( 'Billing address', 'wpshop' ),
				'callback' => array( $this, 'metabox_billing_address' ),
			),
			'wps-third-party-contacts' => array(
				'title'    => __( 'Contacts', 'wpshop' ),
				'callback' => array( $this, 'metabox_contacts' ),
			),
			'wps-third-party-orders'   => array(
				'title'    => __( 'Orders', 'wpshop' ),
				'callback' => array( $this, 'metabox_orders' ),
			),
			'wps-third-party-invoices' => array(
				'title'    => __( 'Invoices', 'wpshop' ),
				'callback' => array( $this, 'metabox_invoices' ),
			),
		);
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page(
			'wps-order',
			__( 'Third Parties', 'wpshop' ),
			__( 'Third Parties', 'wpshop' ),
			'manage_options',
			'wps-third-party',
			array( $this, 'callback_add_menu_page' )
		);
	}

	/**
	 * Gestion JS des metabox
	 *
	 * @since 2.0.0
	 */
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
		if ( isset( $_GET['id'] ) ) {
			$third_party  = Third_Party::g()->get( array( 'id' => $_GET['id'] ), true );
			$args_metabox = array(
				'third_party' => $third_party,
				'id'          => $_GET['id'],
			);

			if ( ! empty( $this->metaboxes ) ) {
				foreach ( $this->metaboxes as $key => $metabox ) {
					add_meta_box(
						$key,
						$metabox['title'],
						$metabox['callback'],
						'wps-third-party',
						'normal',
						'default',
						$args_metabox
					);
				}
			}

			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single', array( 'third_party' => $third_party ) );
		} else {
			$args = array(
				'post_type'      => 'wps-third-party',
				'posts_per_page' => -1,
			);

			if ( ! empty( $_GET['s'] ) ) {
				$args['s'] = $_GET['s'];
			}

			$count = count( get_posts( $args ) );

			$number_page  = ceil( $count / 25 );
			$current_page = isset( $_GET['current_page'] ) ? $_GET['current_page'] : 1;

			$base_url = admin_url( 'admin.php?page=wps-third-party' );

			$begin_url = $base_url . '&current_page=1';
			$end_url   = $base_url . '&current_page=' . $number_page;

			$prev_url = $base_url . '&current_page=' . ( $current_page - 1 );
			$next_url = $base_url . '&current_page=' . ( $current_page + 1 );

			if ( ! empty( $_GET['s'] ) ) {
				$begin_url .= '&s=' . $_GET['s'];
				$end_url   .= '&s=' . $_GET['s'];
				$prev_url  .= '&s=' . $_GET['s'];
				$next_url  .= '&s=' . $_GET['s'];
			}

			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'main', array(
				'number_page'  => $number_page,
				'current_page' => $current_page,
				'count'        => $count,
				'begin_url'    => $begin_url,
				'end_url'      => $end_url,
				'prev_url'     => $prev_url,
				'next_url'     => $next_url,
			) );
		}
	}

	/**
	 * Appel la vue de la metabox des adresses.
	 *
	 * @param  WP_Post $post          Le post actuel.
	 * @param  array   $callback_args Les paramètres envoyées dans le add_meta_box.
	 *
	 * @since 2.0.0
	 */
	public function metabox_billing_address( $post, $callback_args ) {
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-billing-address', array(
			'third_party' => $callback_args['args']['third_party'],
		) );
	}

	/**
	 * Appel la vue de la metabox des contacts.
	 *
	 * @param  WP_Post $post          Le post actuel.
	 * @param  array   $callback_args Les paramètres envoyées dans le add_meta_box.
	 *
	 * @since 2.0.0
	 */
	public function metabox_contacts( $post, $callback_args ) {
		$contacts = array();

		if ( ! empty( $callback_args['args']['third_party']->data['contact_ids'] ) ) {
			$contacts = Contact::g()->get( array( 'include' => $callback_args['args']['third_party']->data['contact_ids'] ) );
		}
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-contacts', array(
			'third_party' => $callback_args['args']['third_party'],
			'contacts'    => $contacts,
		) );
	}

	/**
	 * Appel la vue de la metabox des commandes.
	 *
	 * @param  WP_Post $post          Le post actuel.
	 * @param  array   $callback_args Les paramètres envoyées dans le add_meta_box.
	 *
	 * @since 2.0.0
	 */
	public function metabox_orders( $post, $callback_args ) {
		$orders = Doli_Order::g()->get( array( 'post_parent' => $callback_args['args']['id'] ) );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as &$order ) {
				$order->data['invoice'] = Doli_Invoice::g()->get( array( 'post_parent' => $order->data['id'] ), true );
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-orders', array(
			'orders' => $orders,
		) );
	}

	/**
	 * Appel la vue de la metabox des factures.
	 *
	 * @param  WP_Post $post          Le post actuel.
	 * @param  array   $callback_args Les paramètres envoyées dans le add_meta_box.
	 *
	 * @since 2.0.0
	 */
	public function metabox_invoices( $post, $callback_args ) {

		$invoices = Doli_Invoice::g()->get( array( 'author__in' => $callback_args['args']['third_party']->data['contact_ids'] ) );

		if ( ! empty( $invoices ) ) {
			foreach ( $invoices as &$invoice ) {
				$invoice->data['order'] = Doli_Order::g()->get( array( 'id' => $invoice->data['parent_id'] ), true );
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'metaboxes/metabox-invoices', array(
			'invoices' => $invoices,
		) );
	}

	/**
	 * Renvoies la vue d'édition d'un titre.
	 *
	 * @since 2.0.0
	 */
	public function load_title_edit() {
		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : -1;

		if ( -1 == $post_id ) {
			exit;
		}

		$third_party = Third_Party::g()->get( array( 'id' => $post_id ), true );

		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single-title-edit', array(
			'third_party' => $third_party,
		) );
		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'thirdParties',
			'callback_success' => 'loaddedTitleEdit',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Enregistres le titre du tier.
	 *
	 * @since 2.0.0
	 */
	public function save_third() {
		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : -1;
		$title   = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

		if ( -1 == $post_id ) {
			exit;
		}

		$third_party = Third_Party::g()->get( array( 'id' => $post_id ), true );

		$third_party->data['id'] = $post_id;

		if ( empty( $post_id ) ) {
			$third_party->data['status'] = 'publish';
		}
		$third_party->data['title'] = $title;

		$third_party = Third_Party::g()->update( $third_party->data );

		$external_id = do_action( 'wps_saved_third_party', $third_party->data );

		$third_party->data['external_id'] = $external_id;
		$third_party                      = Third_Party::g()->update( $third_party->data );

		if ( wp_doing_ajax() ) {
			ob_start();
			\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single-title', array(
				'third_party' => $third_party,
			) );
			wp_send_json_success( array(
				'namespace'        => 'wpshop',
				'module'           => 'thirdParties',
				'callback_success' => 'savedThird',
				'view'             => ob_get_clean(),
			) );
		} else {
			wp_redirect( admin_url( 'admin.php?page=wps-third-party&id=' . $third_party->data['id'] ) );
			exit;
		}
	}

	/**
	 * Charges la vue pour éditer l'adresse du tier.
	 *
	 * @since 2.0.0
	 */
	public function load_billing_address() {
		$third_party_id = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;

		if ( empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );

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

	/**
	 * Met à jour l'adresse du tier
	 *
	 * @todo: Merger avec save third
	 * @since 2.0.0
	 */
	public function save_billing_address() {
		$third_party_id   = ! empty( $_POST['third_party_id'] ) ? (int) $_POST['third_party_id'] : 0;
		$third_party_form = ! empty( $_POST['third_party'] ) ? (array) $_POST['third_party'] : array();

		if ( empty( $third_party_id ) ) {
			wp_send_json_error();
		}

		$third_party = Third_Party::g()->get( array( 'id' => $third_party_id ), true );

		$third_party->data['title']   = $third_party_form['title'];
		$third_party->data['address'] = $third_party_form['address'];
		$third_party->data['zip']     = $third_party_form['zip'];
		$third_party->data['email']   = $third_party_form['email'];
		$third_party->data['town']    = $third_party_form['town'];
		$third_party->data['phone']   = $third_party_form['phone'];

		$third_party = Third_Party::g()->update( $third_party->data );

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

	/**
	 * Recherches un contact dans la base de donnée de WP.
	 *
	 * @since 2.0.0
	 */
	public function search_contact() {
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

new Third_Party_Action();
