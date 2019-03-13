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

<a href="<?php echo esc_attr( $doli_url ); ?>product/card.php?id=<?php echo $product->data['external_id']; ?>" target="_blank">Editer sur dolibarr</a>
<div class="wpeo-button action-attribute"
	data-action="associate_and_synchronize"
	data-entry-id="<?php echo esc_attr( $product->data['external_id'] ); ?>"
	data-wp-id="<?php echo esc_attr( $product->data['id'] ); ?>"
	data-from="dolibarr">Synchroniser avec dolibarr</div>

<div class="wpeo-form">
	<?php wp_nonce_field( basename( __FILE__ ), 'wpshop_data_fields' ); ?>

	<div>
		<h3><?php esc_html_e( 'Price', 'wpshop' ); ?></h3>

		<div class="form-element">
			<span class="form-label">Prix HT (€)</span>
			<label class="form-field-container">
				<span><?php echo esc_attr( $product->data['price'] ); ?></span>
			</label>
		</div>

		<div class="form-element">
			<span class="form-label">Taux TVA</span>
			<label class="form-field-container">
				<span><?php echo esc_attr( $product->data['tva_tx'] ); ?></span>
			</label>
		</div>

		<div class="form-element">
			<span class="form-label">Prix TTC (€)</span>
			<label class="form-field-container">
				<span><?php echo esc_attr( $product->data['price_ttc'] ); ?></span>
			</label>
		</div>

	</div>
</div>
