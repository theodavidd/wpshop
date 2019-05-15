<?php
/**
 * Taxonomy Product view.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2006-2018 Eoxia <dev@eoxia.com>
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 * @package   WPshop\Templates
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

get_header(); ?>

	<main id="primary" class="entry content-area" role="main">

		<?php
		if ( have_posts() ) :
			?>
			<header class="primary-header entry-header">
				<!-- <h1 class="page-title"><?php single_post_title(); ?></h1> -->
				<h1 class="page-title entry-title">Titre de la page</h1>
			</header>

			<div class="entry-content">
				<div class="wps-grid-product wpeo-gridlayout grid-3">
					<?php
					while ( have_posts() ) :
						the_post();
						$product = Product::g()->get( array( 'id' => get_the_ID() ), true );
						?>

						<div itemscope itemtype="https://schema.org/Product" class="wps-product">
							<figure class="wps-product-thumbnail">
								<?php the_post_thumbnail( 'wps-product-thumbnail', array( 'itemprop' => 'image' ) ); ?>

								<div class="wps-product-action">
									<a itemprop="url" href="<?php the_permalink(); ?>" class="wpeo-button button-square-40 button-rounded button-light">
										<i class="button-icon fas fa-eye"></i>
									</a>
									<div class="wps-product-buy wpeo-button button-square-40 button-rounded button-light action-attribute"
										data-action="add_to_cart"
										data-nonce="<?php echo wp_create_nonce( 'add_to_cart' ); ?>"
										data-id="<?php echo esc_attr( the_ID() ); ?>"><i class="button-icon fas fa-cart-arrow-down"></i></div>
								</div>
								<a itemprop="url" href="<?php the_permalink(); ?>" class="wps-product-link"></a>
							</figure>
							<div class="wps-product-content">
								<div itemprop="name" class="wps-product-title"><?php the_title(); ?></div>
								<?php if ( ! empty( $product->data['price'] ) ) : ?>
									<div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="wps-product-price">
										<span itemprop="price" content="<?php echo esc_html( number_format( $product->data['price_ttc'], 2, '.', '' ) ); ?>"><?php echo esc_html( number_format( $product->data['price_ttc'], 2, ',', '' ) ); ?></span>
										<span itemprop="priceCurrency" content="EUR"><?php echo esc_html( '€', 'wpshop' ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php
					endwhile;
					?>
				</div>
			</div>

			<?php
		endif;
		?>

	</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
