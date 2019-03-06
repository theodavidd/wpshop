<?php
/**
 * Les actions relatives aux proposals.
 *
 * @author Eoxia <corentin-settelen@hotmail.com>
 * @since 2.0.0
 * @version 2.0.0
 * @copyright 2018 Eoxia
 * @package wpshop
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Les actions relatives aux proposals.
 */
class Proposals_Action {

	/**
	 * Initialise les actions liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'callback_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );

		add_action( 'add_meta_boxes', array( $this, 'callback_add_meta_boxes' ) );
	}

	public function callback_admin_init() {
		remove_post_type_support( 'wps-proposal', 'title' );
		remove_post_type_support( 'wps-proposal', 'editor' );
		remove_post_type_support( 'wps-proposal', 'excerpt' );
	}

	/**
	 * Initialise la page "Third Parties".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		// add_submenu_page( 'wps-order', __( 'Proposals', 'wpshop' ), __( 'Proposals', 'wpshop' ), 'manage_options', 'wps-proposal', array( $this, 'callback_add_menu_page' ) );
	}

	/**
	 * Appel la vue "main" du module "Orders".
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'main' );
	}

	public function callback_add_meta_boxes() {
		$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

		if ( isset( $_GET['post'] ) ) {
			$post_type = get_post_type( $_GET['post'] );
		}

		if ( $post_type == 'wps-proposal' ) {
			remove_meta_box( 'submitdiv', 'wps-proposal', 'side' );
			remove_meta_box( 'slugdiv', 'wps-proposal', 'normal' );

			$proposal = Proposals_Class::g()->get( array( 'id' => $_GET['post'] ), true );

			$args_metabox = array(
				'proposal' => $proposal,
			);

			add_meta_box( 'wps-proposal-customer', __( 'Proposal details #' . $proposal->data['title'], 'wpshop' ), array( $this, 'callback_meta_box' ), 'wps-proposal', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-proposal-products',  __( 'Products', 'wpshop' ), array( $this, 'callback_products' ), 'wps-proposal', 'normal', 'default', $args_metabox );
			add_meta_box( 'wps-proposal-submit', __( 'Proposal actions', 'wpshop'), array( $this, 'callback_proposal_action' ), 'wps-proposal', 'side', 'default', $args_metabox );
		}
	}

	public function callback_meta_box( $post, $callback_args ) {
		$proposal      = $callback_args['args']['proposal'];
		$third_party   = Third_Party_Class::g()->get( array( 'id' => $proposal->data['parent_id'] ), true );
		$link_proposal = admin_url( 'admin-post.php?action=wps_download_proposal&proposal_id=' . $proposal->data['id'] );

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'metabox-proposal-details', array(
			'proposal'      => $proposal,
			'third_party'   => $third_party,
			'link_proposal' => $link_proposal,
		) );
	}

	public function callback_products( $post, $callback_args ) {
		$proposal = $callback_args['args']['proposal'];

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'metabox-orders', array(
			'proposal' => $proposal,
		) );
	}

	public function callback_proposal_action( $post, $callback_args ) {
		$proposal = $callback_args['args']['proposal'];

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'metabox-action', array(
			'proposal' => $proposal,
		) );
	}
}

new Proposals_Action();
