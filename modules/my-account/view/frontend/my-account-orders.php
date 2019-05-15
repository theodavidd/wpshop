<?php
/**
 * Affichage des commandes dans la page "Mon compte"
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

defined( 'ABSPATH' ) || exit;

?>

<div class="wps-list-order wps-list-box">
	<?php
	if ( ! empty( $orders ) ) :
		foreach ( $orders as $order ) :
			?>
			<div class="wps-order wps-box">
				<div class="wps-box-resume">
					<div class="wps-box-primary">
						<div class="wps-box-title"><?php echo esc_html( $order->data['datec']['rendered']['date'] ); ?></div>
						<ul class="wps-box-attributes">
							<li class="wps-box-subtitle-item"><i class="wps-box-subtitle-icon fas fa-shopping-cart"></i> <?php echo esc_attr( $order->data['title'] ); ?></li>
						</ul>
						<div class="wps-box-display-more">
							<i class="wps-box-display-more-icon fas fa-angle-right"></i>
							<span class="wps-box-display-more-text"><?php esc_html_e( 'View details', 'wpshop' ); ?></span>
						</div>
					</div>
					<div class="wps-box-secondary">
						<div class="wps-box-status"><span class="wps-box-status-dot"></span> <?php echo Payment::g()->make_readable_statut( $order ); ?></div>
						<div class="wps-box-price"><?php echo esc_html( number_format( $order->data['total_ttc'], 2, ',', '' ) ); ?>€</div>
					</div>
					<div class="wps-box-action">
						<a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_order&_wpnonce=' . wp_create_nonce( 'download_order' ) . '&order_id=' . $order->data['id'] ) ); ?>" class="wpeo-button button-primary button-square-50 button-rounded">
							<i class="button-icon fas fa-file-download"></i>
						</a>
						<div class="wps-more-options wpeo-dropdown dropdown-right">
							<div class="dropdown-toggle wpeo-button button-transparent button-square-40 button-size-large"><i class="button-icon fas fa-ellipsis-v"></i></div>
							<ul class="dropdown-content">
								<li class="action-attribute dropdown-item"
									data-action="reorder"
									data-nonce="<?php echo esc_attr( wp_create_nonce( 'do_reorder' ) ); ?>"
									data-id="<?php echo esc_attr( $order->data['id'] ); ?>">
									<i class="fas fa-shopping-cart"></i> <?php esc_html_e( 'Reorder', 'wpshop' ); ?></li>
							</ul>
						</div>
					</div>
				</div>

				<div class="wps-box-detail wps-list-product">
					<?php
					if ( ! empty( $order->data['lines'] ) ) :
						foreach( $order->data['lines'] as $line ) :
							?>
							<div itemscope itemtype="https://schema.org/Product" class="wps-product">
								<figure class="wps-product-thumbnail">
									<?php echo get_the_post_thumbnail( $line['wp_id'], 'thumbnail', array( 'itemprop' => 'image' ) ); ?>
								</figure>
								<div class="wps-product-content">
									<div itemprop="name" class="wps-product-title"><?php echo esc_html( $line['libelle'] ); ?></div>
									<!-- <ul class="wps-product-attributes">
										<li class="wps-product-attributes-item"></li>
									</ul> -->
									<div class="wps-product-footer">
										<div class="wps-product-quantity"><?php echo esc_html( $line['qty'] ); ?></div>
										<?php if ( ! empty( $line['total_ttc'] ) ) : ?>
											<div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="wps-product-price">
												<span itemprop="price" content="<?php echo esc_html( number_format( $line['total_ttc'], 2, '.', '' ) ); ?>"><?php echo esc_html( number_format( $line['total_ttc'], 2, '.', '' ) ); ?></span>
												<span itemprop="priceCurrency" content="EUR"><?php echo esc_html( '€', 'wpshop' ); ?></span>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php
						endforeach;
					else :
						esc_html_e( 'No products to display', 'wpshop' );
					endif;
					?>
				</div>
			</div>
			<?php
		endforeach;
	endif;
	?>
</div>
