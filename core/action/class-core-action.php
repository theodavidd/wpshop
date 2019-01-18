<?php
/**
 * Classe gérant les actions principales de WPshop.
 *
 * Elle ajoute les styles et scripts JS principaux pour le bon fonctionnement de WPshop.
 * Elle ajoute également les textes de traductions (fichiers .mo)
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
 * Main actions of wpshop.
 */
class Core_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'callback_register_session' ), 1 );

		add_action( 'admin_enqueue_scripts', array( $this, 'callback_admin_enqueue_scripts' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'callback_enqueue_scripts' ), 11 );

		add_shortcode( 'wpshop_cart', array($this, 'shortcode_func'));// <- Faute de Jimmy
		add_shortcode( 'wpshop_exemple_propal', array($this, 'shortcode_func_wpshop_exemplepropal'));
		add_action( 'wp_ajax_add_product', array( $this, 'callback_add_product' ) );
		add_action( 'wp_ajax_product_focus', array( $this, 'callback_product_focus' ) );
		add_action( 'wp_ajax_delete_product', array( $this, 'callback_delete_product' ) );
		add_action( 'wp_ajax_add_customer', array( $this, 'callback_add_customer' ) );
		add_action( 'wp_ajax_choose_this_product', array( $this, 'callback_choose_this_product' ) );
		add_action( 'wp_ajax_validate_panier', array( $this, 'callback_validate_panier' ) );
		add_action( 'wp_ajax_update_quantity', array( $this, 'callback_update_quantity') );
		add_action( 'wp_ajax_achat_panier', array( $this, 'callback_achat_panier' ) );
		add_action( 'wp_ajax_validation_banque', array( $this, 'callback_validation_banque') );
		add_action( 'wp_ajax_create_pdf', array( $this, 'callback_create_pdf' ) );
		add_action( 'wp_ajax_downloadpdf', array( $this, 'callback_downloadpdf' ) );
	}

	public function callback_register_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Init style and script
	 *
	 * @since 2.0.0
	 */
	public function callback_admin_enqueue_scripts() {
		wp_enqueue_style( 'wpshop-backend-style', PLUGIN_WPSHOP_URL . 'core/asset/css/style.css', array(), \eoxia\Config_Util::$init['wpshop']->version );
		wp_enqueue_script( 'wpshop-backend-script', PLUGIN_WPSHOP_URL . 'core/asset/js/backend.min.js', array(), \eoxia\Config_Util::$init['wpshop']->version );
	}

	public function callback_enqueue_scripts() {
		wp_enqueue_script( 'wpshop-frontend-script', PLUGIN_WPSHOP_URL . 'core/asset/js/frontend.min.js', array(), \eoxia\Config_Util::$init['wpshop']->version );
	}

	public function callback_downloadpdf(){
		check_ajax_referer( 'downloadpdf' );

		$product_id    = ! empty( $_POST['customer_product-id'] ) ? (int) $_POST['customer_product-id'] : 0;
		$customer_name = ! empty( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';
		$customer_id   = ! empty( $_POST['customer_id'] ) ? (int) $_POST['customer_id'] : 0;
		$invoice_id    =  ! empty( $_POST['invoice_id'] ) ? (int) $_POST['invoice_id'] : 0;
		$invoice_path  =  ! empty( $_POST['invoice_path'] ) ? sanitize_text_field( $_POST['invoice_path'] ) : '';

		$response = Request_Util::get( '/documents/' . $invoice_path );

		echo '<pre> |'; print_r( $invoice_path ); echo '|</pre>';

		$zip = $data['body'];

		echo '<pre>'; print_r( $zip ); echo '</pre>';

	}

	/**
	 * Creer la facture en PDF (de l'invoice)
	 *
	 * @return [type] [description]
	 *
	 * @since 2.0.0
 	*/
	public function callback_create_pdf(){
		check_ajax_referer( 'create_pdf' );

		$product_id    = ! empty( $_POST['customer_product-id'] ) ? (int) $_POST['customer_product-id'] : 0;
		$customer_name = ! empty( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';
		$customer_id   = ! empty( $_POST['customer_id'] ) ? (int) $_POST['customer_id'] : 0;
		$proposal_id    =  ! empty( $_POST['proposal_id'] ) ? (int) $_POST['proposal_id'] : 0;
		echo '<pre>'; print_r( $proposal_id ); echo '</pre>';

		$doctype       = 'facture';
		$docid         = $proposal;
		$secretkey     = 'lOXc40lkMq85oyM4VMUb019w2H3tBFcA';

		if ( ! empty ( $doctype ) && ! empty ( $docid ) && ! empty ( $secretkey ) ){
			$path = 'http://localhost/dolibarr-8.0.3/htdocs/custom/apigendoc/restapi/restapi.php/gendoc/' . $doctype . '/' . $docid . '?key=' . $secretkey ;

			$request = wp_remote_request( $path, array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'DOLAPIKEY'    => 'hvdtb63x'
				)
			));
		}

		$data_proposal_focus = json_decode( $this->callback_data_get_proposal( $proposal_id ) );

		$path_pdf = $data_proposal_focus->last_main_doc;

		ob_start();

		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/order-exemple-downloadpdf.view.php' );

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'create_pdf',
			'view'			   => $view
		) );
	}

	public function callback_validation_banque(){
		check_ajax_referer( 'validation_banque' );

		$proposal_id   = ! empty( $_POST['proposal_id'] ) ? (int) $_POST['proposal_id'] : 0;

		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/close', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode( array(
				'status'	  => 2, // 2 = Valide
				'note_private' => 'Payement accepté - Monsieur le banquier',
				'notrigger'    => 0,
			) )
		) );
	}

	public function callback_achat_panier(){

		check_ajax_referer( 'achat_panier' );

		$proposal_id   = ! empty( $_POST['proposal_id'] ) ? (int) $_POST['proposal_id'] : 0;
		$this->callback_validate_proposal( $proposal_id );

		$content_proposal = json_decode( $this->get_proposal( $proposal_id ) );
		$price_ttc_panier = $content_proposal->total;;

		ob_start();

		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/order-achat.view.php' );

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'passer_a_l_achat',
			'view'             => $view
		) );
	}

	public function callback_validate_proposal( $proposal_id ){
		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/validate', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),
		) );
	}

	public function callback_validate_panier( ){
		check_ajax_referer( 'validate_panier' );

		$customer_name = ! empty( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';
		$customer_id   = ! empty( $_POST['customer_id'] ) ? (int) $_POST['customer_id'] : 0;
		$proposal_id   = ! empty( $_POST['proposal_id'] ) ? (int) $_POST['proposal_id'] : 0;

		$content_panier = json_decode( $this->get_content_panier( $proposal_id ) );
		$content_proposal = json_decode( $this->get_proposal( $proposal_id ) );
		$price_ttc_panier = 0;

		if( $content_panier == [] ){ // Panie vide
			echo 'faut mettre un truc dedans';
		}else{
			$price_ttc_panier = $content_proposal->total;
		}

		ob_start();

		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/order-exemple-last.view.php' );

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'choose_product',
			'view'             => $view
		) );
	}

	public function get_proposal( $proposal_id ){
		$request = Request_Util::get( '/htdocs/api/index.php/proposals/' . $proposal_id, array(
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

	public function callback_update_quantity( $proposal_id ){
		check_ajax_referer( 'update_quantity' );

		$proposal_id = ! empty( $_POST['proposal_id'] ) ? (int) $_POST['proposal_id'] : 0;
		$product_id = ! empty( $_POST['product_id'] ) ? (int) $_POST['product_id'] : 0;

		$update_quantity = ! empty( $_POST['update_quantity'] ) ? (int) $_POST['update_quantity'] : 0;
		$update_quantity = abs( $update_quantity ) == 1 ? (int) $update_quantity : 0; // si $update_quantity != 1 || -1 ? 0

		$this->update_proposal( $proposal_id, $product_id, $update_quantity);

		$content_panier  = json_decode( $this->get_content_panier( $proposal_id ) );

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'modify_quantity',
			'view'             => $view
		) );
	}

	/**
	 * Cible le produit

	 * $this->callback_create_invoice()
	 * wp_send_json_success->choose_product()
	 * *
	 * @since 2.0.0
 	*/
	public function callback_choose_this_product() {
		check_ajax_referer( 'choose_product' );

		$product_id    = ! empty( $_POST['customer_product-id'] ) ? (int)$_POST['customer_product-id'] : 0;
		$customer_name = ! empty( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';
		$customer_id   = ! empty( $_POST['customer_id'] ) ? (int) $_POST['customer_id'] : 0;
		$proposal_id   = ! empty( $_POST['proposal_id'] ) ? (int) $_POST['proposal_id'] : 0;

		$this->callback_update_panier( $proposal_id, $product_id );

		$content_panier  = json_decode( $this->get_content_panier( $proposal_id ) );

		ob_start();

		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/order-panier.view.php' );

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'update_panier',
			'view'             => $view
		) );
	}

	public function callback_update_panier( $proposal_id, $product_id ){

		$content_actual_panier = json_decode( $this->get_content_panier( $proposal_id ) );
		$quantite_produit      = 1;
		$upgrade_panier        = -1;

		foreach ($content_actual_panier as $value => $product_panier) {
			if ( $product_panier->fk_product == $product_id ){
				$quantite_produit = $product_panier->qty ++;
				$upgrade_panier = $value;
				break;
			}
		}

		if ( $upgrade_panier == -1 ){

			$product_focus = json_decode( $this->callback_data_focus_product( $product_id ) );

			$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/lines', array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'DOLAPIKEY'    => 'hvdtb63x'
				),

				'body' => json_encode(array(
					'product_type'            => 1,
					'rang'                    => 1, // 1 => Choisis un produit déja créé | 2 => Product / Service a définir en dans la note
					'fk_product'              => $product_id, // id du produit
					'qty'                     => 1, // quantité
					'tva_tx'                  => $product_focus->tva_tx,
					'subprice'                => $product_focus->price, // prix avant remise
					'total_ht'                => $product_focus->price,
					'total_tva'		          => $product_focus->tva_tx,
					'total_ttc'	              => $product_focus->price_ttc,
					'product_label'           => $product_focus->label,
					'multicurrency_code'      => "EUR",
					'multicurrency_subprice'  => $product_focus->price,
					'multicurrency_total_ht'  => $product_focus->price,
					'multicurrency_total_tva' => $product_focus->tva_tx,
					'multicurrency_total_ttc' => $product_focus->price_ttc,
					'remise_percent'          => 0//(int) 1% - 100%
				))
			));
		}
		else{
			$this->update_proposal( $proposal_id, $product_id, 1);
		}
	}

	public function update_proposal( $proposal_id, $product_id, $update_quantity ){

		$rowid = 0;
		$quantity = 0;

		$request = Request_Util::get( '/htdocs/api/index.php/proposals/' . $proposal_id . '/lines', array(
			'headers'   => array(
				'application/json',
				'DOLAPIKEY' => 'hvdtb63x'
			),

		) );

		$list_product_in_proposals = json_decode ( $request[ 'body' ] );

		foreach( $list_product_in_proposals as $product ){
			if( $product->fk_product == $product_id ){

				$rowid = $product->rowid;
				$quantity = $product->qty;
				break;
			}
		}

		$quantity += $update_quantity;

		$quantity = $quantity <= 0 ? $quantity = 1 : $quantity;

		$request = wp_remote_request( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/proposals/' . $proposal_id . '/lines/' . $rowid, array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode(array(
				'qty' => $quantity
			)),

			'method'  => 'PUT'
		));

	}

	public function get_content_panier( $proposal_id ){
		$request = Request_Util::get( '/htdocs/api/index.php/proposals/' . $proposal_id . '/lines', array(
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
	  *  Instance le shortcode [wpshop_exemple_propal]
	  *
	  * require view
	  *
	  * @since 2.0.0
  	*/
	public function shortcode_func_wpshop_exemplepropal( $atts, $content ){
		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/order-exemple.view.php' );
	}

	/**
	 * Creer un client
	 *
	 * $this->create_random_client()
	 * wp_send_json_success->add_customer()
	 *
	 * @since 2.0.0
 	*/
	public function callback_add_customer(){
		check_ajax_referer( 'add_customer' );

		$customer_name = ! empty( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';

		$customer_id   = $this->create_random_client( $customer_name );
		$proposal_id   = $this->create_proposal( $customer_id );

		$listProduits  = json_decode( $this->callback_data_list_product() );

		ob_start();
		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/order-exemple-next.view.php' );

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'add_customer',
			'view'             => $view
		) );
	}

	/**
	 * Post à la base de donnée les informations pour la création d'un utilsisateur
	 *
	 * @param  [string] $customer_name [nom du client a créé]
	 *
	 * wp_remote_post()
	 *
	 * @return [int] [customer_id]
	 *
	 * @since 2.0.0
 	*/
	public function create_random_client( $customer_name ){
		$data = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/thirdparties', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode( array(
				'client'	  => 1, // 1 = Customer
				'fournisseur' => 0, // 0 = Vendor : NO
				'name' 		  => $customer_name,
				//'code_client' => '-1' // generer automatiquement le code client
			) )
		));

		if ( is_array( $request ) ) {
		  $header = $request[ 'headers' ];
		  $body   = $request[ 'body' ];
		  return $body;
	  }else{
		  return 'error';
	  }
	}

	/**
	 * wp_remote_post()
	 *
	 * @return [int] [proposal_id]
	 *
	 * @since 2.0.0
	*/
	public function create_proposal( $customer_id ){
		$date = date_create();

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


		if ( is_array( $request ) ) {
			$header = $request[ 'headers' ];
			$body   = $request[ 'body' ];
			return $body;
		}else{
			return 'error';
		}
	}

