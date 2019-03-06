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

<h3><?php esc_html_e( 'Shipping details', 'wpshop' ); ?></h3>

<?php do_action( 'wps_before_checkout_billing_form' ); ?>

<div class="wpeo-form">
	<div class="wpeo-grid grid-2">
		<div>
			<div class="form-element contact-firstname">
				<span class="form-label">First name</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[firstname]" value="<?php echo ! empty( $contact->data['firstname'] ) ? $contact->data['firstname'] : ''; ?>" />
				</label>
			</div>
		</div>

		<div>
			<div class="form-element contact-lastname">
				<span class="form-label">Last name</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[lastname]"  value="<?php echo ! empty( $contact->data['lastname'] ) ? $contact->data['lastname'] : ''; ?>" />
				</label>
			</div>
		</div>
	</div>

	<div class="form-element third_party-title">
		<span class="form-label">Company name</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[title]" value="<?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : ''; ?>" />
		</label>
	</div>

	<?php $countries = get_countries(); ?>
	<div class="form-element third_party-country_id">
		<span class="form-label">Country*</span>
		<label class="form-field-container">
			<select id="monselect" class="form-field" name="third_party[country_id]">
				<?php
				if ( ! empty( $countries ) ) :
					foreach ( $countries as $key => $country ) :
						$selected = '';

						if ( ! empty( $third_party ) && $key == $third_party->data['country_id'] ) :
							$selected = 'selected="selected"';
						endif;

						?>
						<option <?php echo $selected; ?> value="<?php echo esc_attr( $key ); ?>"><?php echo $country; ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</label>
	</div>

	<div class="form-element third_party-address">
		<span class="form-label">Street address</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[address]" value="<?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : ''; ?>"  />
		</label>
	</div>

	<div class="form-element third_party-zip">
		<span class="form-label">Postcode/ ZIP</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[zip]" value="<?php echo ! empty( $third_party->data['zip'] ) ? $third_party->data['zip'] : ''; ?>"  />
		</label>
	</div>

	<div class="form-element third_party-state">
		<span class="form-label">Town / City</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[town]" value="<?php echo ! empty( $third_party->data['town'] ) ? $third_party->data['town'] : ''; ?>"  />
		</label>
	</div>

	<div class="wpeo-grid grid-2">
		<div>
			<div class="form-element contact-phone">
				<span class="form-label">Phone</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[phone]" value="<?php echo ! empty( $contact->data['phone'] ) ? $contact->data['phone'] : ''; ?>"  />
				</label>
			</div>
		</div>

		<div>
			<div class="form-element contact-email">
				<span class="form-label">Email address*</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[email]" value="<?php echo ! empty( $contact->data['email'] ) ? $contact->data['email'] : ''; ?>"  />
				</label>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'wps_after_checkout_billing_form' ); ?>
