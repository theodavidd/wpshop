/**
 * Gestion JS du tunnel de vente.
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshopFrontend.cart = {};

/**
 * La méthode "init" est appelé automatiquement par la lib JS de Eo-Framework
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshopFrontend.cart.init = function() {
	// window.eoxiaJS.wpshopFrontend.cart.event();
};

window.eoxiaJS.wpshopFrontend.cart.addedToCart = function ( triggeredElement, response ) {
	if ( ! triggeredElement.next().hasClass('view-cart') ) {
		triggeredElement.after( response.data.view );
	}

	var qty = jQuery( '.cart-button .qty-value' ).text();

	if ( ! qty ) {
		qty = 1;
	} else {
		qty++;
	}

	jQuery( '.cart-button .qty' ).html( '(<span class="qty-value">' + qty + '</span>)' );
};

window.eoxiaJS.wpshopFrontend.cart.deletedProdutFromCart = function ( triggeredElement, response ) {
	jQuery( '.primary-content .site-width' ).html( response.data.view );
};
