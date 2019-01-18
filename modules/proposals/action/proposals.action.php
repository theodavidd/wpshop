<?php
/**
 * Les actions relatives aux proposals.
 *
 * @author Eoxia <corentin-settelen@hotmail.com>
 * @since 2.0.0
 * @version 2.0.0
 * @copyright 2018 Eoxia
 * @package wpshop
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Les actions relatives aux proposals.
 */
class Proposals_Action {

	/**
	 * Initialise les actions liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_add_to_cart', array( $this, 'callback_add_to_cart' ), 10, 2 );

		add_action( 'wps_calculate_totals', array( $this, 'callback_calculate_totals' ) );
	}

	public function callback_add_to_cart( $cart, $product ) {
		$cart->proposal_id = $_SESSION['wps_cart']->proposal_id;

		if ( empty( $cart->proposal_id ) ) {
			$proposal_id = Request_Util::post( 'proposals', array(
				'socid' => 1,
				'date'  => current_time( 'mysql' ),
			) );

			$cart->proposal_id = $proposal_id;
		}

		$proposal = Request_Util::post( 'proposals/' . $cart->proposal_id . '/lines', array(
			'desc'                    => $product['content'],
			'fk_product'              => $product['external_id'],
			'product_type'            => 1,
			'qty'                     => 1,
			'tva_tx'                  => 0,
			'subprice'                => $product['price'],
			'remice_percent'          => 0,
			'rang'                    => 1,
			'total_ht'                => $product['price'],
			'total_tva'               => 0,
			'total_ttc'               => $product['price_ttc'],
			'product_label'           => $product['title'],
			'multicurrency_code'      => 'EUR',
			'multicurrency_subprice'  => $product['price'],
			'multicurrency_total_ht'  => $product['price'],
			'multicurrency_total_tva' => 0,
			'multicurrency_total_ttc' => $product['price_ttc'],
		) );
	}

	public function callback_calculate_totals( $cart ) {
		$proposal = Request_Util::get( 'proposals/' . $cart->proposal_id );

		$cart->total_price     = $proposal->total_ht;
		$cart->total_price_ttc = $proposal->total_ttc;
	}
}

new Proposals_Action();
