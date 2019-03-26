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

<div class="wpeo-gridlayout grid-2">
	<?php wp_nonce_field( 'callback_checkout_create_third' ); ?>
	<input type="hidden" class="form-field" name="contact[firstname]" value="<?php echo ! empty( $contact->data['firstname'] ) ? $contact->data['firstname'] : ''; ?>" />
	<input type="hidden" class="form-field" name="contact[lastname]"  value="<?php echo ! empty( $contact->data['lastname'] ) ? $contact->data['lastname'] : ''; ?>" />
	<input type="hidden" class="form-field" name="third_party[title]" value="<?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : ''; ?>" />
	<input type="hidden" name="third_party[country_id]" value="<?php echo $third_party->data['country_id']; ?>" />
	<input type="hidden" class="form-field" name="third_party[address]" value="<?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="third_party[zip]" value="<?php echo ! empty( $third_party->data['zip'] ) ? $third_party->data['zip'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="third_party[town]" value="<?php echo ! empty( $third_party->data['town'] ) ? $third_party->data['town'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="contact[phone]" value="<?php echo ! empty( $third_party->data['phone'] ) ? $third_party->data['phone'] : ''; ?>"  />
	<input type="hidden" class="form-field" name="contact[email]" value="<?php echo ! empty( $contact->data['email'] ) ? $contact->data['email'] : ''; ?>"  />

	<ul>
		<li>
			<span><?php esc_html_e( 'First name', 'wpshop' ); ?>:
				<strong><?php echo ! empty( $contact->data['firstname'] ) ? $contact->data['firstname'] : ''; ?></strong></span>
		</li>
		<li>
			<span><?php esc_html_e( 'Last name', 'wpshop' ); ?>:
				<strong><?php echo ! empty( $contact->data['lastname'] ) ? $contact->data['lastname'] : ''; ?></strong></span>
	</li>
		<li>
			<span><?php esc_html_e( 'Company name', 'wpshop' ); ?>:
				<strong><?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : ''; ?></strong></span>
		</li>
	</ul>

	<ul>
		<li>
			<span><?php esc_html_e( 'Street address', 'wpshop' ); ?>
				<strong><?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : ''; ?></strong></span>
		</li>
		<li>
			<span><?php esc_html_e( 'Postcode / ZIP', 'wpshop' ); ?>
				<strong><?php echo ! empty( $third_party->data['zip'] ) ? $third_party->data['zip'] : ''; ?></strong></span>
		</li>
		<li>
			<span><?php esc_html_e( 'Town / City', 'wpshop' ); ?>
				<strong><?php echo ! empty( $third_party->data['town'] ) ? $third_party->data['town'] : ''; ?></strong></span>
		</li>
		<li>
			<span><?php esc_html_e( 'Phone number', 'wpshop' ); ?>
				<strong><?php echo ! empty( $third_party->data['phone'] ) ? $third_party->data['phone'] : ''; ?></strong></span>
		</li>
		<li>
			<span><?php esc_html_e( 'Email', 'wpshop' ); ?>
				<strong><?php echo ! empty( $contact->data['email'] ) ? $contact->data['email'] : ''; ?></strong></span>
		</li>
	</ul>
</div>

<?php
if ( 0 !== $third_party->data['id'] ) :
	?>
	<a class="wpeo-button button-grey action-attribute alignright"
		data-action="load_edit_billing_address"
		data-nonce="<?php echo esc_attr( wp_create_nonce( 'load_edit_billing_address' ) ); ?>"
		data-id="<?php echo $contact->data['id']; ?>"><?php esc_html_e( 'Edit billing address', 'wpshop' ); ?></a>
	<?php
endif;
?>

<?php do_action( 'wps_after_checkout_billing_form' ); ?>
