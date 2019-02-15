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
		add_action( 'wps_synchro_order', array( $this, 'synchro_invoice' ), 10, 2 );
		add_action( 'wps_payment_complete', array( $this, 'create_invoice' ), 20, 1 );

		add_action( 'admin_post_wps_download_invoice', array( $this, 'download_invoice' ) );
	}

	public function synchro_invoice( $wp_order, $doli_proposal ) {
		$order = Request_Util::get( 'orders/' . $doli_proposal->id );

		if ( isset( $order->linkedObjectsIds->facture ) ) {
			if ( ! empty( $order->linkedObjectsIds->facture ) ) {
				foreach ( $order->linkedObjectsIds->facture as $linked_id => $invoice_id ) {
					$doli_invoice = Request_Util::get( 'invoices/' . (int) $invoice_id );

					$invoice = Doli_Invoice::g()->get( array(
						'meta_key'   => '_external_id',
						'meta_value' => (int) $invoice_id,
					), true );

					if ( empty( $invoice ) ) {
						$invoice = Doli_Invoice::g()->get( array( 'schema' => true ), true );
					}

					$invoice->data['external_id'] = (int) $doli_invoice->id;
					$invoice->data['post_parent'] = (int) $wp_order['id'];
					$invoice->data['title']       = $doli_invoice->ref;
					$invoice->data['status']      = 'publish';
					$invoice->data['author_id']   = Third_Party_Class::g()->get_id_or_sync( $doli_invoice->socid );

					$invoice = Doli_Invoice::g()->update( $invoice->data );

					do_action( 'wps_synchro_invoice', $invoice->data, $doli_invoice );
				}
			}
		}
	}

	public function create_invoice( $data ) {
		$order = Orders_Class::g()->get( array( 'id' => $data['custom'] ), true );

		$invoice = Request_Util::post( 'invoices/createfromorder/' . $order->data['external_id'] );
		$invoice = Request_Util::post( 'invoices/' . $invoice->id . '/validate', array(
			'notrigger' => 0,
		) );
		$invoice = Request_Util::post( 'invoices/' . $invoice->id . '/settopaid' );

		Request_Util::put( 'documents/builddoc', array(
			'module_part'   => 'invoice',
			'original_file' => $invoice->ref . '/' . $invoice->ref . '.pdf',
			'doc_template'  => 'crabe',
			'langcode'      => 'fr_FR',
		) );

		Doli_Invoice::g()->sync( $order, $invoice );
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
