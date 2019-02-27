<?php
/**
 * Affichage des données de la commande
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

<form method="POST" action="<?php echo admin_url( 'admin-post' ); ?>">
	<input type="hidden" name="action" value="synchronization" />
	<input type="hidden" name="id" value="<?php echo esc_attr( $order->data['id'] ); ?>" />

	<input type="submit" value="<?php echo esc_attr( 'Synchronization', 'wpshop' ); ?>" />
</form>
