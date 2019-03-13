/**
 * Gestion JS du tunnel de vente.
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshopFrontend.myAccount = {};

/**
 * La méthode "init" est appelé automatiquement par la lib JS de Eo-Framework
 *
 * @since 2.0.0
 */
window.eoxiaJS.wpshopFrontend.myAccount.init = function() {};

window.eoxiaJS.wpshopFrontend.myAccount.reorderSuccess = function( triggeredElement, response ) {
	document.location.href = response.data.redirect_url;
};
