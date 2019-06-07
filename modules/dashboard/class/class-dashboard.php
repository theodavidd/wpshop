<?php
/**
 * Les fonctions principales du tableau de bord.
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
 * Dashboard class.
 */
class Dashboard extends \eoxia\Singleton_Util {

	/**
	 * Définition des metaboxes
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $metaboxes = array();

	/**
	 * Obligatoire pour Singleton_Util
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->metaboxes = apply_filters( 'wps_dashboard_metaboxes', array(
			'wps-dashboard-invoices'   => array(
				'callback' => array( $this, 'metabox_invoices' ),
			),
			'wps-dashboard-orders'     => array(
				'callback' => array( $this, 'metabox_orders' ),
			),
			'wps-dashboard-customers'  => array(
				'callback' => array( $this, 'metabox_customers' ),
			),
			'wps-dashboard-quotations' => array(
				'callback' => array( $this, 'metabox_quotations' ),
			),
			'wps-dashboard-products'   => array(
				'callback' => array( $this, 'metabox_products' ),
			),
			'wps-dashboard-payments'   => array(
				'callback' => array( $this, 'metabox_payments' ),
			),
		) );
	}

	/**
	 * Appel la vue "main" du module "dashboard".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		if ( ! empty( $this->metaboxes ) ) {
			foreach ( $this->metaboxes as $key => $metabox ) {
				add_action( 'wps_dashboard', $metabox['callback'], 10, 0 );
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'main' );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_invoices() {
		$invoices = Doli_Invoice::g()->get( array( 'posts_per_page' => 3 ) );

		if ( ! empty( $invoices ) ) {
			foreach ( $invoices as &$invoice ) {
				$invoice->data['third_party'] = Third_Party::g()->get( array( 'id' => $invoice->data['third_party_id'] ), true );
			}
		}

		unset( $invoice );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-invoices', array(
			'invoices' => $invoices,
		) );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_orders() {
		$orders = Doli_Order::g()->get( array( 'posts_per_page' => 3 ) );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as &$order ) {
				$order->data['third_party'] = Third_Party::g()->get( array( 'id' => $order->data['parent_id'] ), true );
			}
		}

		unset( $order );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-orders', array(
			'orders' => $orders,
		) );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_customers() {
		$third_parties = Third_Party::g()->get( array( 'posts_per_page' => 3 ) );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-customers', array(
			'third_parties' => $third_parties,
		) );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_quotations() {
		$proposals = Proposals::g()->get( array( 'posts_per_page' => 3 ) );

		if ( ! empty( $proposals ) ) {
			foreach ( $proposals as &$proposal ) {
				$proposal->data['third_party'] = Third_Party::g()->get( array( 'id' => $proposal->data['parent_id'] ), true );
			}
		}

		unset( $proposal );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-quotations', array(
			'proposals' => $proposals,
		) );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_products() {
		$products = Product::g()->get( array( 'posts_per_page' => 3 ) );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-products', array(
			'products' => $products,
		) );
	}

	/**
	 * La metabox de synchronisation.
	 *
	 * @since 2.0.0
	 */
	public function metabox_payments() {
		$payments = Doli_Payment::g()->get( array( 'posts_per_page' => 3 ) );

		if ( ! empty( $payments ) ) {
			foreach ( $payments as &$payment ) {
				$payment->data['invoice'] = Doli_Invoice::g()->get( array( 'id' => $payment->data['parent_id'] ), true );
			}
		}

		unset( $payment );

		\eoxia\View_Util::exec( 'wpshop', 'dashboard', 'metaboxes/metabox-payments', array(
			'payments' => $payments,
		) );
	}
}

Dashboard::g();
