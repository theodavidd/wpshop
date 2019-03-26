<?php
/**
 * La vue single de la page d'un tier lors de l'édition.
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
	<div class="page-header">
		<h2>
			<?php
			if ( 0 == $third_party->data['id'] ) :
				\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single-title-edit', array(
					'third_party' => $third_party,
				) );
			else :
				\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'single-title', array(
					'third_party' => $third_party,
				) );
			endif;
			?>
		</h2>

		<div class="wps-sync">
			<div class="button-synchro <?php echo ! empty( $third_party->data['external_id'] ) ? 'action-attribute' : 'wpeo-modal-event'; ?>"
				data-class="synchro-single"
				data-id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
				data-action="load_synchro_modal_single"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_modal_synchro_single' ) ); ?>">

				<i class="fas fa-sync"></i>
			</div>

			<?php
			if ( ! empty( $third_party->data['external_id'] ) ) :
				$sync_label = esc_html__( 'Last sync :', 'wpshop' ) . ' ' . $third_party->data['last_sync']['rendered']['date_human_readable'];
			else :
				$sync_label = esc_html__( 'Associate and sync', 'wpshop' );
			endif;
			?>
			<div class="statut statut-green wpeo-tooltip-event"
				data-direction="bottom"
				aria-label="<?php echo esc_html( $sync_label ); ?>"></div>
		</div>
	</div>

	<div class="wps-page-content wpeo-gridlayout grid-6">
		<?php do_action( 'wps-third-party', $third_party ); ?>
	</div>
</div>
