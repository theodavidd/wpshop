<?php
/**
 * Gestion des shortcodes des produits.
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
 * Product Shortcode Class.
 */
class Products_Shortcode {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_shortcode( 'wps_product', array( $this, 'do_shortcode_product' ) );
	}

	/**
	 * Gestion du shortcode
	 *
	 * @since 2.0.0
	 *
	 * @param  array $atts Les paramètres du shortcode. (Voir shorcode_atts
	 * ci dessous pour les paramètres disponibles).
	 */
	public function do_shortcode_product( $atts ) {
		$a = shortcode_atts( array(
			'id'         => 0,
			'ids'        => array(),
			'categories' => array(),
		), $atts );

		$products = array();
		$args     = array();

		if ( ! empty( $a['id'] ) ) {
			$args['id'] = $a['id'];
		}

		if ( ! empty( $a['ids'] ) ) {
			$a['ids']         = explode( ',', $a['ids'] );
			$args['post__in'] = $a['ids'];
		}

		if ( ! empty( $a['categories'] ) ) {
			$args['category_name'] = $a['categories'];
		}

		$products = Product::g()->get( $args );
	}
}

new Products_Shortcode();
