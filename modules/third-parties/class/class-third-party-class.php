<?php
/**
 * Les fonctions principales des tiers.
 *
 * Le controlleur du modèle Third_Party_Model.
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
class Third_Party_Class extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Third_Party_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-third-party';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'third-party';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'third-party';

	/**
	 * La taxonomy lié à ce post type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $attached_taxonomy_type = '';

	/**
	 * Récupères la liste des produits et appel la vue "list" du module "Product".
	 *
	 * @since 2.0.0
	 */
	public function display() {
		$third_parties = $this->get();

		\eoxia\View_Util::exec( 'wpshop', 'third-parties', 'list', array(
			'third_parties' => $third_parties,
		) );
	}

	public function save( $data ) {
		$third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );

		$third_party->data['title']            = $data['third_party']['title'];
		$third_party->data['address']          = $data['third_party']['address'];
		$third_party->data['zip']              = $data['third_party']['zip'];
		$third_party->data['state']            = $data['third_party']['state'];
		$third_party->data['country']          = $data['third_party']['country'];


		if ( is_user_logged_in() ) {
			return apply_filters( 'wps_update_third_party', $data, $third_party->data );
		}

		return apply_filters( 'wps_save_third_party', $data, $third_party->data );
	}

	public function get_id_or_sync( $external_id )
	{
		// Set author.
		$third_party = Third_Party_Class::g()->get( array(
			'meta_key'   => '_external_id',
			'meta_value' => $external_id,
		), true );

		if ( empty( $third_party ) ) {
			$third_party_data = Request_Util::get( 'thirdparties/' . $doli_invoice->socid );
			$third_party      = Third_Party_Class::g()->sync( $external_id, $third_party_data );

			$third_party->data['contact_ids'] = Contact_Class::g()->synchro_contact( $third_party );
			Third_Party_Class::g()->update( $third_party->data );
		}

		return $third_party->data['id'];
	}

	public function sync( $wp_id, $external_id, $data ) {
		$third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );

		if ( ! empty( $wp_id ) ) {
			$third_party->data['id'] = $wp_id;
		}

		$third_party->data['external_id']      = (int) $external_id;
		$third_party->data['title']            = $data->name;
		$third_party->data['forme_juridique']  = $data->forme_juridique;
		$third_party->data['code_fournisseur'] = $data->code_fournisseur;
		$third_party->data['address']          = $data->address;
		$third_party->data['zip']              = $data->zip;
		$third_party->data['state']            = $data->state;
		$third_party->data['country']          = $data->country;
		$third_party->data['email']            = $data->email;

		return Third_Party_Class::g()->update( $third_party->data );
	}
}

Third_Party_Class::g();
