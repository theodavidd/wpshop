<?php
/**
 * Les fonctions principales des produits.
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
 * Product Class.
 */
class Product extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Product_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-product';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'product';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'product';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = 'wps-product-cat';

	/**
	 * Le nom du post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $post_type_name = 'Products';

	public $limit = 10;

	public $option_per_page = 'product_per_page';

	/**
	 * Récupères la liste des produits et appel la vue "list" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$per_page = get_user_meta( get_current_user_id(), $this->option_per_page, true );

		if ( empty( $per_page ) || 1 > $per_page ) {
			$per_page = $this->limit;
		}

		$current_page = isset( $_GET['current_page'] ) ? $_GET['current_page'] : 1;

		$s = ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

		$product_ids = Product::g()->search( $s, array(
			'offset'         => ( $current_page - 1 ) * $per_page,
			'posts_per_page' => $per_page,
			'post_status'    => 'any',
		) );

		$products = array();

		if ( ! empty( $product_ids ) ) {
			$products = $this->get( array(
				'post__in' => $product_ids,
			) );
		}

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		\eoxia\View_Util::exec( 'wpshop', 'products', 'list', array(
			'products' => $products,
			'doli_url' => $dolibarr_option['dolibarr_url'],
		) );
	}

	/**
	 * Ajoutes une metabox pour configurer le produit.
	 *
	 * @since 2.0.0
	 */
	public function callback_register_meta_box() {
		add_meta_box(
			'wps_product_configuration',
			'Product configuration',
			array( $this, 'callback_add_meta_box' ),
			'wps-product'
		);
	}

	/**
	 * La vue de la metabox pour configurer le produit
	 *
	 * @param WP_Post $post Le produit.
	 *
	 * @since 2.0.0
	 */
	public function callback_add_meta_box( $post ) {
		$product = $this->get( array( 'id' => $post->ID ), true );

		if ( empty( $product ) ) {
			$product = $this->get( array( 'schema' => true ), true );
		}

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );
		\eoxia\View_Util::exec( 'wpshop', 'products', 'metabox/main', array(
			'product'  => $product,
			'doli_url' => $dolibarr_option['dolibarr_url'],
		) );
	}

	public function search( $s = '', $default_args = array(), $count = false ) {
		$args = array(
			'post_type'      => 'wps-product',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => 'any',
		);

		$args = wp_parse_args( $default_args, $args );

		if ( ! empty( $s ) ) {
			$products_id = get_posts( array(
				's'              => $s,
				'fields'         => 'ids',
				'post_type'      => 'wps-product',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			) );

			if ( empty( $products_id ) ) {
				if ( $count ) {
					return 0;
				} else {
					return array();
				}
			} else {
				$args['post__in'] = $products_id;

				if ( $count ) {
					return count( get_posts( $args ) );
				} else {
					return $products_id;
				}
			}
		}

		if ( $count ) {
			return count( get_posts( $args ) );
		} else {
			return get_posts( $args );
		}
	}
}

Product::g();
