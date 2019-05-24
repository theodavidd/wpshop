<?php
/**
 * Le contenu de la modal de synchronisation.
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

<div class="wpeo-gridlayout grid-3">
	<?php
	if ( ! empty( $sync_infos ) ) :
		foreach ( $sync_infos as $key => $info ) :
			$stats = '0 / ' . $info['total_number'];
			?>
			<div>
				<div class="item waiting-item" id="wpeo-upate-item-<?php echo $key; ?>" >
					<div class="item-spin">
						<span class="wps-spinner"><i class="fas fa-circle-notch fa-spin"></i></span>
						<i class="icon dashicons" ></i>
					</div>
					<div class="item-container">
						<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="POST">
							<input type="hidden" name="action" value="sync" />
							<?php wp_nonce_field( 'sync' ); ?>
							<input type="hidden" name="type" value="<?php echo $key; ?>" />

							<div class="item-content">
								<div class="item-title"><?php echo esc_attr( $info['title'] ); ?></div>
							</div>
							<div class="item-result">
								<input type="hidden" name="total_number" value="<?php echo ( null !== $info['total_number'] ? esc_attr( $info['total_number'] ) : 0 ); ?>" />
								<input type="hidden" name="done_number" value="0" />
								<div class="item-progress" >
									<div class="item-progression" >&nbsp;</div>
									<div class="item-stats" ><?php echo esc_html( $stats ); ?></div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
		endforeach;
	endif;
	?>
</div>