// ----------- PAGE A ---------------
	/**
	 * Instancie le shortcode [wpshop_cart]
	 *
	 * $this->callback_data_list_product()
	 *
	 * @since 2.0.0
 	*/
	public function shortcode_func( $atts, $content ){

		$total_price      	   = 0.00;
		$totalFraisDeLivraison = 0.00;
		$total_price_ttc	   = 0.00;
		$listProduits 		   = [];

		$totalTVA 			   = [];
		$totalTVA[7]           = 0;
		$totalTVA[20]          = 0;

		$listProduits  = json_decode( $this->callback_data_list_product() );

		if ( ! empty( $listProduits ) && ! isset( $listProduits->error )){

			foreach ( $listProduits as $produit ){
					$total_price 				+= $produit->price;
					$total_price_ttc 			+= $produit->price_ttc;

					if ( $produit->tva_tx == 7 || $produit->tva_tx == 20 ){
						$totalTVA[$produit->tva_tx] += $produit->price_ttc - $produit->price;
					}
			}
		}
		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/main.view.php' );
	}

	/**
	 * Renvois la liste des produits
	 *
	 * Request_Util::get()
	 *
	 * @return [json_encode] [return la liste des produits]
	 *
	 * @since 2.0.0
 	*/
	public function callback_data_list_product( ){
		$request = Request_Util::get( '/htdocs/api/index.php/products?sortfield=t.ref&sortorder=ASC&limit=100', array(
			'headers' => array(
				'Content-type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x',
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
	 * Ajoute un produit
	 *
	 * wp_remote_post()
	 * wp_send_json_success->show_popup()
 	 *
 	 * @since 2.0.0
 	*/
	public function callback_add_product( ){

		$title       = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description = ! empty( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
		$price       = ! empty( $_POST['price'] ) ? (int) $_POST['price'] : 0;
		$quantity 	 = ! empty( $_POST['quantity'] ) ? (int) $_POST['quantity'] : 0;

		$request = wp_remote_post( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/products', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'body' => json_encode(array(
				'ref'		   => rand(1,50000),
				'label' 	   => $title,
				'description'  => $description,
				'type' 		   => 0,
				'price'	 	   => $price,
				'price_ttc'	   => 10,
				'stock_reel'   => $quantity,
				'stock_theorique' => $quantity,
				'localtax1_tx' => 0,
				"status" 	   => '1',
				"status_buy"   => '1'
			))
		));

		ob_start();

		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/product-table.view.php' ) ;

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'show_popup',
			'view'             => $view
		) );


	}

	/**
	 * Cible un produit
	 *
	 * $this->callback_data_focus_product()
	 * wp_send_json_success->product_focus()
	 *
	 * @since 2.0.0
 	*/
	public function callback_product_focus( ){

		check_ajax_referer( 'product_focus' );

		$product_id    = ! empty( $_POST['product_id'] ) ? (int)$_POST['product_id'] : 0;
		$product_focus = json_decode( $this->callback_data_focus_product( $product_id ));
		$view 		   = '';

		ob_start();

		require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/product-focus.view.php' ) ;

		$view = ob_get_clean();

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => 'core',
			'callback_success' => 'product_focus',
			'view'             => $view,
		) );
	}

	/**
	 * Get dans la base de donnée, le produit ciblé
	 *
	 * Request_Util::get()
	 *
	 * @param  [int] $product_id [l'id du produit ciblé]
	 * @return [json_encode] [return les informtions sur le produit ciblé]
	 *
	 * @since 2.0.0
 	*/
	public function callback_data_focus_product( $product_id ){
		$request = Request_Util::get( '/htdocs/api/index.php/products/' . $product_id, array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			)
		));

		if ( is_array( $request ) ) {
		  $header = $request[ 'headers' ];
		  $body   = $request[ 'body' ];
		  return $body;
		}
		return null;
	}

	/**
	 * Supprime un produit ciblé
	 *
	 * wp_remote_request()
	 * Method => 'DELETE'
	 *
	 * @since 2.0.0
 	*/
	public function callback_delete_product(  ){

		$request = wp_remote_request( 'http://localhost/dolibarr-8.0.3/htdocs/api/index.php/products/' . $_POST['product_id'], array(
			'headers' => array( //
				'Content-Type' => 'application/json',
				'DOLAPIKEY'    => 'hvdtb63x'
			),

			'method'  => 'DELETE'
		));

		//echo '<pre>'; print_r( $request ); echo '</pre>';

		wp_send_json_success( array(
			'namespace'        => 'wpshop',
			'module'           => '',
			'callback_success' => '',
		) );

	} // no return
}

new Core_Action();

/**
 * Corentin
 * 2.0.0
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
