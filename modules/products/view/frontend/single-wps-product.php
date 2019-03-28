<?php
/**
 * Single Product view.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2006-2018 Eoxia <dev@eoxia.com>
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 * @package   WPshop\Templates
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php $product = Product::g()->get( array( 'id' => get_the_ID() ), true ); ?>

			<div class="wpeo-gridlayout grid-3 grid-gap-3">
				<figure class="wps-thumbnail">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
				<div class="wps-product-content gridw-2">
					<header class="primary-header site-width">
						<?php the_title( '<h1 class="page-title wps-product-title">', '</h1>' ); ?>
					</header><!-- .primary-header -->

					<div class="primary-content">
						<div class="wps-product-price"><?php echo ! empty( $product->data['price'] ) ? esc_html( $product->data['price_ttc'] ) . '€ TTC' : ''; ?></div>
						<div class="wps-product-description"><?php the_content(); ?></div>
						<div class="wps-product-buy wpeo-button action-attribute"
							data-action="add_to_cart"
							data-nonce="<?php echo wp_create_nonce( 'add_to_cart' ); ?>"
							data-id="<?php echo esc_attr( the_ID() ); ?>"><?php esc_html_e( 'Add to cart', 'wpshop' ); ?></div>
					</div>
				</div>
			</div>

		<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
