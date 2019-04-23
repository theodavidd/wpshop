<?php
/**
 * Ajoutes le bouton pour passer la commande.
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

<a class="action-input wpeo-button alignright" data-type="order" data-parent="wps-checkout-step-2"><?php esc_html_e( 'Place order', 'wpshop' ); ?></a>
