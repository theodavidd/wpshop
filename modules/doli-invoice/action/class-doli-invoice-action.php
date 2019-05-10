<?php
/**
 * Gestion des actions des factures.
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
 * Doli Invoice Action Class.
 */
class Doli_Invoice_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_tmp_invoice_dir' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ), 10 );

		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'wps_payment_complete', array( $this, 'create_invoice' ), 20, 1 );

		add_action( 'admin_post_wps_download_invoice', array( $this, 'download_invoice' ) );
	}

	/**
	 * Créer un répertoire temporaire pour les factures.
	 *
	 * @since 2.0.0
	 */
	public function create_tmp_invoice_dir() {
		$dir = wp_upload_dir();

		$path = $dir['basedir'] . '/invoices';

		if ( wp_mkdir_p( $path ) && ! file_exists( $path . '/.htaccess' ) ) {
			$f = fopen( $path . '/.htaccess', 'a+' );
			fwrite( $f, "Options -Indexes\r\ndeny from all" );
			fclose( $f );
		}
	}

	/**
	 * Ajoutes la metabox details
	 *
	 * @since 2.0.0
	 */
	public function add_meta_box() {
		if ( isset( $_GET['id'] ) && isset( $_GET['page'] ) && 'wps-invoice' == $_GET['page'] ) {
			$invoice = Doli_Invoice::g()->get( array( 'id' => $_GET['id'] ), true );

			$args_metabox = array(
				'invoice' => $invoice,
				'id'      => $_GET['id'],
			);

			/* translators: Order details CO00010 */
			$box_invoice_detail_title = sprintf( __( 'Invoice details %s', 'wpshop' ), $invoice->data['title'] );

			add_meta_box( 'wps-invoice-customer', $box_invoice_detail_title, array( $this, 'callback_meta_box' ), 'wps-invoice', 'normal', 'default', $args_metabox );
		}
	}

	/**
	 * Initialise la page "Facture".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_submenu_page( 'wps-order', __( 'Invoices', 'wpshop' ), __( 'Invoices', 'wpshop' ), 'manage_options', 'wps-invoice', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Affichage de la vue du menu
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		if ( isset( $_GET['id'] ) ) {
			$invoice = Doli_Invoice::g()->get( array( 'id' => $_GET['id'] ), true );
			\eoxia\View_Util::exec( 'wpshop', 'doli-invoice', 'single', array( 'invoice' => $invoice ) );
		} else {
			$args = array(
				'post_type'      => 'wps-doli-invoice',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			);

			$count = count( get_posts( $args ) );

			\eoxia\View_Util::exec( 'wpshop', 'doli-invoice', 'main', array(
				'count' => $count,
			) );
		}
	}

	/**
	 * La metabox des détails de la commande
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Post $post          Les données du post.
	 * @param  array   $callback_args Tableau contenu les données de la commande.
	 */
	public function callback_meta_box( $post, $callback_args ) {
		$invoice      = $callback_args['args']['invoice'];
		$third_party  = Third_Party::g()->get( array( 'id' => $invoice->data['third_party_id'] ), true );
		$link_invoice = '';

		if ( ! empty( $invoice ) ) {
			$invoice->data['payments'] = array();
			$invoice->data['payments'] = Doli_Payment::g()->get( array( 'post_parent' => $invoice->data['id'] ) );
			$link_invoice              = admin_url( 'admin-post.php?action=wps_download_invoice_wpnonce=' . wp_create_nonce( 'download_invoice' ) . '&invoice_id=' . $invoice->data['id'] );
		}

		\eoxia\View_Util::exec( 'wpshop', 'doli-invoice', 'metabox-invoice-details', array(
			'invoice'      => $invoice,
			'third_party'  => $third_party,
			'link_invoice' => $link_invoice,
		) );
	}

	/**
	 * Box affichant les produits de la commande
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Post $post          Les données du post.
	 * @param  array   $callback_args Tableau contenu les données de la commande.
	 */
	public function callback_products( $post, $callback_args ) {
		$invoice = $callback_args['args']['invoice'];

		$tva_lines = array();

		if ( ! empty( $invoice->data['lines'] ) ) {
			foreach ( $invoice->data['lines'] as $line ) {
				if ( empty( $tva_lines[ $line['tva_tx'] ] ) ) {
					$tva_lines[ $line['tva_tx'] ] = 0;
				}

				$tva_lines[ $line['tva_tx'] ] += $line['total_tva'];
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'doli-invoice', 'metabox-invoice-products', array(
			'invoice'   => $invoice,
			'tva_lines' => $tva_lines,
		) );
	}

	/**
	 * Créer la facture si la commande est payé.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $data Les données venant de PayPal.
	 */
	public function create_invoice( $data ) {
		$order = Doli_Order::g()->get( array( 'id' => (int) $data['custom'] ), true );

		$doli_invoice = Request_Util::post( 'invoices/createfromorder/' . $order->data['external_id'] );
		$doli_invoice = Request_Util::post( 'invoices/' . $doli_invoice->id . '/validate', array(
			'notrigger' => 0,
		) );

		$doli_payment = Request_Util::post( 'invoices/' . $doli_invoice->id . '/payments', array(
			'datepaye'          => current_time( 'timestamp' ),
			'paiementid'        => (int) $doli_invoice->mode_reglement_id,
			'closepaidinvoices' => 'yes',
			'accountid'         => 1,
		) );

		Request_Util::put( 'documents/builddoc', array(
			'module_part'   => 'invoice',
			'original_file' => $doli_invoice->ref . '/' . $doli_invoice->ref . '.pdf',
			'doc_template'  => 'crabe',
			'langcode'      => 'fr_FR',
		) );

		$wp_invoice = Doli_Invoice::g()->get( array( 'schema' => true ), true );
		$wp_invoice = Doli_Invoice::g()->doli_to_wp( $doli_invoice, $wp_invoice );

		$wp_invoice->data['author_id'] = $order->data['author_id'];

		Doli_Invoice::g()->update( $wp_invoice->data );

		$third_party = Third_Party::g()->get( array( 'id' => $order->data['parent_id'] ), true );
		$contact     = Contact::g()->get( array( 'id' => $wp_invoice->data['author_id'] ), true );

		$invoice_file = Request_Util::get( 'documents/download?module_part=facture&original_file=' . $wp_invoice->data['title'] . '/' . $wp_invoice->data['title'] . '.pdf' );
		$content      = base64_decode( $invoice_file->content );

		$dir       = wp_upload_dir();
		$path      = $dir['basedir'] . '/invoices';
		$path_file = $path . '/' . $wp_invoice->data['title'] . '.pdf';

		$f = fopen( $path . '/' . $wp_invoice->data['title'] . '.pdf', 'a+' );
		fwrite( $f, $content );
		fclose( $f );

		Emails::g()->send_mail( $contact->data['email'], 'wps_email_customer_invoice', array(
			'order'       => $order,
			'invoice'     => $wp_invoice,
			'third_party' => $third_party,
			'contact'     => $contact,
			'attachments' => array( $path_file ),
		) );

		unlink( $path_file );

		// Création du règlement vers WP.
		$wp_payment = Doli_Payment::g()->get( array( 'schema' => true ), true );

		$doli_payments = Request_Util::get( 'invoices/' . $doli_invoice->id . '/payments' );
		$doli_payment  = $doli_payments[0];

		Doli_Payment::g()->doli_to_wp( $doli_invoice->id, $doli_payment, $wp_payment );
	}

	/**
	 * Télécharges la facture.
	 *
	 * @since 2.0.0
	 */
	public function download_invoice() {
		check_admin_referer( 'download_invoice' );

		$order_id = ! empty( $_GET['order_id'] ) ? (int) $_GET['order_id'] : 0;
		$avoir    = ! empty( $_GET['avoir'] ) ? (int) $_GET['avoir'] : 0;

		if ( ! $order_id ) {
			exit;
		}

		$contact     = Contact::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$order       = Doli_Order::g()->get( array( 'id' => $order_id ), true );
		$invoice     = Doli_Invoice::g()->get( array(
			'post_parent'    => $order_id,
			'meta_key'       => '_avoir',
			'meta_value'     => $avoir,
			'posts_per_page' => 1,
		), true );

		if ( ( isset( $third_party->data ) && $order->data['parent_id'] != $third_party->data['id'] ) && ! current_user_can( 'administrator' ) ) {
			exit;
		}

		$invoice_file = Request_Util::get( 'documents/download?module_part=facture&original_file=' . $invoice->data['title'] . '/' . $invoice->data['title'] . '.pdf' );

		$content = base64_decode( $invoice_file->content );

		header( 'Cache-Control: no-cache' );
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: inline; filename="' . $invoice->data['title'] . '.pdf"' );
		header( 'Content-Length: ' . strlen( $content ) );

		echo $content;

		exit;
	}
}

new Doli_Invoice_Action();
