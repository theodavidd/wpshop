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

<div class="wrap wpeo-wrap">
	<h2>
		<?php esc_html_e( 'Products', 'wpshop' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=wps-product' ) ); ?>" class="wpeo-button button-main"><?php esc_html_e( 'Add', 'wpshop' ); ?></a>

		<div class="wpeo-button button-main wpeo-modal-event"
			data-action="load_modal_synchro"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_modal_synchro' ) ); ?>"
			data-class="modal-sync"
			data-sync="products"
			data-title="<?php echo esc_attr_e( 'Data synchronization', 'wpshop' ); ?>">
			<span><?php esc_html_e( 'All Product Sync', 'wpshop' ); ?></span>
		</div>

	</h2>


	<div class="wps-filter-bar wpeo-form form-light">
		<div class="form-element">
			<label class="form-field-container">
				<span class="form-field-icon-prev"><i class="fas fa-filter"></i></span>
				<select id="monselect" class="form-field">
					<option value="valeur1" selected><?php esc_html_e( 'Date', 'wpshop' ); ?></option>
					<option value="valeur2"><?php esc_html_e( 'Title', 'wpshop' ); ?></option>
					<option value="valeur3"><?php esc_html_e( 'Price HT(€)', 'wpshop' ); ?></option>
					<option value="valeur4"><?php esc_html_e( 'Desynchronized products', 'wpshop' ); ?></option>
				</select>
			</label>
		</div>

		<a href="#" class="wpeo-button button-filter"><?php esc_html_e( 'Filter', 'wpshop' ); ?></a>

		<div class="form-element">
			<label class="form-field-container">
				<span class="form-field-icon-prev"><i class="fas fa-search"></i></span>
				<input type="text" class="form-field" />
			</label>
		</div>

		<a href="#" class="wpeo-button button-filter"><?php esc_html_e( 'Search', 'wpshop' ); ?></a>

		<div></div>
		<div></div>
		<div><?php echo $count . ' éléments'; ?></div>
	</div>

	<?php Product::g()->display(); ?>
</div>
