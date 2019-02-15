<?php
/**
 * Gestion des actions de synchronisations des entités avec dolibarr.
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
 * Action of Third Party module.
 */
class Doli_Syncho_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_load_modal_synchro', array( $this, 'load_modal_synchro' ) );

		add_action( 'wp_ajax_sync_third_parties', array( $this, 'sync_third_parties' ) );
		add_action( 'wp_ajax_sync_contacts', array( $this, 'sync_contacts' ) );
		add_action( 'wp_ajax_sync_products', array( $this, 'sync_products' ) );
		add_action( 'wp_ajax_sync_orders', array( $this, 'sync_orders' ) );
	}

	public function load_modal_synchro() {
		$sync_infos = Doli_Synchro::g()->sync_infos;

		if ( ! empty( $sync_infos ) ) {
			foreach ( $sync_infos as &$sync_info ) {
				$sync_info['total_number'] = 0;
				if ( ! empty( $sync_info['endpoint'] ) ) {
					$sync_info['total_number'] = count( Request_Util::get( $sync_info['endpoint'] ) );
				}
			}
		}
		ob_start();
		\eoxia\View_Util::exec( 'wpshop', 'doli-synchro', 'main', array(
			'sync_infos' => $sync_infos,
		) );
		$view = ob_get_clean();
		wp_send_json_success( array(
			'view' => $view,
	 	) );
	}

	public function sync_third_parties() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Doli_Third_Party_Class::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 0,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_contacts() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		if ( Doli_Contact_Class::g()->synchro( $done_number, 10 ) ) {
			$done_number += 10;
		}

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 0,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_products() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$done_number += 10;

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}

	public function sync_orders() {
		$done         = false;
		$done_number  = ! empty( $_POST['done_number'] ) ? (int) $_POST['done_number'] : 0;
		$total_number = ! empty( $_POST['total_number'] ) ? (int) $_POST['total_number'] : 0;

		$done_number += 10;

		if ( $done_number >= $total_number ) {
			$done_number = $total_number;
			$done  = true;
		}

		wp_send_json_success( array(
			'updateComplete'     => false,
			'done'               => $done,
			'progression'        => $done_number . '/' . $total_number,
			'progressionPerCent' => 0 !== $total_number ? ( ( $done_number * 100 ) / $total_number ) : 100,
			'doneDescription'    => $done_number . '/' . $total_number,
			'doneElementNumber'  => $done_number,
			'errors'             => null,
		) );
	}
}

new Doli_Syncho_Action();
