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
 * Doli Invoice Class.
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

	/**
	 * Le nom du post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $post_type_name = 'Doli Invoice';

	/**
	 * Synchronise depuis Dolibarr vers WP.
	 *
	 * @since 2.0.0
	 *
	 * @param stdClass      $doli_invoice Les données de la facture venant de
	 * Dolibarr.
	 * @param Invoice_Model $wp_invoice   Les données de la facture WP.
	 *
	 * @return Invoice_Model              Les données de la facture WP avec les
	 * données de Dolibarr.
	 */
	public function doli_to_wp( $doli_invoice, $wp_invoice ) {
		$order = null;

		$doli_invoice                    = Request_Util::get( 'invoices/' . $doli_invoice->id ); // Charges par la route single des factures pour avoir accès à linkedObjectsIds->commande.
		$wp_invoice->data['external_id'] = (int) $doli_invoice->id;

		if ( ! empty( $doli_invoice->linkedObjectsIds->commande ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.
			$order_id                        = Orders::g()->get_wp_id_by_doli_id( end( $doli_invoice->linkedObjectsIds->commande ) );
			$wp_invoice->data['post_parent'] = $order_id;

			$order                         = Orders::g()->get( array( 'id' => $order_id ), true );
			$wp_invoice->data['author_id'] = $order->data['author_id'];
		}
		$wp_invoice->data['title']          = $doli_invoice->ref;
		$wp_invoice->data['lines']          = $doli_invoice->lines;
		$wp_invoice->data['total_ttc']      = $doli_invoice->total_ttc;
		$wp_invoice->data['total_ht']       = $doli_invoice->total_ht;
		$wp_invoice->data['total_ht']       = $doli_invoice->total_ht;
		$wp_invoice->data['payment_method'] = Doli_Payment::g()->convert_to_wp( $doli_invoice->mode_reglement_code );
		$wp_invoice->data['paye']           = (int) $doli_invoice->paye;

		$status = '';

		switch ( $doli_invoice->statut ) {
			case 1:
				$status = 'wps-canceled';
				break;
			case 0:
				$status = 'draft';
				break;
			case 1:
				$status = 'publish';
				break;
			default:
				$status = 'publish';
				break;
		}

		$wp_invoice->data['status'] = $status;

		$wp_invoice = Doli_Invoice::g()->update( $wp_invoice->data );

		// Récupères les paiements attachés à cette facture.
		$doli_payments = Request_Util::get( 'invoices/' . $doli_invoice->id . '/payments' );

		if ( ! empty( $doli_payments ) ) {
			foreach ( $doli_payments as $doli_payment ) {
				$payment = Doli_Payment::g()->get( array(
					'post_title' => $doli_payment->ref,
				), true );

				if ( empty( $payment ) ) {
					$payment = Doli_Payment::g()->get( array( 'schema' => true ), true );
				}

				$payment->data['post_parent']  = (int) $wp_invoice->data['id'];
				$payment->data['title']        = $doli_payment->ref;
				$payment->data['status']       = 'publish';
				$payment->data['payment_type'] = $doli_payment->type;
				$payment->data['amount']       = $doli_payment->amount;
				$payment->data['date']         = $doli_payment->date;

				Doli_Payment::g()->update( $payment->data );
			}
		}

		do_action( 'wps_synchro_invoice', $wp_invoice->data, $doli_invoice );

		return $wp_invoice;
	}
}

Doli_Invoice::g();
