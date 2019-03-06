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
class Doli_Synchro_Filter extends \eoxia\Singleton_Util {

	protected function construct() {
		add_filter( 'wps_countries', array( $this, 'doli_countries' ) );
	}

	public function doli_countries( $countries ) {
		$countries        = Request_Util::get( 'setup/dictionary/countries?sortfield=code&sortorder=ASC&limit=100' );
		$countries_for_wp = array();

		if ( ! empty( $countries ) ) {
			foreach ( $countries as $country ) {
				$countries_for_wp[ $country->id ] = $country->label;
			}
		}

		return $countries_for_wp;
	}
}

Doli_Synchro_Filter::g();
