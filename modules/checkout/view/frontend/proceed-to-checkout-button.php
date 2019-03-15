<?php
/**
 * Bouton pour passer au tunnel de vente.
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

<a href="<?php echo esc_url( $link_checkout ); ?>" class="wpeo-button alignright">
	<?php esc_html_e( 'Proceed to checkout', 'wpshop' ); ?>
</a>
