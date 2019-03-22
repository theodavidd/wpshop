<?php
/**
 * Affichage des données commercials d'un tier: devis, commande et facture.
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

<ul>
	<li>
		<?php echo ! empty( $devis ) ? $devis->data['title'] : '-'; ?>
	</li>
	<li>
		<?php echo ! empty( $order ) ? $order->data['title'] : '-'; ?>
	</li>
	<li>
		<?php echo ! empty( $invoice ) ? $invoice->data['title'] : '-'; ?>
	</li>
</ul>
