<?php
/**
 * Les fonctions principales des synchronisations.
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
 *  Doli Synchro Class.
 */
class Doli_Synchro extends \eoxia\Singleton_Util {

	/**
	 * Tableau contenant les synchronisations à effectuer.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $sync_infos = array();

	/**
	 * Limites de synchronisation par requête.
	 *
	 * @since 2.0.0
	 *
	 * @var integer
	 */
	public $limit_entries_by_request = 50;

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {
		$this->sync_infos = array(
			'third-parties' => array(
				'title'    => __( 'Third parties', 'wpshop' ),
				'action'   => 'sync_third_parties',
				'nonce'    => 'sync_third_parties',
				'endpoint' => 'thirdparties',
			),
			'contacts'      => array(
				'title'    => __( 'Contacts', 'wpshop' ),
				'action'   => 'sync_contacts',
				'nonce'    => 'sync_contacts',
				'endpoint' => 'contacts',
			),
			'products'      => array(
				'title'    => __( 'Products', 'wpshop' ),
				'action'   => 'sync_products',
				'nonce'    => 'sync_products',
				'endpoint' => 'wpshopapi/product/get/web',
			),
			'proposals'     => array(
				'title'    => __( 'Proposals', 'wpshop' ),
				'action'   => 'sync_proposals',
				'nonce'    => 'sync_proposals',
				'endpoint' => 'proposals',
			),
			'orders'        => array(
				'title'    => __( 'Orders', 'wpshop' ),
				'action'   => 'sync_orders',
				'nonce'    => 'sync_orders',
				'endpoint' => 'orders',
			),
			'invoices'      => array(
				'title'    => __( 'Invoices', 'wpshop' ),
				'action'   => 'sync_invoices',
				'nonce'    => 'sync_invoices',
				'endpoint' => 'invoices',
			),
			'payments'      => array(
				'title'    => __( 'Payments', 'wpshop' ),
				'action'   => 'sync_payments',
				'nonce'    => 'sync_payments',
				'endpoint' => 'invoices', // Total is invoice too.
			),
		);
	}

	public function count_entries( $sync_info ) {
		if ( ! empty( $sync_info['endpoint'] ) ) {
			$args = array(
				'limit' => $this->limit_entries_by_request,
				'page'  => $sync_info['page'],
			);

			$args = implode( '&', array_map( function( $v, $k ) {
				return $k . '=' . $v;
			}, $args, array_keys( $args ) ) );

			$tmp = Request_Util::get( $sync_info['endpoint'] . '?' . $args );

			if ( $tmp ) {
				$count                      = count( $tmp );
				$sync_info['total_number'] += count( $tmp );

				if ( $count >= $this->limit_entries_by_request ) {
					$sync_info['page']++;
					$sync_info = $this->count_entries( $sync_info );
				}
			}
		}

		return $sync_info;
	}

