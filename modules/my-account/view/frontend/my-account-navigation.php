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
?>

<ul>
	<li><a class="<?php echo ( 'orders' === $tab ) ? 'active' : ''; ?>" href="<?php echo esc_attr( \wpshop\Pages_Class::g()->get_account_link() . 'orders/' ); ?>">Orders</a></li>
	<li><a href="<?php echo esc_attr( site_url( 'my-account/logout/' ) ); ?>">Logout</a></li>
</ul>
