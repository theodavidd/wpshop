<?php
/**
 * Gestion des actions des commandes.
 *
 * Ajoutes une page "Orders" dans le menu de WordPress.
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
 * Action of Order module.
 */
class Doli_Invoice_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_payment_complete', array( $this, 'create_invoice' ), 20, 1 );

		add_action( 'admin_post_wps_download_invoice', array( $this, 'download_invoice' ) );
	}

	public function create_invoice( $data ) {
		$order = Orders_Class::g()->get( array( 'id' => $data['custom'] ), true );

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


		$wp_invoice = Invoice_Class::g()->get( array( 'schema' => true ), true );

		Doli_Invoice::g()->doli_to_wp( $doli_invoice, $wp_invoice );
	}

	public function download_invoice() {
		$order_id = ! empty( $_GET['order_id'] ) ? (int) $_GET['order_id'] : 0;

		if ( ! $order_id ) {
			exit;
		}

		$contact     = Contact_Class::g()->get( array( 'id' => get_current_user_id() ), true );
		$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		$order       = Orders_Class::g()->get( array( 'id' => $order_id ), true );
		$invoice     = Doli_Invoice::g()->get( array( 'post_parent' => $order_id ), true );

		if ( ( isset( $third_party->data ) && $order->data['parent_id'] != $third_party->data['id'] ) && ! current_user_can( 'administrator' ) )
			exit;

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
