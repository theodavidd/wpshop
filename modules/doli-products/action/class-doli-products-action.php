<?php
/**
 * Gestion des actions des produits avec dolibarr.
 *
 * Gestion de la création d'un nouveau produit.
 * Gestion de la mise à jour d'un produit.
 * Gestion de la suppression d'un produit.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
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
 * Doli Products Action Class.
 */
class Doli_Products_Action extends \eoxia\Singleton_Util {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		add_action( 'save_post', array( $this, 'callback_save_post' ), 20, 2 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Appel l'api de dolibarr pour mêttre à jour ou créer un produit sur
	 * dolibarr.
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $post_id ID du produit.
	 * @param  WP_Post $post    Les données du produit.
	 *
	 * @return integer|void.
	 */
	public function callback_save_post( $post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) || ! Settings::g()->dolibarr_is_active() ) {
			return $post_id;
		}

		if ( 'wps-product' !== $post->post_type || 'publish' !== $post->post_status ) {
			return $post_id;
		}

		$product = Product::g()->get( array( 'id' => $post_id ), true );

		if ( empty( $product ) || ( ! empty( $product ) && 0 === $product->data['id'] ) ) {
			return $post_id;
		}

		$product_data           = ! empty( $_POST['product_data'] ) ? (array) $_POST['product_data'] : array();
		$product_data['price']  = isset( $product_data['price'] ) ? (float) round( str_replace( ',', '.', $product_data['price'] ), 2 ) : $product->data['price'];
		$product_data['tva_tx'] = ! empty( $product_data['tva_tx'] ) ? (float) round( str_replace( ',', '.', $product_data['tva_tx'] ), 2 ) : $product->data['tva_tx'];
		if ( is_null( $product_data['price'] ) ) {
			$product_data['price'] = 00.00;
		}

		// Synchronisation Produit.
		if ( ! empty( $product->data['external_id'] ) ) {
			$data = array(
				'label'       => $product->data['title'],
				'description' => $product->data['content'],
				'price'       => $product_data['price'],
				'tva_tx'      => $product_data['tva_tx'],
				'doli_id'     => (int) $product->data['external_id'],
				'wp_id'       => (int) $product->data['id'],
				'type'        => 'product',
			);

			$doli_product = Request_Util::post( 'wpshop/object/', $data );

			update_post_meta( $post_id, '_price', $doli_product->price );
			update_post_meta( $post_id, '_tva_tx', $doli_product->tva_tx );
			update_post_meta( $post_id, '_price_ttc', $doli_product->price_ttc );
			update_post_meta( $post_id, '_tva_amount', ( $doli_product->price_ttc - $doli_product->price ) );
			update_post_meta( $post_id, '_date_last_synchro', $doli_product->last_sync_date );

			// translators: Update product {json_data}.
			\eoxia\LOG_Util::log( sprintf( 'Update product %s', json_encode( $doli_product ) ), 'wpshop2' );
		} else {
			$data = array(
				'ref'         => sanitize_title( $product->data['title'] ),
				'label'       => $product->data['title'],
				'description' => $product->data['content'],
				'price'       => $product_data['price'],
				'tva_tx'      => $product_data['tva_tx'],
				'status'      => 1, // En vente.
				'status_buy'  => 1, // En achat.
				'wp_id'       => $product->data['id'],
				'type'        => 'product',
			);

			$doli_product = Request_Util::post( 'wpshop/object', $data );

			if ( isset( $doli_product->error ) ) {
				set_transient( 'wps_product_already_exist', __( 'Product not created on dolibarr. It already exist on Dolibarr.', 'wpshop' ), 0 );
				return;
			}

			update_post_meta( $post_id, '_price', $doli_product->price );
			update_post_meta( $post_id, '_tva_tx', $doli_product->tva_tx );
			update_post_meta( $post_id, '_price_ttc', $doli_product->price_ttc );
			update_post_meta( $post_id, '_tva_amount', ( $doli_product->price_ttc - $doli_product->price ) );
			update_post_meta( $post_id, '_date_last_synchro', $doli_product->last_sync_date );
			update_post_meta( $post_id, '_external_id', $doli_product->id );

			// translators: Create product {json_data}.
			\eoxia\LOG_Util::log( sprintf( 'Create product %s', json_encode( $doli_product ) ), 'wpshop2' );
		}
	}

	public function admin_notices() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}

		if ( $_REQUEST['action'] != 'edit' ) {
			return;
		}

		$transient = get_transient( 'wps_product_already_exist' );
		delete_transient( 'wps_product_already_exist' );

		if ( ! empty( $transient ) ) {
			?>
			<div class="error">
				<p><?php echo $transient; ?></p>
			</div>
			<?php
		}
	}
}

new Doli_Products_Action();
