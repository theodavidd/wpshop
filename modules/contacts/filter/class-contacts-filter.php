<?php
/**
 * Les filtres des contacts.
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
 * Contact Filter class.
 */
class Contact_Filter {

	public function __construct() {
		add_filter( 'wps_contact_item_contact_title_after', array( $this, 'add_user_switching' ) );
	}

	/**
	 * Affiches les tiers
	 *
	 * @since 2.0.0
	 *
	 * @param Third_Party_Model $third_party Les données du tier.
	 */
	public function add_user_switching( $contact ) {
		if ( class_exists( '\user_switching' ) ) {
			$user = get_user_by( 'id', $contact->data['id'] );

			$link = \user_switching::maybe_switch_url( $user );

			?><a href="<?php echo esc_attr( $link ); ?>"><i class="fas fa-random"></i></a><?php
		}
	}
}

new Contact_Filter();
