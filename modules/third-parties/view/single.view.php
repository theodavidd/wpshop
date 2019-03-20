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

	<p>
		<span class="wpeo-button button-main <?php echo ! empty( $third_party->data['external_id'] ) ? 'action-attribute' : 'wpeo-modal-event'; ?>"
			data-title="Choix du tier à associer"
			data-class="synchro-single"
			data-id="<?php echo esc_attr( $third_party->data['id'] ); ?>"
			data-action="load_synchro_modal_single"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_modal_synchro_single' ) ); ?>">
			<span>
				<?php
				if ( ! empty( $third_party->data['external_id'] ) ) :
					?>
					Dernière synchronisation le <?php echo $third_party->data['last_sync']['rendered']['date_human_readable']; ?>. Resynchroniser.
					<?php
				else :
					?>
					Associer et synchroniser
					<?php
				endif;
				?>
			</span>
		</span>
	</p>

	<?php do_meta_boxes( 'wps-third-party', 'normal', '' ); ?>
</div>
