<?php
/**
 * La vue principale de la page des produits (wps-product)
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
	<h2>
		<?php esc_html_e( 'Products', 'wpshop' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=wps-product' ) ); ?>" class="wpeo-button button-main"><?php esc_html_e( 'Add', 'wpshop' ); ?></a>

		<?php
		if ( Settings::g()->dolibarr_is_active() ) :
			?>
			<div class="wpeo-button button-main wpeo-modal-event"
				data-action="load_modal_synchro"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_modal_synchro' ) ); ?>"
				data-class="modal-sync modal-force-display"
				data-sync="products"
				data-title="<?php echo esc_attr_e( 'Data synchronization', 'wpshop' ); ?>">
				<span><?php esc_html_e( 'All Product Sync', 'wpshop' ); ?></span>
			</div>
			<?php
		endif;
		?>
	</h2>

	<form method="GET" action="<?php echo admin_url( 'admin.php' ); ?>" class="wps-filter-bar wpeo-form form-light">
		<div class="form-element">
			<label class="form-field-container">
				<span class="form-field-icon-prev"><i class="fas fa-search"></i></span>
				<input type="hidden" name="page" value="wps-product" />
				<input type="text" name="s" class="form-field" value="<?php echo esc_attr( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ); ?>" />
			</label>
		</div>

		<input type="submit" class="wpeo-button button-main button-filter" value="<?php esc_html_e( 'Search', 'wpshop' ); ?>" />

		<div></div>
		<div></div>
		<div class="alignright"><?php echo $count . ' éléments'; ?></div>
	</form>

	<?php
	if ( ! empty( $_GET['s'] ) ) :
		?>
		<p>Résultats de recherche pour « <?php echo $_GET['s']; ?> »</p>
		<?php
	endif;
	?>

	<?php if ( $number_page > 1 ) : ?>
		<ul class="wpeo-pagination">
			<?php
			if ( 1 !== $current_page ) :
				?>
				<li class="pagination-element pagination-prev">
					<a href="<?php echo esc_attr( $begin_url ); ?>"><<</a>
				</li>

				<li class="pagination-element pagination-prev">
					<a href="<?php echo esc_attr( $prev_url ); ?>"><</a>
				</li>
				<?php
			endif;
			?>

			<form method="GET" action="<?php echo admin_url( 'admin.php' ); ?>" />
				<input type="hidden" name="page" value="wps-product" />
				<input type="hidden" name="s" value="<?php echo esc_attr( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ); ?>" />
				<input style="width: 50px;" type="text" name="current_page" value="<?php echo esc_attr( $current_page ); ?>" />
			</form>

			sur <?php echo $number_page; ?>

			<?php
			if ( $current_page !== $number_page ) :
				?>
				<li class="pagination-element pagination-next">
					<a href="<?php echo esc_attr( $next_url ); ?>">></a>
				</li>

				<li class="pagination-element pagination-next">
					<a href="<?php echo esc_attr( $end_url ); ?>">>></a>
				</li>
				<?php
			endif;
			?>
		</ul>
	<?php endif; ?>

	<?php Product::g()->display(); ?>
</div>
