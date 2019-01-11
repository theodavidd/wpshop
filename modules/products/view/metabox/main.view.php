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

<div class="wpeo-gridlayout grid-2 wpeo-form">
	<?php wp_nonce_field( basename( __FILE__ ), 'wpshop_data_fields' ); ?>

	<div>
		<h3><?php esc_html_e( 'Prix', 'wpshop' ); ?></h3>

		<div class="form-element">
			<span class="form-label">Prix HT (€)</span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="product_data[price]" value="<?php echo esc_attr( $product->data['price'] ); ?>" />
			</label>
		</div>

		<div class="form-element">
			<span class="form-label">Taux TVA</span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="product_data[tva_tx]" value="<?php echo esc_attr( $product->data['tva_tx'] ); ?>" />
			</label>
		</div>

		<div class="form-element">
			<span class="form-label">Prix TTC (€)</span>
			<label class="form-field-container">
				<span><?php echo esc_attr( $product->data['price_ttc'] ); ?></span>
			</label>
		</div>

	</div>

	<div>
		<h3><?php esc_html_e( 'Identifiant', 'wpshop' ); ?></h3>

		<div class="form-element">
			<span class="form-label">Code barre</span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="product_data[barcode]" value="<?php echo esc_attr( $product->data['barcode'] ); ?>" />
			</label>
		</div>
	</div>
</div>
