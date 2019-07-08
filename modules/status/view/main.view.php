<?php
/**
 * La vue principale de la page "État"
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

<div class="wrap wpeo-wrap">
	<h2><?php esc_html_e( 'Status', 'wpshop' ); ?></h2>

	<div class="wpeo-gridlayout grid-1">

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'WordPress Environment', 'wpshop' ); ?></h3>

			<table>
				<tr>
					<td>WordPress address (URL):</td>
					<td><?php echo get_option( 'siteurl' ); ?></td>
				</tr>
				<tr>
					<td>Site address (URL):</td>
					<td><?php echo get_option( 'home' ); ?></td>
				</tr>
				<tr>
					<td>WPshop version:</td>
					<td><?php echo \eoxia\Config_Util::$init['wpshop']->version; ?></td>
				</tr>
				<tr>
					<td>Log directory writable:</td>
					<td><?php echo dirname( ini_get( 'error_log' ) ); ?></td>
				</tr>
				<tr>
					<td>WordPress version:</td>
					<td><?php echo get_bloginfo( 'version' ); ?></td>
				</tr>
				<tr>
					<td>WordPress multisite:</td>
					<td><?php echo is_multisite() ? __( 'Yes', 'wpshop' ) : __( 'No', 'wpshop' ); ?></td>
				</tr>
				<tr>
					<td>WordPress memory limit:</td>
					<td><?php echo ini_get('memory_limit'); ?></td>
				</tr>
				<tr>
					<td>WordPress debug mode:</td>
					<td><?php echo WP_DEBUG ? __( 'Yes', 'wpshop' ) : __( 'No', 'wpshop' ); ?></td>
				</tr>
				<tr>
					<td>Language:</td>
					<td><?php echo get_locale(); ?></td>
				</tr>
			</table>
		</div>

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'Server Environment', 'wpshop' ); ?></h3>

			<table>
				<tr>
					<td>Server info:</td>
					<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
				</tr>
				<tr>
					<td>PHP version:</td>
					<td><?php echo phpversion(); ?></td>
				</tr>
				<tr>
					<td>PHP post max size:</td>
					<td><?php echo ini_get( 'post_max_size' ); ?></td>
				</tr>
				<tr>
					<td>PHP time limit:</td>
					<td><?php echo ini_get( 'max_execution_time' ); ?></td>
				</tr>
				<tr>
					<td>PHP max input vars:</td>
					<td><?php echo ini_get( 'max_input_vars' ); ?></td>
				</tr>
				<tr>
					<td>cURL version:</td>
					<td><?php echo $curl_version['version']; ?></td>
				</tr>
				<tr>
					<td>MySQL version:</td>
					<td><?php echo $mysql_version; ?></td>
				</tr>
				<tr>
					<td>Max upload size:</td>
					<td><?php echo ini_get('upload_max_filesize'); ?></td>
				</tr>
			</table>
		</div>

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'Database', 'wpshop' ); ?></h3>

			<table>
				<tr>
					<td>WPshop database version:</td>
					<td><?php echo $wpshop_db_version; ?></td>
				</tr>
				<tr>
					<td>Database prefix</td>
					<td><?php echo $db_prefix; ?></td>
				</tr>
			</table>
		</div>

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'Security', 'wpshop' ); ?></h3>

			<table>
				<tr>
					<td>Secure connection (HTTPS)</td>
					<td><?php echo is_ssl() ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>'; ?></td>
				</tr>
				<tr>
					<td>Hide errors from visitors</td>
					<td><?php echo ( WP_DEBUG ) || ( defined( WP_DEBUG_DISPLAY ) && WP_DEBUG && WP_DEBUG_DISPLAY ) ? '<i class="fas fa-times"></i>' : '<i class="fas fa-check"></i>'; ?></td>
				</tr>
			</table>
		</div>

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'Active plugins', 'wpshop' ); ?></h3>

			<table>
				<?php
				if ( ! empty( $plugins ) ) :
					foreach( $plugins as $key => $plugin ) :
						if ( ! is_plugin_active( $key ) ) :
							continue;
						endif;
						?>
						<tr>
							<td><?php echo $plugin['Name']; ?></td>
							<td>
								<strong><?php echo $plugin['Version']; ?></strong>

								<span>
									<?php
									if ( ! $plugin['Uptodate'] ) :
										?>
										<i class="fas fa-times"></i>
										<?php
										esc_html_e( 'This plugin is not up to date', 'wpshop' );
									else:
										?>
										<i class="fas fa-check"></i>
										<?php
									endif;
									?>
								</span>
							</td>
						</tr>
						<?php
					endforeach;
				endif;
				?>
			</table>
		</div>

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'Inactive plugins', 'wpshop' ); ?></h3>

			<table>
				<?php
				if ( ! empty( $plugins ) ) :
					foreach( $plugins as $key => $plugin ) :
						if ( is_plugin_active( $key ) ) :
							continue;
						endif;
						?>
						<tr>
							<td><?php echo $plugin['Name']; ?></td>
							<td>
								<strong><?php echo $plugin['Version']; ?></strong>

								<span>
									<?php
									if ( ! $plugin['Uptodate'] ) :
										?>
										<i class="fas fa-times"></i>
										<?php
										esc_html_e( 'This plugin is not up to date', 'wpshop' );
									else:
										?>
										<i class="fas fa-check"></i>
										<?php
									endif;
									?>
								</span>
							</td>
						</tr>
						<?php
					endforeach;
				endif;
				?>
			</table>
		</div>

		<div class="wps-metabox">
			<h3 class="metabox-title"><?php esc_html_e( 'WPshop pages', 'wpshop' ); ?></h3>

			<table>
				<?php
				if ( ! empty( Pages::g()->page_state_titles ) ) :
					foreach ( Pages::g()->page_state_titles as $key => $page_option ) :
						$text = '<i class="fas fa-times"></i>' . __( 'Page not set', 'wpshop' );

						$page = get_page( $page_ids_options[ $key ] );

						if ( ! empty( $page ) ) :
							$text = '<i class="fas fa-check"></i>' . ' #' . $page->ID . ' ' . $page->post_title;
						endif;
						?>
						<tr>
							<td><?php echo esc_html( $page_option ); ?></td>
							<td><?php echo $text; ?></td>
						</tr>
						<?php
					endforeach;
				endif;
				?>
			</table>
		</div>
	</div>
</div>
