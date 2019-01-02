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
class Proposals_Class {

	/**
	 * Objet qui vérifie le contenu des requetes (=> Code HTTP)
	 *
	 * @var string
	 */
	protected $valide_request = 'sticky-note';

	/**
	 * Constructeur de la classe
	 *
	 * @since 2.0.0
	 * @version 2.0.0
	 *
	 * @return void
	 */
	protected function construct() {}

	protected function data_is_valid( $data ){ // A DEVELOPPER
		/*$json_request = ( json_decode( $data ) != NULL ) ? $json_request : return null;
		$body         = wp_remote_retrieve_body ( $data );
		$header       = wp_remote_retrieve_reponse_headers ( $data );
		$reponse_code = wp_remote_retrieve_response_code( $data );


		if ( is_array( $data ) ) {
			//if( $data['response'] != [] && $data['response'] != null ){
				echo '<pre>'; print_r( $data['response'] ); echo '</pre>';

				$data_value = $data['headers'];

				echo '<pre>'; print_r( $data_value ); echo '</pre>';
			//}
		}

		exit;*/
	}


	/**
	 * Supprime un proposal
	 * @param [int] $proposal_id [id du proposal à supprimer]
	 * @since 2.0.0
	 * @version 2.0.0
 	*/
	public function delete_proposals( $proposal_id ){
		$request = wp_remote_request( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id, array(
			'headers' => array( //
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'method'  => 'DELETE'
		));

	}

	/**
	 * [recupere le contenu d'un proposal]
	 * @param  [int] $proposal_id [id du proposal]
	 * @return [type]              [description]
 	*/
	public function get_proposals( $proposal_id ){

		$request = wp_remote_get( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/lines', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );

		return $this->data_is_valid( $request );
	}

	/**
	 * [post_proposals description]
	 * @param  [type] $customer_id [description]
	 * @return [type]              [description]
 	*/
	public function post_proposals( $customer_id ){

		$date    = date_create(); // Recupere la date actuel

		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode(array(
				'socid'      => $customer_id, // id du customer
				'date'       => date_timestamp_get($date), // La date est à convertir à paritr de la création d'internet // http://www.timestamp.fr/?
			))
		));
	}


	/**
	 * [get_proposals_lines description]
	 * @param  [type] $proposal_id [description]
	 * @return [type]              [description]
 	*/
	public function get_proposals_lines( $proposal_id ){

		$request = wp_remote_get( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/lines', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );

		if ( is_array( $request ) ) {
		  $header = $request[ 'headers' ];
		  $body   = $request[ 'body' ];
		  return $body;
		}
		return null;
	}

	/**
	 * [post_proposals_lines description]
	 * @param  [type] $product     [description]
	 * @param  [type] $proposal_id [description]
	 * @return [type]              [description]
	 */
	public function post_proposals_lines( $product, $proposal_id ) {

		wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/lines', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode(array(
				'desc'                    => $product->description, // Description du produit
				'fk_product'              => $product->id, // id du produit
				'product_type'            => 1,
				'qty'                     => 1, // quantité
				'tva_tx'                  => $product->tva_tx,
				'subprice'                => $product->price, // prix avant remise
				'remise_percent'          => 0//(int) 1% - 100%
				'rang'                    => 1, // 1 => Choisis un produit déja créé | 2 => Product / Service a définir en dans la note
				'total_ht'                => $product->price, // Prix du produit Hors taxe
				'total_tva'		          => $product->tva_tx, // Prix total de la tva
				'total_ttc'	              => $product->price_ttc, // Prix total TTC
				'product_label'           => $product->label,
				'multicurrency_code'      => "EUR",
				'multicurrency_subprice'  => $product->price,
				'multicurrency_total_ht'  => $product->price,
				'multicurrency_total_tva' => $product->tva_tx,
				'multicurrency_total_ttc' => $product->price_ttc,
			))
		));
	}


	/**
	* [put_proposals_lines description]
	* @param  [type] $product         [description]
	* @param  [type] $proposal_id     [description]
	* @param  [type] $proposal_lineid [description]
	* @return [type]                  [description]
	*/
	public function put_proposals_lines( $product, $proposal_id, $proposal_lineid ){

		$request = wp_remote_request( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/lines/' . $upgrade_panier, array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode(array(
				'qty' => 2,
			)),

			'method'  => 'PUT'
		));
	}

	/**
	 * [post_proposals_settodraft description]
	 * @param  [type] $proposal_id [description]
	 * @return [type]              [description]
	 */
	public function post_proposals_settodraft( $proposal_id ){

		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/settodraft', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );
	}

	/**
	 * [post_proposals_validate description]
	 * @param  [type] $proposal_id [description]
	 * @return [type]              [description]
 	*/
	public function post_proposals_validate( $proposal_id ){

		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/validate', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );
	}

	/**
	 * [post_proposals_close description]
	 * @param  [type] $proposal_id [description]
	 * @return [type]              [description]
 	*/
	public function post_proposals_close( $proposal_id ){

		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/close', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );
	}

	/**
	 * [post_proposals_validate description]
	 * @param  [type] $proposal_id [description]
	 * @return [type]              [description]
	 */
	public function post_proposals_setinvoiced( $proposal_id ){
		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/setinvoiced', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );
	}
}

Proposals_Class::g();
