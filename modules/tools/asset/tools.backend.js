/**
 * Initialise l'objet "wpshop" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
window.eoxiaJS.wpshop.tools = {};

/**
 * La méthode appelée automatiquement par la bibliothèque EoxiaJS.
 *
 * @return {void}
 *
 * @since 1.0.0
 * @version 1.0.0
 */
window.eoxiaJS.wpshop.tools.init = function() {
	jQuery( document ).on( 'change', '.import-third-party input[type="file"]', window.eoxiaJS.wpshop.tools.import )
};

window.eoxiaJS.wpshop.tools.import = function( event ) {
	var data = new FormData();

	event.preventDefault();

	data.append( 'file', jQuery( this )[0].files[0] );
	data.append( 'action', 'import_third_party' );
	data.append( '_wpnonce', jQuery( this ).closest( 'form' ).find( 'input[name="_wpnonce"]' ).val() );
	data.append( 'index_element', 0 );

	window.eoxiaJS.wpshop.tools.requestImport( data );
}

/**
 * Lances la requête pour importer un modèle de donnée.
 * Modifie la barre de progression.
 *
 * @since 6.1.5.5
 *
 * @param  {object} data Les données pour la requête
 * @return {void}
 */
window.eoxiaJS.wpshop.tools.requestImport = function( data ) {
	jQuery.ajax( {
		url: ajaxurl,
		data: data,
		processData: false,
		contentType: false,
		type: 'POST',
		beforeSend: function() {
			window.eoxiaJS.loader.display(  jQuery( '.import-third-party .wpeo-button' ) );
			jQuery( '.import-details' ).html( 'In progress' );
		},
		success: function( response ) {
			var data = new FormData();

			if ( response.success ) {
				jQuery( '.import-third-party progress' ).attr( 'max', response.data.count_element );
				jQuery( '.import-third-party progress' ).val( ( response.data.index_element / response.data.count_element ) * response.data.count_element );

				if ( ! response.data.end ) {
					data.append( 'action', 'import_third_party' );
					data.append( '_wpnonce', jQuery( '.import-third-party' ).find( 'input[name="_wpnonce"]' ).val() );
					data.append( 'path_to_json', response.data.path_to_json );
					data.append( 'index_element', response.data.index_element );
					data.append( 'count_element', response.data.count_element );
					jQuery( '.import-detail' ).html( 'Progress' );
					window.eoxiaJS.wpshop.tools.requestImport( data );
				} else {
					jQuery( '.import-detail' ).html( 'Importation terminé' );
					window.eoxiaJS.loader.remove(  jQuery( '.import-third-party .wpeo-button' ) );

				}
			} else {
			}
		}
	} );
};
