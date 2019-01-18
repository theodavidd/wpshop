<?php
/**
 * Les fonctions principales des tiers avec dolibarr.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Classes
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Third Party class.
 */
class Doli_Contact_Class extends \eoxia\Singleton_Util {

	protected function construct() {}

	public function save( $third_party_id, $posted_data  ) {
		if ( empty( $posted_data['contact']['lastname'] ) ) {
			$email = explode( '@', $posted_data['contact']['email'] );

			$posted_data['contact']['lastname'] = $email[0];
		}

		$contact_id = Request_Util::post( 'contacts', array(
			'lastname'  => $posted_data['contact']['lastname'],
			'firstname' => $posted_data['contact']['firstname'],
			'email'     => $posted_data['contact']['email'],
			'phone_pro' => $posted_data['contact']['phone'],
			'socid'     => $third_party_id,
		) );

		return $contact_id;
	}
}

Doli_Contact_Class::g();
