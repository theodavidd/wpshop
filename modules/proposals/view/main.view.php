<?php
/**
 * La vue principale de la page des produits (wps-product)
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

<div class="wrap">
	<h2><?php esc_html_e( 'Proposals', 'wpshop' ); ?></h2>

	<a href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=wps-proposal' ) ); ?>" class="wpeo-button button-main"><?php esc_html_e( 'Add', 'wpshop' ); ?></a>

	<?php Proposals::g()->display(); ?>
</div>
