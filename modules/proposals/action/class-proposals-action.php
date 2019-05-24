<?php
/**
 * Les actions relatives aux devis avec Dolibarr.
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
 * Doli Order Action Class.
 */
class Proposals_Action {

	/**
	 * Initialise les actions liées aux proposals.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'callback_admin_init' ) );
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );
		add_action( 'admin_menu', array( $this, 'callback_admin_menu' ) );
	}

	/**
	 * Ajoutes des status dans la commande.
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_init() {
		remove_post_type_support( 'wps-proposal', 'title' );
		remove_post_type_support( 'wps-proposal', 'editor' );
		remove_post_type_support( 'wps-proposal', 'excerpt' );
	}

	/**
	 * Ajoutes la metabox details
	 *
	 * @since 2.0.0
	 */
	public function add_meta_box() {
		if ( isset( $_GET['id'] ) && isset( $_GET['page'] ) && 'wps-proposal' === $_GET['page'] ) {
			$proposal = Proposals::g()->get( array( 'id' => $_GET['id'] ), true );

			$args_metabox = array(
				'proposal' => $proposal,
				'id'       => $_GET['id'],
			);

			/* translators: Order details CO00010 */
			$box_proposal_detail_title = sprintf( __( 'Proposal details %s', 'wpshop' ), $proposal->data['title'] );

			add_meta_box( 'wps-proposal-customer', $box_proposal_detail_title, array( $this, 'callback_meta_box' ), 'wps-proposal', 'normal', 'default', $args_metabox );
		}
	}

	/**
	 * Initialise la page "Commande".
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_menu() {
		$hook = add_submenu_page( 'wpshop', __( 'Proposals', 'wpshop' ), __( 'Proposals', 'wpshop' ), 'manage_options', 'wps-proposal', array( $this, 'callback_add_menu_page' ) );

		add_action( 'load-' . $hook, array( $this, 'callback_add_screen_option' ) );
	}

	public function callback_add_screen_option() {
		add_screen_option(
			'per_page',
			array(
				'label'   => _x( 'Proposals', 'Proposal per page', 'wpshop' ),
				'default' => Proposals::g()->limit,
				'option'  => Proposals::g()->option_per_page,
			)
		);
	}

	/**
	 * Affichage de la vue du menu
	 *
	 * @since 2.0.0
	 */
	public function callback_add_menu_page() {
		if ( isset( $_GET['id'] ) ) {
			$proposal = Proposals::g()->get( array( 'id' => $_GET['id'] ), true );

			\eoxia\View_Util::exec( 'wpshop', 'proposals', 'single', array( 'proposal' => $proposal ) );
		} else {
			$per_page = get_user_meta( get_current_user_id(), Third_Party::g()->option_per_page, true );

			if ( empty( $per_page ) || 1 > $per_page ) {
				$per_page = Third_Party::g()->limit;
			}

			$s = ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

			$count = Proposals::g()->search( $s, array(), true );

			$number_page  = ceil( $count / $per_page );
			$current_page = isset( $_GET['current_page'] ) ? $_GET['current_page'] : 1;

			$base_url = admin_url( 'admin.php?page=wps-proposal' );

			$begin_url = $base_url . '&current_page=1';
			$end_url   = $base_url . '&current_page=' . $number_page;

			$prev_url = $base_url . '&current_page=' . ( $current_page - 1 );
			$next_url = $base_url . '&current_page=' . ( $current_page + 1 );

			if ( ! empty( $_GET['s'] ) ) {
				$begin_url .= '&s=' . $_GET['s'];
				$end_url   .= '&s=' . $_GET['s'];
				$prev_url  .= '&s=' . $_GET['s'];
				$next_url  .= '&s=' . $_GET['s'];
			}

			\eoxia\View_Util::exec( 'wpshop', 'proposals', 'main', array(
				'number_page'  => $number_page,
				'current_page' => $current_page,
				'count'        => $count,
				'begin_url'    => $begin_url,
				'end_url'      => $end_url,
				'prev_url'     => $prev_url,
				'next_url'     => $next_url,
			) );
		}
	}

	/**
	 * La metabox des détails de la commande
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Post $post          Les données du post.
	 * @param  array   $callback_args Tableau contenu les données de la commande.
	 */
	public function callback_meta_box( $post, $callback_args ) {
		$proposal     = $callback_args['args']['proposal'];
		$third_party  = Third_Party::g()->get( array( 'id' => $proposal->data['parent_id'] ), true );
		$invoice      = null;
		$link_invoice = '';

		if ( Settings::g()->dolibarr_is_active() ) {
			$invoice      = Doli_Invoice::g()->get( array( 'post_parent' => $proposal->data['id'] ), true );
			if ( ! empty( $invoice ) ) {
				$invoice->data['payments'] = array();
				$invoice->data['payments'] = Doli_Payment::g()->get( array( 'post_parent' => $invoice->data['id'] ) );
				$link_invoice              = admin_url( 'admin-post.php?action=wps_download_invoice_wpnonce=' . wp_create_nonce( 'download_invoice' ) . '&proposal_id=' . $proposal->data['id'] );
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'metabox-proposal-details', array(
			'proposal'     => $proposal,
			'third_party'  => $third_party,
			'invoice'      => $invoice,
			'link_invoice' => $link_invoice,
		) );
	}

	/**
	 * Box affichant les produits de la commande
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Post $post          Les données du post.
	 * @param  array   $callback_args Tableau contenu les données de la commande.
	 */
	public function callback_products( $post, $callback_args ) {
		$proposal = $callback_args['args']['proposal'];

		$tva_lines = array();
		if ( ! empty( $proposal->data['lines'] ) ) {
			foreach ( $proposal->data['lines'] as $line ) {
				if ( empty( $tva_lines[ $line['tva_tx'] ] ) ) {
					$tva_lines[ $line['tva_tx'] ] = 0;
				}

				$tva_lines[ $line['tva_tx'] ] += $line['total_tva'];
			}
		}

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'metabox-proposal-products', array(
			'proposal'  => $proposal,
			'tva_lines' => $tva_lines,
		) );
	}

	/**
	 * Box affichant les actions de la commande.
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Post $post          Les données du post.
	 * @param  array   $callback_args Tableau contenu les données de la commande.
	 */
	public function callback_proposal_action( $post, $callback_args ) {
		$proposal = $callback_args['args']['proposal'];

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'metabox-action', array(
			'order' => $order,
		) );
	}
}

new Proposals_Action();
