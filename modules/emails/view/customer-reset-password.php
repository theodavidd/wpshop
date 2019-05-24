<?php
/**
 * A supprimer. Géré par WP.
 *
 * @todo: A supprimer
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

do_action( 'wps_email_header' ); ?>

<p>Reset password</p>

<?php
do_action( 'wps_email_order_details' );

do_action( 'wps_email_order_meta' );

do_action( 'wps_email_customer_details' );

do_action( 'wps_email_footer' );
