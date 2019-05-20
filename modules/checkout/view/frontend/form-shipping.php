<?php
/**
 * Le formulaire pour créer son adresse de livraison
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

<div class="wps-checkout-subtitle"><?php esc_html_e( 'Personnal and shipping informations', 'wpshop' ); ?></div>

<?php do_action( 'wps_before_checkout_billing_form' ); ?>

<div class="wpeo-gridlayout grid-6">
	<?php wp_nonce_field( 'callback_checkout_create_third' ); ?>
	<input type="hidden" class="form-field" name="contact[firstname]" value="<?php echo ! empty( $contact->data['firstname'] ) ? $contact->data['firstname'] : ''; ?>" />
	<input type="hidden" class="form-field" name="contact[lastname]"  value="<?php echo ! empty( $contact->data['lastname'] ) ? $contact->data['lastname'] : ''; ?>" />
	<input type="hidden" class="form-field" name="third_party[title]" value="<?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : ''; ?>" />
	<input type="hidden" name="third_party[country_id]" value="<?php echo $third_party->data['country_id']; ?>" />
	<input type="hidden" class="form-field" name="third_party[address]" value="<?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="third_party[zip]" value="<?php echo ! empty( $third_party->data['zip'] ) ? $third_party->data['zip'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="third_party[town]" value="<?php echo ! empty( $third_party->data['town'] ) ? $third_party->data['town'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="third_party[country_id]" value="<?php echo ! empty( $third_party->data['country_id'] ) ? $third_party->data['country_id'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="contact[phone]" value="<?php echo ! empty( $third_party->data['phone'] ) ? $third_party->data['phone'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="contact[email]" value="<?php echo ! empty( $contact->data['email'] ) ? $contact->data['email'] : ''; ?>"  />

	<div class="form-element contact-firstname gridw-3">
		<label class="form-field-container">
			<input type="text" class="form-field" name="contact[firstname]" placeholder="<?php esc_html_e( 'First name', 'wpshop' ); ?>" value="<?php echo ! empty( $contact->data['firstname'] ) ? $contact->data['firstname'] : ''; ?>" />
		</label>
	</div>

	<div class="form-element contact-lastname gridw-3">
		<label class="form-field-container">
			<input type="text" class="form-field" name="contact[lastname]" placeholder="<?php esc_html_e( 'Last name', 'wpshop' ); ?>"  value="<?php echo ! empty( $contact->data['lastname'] ) ? $contact->data['lastname'] : ''; ?>" />
		</label>
	</div>

	<div class="form-element third_party-title gridw-6">
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[title]" placeholder="<?php esc_html_e( 'Company name', 'wpshop' ); ?>" value="<?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : ''; ?>" />
		</label>
	</div>

	<div class="form-element contact-email form-element-required gridw-3">
		<label class="form-field-container">
			<input type="text" class="form-field" name="contact[email]" placeholder="<?php esc_html_e( 'Email', 'wpshop' ); ?>" value="<?php echo ! empty( $contact->data['email'] ) ? $contact->data['email'] : ''; ?>"  />
		</label>
	</div>

	<div class="form-element contact-phone gridw-3">
		<label class="form-field-container">
			<input type="text" class="form-field" name="contact[phone]" placeholder="<?php esc_html_e( 'Phone number', 'wpshop' ); ?>" value="<?php echo ! empty( $third_party->data['phone'] ) ? $third_party->data['phone'] : ''; ?>"  />
		</label>
	</div>

	<div class="form-element third_party-address form-element-required gridw-6">
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[address]" placeholder="<?php esc_html_e( 'Street address', 'wpshop' ); ?>" value="<?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : ''; ?>"  />
		</label>
	</div>

	<?php $countries = get_countries(); ?>
	<div class="form-element third_party-country_id form-element-required gridw-2">
		<label class="form-field-container">
			<select id="monselect" class="form-field" name="third_party[country_id]">
				<?php
				if ( ! empty( $countries ) ) :
					foreach ( $countries as $country ) :
						$selected = '';

						if ( ! empty( $third_party ) && $country['id'] === $third_party->data['country_id'] ) :
							$selected = 'selected="selected"';
						endif;

						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $country['id'] ); ?>"><?php echo $country['label']; ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element third_party-town form-element-required gridw-2">
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[town]" placeholder="<?php esc_html_e( 'Town / City', 'wpshop' ); ?>" value="<?php echo ! empty( $third_party->data['town'] ) ? $third_party->data['town'] : ''; ?>"  />
		</label>
	</div>

	<div class="form-element third_party-zip form-element-required gridw-2">
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[zip]" placeholder="<?php esc_html_e( 'Postcode / ZIP', 'wpshop' ); ?>" value="<?php echo ! empty( $third_party->data['zip'] ) ? $third_party->data['zip'] : ''; ?>"  />
		</label>
	</div>

</div>

<?php
if ( 0 !== $third_party->data['id'] ) :
	?>
	<!-- <a class="wpeo-button button-grey action-attribute alignright"
		data-action="load_edit_billing_address"
		data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_edit_billing_address' ) ); ?>"
		data-id="<?php echo $contact->data['id']; ?>"><?php esc_html_e( 'Edit billing address', 'wpshop' ); ?></a> -->
	<?php
endif;
?>

<?php do_action( 'wps_after_checkout_billing_form' ); ?>
