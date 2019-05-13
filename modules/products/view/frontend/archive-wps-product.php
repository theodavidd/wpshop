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

	<main id="primary" class="content-area" role="main">

		<?php
		if ( have_posts() ) :
			?>
Coucou
			<header class="primary-header">
				<h1 class="page-title"><?php echo esc_html( single_cat_title() ); ?></h1>
				<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
			</header><!-- .primary-header -->

			<div class="wps-list-product wpeo-gridlayout grid-4">
				<?php
				while ( have_posts() ) :
					the_post();
					$product = Product::g()->get( array( 'id' => get_the_ID() ), true );
					?>

					<div class="wps-product">
						<figure class="wps-product-thumbnail">
							<?php the_post_thumbnail( 'large' ); ?>
						</figure>
						<div class="wps-product-content">
							<div class="wps-product-title"><?php the_title(); ?></div>
							<div class="wpeo-gridlayout grid-2">
								<div class="wps-product-price"><?php echo ! empty( $product->data['price'] ) ? esc_html( number_format( $product->data['price_ttc'], 2, ',', '' ) ) . ' €' : ''; ?></div>
								<div class="wps-product-buy wpeo-button button-square-40 action-attribute"
									data-action="add_to_cart"
									data-nonce="<?php echo wp_create_nonce( 'add_to_cart' ); ?>"
									data-id="<?php echo esc_attr( the_ID() ); ?>"><i class="button-icon fas fa-cart-arrow-down"></i></div>
							</div>
						</div>
					</div>
					<?php
				endwhile;
				?>
			</div>

			<?php
		endif;
		?>

	</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
