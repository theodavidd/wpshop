<?php
/**
 * Les fonctions principales des options
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
 * Settings Class.
 */
class Settings extends \eoxia\Singleton_Util {

	/**
	 * Les options par défauts.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $default_settings;

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->default_settings = array(
			'dolibarr_url'    => '',
			'dolibarr_secret' => '',
			'shop_email'      => '',
		);
	}

	/**
	 * Affiches l'onglet "Général" de la page options.
	 *
	 * @param  string $section La section.
	 *
	 * @since 2.0.0
	 */
	public function display_general( $section = '' ) {
		$dolibarr_option = get_option( 'wps_dolibarr', $this->default_settings );

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'general', array(
			'dolibarr_option' => $dolibarr_option,
		) );
	}

	/**
	 * Affiches l'onglet "Pages" de la page options.
	 *
	 * @param  string $section La section.
	 *
	 * @since 2.0.0
	 */
	public function display_pages( $section = '' ) {
		$pages = get_pages();

		array_unshift( $pages, (object) array(
			'ID'         => 0,
			'post_title' => __( 'No page', 'wpshop' ),
		) );

		$page_ids_options = get_option( 'wps_page_ids', Pages_Class::g()->default_options );

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'pages', array(
			'pages'            => $pages,
			'page_ids_options' => $page_ids_options,
		) );
	}

	/**
	 * Affiches l'onglet "Emails" de la page options.
	 *
	 * @param  string $section La section.
	 *
	 * @since 2.0.0
	 */
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

	/**
	 * Affiches l'onglet "Méthode de paiement" de la page options.
	 *
	 * @param  string $section La section.
	 *
	 * @since 2.0.0
	 */
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

Settings::g();
