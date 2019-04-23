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
	<?php
	if ( Settings::g()->dolibarr_is_active() ) :
		?>
		<li><a class="<?php echo ( 'orders' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'orders/' ); ?>"><?php esc_html_e( 'Orders', 'wpshop' ); ?></a></li>
		<li><a class="<?php echo ( 'invoices' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'invoices/' ); ?>"><?php esc_html_e( 'Invoices', 'wpshop' ); ?></a></li>
		<?php
	endif;
	?>
	<li><a class="<?php echo ( 'quotations' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'quotations/' ); ?>"><?php esc_html_e( 'Quotations', 'wpshop' ); ?></a></li>
	<li><a class="<?php echo ( 'download' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'download/' ); ?>"><?php esc_html_e( 'Downloads', 'wpshop' ); ?></a></li>
	<li><a href="<?php echo wp_logout_url( home_url() ); ?>"><?php esc_html_e( 'Logout', 'wpshop' ); ?></a></li>
</ul>
