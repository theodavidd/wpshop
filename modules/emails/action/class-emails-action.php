<?php
/**
 * Gestion des actions des réglages.
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
 * Action of product module.
 */
class Emails_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_wps_copy_email_template', array( $this, 'callback_copy_email_template' ) );

		add_action( 'wps_email_order_details', array( $this, 'order_details' ) );
		add_action( 'wps_email_order_details', array( $this, 'type_payment' ), 20, 1 );
	}

	public function callback_copy_email_template() {
		$tab          = 'emails';
		$section      = ! empty( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';
		$email        = Emails_Class::g()->emails[ $section ];
		$file_to_copy = \eoxia\Config_Util::$init['wpshop']->emails->path . '/view/' . $email['filename_template'];
		$path         = get_template_directory() . '/wpshop/emails/view/' . $email['filename_template'];

		if ( wp_mkdir_p( dirname( $path ) ) && ! file_exists( $path ) ) {
			copy( $file_to_copy, $path );
		}

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab= ' . $tab . '&section=' . $section ) );
	}

	public function order_details( $order ) {
		include( Template_Util::get_template_part( 'emails', 'order-details' ) );
	}

	public function type_payment( $order ) {
		$payment_methods = get_option( 'wps_payment_methods', Payment_Class::g()->default_options );

		include( Template_Util::get_template_part( 'emails', 'type-payment' ) );

	}
}

new Emails_Action();
