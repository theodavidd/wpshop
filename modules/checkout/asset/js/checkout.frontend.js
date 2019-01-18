/**
 * Gestion JS du tunnel de vente.
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshopFrontend.checkout = {};

/**
 * La méthode "init" est appelé automatiquement par la lib JS de Eo-Framework
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshopFrontend.checkout.init = function() {};

window.eoxiaJS.wpshopFrontend.checkout.checkoutErrors = function( triggeredElement, response ) {
	if ( 0 === jQuery( 'form.wps-checkout ul.error.notice' ).length ) {
		jQuery( 'form.wps-checkout' ).prepend( response.data.template );
	} else {
		jQuery( 'form.wps-checkout ul.error.notice' ).replaceWith( response.data.template );
	}

	for ( var key in response.data.errors.error_data ) {
		jQuery( 'form.wps-checkout .' + response.data.errors.error_data[ key ].input_class ).addClass( 'form-element-error' );
	}
};
