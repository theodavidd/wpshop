<?php
/**
 * Gestion des filtres des produits.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Filters
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Filters of product module.
 */
class Doli_Third_Parties_Filter {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'wps_save_and_associate_contact', array( $this, 'add_contact_soc' ), 10, 2 );
	}

	public function add_contact_soc( $contact, $third_party ) {
		$contact['third_party_id'] = $third_party->data['external_id'];

		return $contact;
	}
}

new Doli_Third_Parties_Filter();
