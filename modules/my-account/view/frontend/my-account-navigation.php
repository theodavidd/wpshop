<?php
/**
 * Affichage de la page mon compte
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<ul class="wps-account-navigation gridw-1">
	<li><a class="<?php echo ( 'orders' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages_Class::g()->get_account_link() . 'orders/' ); ?>"><?php esc_html_e( 'Orders', 'wpshop' ); ?></a></li>
	<li><a href="<?php echo wp_logout_url(); ?>"><?php esc_html_e( 'Logout', 'wpshop' ); ?></a></li>
</ul>
