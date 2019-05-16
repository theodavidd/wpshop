<?php
/**
 * Gestion des actions des produits.
 *
 * Ajoutes une page "Product" dans le menu de WordPress.
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
 * Product Action Class.
 */
class Product_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ), 0 );
		add_action( 'save_post', array( $this, 'callback_save_post' ), 10, 2 );
		add_action( 'template_redirect', array( $this, 'init_product_archive_page' ) );

	}

	/**
	 * Initialise la page "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		add_menu_page( __( 'Products', 'wpshop' ), __( 'Products', 'wpshop' ), 'manage_options', 'wps-product', '', 'dashicons-cart' );
		add_submenu_page( 'wps-product', __( 'Products', 'wpshop' ), __( 'Products', 'wpshop' ), 'manage_options', 'wps-product', array( $this, 'callback_add_menu_page' ) );
		add_submenu_page( 'wps-product', __( 'Add', 'wpshop' ), __( 'Add', 'wpshop' ), 'manage_options', 'post-new.php?post_type=wps-product' );
		add_submenu_page( 'wps-product', __( 'Products Category', 'wpshop' ), __( 'Products Category', 'wpshop' ), 'manage_options', 'edit-tags.php?taxonomy=wps-product-cat&post_type=wps-product' );
	}

	/**
	 * Appel la vue "main" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		$args = array(
			'post_type'      => 'wps-product',
			'posts_per_page' => -1,
		);

		$count = count( get_posts( $args ) );

		\eoxia\View_Util::exec( 'wpshop', 'products', 'main', array(
			'count' => $count,
		) );
	}

	/**
	 * Enregistres les métadonnées d'un produit.
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $post_id L'ID du produit.
	 * @param  WP_Post $post    Les données du produit.
	 *
	 * @return integer|void
	 */
	public function callback_save_post( $post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( 'wps-product' !== $post->post_type && 'publish' !== $post->post_status ) {
			return $post_id;
		}

		$product = Product::g()->get( array( 'id' => $post_id ), true );

		if ( empty( $product ) || ( ! empty( $product ) && 0 === $product->data['id'] ) ) {
			return $post_id;
		}

		$product_data                         = ! empty( $_POST['product_data'] ) ? (array) $_POST['product_data'] : array();
		$product_data['price']                = isset( $product_data['price'] ) ? (float) round( str_replace( ',', '.', $product_data['price'] ), 2 ) : $product->data['price'];
		$product_data['tva_tx']               = ! empty( $product_data['tva_tx'] ) ? (float) round( str_replace( ',', '.', $product_data['tva_tx'] ), 2 ) : $product->data['tva_tx'];
		$product_data['product_downloadable'] = ( ! empty( $product_data['product_downloadable'] ) && 'true' === $product_data['product_downloadable'] ) ? true : false;
		$product_data['price_ttc']            = price2num( $product_data['price'] * ( 1 + ( $product_data['tva_tx'] / 100 ) ) );

		update_post_meta( $post_id, '_price', $product_data['price'] );
		update_post_meta( $post_id, '_tva_tx', $product_data['tva_tx'] );
		update_post_meta( $post_id, '_price_ttc', $product_data['price_ttc'] );
		update_post_meta( $post_id, '_product_downloadable', $product_data['product_downloadable'] );
	}


	/**
	 * Création d'une page spéciale pour afficher les archives
	 *
	 * @since 2.0.0
	 */
	public function init_product_archive_page() {
		global $post;
		global $wp_query;

		$queried_object = get_queried_object();
		$shop_page_id   = get_option( 'wps_page_ids', Pages::g()->default_options );
		$shop_page      = get_post( $shop_page_id['shop_id'] );

		// Arrête la fonction si on n'est pas une sur une page catégorie de produit.
		if ( ! is_tax( get_object_taxonomies( 'wps-product-cat' ) ) ) {
			return;
		}

		// Récupère la Description de la catégorie.
		if ( ! empty( $queried_object->description ) ) {
			$archive_description = '<div class="wps-archive-description">' . $queried_object->description . '</div>';
		} else {
			$archive_description = '';
		}
		$archive_description = apply_filters( 'wps_archive_description', $archive_description );

		// Création de notre propre page.
		$dummy_post_properties = array(
			'ID'                    => 0,
			'post_status'           => 'publish',
			'post_author'           => $shop_page->post_author,
			'post_parent'           => 0,
			'post_type'             => 'page',
			'post_date'             => $shop_page->post_date,
			'post_date_gmt'         => $shop_page->post_date_gmt,
			'post_modified'         => $shop_page->post_modified,
			'post_modified_gmt'     => $shop_page->post_modified_gmt,
			'post_content'          => $archive_description . $this->generate_archive_page_content(),
			'post_title'            => $queried_object->name,
			'post_excerpt'          => '',
			'post_content_filtered' => '',
			'post_mime_type'        => '',
			'post_password'         => '',
			'post_name'             => $queried_object->slug,
			'guid'                  => '',
			'menu_order'            => 0,
			'pinged'                => '',
			'to_ping'               => '',
			'ping_status'           => '',
			'comment_status'        => 'closed',
			'comment_count'         => 0,
			'filter'                => 'raw',
		);

		$post = new \WP_Post( (object) $dummy_post_properties );

		$wp_query->post  = $post;
		$wp_query->posts = array( $post );

		$wp_query->post_count    = 1;
		$wp_query->is_404        = false;
		$wp_query->is_page       = true;
		$wp_query->is_single     = true;
		$wp_query->is_archive    = false;
		$wp_query->is_tax        = true;
		$wp_query->max_num_pages = 0;

		setup_postdata( $post );
		remove_all_filters( 'the_content' );
		remove_all_filters( 'the_excerpt' );
		add_filter( 'template_include', array( $this, 'force_single_template_filter' ) );
	}

	/**
	 * Force l'affichage d'un template single
	 *
	 * @since 2.0.0
	 *
	 * @param string $template Chemin du template.
	 * @return string
	 */
	public function force_single_template_filter( $template ) {
		$possible_templates = array(
			'page',
			'single',
			'singular',
			'index',
		);

		foreach ( $possible_templates as $possible_template ) {
			$path = get_query_template( $possible_template );
			if ( $path ) {
				return $path;
			}
		}

		return $template;
	}

	/**
	 * Génère le HTML de la page archive
	 *
	 * @since 2.0.0
	 *
	 * @return string Output HTML content
	 */
	public function generate_archive_page_content() {
		global $wp_query;
		global $post;

		$wps_query = new \WP_Query( $wp_query->query_vars );

		foreach( $wps_query->posts as &$product ) {
			$product->price_ttc = get_post_meta( $product->ID, '_price_ttc', true );
		}

		setup_postdata( $post );

		ob_start();
		include( Template_Util::get_template_part( 'products', 'wps-product-grid-container' ) );
		$view = ob_get_clean();

		return $view;
	}

}

new Product_Action();
