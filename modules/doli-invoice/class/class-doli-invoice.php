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
}

Doli_Invoice::g();
