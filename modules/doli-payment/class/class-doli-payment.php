<?php
/**
 * Les fonctions principales des paiements.
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
 * Doli Payment Class.
 */
class Doli_Payment extends \eoxia\Post_Class {

	/**
	 * Model name @see ../model/*.model.php.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $model_name = '\wpshop\Doli_Payment_Model';

	/**
	 * Post type
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $type = 'wps-doli-payment';

	/**
	 * La clé principale du modèle
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'doli-payment';

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $base = 'doli-payment';

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
	protected $post_type_name = 'Doli Payment';

	/**
	 * Convertie vers l'ID de dolibarr
	 *
	 * @since 2.0.0
	 *
	 * @todo: a vérifier
	 *
	 * @param array $payment_method Méthode de paiement.
	 *
	 * @return integer l'ID de dolibarr.
	 */
	public function convert_to_doli_id( $payment_method ) {
		$payment_methods_option = get_option( 'wps_payment_methods', Payment::g()->default_options );
		$method                 = $payment_methods_option[ $payment_method ];

		$payment_types = Request_Util::get( 'setup/dictionary/payment_types' );

		if ( ! empty( $payment_types ) ) {
			foreach ( $payment_types as $element ) {
				if ( $element->code === $method['doli_type'] ) {
					return $element->id;
				}
			}
		}

		return 0;
	}

	/**
	 * Convertie vers un texte lisible.
	 *
	 * @since 2.0.0
	 *
	 * @todo: a vérifier
	 *
	 * @param  string $payment_method Méthode de paiement venant de WP.
	 *
	 * @return string                 Texte lisible.
	 */
	public function convert_to_wp( $payment_method ) {
		if ( 'CB' === $payment_method ) {
			return 'paypal';
		} elseif ( 'CHQ' === $payment_method ) {
			return 'Cheque';
		} elseif ( 'LIQ' === $payment_method ) {
			return 'Espèce';
		}
	}
}

Doli_Payment::g();
