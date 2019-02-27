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
class Pages_Class extends \eoxia\Singleton_Util {
	public $default_options;

	public $page_ids;

	protected function construct() {

		$this->default_options = array(
			'shop_id'           => 0,
			'cart_id'           => 0,
			'checkout_id'       => 0,
			'my_account_id'     => 0,
			'valid_checkout_id' => 0,
			'valid_proposal_id' => 0,
		);

		$this->page_ids = get_option( 'wps_page_ids', $this->default_options );
	}

	public function get_slug_link_shop_page( $page_id ) {
		if ( ! empty( $this->page_ids ) ) {
			foreach ( $this->page_ids as $key => $id ) {
				if ( $id === $page_id ) {
					$page = get_page( $page_id );
					return $page->post_name;
				}
			}
		}

		return '';
	}

	public function get_slug_shop_page() {

		$page = get_page( $this->page_ids['shop_id'] );

		if ( ! $page ) {
			return false;
		}

		return $page->post_name;
	}

	public function get_account_link() {
		return get_permalink( $this->page_ids['my_account_id'] );
	}

	public function get_cart_link() {
		return get_permalink( $this->page_ids['cart_id'] );
	}

	public function get_checkout_link() {
		return get_permalink( $this->page_ids['checkout_id'] );
	}

	public function get_valid_checkout_link() {
		return get_permalink( $this->page_ids['valid_checkout_id'] );
	}

	public function get_valid_proposal_link() {
		return get_permalink( $this->page_ids['valid_proposal_id'] );
	}

	public function get_slug_by_page_id( $page_id ) {
		if ( ! empty( $this->page_ids ) ) {
			foreach ( $this->page_ids as $key => $id ) {
				if ( $id === $page_id ) {
					return $key;
				}
			}
		}

		return '';
	}
}

Pages_Class::g();