	/**
	 * Associes et synchronises les données d'une entité.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $from     De quel coté la synchro ? WordPress ou Dolibarr.
	 * @param  integer $wp_id    L'ID de l'entitée sur WordPress.
	 * @param  integer $entry_id L'ID de l'entitée sur Dolibarr.
	 *
	 * @return void
	 */
	public function associate_and_synchronize( $from, $wp_id, $entry_id ) {
		$post_type = get_post_type( $wp_id );

		$wp_error = new \WP_Error();
		$messages = array();

		switch ( $post_type ) {
			case 'wps-third-party':
				if ( 'dolibarr' === $from ) {
					$doli_third_party = Request_Util::get( 'thirdparties/' . $entry_id );
					$wp_third_party   = Third_Party::g()->get( array( 'id' => $wp_id ), true );


					Doli_Third_Parties::g()->doli_to_wp( $doli_third_party, $wp_third_party );
					$messages[] = sprintf( __( 'Erase data for thie third party <strong>%s</strong> with the <strong>dolibarr</strong> data', 'wpshop' ), $wp_third_party->data['title'] );

					$messages = array_merge( $messages, Third_Party::g()->dessociate_contact( $wp_third_party ) );

					$doli_third_party->contacts = Request_Util::get( 'contacts?sortfield=t.rowid&sortorder=ASC&limit=-1&thirdparty_ids=' . $entry_id );

					if ( ! empty( $doli_third_party->contacts ) ) {
						foreach ( $doli_third_party->contacts as $doli_contact ) {
							// Gestion contact déjà existant
							$wp_contact = Contact::g()->get( array(
								'search' => $doli_contact->email,
							), true );

							$messages[] = sprintf( __( 'Work in progress for the contact <strong>%s</strong>', 'wpshop' ), $wp_contact->data['email'] );

							if ( ! empty( $wp_contact ) ) {
								// Est-ce qu'il a une société ?
								if ( ! empty( $wp_contact->data['third_party_id'] ) && $wp_contact->data['third_party_id'] !== $wp_third_party->data['id'] ) {
									$third_party_contact = Third_Party::g()->get( array( 'id' => $wp_contact->data['third_party_id'] ), true );
									// Erreur
									$wp_error->add( 'contact-already-exist', sprintf( __( '<strong>%s</strong> is already associated to the third party %s. <strong>This entry was not associated and not synchronized</strong>', 'wpshop' ), '<strong>' . esc_html( $wp_contact->data['email'] ) . '</strong>', '<strong>' . esc_html( $third_party_contact->data['title'] ) . '</strong>' ) );
									$messages[] = sprintf( __( 'Error for the contact <strong>%s</strong>, see warning section <i class="fas fa-exclamation-triangle"></i>', 'wpshop' ), $wp_contact->data['email'] );
								} else {
									// On le met à jour et on l'affecte à la société
									Doli_Contact::g()->doli_to_wp( $doli_contact, $wp_contact );
									$messages[] = sprintf( __( 'Erase data for the contact <strong>%s</strong> with <strong>Dolibarr</strong> data and associate it to <strong>%s</strong>', 'wpshop' ), $wp_contact->data['email'], $wp_third_party->data['title'] );

								}

							} else {
								// On le créer et on l'affecte à la société
								Doli_Contact::g()->doli_to_wp( $doli_contact, $wp_contact );
								$messages[] = sprintf( __( 'Create the contact <strong>%s</strong> with <strong>Dolibarr</strong> data and associate it to <strong>%s</strong>', 'wpshop' ), $wp_contact->data['email'], $wp_third_party->data['title'] );
							}
						}
					}
				}

				if ( 'wp' === $from ) {
					$wp_third_party   = Third_Party::g()->get( array( 'id' => $wp_id ), true );
					$doli_third_party = Request_Util::get( 'thirdparties/' . $entry_id );

					Doli_Third_Parties::g()->wp_to_doli( $wp_third_party, $doli_third_party );
				}
				break;
			case 'wps-product':
				if ( 'dolibarr' === $from ) {
					$doli_product = Request_Util::get( 'products/' . $entry_id );
					$wp_product   = Product::g()->get( array( 'id' => $wp_id ), true );

					Doli_Products::g()->doli_to_wp( $doli_product, $wp_product );

					Request_Util::post( 'wpshopapi/associate/product', array(
						'wp_product' => $wp_id,
						'fk_product' => $entry_id,
					) );
				}

				if ( 'wp' === $from ) {
					$wp_product   = Product::g()->get( array( 'id' => $wp_id ), true );
					$doli_product = Request_Util::get( 'products/' . $entry_id );

					update_post_meta( $wp_id, '_external_id', $entry_id );
					$wp_product->data['external_id'] = $entry_id;
					Doli_Products::g()->wp_to_doli( $wp_product, $doli_product );
				}
				break;
			case 'wps-order':
				if ( 'dolibarr' === $from ) {
					$doli_order = Request_Util::get( 'orders/' . $entry_id );
					$wp_order   = Doli_Order::g()->get( array( 'id' => $wp_id ), true );

					Doli_Order::g()->doli_to_wp( $doli_order, $wp_order );

					Request_Util::post( 'wpshopapi/associate/order', array(
						'wp_product' => $wp_id,
						'fk_product' => $entry_id,
					) );

					// Facture
					if ( ! empty( $doli_order->linkedObjectsIds->facture ) ) {
						foreach ( $doli_order->linkedObjectsIds->facture as $facture_id ) {
							$doli_invoice = Request_Util::get( 'invoices/' . $facture_id );

							$wp_invoice = Doli_Invoice::g()->get( array(
								'meta_key'   => '_external_id',
								'meta_value' => (int) $facture_id,
							), true );

							if ( empty( $wp_invoice ) ) {
								$wp_invoice = Doli_Invoice::g()->get( array( 'schema' => true ), true );
							}

							Doli_Invoice::g()->doli_to_wp( $doli_invoice, $wp_invoice );
						}
					}

					// Règlement
				}
				break;
			default:
				break;
		}

		update_post_meta( $wp_id, '_last_sync', current_time( 'mysql' ) );

		return array(
			'messages' => $messages,
			'wp_error' => $wp_error,
		);
	}
}

Doli_Synchro::g();
