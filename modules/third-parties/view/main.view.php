<?php
/**
 * La vue principale de la page des tiers.
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
	<h2><?php esc_html_e( 'Third Parties', 'wpshop' ); ?></h2>

	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wps-third-party&id=0' ) ); ?>" class="wpeo-button button-main"><?php esc_html_e( 'Add', 'wpshop' ); ?></a>

	<?php
	if ( ! empty( $_GET['s'] ) ) :
		?>
		<p>Résultats de recherche pour « <?php echo $_GET['s']; ?> »</p>
		<?php
	endif;
	?>

	<form method="GET" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="page" value="wps-third-party" />
		<input type="text" name="s" value="<?php echo esc_attr( ! empty( $_GET['s'] ) ? $_GET['s'] : '' ); ?>" />
		<input type="submit" value="Search in third party" />
	</form>

	<p><?php echo $count . ' éléments'; ?></p>

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
				<input type="hidden" name="page" value="wps-third-party" />
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
	<?php Third_Party_Class::g()->display(); ?>
</div>
