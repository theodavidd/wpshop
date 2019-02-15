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
class Settings_Class extends \eoxia\Singleton_Util {
	protected function construct() {}

	public function display_general( $section = '' ) {
		$dolibarr_option = get_option( 'wps_dolibarr', array(
			'dolibarr_url'    => '',
			'dolibarr_secret' => '',
		) );

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'general', array(
			'dolibarr_option' => $dolibarr_option,
		) );
	}

	public function display_pages( $section = '' ) {
		$pages = get_pages();

		array_unshift( $pages, (object) array(
			'ID'         => 0,
			'post_title' => __( 'No page', 'wpshop' ),
		) );

		$page_ids_options = get_option( 'wps_page_ids', array(
			'shop_id'           => 0,
			'cart_id'           => 0,
			'checkout_id'       => 0,
			'my_account_id'     => 0,
			'valid_checkout_id' => 0,
		) );

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'pages', array(
			'pages'            => $pages,
			'page_ids_options' => $page_ids_options,
		) );
	}

	public function display_emails( $section = '' ) {
		if ( ! empty( $section ) ) {
			$email            = Emails_Class::g()->emails[ $section ];
			$path_to_template = Emails_Class::g()->get_path( $email['filename_template'] );
			$content          = file_get_contents( $path_to_template );

			\eoxia\View_Util::exec( 'wpshop', 'settings', 'email-single', array(
				'section'     => $section,
				'email'       => $email,
				'content'     => $content,
				'is_override' => Emails_Class::g()->is_override( $email['filename_template'] ),
			) );
		} else {
			\eoxia\View_Util::exec( 'wpshop', 'settings', 'emails', array(
				'emails' => Emails_Class::g()->emails,
			) );
		}
	}

	public function display_delivery_method( $section = '' ) {
		\eoxia\View_Util::exec( 'wpshop', 'settings', 'delivery-method' );
	}

	public function display_shipping_cost( $section = '' ) {
		\eoxia\View_Util::exec( 'wpshop', 'settings', 'shipping-cost' );
	}

	public function display_payment_method( $section = '' ) {
		$payment_methods = get_option( 'wps_payment_methods', Payment_Class::g()->default_options );
		if ( ! empty( $section ) ) {
			$payment_methods = $payment_methods;
			$payment         = $payment_methods[ $section ];

			\eoxia\View_Util::exec( 'wpshop', 'settings', 'payment-method-single', array(
				'section' => $section,
				'payment' => $payment,
			) );
		} else {
			\eoxia\View_Util::exec( 'wpshop', 'settings', 'payment-method', array(
				'payment_methods' => $payment_methods,
			) );
		}
	}

}

Settings_Class::g();
