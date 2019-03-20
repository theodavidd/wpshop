<?php
/**
 * Affichage d'un produit dans le listing  de la page des produits (wps-product)
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

<div class="table-row">
	<div class="table-cell table-25"><input type="checkbox" class="check"/></div>
	<div class="table-cell table-100 table-padding-0"><?php echo get_the_post_thumbnail( $product->data['id'], array( 80, 80 ) ); ?></div>
	<div class="table-cell table-full">
		<ul class="product-id">
			<li><i class="fas fa-hashtag"></i>WP : <?php echo esc_html( $product->data['id'] ); ?></li>
			<?php if ( ! empty( $product->data['external_id'] ) ) : ?>
				<li><i class="fas fa-hashtag"></i>Doli : <?php echo esc_html( $product->data['external_id'] ); ?></li>
			<?php endif; ?>
		</ul>
		<div class="product-title">
			<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $product->data['id'] . '&action=edit' ) ); ?>"><?php echo esc_html( $product->data['title'] ); ?></a>
		</div>
		<ul class="product-actions">
			<li><a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $product->data['id'] . '&action=edit' ) ); ?>"><?php esc_html_e( 'Edit', 'wpshop' ); ?></a></li>
			<!-- <li><a href="#"><?php esc_html_e( 'Quick edit', 'wpshop' ); ?></a></li> -->
			<?php if ( ! empty( $product->data['external_id'] ) ) : ?>
				<li><a href="<?php echo esc_attr( $doli_url ); ?>product/card.php?id=<?php echo $product->data['external_id']; ?>" target="_blank"><?php esc_html_e( 'Edit in Dolibarr', 'wpshop' ); ?></a></li>
			<?php endif; ?>
			<!-- <li class="delete"><a href="#"><?php esc_html_e( 'Delete', 'wpshop' ); ?></a></li> -->
			<li><a href="<?php echo esc_attr( get_post_permalink( $product->data['id'] ) ); ?>"><?php esc_html_e( 'Preview', 'wpshop' ); ?></a></li>
		</ul>
	</div>
	<div class="table-cell table-100"><?php echo esc_html( number_format( $product->data['price'], 2, ',', '' ) ); ?>€</div>
	<div class="table-cell table-100"><?php echo esc_html( number_format( $product->data['tva_tx'], 2, ',', '' ) ); ?>%</div>
	<div class="table-cell table-100"><strong><?php echo esc_html( number_format( $product->data['price_ttc'], 2, ',', '' ) ); ?>€</strong></div>
	<div class="table-cell table-100">
		<!-- <div class="button-synchro"><i class="fas fa-sync"></i></div>
		<div class="statut statut-green wpeo-tooltip-event" data-direction="left" aria-label="Date de la derniere synchro"></div> -->
	</div>
</div>
