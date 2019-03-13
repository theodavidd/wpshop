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
class Product_Class extends \eoxia\Post_Class {

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

	protected $post_type_name = 'Products';

	/**
	 * Récupères la liste des produits et appel la vue "list" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$products = $this->get( array(
			'orderby'        => 'title',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		) );

		\eoxia\View_Util::exec( 'wpshop', 'products', 'list', array(
			'products' => $products,
		) );
	}

	public function callback_register_meta_box() {
		add_meta_box(
			'wps_product_configuration',
			'Product configuration',
			array( $this, 'callback_add_meta_box' ),
			'wps-product'
		);
	}

	public function callback_add_meta_box( $post ) {
		$product = $this->get( array( 'id' => $post->ID ), true );

		if ( empty( $product ) ) {
			$product = $this->get( array( 'schema' => true ), true );
		}

		$dolibarr_option = get_option( 'wps_dolibarr', Settings_Class::g()->default_settings );
		\eoxia\View_Util::exec( 'wpshop', 'products', 'metabox/main', array(
			'product'  => $product,
			'doli_url' => $dolibarr_option['dolibarr_url']
		) );
	}
}

Product_Class::g();
