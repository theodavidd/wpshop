<?php
/**
 * Gestion des filtre de santé pour WPshop.
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
 * Health Filter Class.
 */
class Health_Filter {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'debug_information', array( $this, 'add_wpshop_debug_info' ) );
	}

	public function add_wpshop_debug_info( $debug_info ) {
		$page_ids_options = get_option( 'wps_page_ids', Pages::g()->default_options );

		$debug_info['wpshop'] = array(
			'label'    => __( 'WPshop', 'wpshop' ),
			'fields'   => array(),
		);

		if ( ! empty( Pages::g()->page_state_titles ) ) {
			foreach ( Pages::g()->page_state_titles as $key => $page_option ) {
				$value = __( 'Page not set', 'wpshop' );
				$page  = get_page( $page_ids_options[ $key ] );

				if ( ! empty( $page ) ) {
					$value = $page->guid;
				}

				$debug_info['wpshop']['fields'][ $page_option ] = array(
					'label' => 'Page "' . $page_option . '"',
					'value' => $value,
				);
			}
		}

		//echo "<pre>"; print_r( $page_ids_options ); echo "</pre>";exit;
		// 		'license' => array(
		// 			'label'    => __( 'License', 'wpshop' ),
		// 			'value'   => get_option( 'my-plugin-license', __( 'No license found', 'wpshop' ) ),
		// 			'private' => true,
		// 		),
		// 	),
		// );

		return $debug_info;
	}
}

new Health_Filter();
