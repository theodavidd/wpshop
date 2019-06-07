<?php
/**
 * Résultat de l'association
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

<?php
if ( ! empty( $notice['messages'] ) ) :
	?>
	<div class="wpeo-notice notice-success">
		<ul class="notice-content">
			<?php
			foreach ( $notice['messages'] as $message ) :
				?>
				<li><?php echo $message; ?></li>
				<?php
			endforeach;
			?>
		</ul>
	</div>
	<?php
endif;

if ( ! empty( $notice['errors'] ) ) :
	?>
	<div class="wpeo-notice notice-warning">
		<ul class="notice-content">
			<?php
			foreach ( $notice['errors'] as $message ) :
				?>
				<li><?php echo $message; ?></li>
				<?php
			endforeach;
			?>
		</ul>
	</div>
	<?php
endif;
?>

<a href="<?php echo esc_attr( $url ); ?>" class="wpeo-button button-main">
	<span><?php esc_html_e( 'Refresh page', 'wpshop' ); ?></span>
</a>
