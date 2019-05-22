<?php
/**
 * La vue principale de la page des produits (wps-product)
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

<div class="wpeo-wrap">
	<div class="wpeo-form">
		<?php wp_nonce_field( basename( __FILE__ ), 'wpshop_data_fields' ); ?>

		<div class="wpeo-gridlayout grid-3">
			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'Price HT(€)', 'wpshop' ); ?></span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="product_data[price]" value="<?php echo esc_attr( $product->data['price'] ); ?>" />
				</label>
			</div>

			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'VAT Rate', 'wpshop' ); ?></span>

				<label class="form-field-container">
					<select name="product_data[tva_tx]">
						<?php
						$has_selected = false;
						if ( ! empty( Settings::g()->tva ) ) :
							foreach ( Settings::g()->tva as $tva ) :
								$selected = '';
								if ( (float) $tva === (float) $product->data['tva_tx'] || ( ! $has_selected && $tva === 20 ) ) :
									$selected = 'selected="selected"';
									$has_selected = true;
								endif;
								?>
								<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $tva ); ?>"><?php echo esc_html( $tva ); ?>%</option>
								<?php
							endforeach;
						endif;
						?>
					</select>
				</label>
			</div>

			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'Price TTC(€)', 'wpshop' ); ?></span>
				<label class="form-field-container">
					<span><?php echo esc_attr( $product->data['price_ttc'] ); ?>€</span>
				</label>
			</div>

			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'Product Downloadable', 'wpshop' ); ?></span>
				<input type="hidden" name="product_data[product_downloadable]" class="product_downloadable" value="<?php echo '1' == $product->data['product_downloadable'] ? 'true' : 'false'; ?>" />
				<i style="font-size: 2em;" class="toggle fas fa-toggle-<?php echo $product->data['product_downloadable'] ? 'on' : 'off'; ?>" data-bloc="label-upload" data-input="product_downloadable"></i>
				<label class="label-upload form-field-container" style="<?php echo $product->data['product_downloadable'] ? '' : 'display: none;'; ?>">
					<?php echo do_shortcode( '[wpeo_upload id="' . $product->data['id'] . '" upload_dir="wpshop_uploads" field_name="downloadable_product_id" single="false" model_name="/wpshop/Product" mime_type="" display_type="list"]' ); ?>
				</label>
			</div>
		</div>
	</div>
</div>
