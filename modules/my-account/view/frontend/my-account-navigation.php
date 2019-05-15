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

<ul class="wps-account-navigation gridw-2">
	<?php
	if ( Settings::g()->dolibarr_is_active() ) :
		?>
		<li class="wps-account-navigation-item"><a class="<?php echo ( 'orders' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'orders/' ); ?>"><i class="navigation-icon fas fa-shopping-cart"></i> <?php esc_html_e( 'Orders', 'wpshop' ); ?></a></li>
		<li class="wps-account-navigation-item"><a class="<?php echo ( 'invoices' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'invoices/' ); ?>"><i class="navigation-icon fas fa-file-invoice-dollar"></i> <?php esc_html_e( 'Invoices', 'wpshop' ); ?></a></li>
		<?php
	endif;
	?>
	<li class="wps-account-navigation-item"><a class="<?php echo ( 'quotations' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'quotations/' ); ?>"><i class="navigation-icon fas fa-file-signature"></i> <?php esc_html_e( 'Quotations', 'wpshop' ); ?></a></li>
	<li class="wps-account-navigation-item"><a class="<?php echo ( 'download' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages::g()->get_account_link() . 'download/' ); ?>"><i class="navigation-icon fas fa-file-download"></i> <?php esc_html_e( 'Downloads', 'wpshop' ); ?></a></li>
	<li class="wps-account-navigation-item"><a href="<?php echo wp_logout_url( home_url() ); ?>"><i class="navigation-icon fas fa-sign-out-alt"></i> <?php esc_html_e( 'Logout', 'wpshop' ); ?></a></li>
</ul>
