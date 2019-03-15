<?php
/**
 * Les fonctions principales de la session du panier.
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
 * Cart Session Class.
 */
class Cart_Session extends \eoxia\Singleton_Util {

	/**
	 * Les données externe.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $external_data = array();

	/**
	 * Un tableau de produit
	 *
	 * @var array
	 */
	public $cart_contents = array();

	/**
	 * Le prix total HT.
	 *
	 * @since 2.0.0
	 *
	 * @var float
	 */
	public $total_price;

	/**
	 * Le prix total TTC.
	 *
	 * @since 2.0.0
	 *
	 * @var float
	 */
	public $total_price_ttc;

	/**
	 * L'ID du devis.
	 *
	 * @since 2.0.0
	 *
	 * @var integer
	 */
	public $proposal_id;

	/**
	 * L'ID de la commande
	 *
	 * @since 2.0.0
	 *
	 * @var integer
	 */
	public $order_id;

	/**
	 * Le constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->cart_contents   = isset( $_SESSION['wps_cart'] ) ? $_SESSION['wps_cart'] : array();
		$this->total_price     = isset( $_SESSION['wps_total_price'] ) ? $_SESSION['wps_total_price'] : null;
		$this->total_price_ttc = isset( $_SESSION['wps_total_price_ttc'] ) ? $_SESSION['wps_total_price_ttc'] : null;
		$this->proposal_id     = isset( $_SESSION['wps_proposal_id'] ) ? $_SESSION['wps_proposal_id'] : null;
		$this->order_id        = isset( $_SESSION['wps_order_id'] ) ? $_SESSION['wps_order_id'] : null;
		$this->external_data   = isset( $_SESSION['wps_external_data'] ) ? $_SESSION['wps_external_data'] : array();
	}

	/**
	 * Ajoutes une donnée external
	 *
	 * @since 2.0.0
	 *
	 * @param string $property Le nom.
	 * @param mixed  $value    La valeur.
	 */
	public function add_external_data( $property, $value ) {
		$this->external_data[ $property ] = $value;
	}

	/**
	 * Met à jour la SESSION
	 *
	 * @since 2.0.0
	 */
	public function update_session() {
		$_SESSION['wps_cart']            = $this->cart_contents;
		$_SESSION['wps_total_price']     = $this->total_price;
		$_SESSION['wps_total_price_ttc'] = $this->total_price_ttc;
		$_SESSION['wps_proposal_id']     = $this->proposal_id;
		$_SESSION['wps_order_id']        = $this->order_id;
		$_SESSION['wps_external_data']   = $this->external_data;
	}

	/**
	 * Supprimes toutes les données de la SESSION
	 *
	 * @since 2.0.0
	 */
	public function destroy() {
		unset( $_SESSION['wps_cart'] );
		unset( $_SESSION['wps_total_price'] );
		unset( $_SESSION['wps_total_price_ttc'] );
		unset( $_SESSION['wps_proposal_id'] );
		unset( $_SESSION['wps_order_id'] );
		unset( $_SESSION['wps_external_data'] );
	}
}

Class_Cart_Session::g();
