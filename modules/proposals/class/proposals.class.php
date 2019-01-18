<?php
/**
 * Gestion des proposals.
 *
 * @author Eoxia <dev@eoxia.com>
 * @since 2.0.0
 * @version 2.0.0
 * @copyright 2018 Eoxia
 * @package wpshop
 */

namespace wpshop;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gestion des Proposals CRUD.
 */
class Proposals_Class extends \eoxia\Post_Class {

	/**
	 * Constructeur de la classe
	 *
	 * @since 2.0.0
	 * @version 2.0.0
	 */
	protected function construct() {}

	public function update_third_party( $third_party_id ) {

		$proposal_id = Class_Cart_Session::g()->external_data['proposal_id'];

		$proposal = Request_Util::put( 'proposals/' . $proposal_id, array(
			'socid' => $third_party_id,
		) );

		return true;
	}
}
