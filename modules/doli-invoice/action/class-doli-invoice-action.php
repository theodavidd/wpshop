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
		$doli_invoice = Request_Util::post( 'invoices/' . $doli_invoice->id . '/settopaid' );

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
	}

	/**
	 * Télécharges la facture.
	 *
	 * @since 2.0.0
	 */
	public function download_invoice() {
		$order_id = ! empty( $_GET['order_id'] ) ? (int) $_GET['order_id'] : 0;

		if ( ! $order_id ) {
			exit;
		}

		$contact     = Contact::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$order       = Doli_Order::g()->get( array( 'id' => $order_id ), true );
		$invoice     = Doli_Invoice::g()->get( array( 'post_parent' => $order_id ), true );

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
