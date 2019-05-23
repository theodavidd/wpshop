<?php
/**
 * Les fonctions principales des pages.
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
 * Pages Class.
 */
class Pages extends \eoxia\Singleton_Util {
	/**
	 * Tableau contenant toutes les pages personnalisables par défaut.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $default_options;

	/**
	 * Les titres des pages lisible.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $page_state_titles;

	/**
	 * Tableau contenant toutes les pages personnalisables dans la base de
	 * donnée.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $page_ids;

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->default_options = array(
			'shop_id'                    => 0,
			'cart_id'                    => 0,
			'checkout_id'                => 0,
			'my_account_id'              => 0,
			'valid_page_id'              => 0,
			'general_conditions_of_sale' => 0,
		);

		$this->page_state_titles = array(
			'shop_id'                    => __( 'Shop', 'wpshop' ),
			'cart_id'                    => __( 'Cart', 'wpshop' ),
			'checkout_id'                => __( 'Checkout', 'wpshop' ),
			'my_account_id'              => __( 'My account', 'wpshop' ),
			'valid_page_id'              => __( 'Valid page', 'wpshop' ),
			'general_conditions_of_sale' => __( 'General condiftions of sale', 'wpshop' ),
		);

		$this->page_ids = get_option( 'wps_page_ids', $this->default_options );
	}

	/**
	 * Récupères le slug de la page par rapport à son ID.
	 *
	 * @since 2.0.0
	 *
	 * @param  integer $page_id ID de la page.
	 *
	 * @return string           Le slug de la page.
	 */
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

	/**
	 * Récupères le slug de la page shop.
	 *
	 * @since 2.0.0
	 *
	 * @todo Doublone avec get slug link shop page.
	 *
	 * @return string Le slug de la page shop.
	 */
	public function get_slug_shop_page() {
		$page = get_page( $this->page_ids['shop_id'] );

		if ( ! $page ) {
			return false;
		}

		return $page->post_name;
	}

	/**
	 * Récupères le lien vers la page mon compte.
	 *
	 * @since 2.0.0
	 *
	 * @return string Le lien vers la page mon compte.
	 */
	public function get_account_link() {
		return get_permalink( $this->page_ids['my_account_id'] );
	}

	/**
	 * Récupères le lien vers la page "Mon compte".
	 *
	 * @since 2.0.0
	 *
	 * @return string Le lien vers la page "Mon compte".
	 */
	public function get_cart_link() {
		return get_permalink( $this->page_ids['cart_id'] );
	}

	/**
	 * Récupères le lien vers la page "Paiement".
	 *
	 * @since 2.0.0
	 *
	 * @return string Le lien vers la page "Paiement".
	 */
	public function get_checkout_link() {
		return get_permalink( $this->page_ids['checkout_id'] );
	}

	/**
	 * Récupères le lien vers la page "Validation du paiement".
	 *
	 * @since 2.0.0
	 *
	 * @return string Le lien vers la page "Validation du paiement".
	 */
	public function get_valid_page_link() {
		return get_permalink( $this->page_ids['valid_page_id'] );
	}

	/**
	 * Récupères le lien vers la page "Condition générale de vente".
	 *
	 * @since 2.0.0
	 *
	 * @return string Le lien vers la page "Condition général de vente".
	 */
	public function get_general_conditions_of_sale_link() {
		return get_permalink( $this->page_ids['general_conditions_of_sale'] );
	}

	public function is_checkout_page() {
		$this->page_ids = get_option( 'wps_page_ids', $this->default_options );

		return ( get_the_ID() === $this->page_ids['checkout_id'] ) ? true : false;
	}

	public function is_valid_page() {
		$this->page_ids = get_option( 'wps_page_ids', $this->default_options );

		return ( get_the_ID() === $this->page_ids['valid_page_id'] ) ? true : false;
	}
}

Pages::g();
