<?php
/**
 * La vue principale de la page de réglages
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
	<h2><?php esc_html_e( 'Settings', 'wpshop' ); ?></h2>

	<?php
	if ( ! empty( $transient ) ) :
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo $transient; ?></p>
		</div>
		<?php
	endif;
	?>

	<div class="wpeo-tab">
		<ul class="tab-list">
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=wps_load_settings_tab&tab=general' ) ); ?>" class="tab-element <?php echo $tab == 'general' ? 'tab-active' : ''; ?>">Général</a>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=wps_load_settings_tab&tab=pages' ) ); ?>" class="tab-element <?php echo $tab == 'pages' ? 'tab-active' : ''; ?>">Pages</a>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=wps_load_settings_tab&tab=emails' ) ); ?>" class="tab-element <?php echo $tab == 'emails' ? 'tab-active' : ''; ?>">Emails</a>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=wps_load_settings_tab&tab=payment_method' ) ); ?>" class="tab-element <?php echo $tab == 'payment_method' ? 'tab-active' : ''; ?>">Mode de paiements</a>
		</ul>

		<div class="tab-container">
			<div class="tab-content tab-active">


				<?php call_user_func( array( Settings_Class::g(), 'display_' . $tab ), $section ); ?>
			</div>
		</div>
	</div>
</div>
