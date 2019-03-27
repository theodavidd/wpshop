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
	<div class="page-header">
		<a href="<?php echo esc_attr( $doli_url ); ?>product/card.php?id=<?php echo $product->data['external_id']; ?>"
			class="wpeo-button button-main"
			target="_blank"><?php esc_html_e( 'See in Dolibarr', 'wpshop' ); ?></a>

		<div class="wps-sync">
			<div class="button-synchro action-attribute"
				data-action="associate_and_synchronize"
				data-entry-id="<?php echo esc_attr( $product->data['external_id'] ); ?>"
				data-wp-id="<?php echo esc_attr( $product->data['id'] ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'associate_and_synchronize' ) ); ?>"
				data-from="dolibarr"><i class="fas fa-sync"></i></div>

			<div class="statut statut-green wpeo-tooltip-event"
				data-direction="bottom"
				aria-label="<?php echo esc_html__( 'Last sync :', 'wpshop' ) . ' '; ?>"></div>
		</div>
	</div>

	<div class="wpeo-form">
		<?php wp_nonce_field( basename( __FILE__ ), 'wpshop_data_fields' ); ?>

		<div class="wpeo-gridlayout grid-3">

			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'Price HT(€)', 'wpshop' ); ?></span>
				<label class="form-field-container">
					<span><?php echo esc_attr( $product->data['price'] ); ?>€</span>
				</label>
			</div>

			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'VAT Rate', 'wpshop' ); ?></span>
				<label class="form-field-container">
					<span><?php echo esc_attr( $product->data['tva_tx'] ); ?>%</span>
				</label>
			</div>

			<div class="form-element">
				<span class="form-label"><?php esc_html_e( 'Price TTC(€)', 'wpshop' ); ?></span>
				<label class="form-field-container">
					<span><?php echo esc_attr( $product->data['price_ttc'] ); ?>€</span>
				</label>
			</div>

		</div>
	</div>
</div>
