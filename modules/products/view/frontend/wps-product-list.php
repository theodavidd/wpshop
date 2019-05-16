<?php
/**
 * Product list view
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2006-2018 Eoxia <dev@eoxia.com>
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 * @package   WPshop\Templates
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

echo '<pre>'; print_r( $product ); echo '</pre>';
?>
<div itemscope itemtype="https://schema.org/Product" class="wps-product">
	<figure class="wps-product-thumbnail">
		<!-- <?php echo get_the_post_thumbnail( $product['wp_id'], 'thumbnail', array( 'itemprop' => 'image' ) ); ?> -->
	</figure>
	<div class="wps-product-content">
		<div itemprop="name" class="wps-product-title"><?php echo esc_html( $product['libelle'] ); ?></div>
		<!-- <ul class="wps-product-attributes">
			<li class="wps-product-attributes-item"></li>
		</ul> -->
		<div class="wps-product-footer">
			<div class="wps-product-quantity"><?php echo esc_html( $product['qty'] ); ?></div>
			<?php if ( ! empty( $product['total_ttc'] ) ) : ?>
				<div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="wps-product-price">
					<span itemprop="price" content="<?php echo esc_html( number_format( $product['total_ttc'], 2, '.', '' ) ); ?>"><?php echo esc_html( number_format( $product['total_ttc'], 2, '.', '' ) ); ?></span>
					<span itemprop="priceCurrency" content="EUR"><?php echo esc_html( '€', 'wpshop' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
