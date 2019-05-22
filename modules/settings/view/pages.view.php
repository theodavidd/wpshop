<?php
/**
 * La vue principale de la page de réglages
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

<form class="wpeo-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo esc_attr( 'wps_update_pages_settings' ); ?>" />
	<input type="hidden" name="tab" value="pages" />
	<?php wp_nonce_field( 'callback_update_pages_settings' ); ?>

	<div class="form-element">
		<span class="form-label">Page boutique</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_shop_id">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['shop_id'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Page panier</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_cart_id">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['cart_id'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Page paiement</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_checkout_id">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['checkout_id'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Page mon compte</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_my_account_id">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['my_account_id'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Page validation de commande</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_valid_checkout_id">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['valid_checkout_id'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Page validation du devis</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_valid_proposal_id">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['valid_proposal_id'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element">
		<span class="form-label">Page conditions générales de vente</span>
		<label class="form-field-container">
			<select id="" class="form-field" name="wps_page_general_conditions_of_sale">
				<?php
				if ( ! empty( $pages ) ) :
					foreach ( $pages as $page ) :
						$selected = '';

						if ( $page->ID === $page_ids_options['general_conditions_of_sale'] ) :
							$selected = 'selected="selected"';
						endif;
						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div>
		<input type="submit" class="wpeo-button button-main" value="Enregister les modifications" />
	</div>
</form>
