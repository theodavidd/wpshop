<?php
/**
 * Gestion API.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
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
 * API Class.
 */
class API extends \eoxia\Singleton_Util {

	/**
	 * Construct
	 *
	 * @since 7.1.0
	 */
	protected function construct() {}

	public function generate_token() {
		$length            = 20;
		$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_][-&';
		$characters_length = strlen( $characters );

		$security_id = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$security_id .= $characters[ rand( 0, $characters_length - 1) ];
		}

		return $security_id;
	}

	public function get_user_by_token( $token ) {
		$users = get_users( array(
			'meta_key'   => '_wpshop_api_key',
			'meta_value' => $token,
			'number'     => 1,
		) );

		if ( empty( $users ) ) {
			return null;
		}

		if ( ! empty( $users ) && 1 === count( $users ) ) {
			$token_base = get_user_meta( $users[0]->ID, '_wpshop_api_key', true );

			if ( $token_base !== $token ) {
				return false;
			}

			return $users[0];
		}

		return null;
	}
}

new API();
