<?php
/**
 * Gestion des requêtes.
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
 * Gestion des requêtes.
 */
class Request_Util extends \eoxia\Singleton_Util {

	/**
	 * Le constructeur
	 *
	 * @since 0.2.0
	 */
	protected function construct() {}

	/**
	 * Requête POST.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $end_point L'url a appeller
	 * @param  array $body     Les données du formulaire.
	 * @param  string $hash    Le token
	 *
	 * @return array|boolean   Retournes les données de la requête ou false.
	 */
	public static function post( $end_point, $data = array(), $method = 'POST' ) {
		$dolibarr_option = get_option( 'wps_dolibarr', Settings_Class::g()->default_settings );

		$api_url = $dolibarr_option['dolibarr_url'] . $end_point;

		$request = wp_remote_post( $api_url, array(
			'method'   => $method,
			'blocking' => true,
			'headers'  => array(
				'Content-type' => 'application/json',
				'DOLAPIKEY'    => $dolibarr_option['dolibarr_secret'],
			),
			'sslverify' => false,
			'body'      => json_encode( $data ),
		) );

		if ( ! is_wp_error( $request ) ) {
			if ( $request['response']['code'] == 200 ) {
				$response = json_decode( $request['body'] );
				return $response;
			} else {
				return false;
			}
		}

		return false;
	}

	public static function put( $end_point, $data ) {
		return Request_Util::post( $end_point, $data, 'PUT' );
	}

	/**
	 * Requête GET.
	 * @param  string $api_url L'url a appeller
	 * @param  string $hash    Le token
	 *
	 * @return array|boolean   Retournes les données de la requête ou false.
	 */
	public static function get( $end_point ) {
		$dolibarr_option = get_option( 'wps_dolibarr', Settings_Class::g()->default_settings );

		$api_url = $dolibarr_option['dolibarr_url'] . $end_point;
		$request = wp_remote_get( $api_url, array(
			'headers' => array(
				'Content-type' => 'application/json',
				'DOLAPIKEY'    => $dolibarr_option['dolibarr_secret'],
			),
		) );

		if ( ! is_wp_error( $request ) ) {
			if ( $request['response']['code'] == 200 ) {
				$response = json_decode( $request['body'] );
				return $response;
			}
		}

		return false;
	}
}

new Request_Util();
