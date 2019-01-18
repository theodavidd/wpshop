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

<h3><?php _e( 'Billing details', 'wpshop' ); ?></h3>

<?php do_action( 'wps_before_checkout_billing_form' ); ?>

<div class="wpeo-form">
	<div class="wpeo-grid grid-2">
		<div>
			<div class="form-element contact-firstname">
				<span class="form-label">First name</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[firstname]" />
				</label>
			</div>
		</div>

		<div>
			<div class="form-element contact-lastname">
				<span class="form-label">Last name</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[lastname]" />
				</label>
			</div>
		</div>
	</div>

	<div class="form-element third_party-title">
		<span class="form-label">Company name</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[title]" />
		</label>
	</div>

	<div class="form-element third_party-country">
		<span class="form-label">Country</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[country]" />
		</label>
	</div>

	<div class="form-element third_party-address">
		<span class="form-label">Street address</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[address]" />
		</label>
	</div>

	<div class="form-element third_party-zip">
		<span class="form-label">Postcode/ ZIP</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[zip]" />
		</label>
	</div>

	<div class="form-element third_party-state">
		<span class="form-label">Town / City</span>
		<label class="form-field-container">
			<input type="text" class="form-field" name="third_party[state]" />
		</label>
	</div>

	<div class="wpeo-grid grid-2">
		<div>
			<div class="form-element contact-phone">
				<span class="form-label">Phone</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[phone]" />
				</label>
			</div>
		</div>

		<div>
			<div class="form-element contact-email">
				<span class="form-label">Email address*</span>
				<label class="form-field-container">
					<input type="text" class="form-field" name="contact[email]" />
				</label>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'wps_after_checkout_billing_form' ); ?>
