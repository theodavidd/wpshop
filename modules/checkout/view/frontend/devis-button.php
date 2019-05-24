<?php
/**
 * Ajoutes le bouton pour passer la commande.
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

defined( 'ABSPATH' ) || exit; ?>

<a class="wps-checkout-quotation-button action-input" data-type="proposal" data-parent="wps-checkout">
	<?php esc_html_e( 'Ask for a Quotation', 'wpshop' ); ?>
</a>
