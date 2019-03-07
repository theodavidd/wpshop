<?php
/**
 * Single Product view.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2006-2018 Eoxia <dev@eoxia.com>
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 * @package   WPShop\Templates
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) : the_post();
			the_title();
			?>
			<div class="wpeo-button action-attribute"
				data-action="add_to_cart"
				data-nonce="<?php echo wp_create_nonce( 'add_to_cart' ); ?>"
				data-id="<?php echo esc_attr( the_ID() ); ?>"><?php esc_html_e( 'Add to cart', 'wpshop' ); ?></div>
			<?php
		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
