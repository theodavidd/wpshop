<?php
/**
 * La vue principale de la page de réglages
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

<h3><a href="<?php echo admin_url( 'admin.php?page=wps-settings&tab=payment_method' ); ?>"><?php esc_html_e( 'Payment method', 'wpshop' ); ?></a> -> <?php echo $payment['title']; ?></h3>

<?php do_action( 'wps_setting_payment_method_' . $section ); ?>
