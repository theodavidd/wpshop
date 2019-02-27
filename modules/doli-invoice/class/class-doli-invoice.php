<?php
/**
 * Les fonctions principales des produits.
 *
 * Le controlleur du modèle Product_Model.
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
* Handle product
*/
class Doli_Invoice extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Doli_Invoice_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-doli-invoice';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'doli-invoice';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'doli-invoice';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	protected $post_type_name = 'Doli Invoice';

	public function sync( $wp_order, $doli_invoice )
	{
		$invoice = Doli_Invoice::g()->get( array(
			'meta_key'   => '_external_id',
			'meta_value' => (int) $doli_invoice->id,
		), true );

		if ( empty( $invoice ) ) {
			$invoice = Doli_Invoice::g()->get( array( 'schema' => true ), true );
		}

		$invoice->data['external_id'] = (int) $doli_invoice->id;
		$invoice->data['post_parent'] = (int) $wp_order->data['id'];
		$invoice->data['title']       = $doli_invoice->ref;
		$invoice->data['status']      = 'publish';
		$invoice->data['author_id']   = $wp_order->data['author_id'];

		$invoice = Doli_Invoice::g()->update( $invoice->data );

		do_action( 'wps_synchro_invoice', $invoice->data, $doli_invoice );
	}

	public function synchro( $index, $limit ) {
		$doli_invoices = Request_Util::get( 'invoices?sortfield=t.rowid&sortorder=ASC&limit=' . $limit . '&page=' . ( $index / $limit ) );

		if ( ! empty( $doli_invoices ) ) {
			foreach ( $doli_invoices as $doli_invoice ) {
				$doli_invoice = Request_Util::get( 'invoices/' . $doli_invoice->id );

				$invoice = Doli_Invoice::g()->get( array(
					'meta_key'   => '_external_id',
					'meta_value' => (int) $doli_invoice->id,
				), true );

				if ( empty( $invoice ) ) {
					$invoice = Doli_Invoice::g()->get( array( 'schema' => true ), true );
				}

				$invoice->data['external_id'] = (int) $doli_invoice->id;
				$invoice->data['post_parent'] = Orders_Class::g()->get_id_by_doli_id( end( $doli_invoice->linkedObjectsIds->commande ) );
				$invoice->data['title']       = $doli_invoice->ref;
				$invoice->data['status']      = 'publish';
				$invoice->data['author_id']   = Third_Party_Class::g()->get_id_or_sync( $doli_invoice->socid );

				$invoice = Doli_Invoice::g()->update( $invoice->data );

				$doli_payments = Request_Util::get( 'invoices/' . $doli_invoice->id . '/payments' );

				if ( ! empty( $doli_payments ) ) {
					foreach ( $doli_payments as $doli_payment ) {
						$payment = Doli_Payment::g()->get( array(
							'post_title' => $doli_payment->ref,
						), true );

						if ( empty( $payment ) ) {
							$payment = Doli_Payment::g()->get( array( 'schema' => true ), true );
						}

						$payment->data['post_parent']  = (int) $invoice->data['id'];
						$payment->data['title']        = $doli_payment->ref;
						$payment->data['status']       = 'publish';
						$payment->data['payment_type'] = $doli_payment->type;
						$payment->data['amount']       = $doli_payment->amount;
						$payment->data['date']         = $doli_payment->date;

						Doli_Payment::g()->update( $payment->data );
					}
				}
			}
		}

		return true;
	}
}

Doli_Invoice::g();
