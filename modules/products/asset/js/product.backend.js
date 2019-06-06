/**
 * Gestion JS des produits.
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshop.product = {};

/**
 * La méthode "init" est appelé automatiquement par la lib JS de Eo-Framework
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshop.product.init = function() {
	window.eoxiaJS.wpshop.product.event();
};

window.eoxiaJS.wpshop.product.event = function() {
	jQuery( document ).on( 'wps-change-toggle', '.stock-field .toggle', window.eoxiaJS.wpshop.product.displayBlockStock );
};

window.eoxiaJS.wpshop.product.displayBlockStock = function( event, toggleState ) {
	if ( toggleState ) {
		jQuery( '.stock-block' ).fadeIn();
	} else {
		jQuery( '.stock-block' ).fadeOut();
	}
};
