<?php
/**
 * Product grid view
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2006-2018 Eoxia <dev@eoxia.com>
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 * @package   WPshop\Templates
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

if ( $wps_query->have_posts() ) :
	?>
	<div class="wps-product-grid wpeo-gridlayout grid-3">
		<?php
		while ( $wps_query->have_posts() ) : $wps_query->the_post();
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
					<?php if ( ! empty( $post->price_ttc ) ) : ?>
						<div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="wps-product-price">
							<span itemprop="price" content="<?php echo esc_html( number_format( $post->price_ttc, 2, '.', '' ) ); ?>"><?php echo esc_html( number_format( $post->price_ttc, 2, ',', '' ) ); ?></span>
							<span itemprop="priceCurrency" content="EUR"><?php echo esc_html( '€', 'wpshop' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		endwhile;
		?>
	</div>
	<?php
	$big = 999999999;
	 echo paginate_links( array(
		'base'      => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
		'format'    => '?paged=%#%',
		'current'   => max( 1, get_query_var('paged') ),
		'total'     => $wps_query->max_num_pages,
		'next_text' => '<i class="dashicons dashicons-arrow-right"></i>',
		'prev_text' => '<i class="dashicons dashicons-arrow-left"></i>',
	) );

	wp_reset_postdata();
endif;
