<?php
/**
 * Classe gérant les devis.
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
 * Proposals Class.
 */
class Proposals extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Proposals_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-proposal';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'proposal';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'proposal';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	/**
	 * Le nom du post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $post_type_name = 'Proposals';

	public $limit = 10;

	public $option_per_page = 'proposal_per_page';

	/**
	 * Récupères la liste des devis et appel la vue "list" du module "order".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$per_page = get_user_meta( get_current_user_id(), $this->option_per_page, true );

		if ( empty( $per_page ) || 1 > $per_page ) {
			$per_page = $this->limit;
		}

		$current_page = isset( $_GET['current_page'] ) ? $_GET['current_page'] : 1;

		$s = ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

		$proposal_ids = Proposals::g()->search( $s, array(
			'offset'         => ( $current_page - 1 ) * $per_page,
			'posts_per_page' => $per_page,
			'post_status'    => 'any',
		) );

		$proposals = array();

		if ( ! empty( $proposal_ids ) ) {
			$proposals = $this->get( array(
				'post__in' => $proposal_ids,
			) );
		}

		if ( ! empty( $proposals ) ) {
			foreach ( $proposals as &$element ) {
				$element->data['tier'] = null;

				if ( ! empty( $element->data['parent_id'] ) ) {
					$element->data['tier'] = Third_Party::g()->get( array( 'id' => $element->data['parent_id'] ), true );
				}
			}
		}

		$dolibarr_option = get_option( 'wps_dolibarr', Settings::g()->default_settings );

		\eoxia\View_Util::exec( 'wpshop', 'proposals', 'list', array(
			'proposals' => $proposals,
			'doli_url'  => $dolibarr_option['dolibarr_url'],
		) );
	}

	/**
	 * Récupères la dernière ref des devis.
	 *
	 * @since 2.0.0
	 *
	 * @return string La référence.
	 */
	public function get_last_ref() {
		global $wpdb;

		$last_ref = $wpdb->get_var( "
			SELECT meta_value FROM $wpdb->postmeta AS PM
				JOIN $wpdb->posts AS P ON PM.post_id=P.ID

			WHERE PM.meta_key='_ref'
				AND P.post_type='wps-proposal'
		" );

		return $last_ref;
	}

	public function search( $s = '', $default_args = array(), $count = false ) {
		$args = array(
			'post_type'      => 'wps-proposal',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => 'any',
		);

		$args = wp_parse_args( $default_args, $args );

		if ( ! empty( $s ) ) {
			$proposals_id = get_posts( array(
				's'              => $s,
				'fields'         => 'ids',
				'post_type'      => 'wps-proposal',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			) );

			if ( empty( $proposals_id ) ) {
				if ( $count ) {
					return 0;
				} else {
					return array();
				}
			} else {
				$args['post__in'] = $proposals_id;

				if ( $count ) {
					return count( get_posts( $args ) );
				} else {
					return $proposals_id;
				}
			}
		}

		if ( $count ) {
			return count( get_posts( $args ) );
		} else {
			return get_posts( $args );
		}
	}
}

Proposals::g();
