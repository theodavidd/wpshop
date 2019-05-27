/**
 * Initialise l'objet "wpshop" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
window.eoxiaJS.wpshop.doliSync = {};
window.eoxiaJS.wpshop.doliSync.completed = false;

/**
 * La méthode appelée automatiquement par la bibliothèque EoxiaJS.
 *
 * @return {void}
 *
 * @since 1.0.0
 * @version 1.0.0
 */
window.eoxiaJS.wpshop.doliSync.init = function() {
	jQuery( document ).on( 'modal-opened', '.modal-sync', function() {
		if ( 0 < jQuery( '.waiting-item' ).length ) {
			window.eoxiaJS.wpshop.doliSync.declareUpdateForm();
			window.eoxiaJS.wpshop.doliSync.requestUpdate();
			window.addEventListener( 'beforeunload', window.eoxiaJS.wpshop.doliSync.safeExit );
		}
	});
};

/**
 * Déclare les formulaires pour les mises à jour et leur fonctionnement.
 *
 * @type {void}
 */
window.eoxiaJS.wpshop.doliSync.declareUpdateForm = function() {
	jQuery( '.item' ).find( 'form' ).ajaxForm({
		dataType: 'json',
		success: function( responseText, statusText, xhr, $form ) {
			if ( ! responseText.data.updateComplete ) {
				$form.find( '.item-stats' ).html( responseText.data.progression );
				$form.find( 'input[name="done_number"]' ).val( responseText.data.doneElementNumber );
				$form.find( '.item-progression' ).css( 'width', responseText.data.progressionPerCent + '%' );

				if ( responseText.data.done ) {
					$form.closest( '.item' ).removeClass( 'waiting-item' );
					$form.closest( '.item' ).removeClass( 'in-progress-item' );
					$form.closest( '.item' ).addClass( 'done-item' );
					$form.find( '.item-stats' ).html( responseText.data.doneDescription );
				}
			} else {
				if ( ! window.eoxiaJS.wpshop.doliSync.completed ) {
					$form.find( '.item-stats' ).html( responseText.data.progression );
					$form.find( 'input[name="done_number"]' ).val( responseText.data.doneElementNumber );
					$form.find( '.item-progression' ).css( 'width', responseText.data.progressionPerCent + '%' );

					if ( responseText.data.done ) {
						$form.closest( '.item' ).removeClass( 'waiting-item' );
						$form.closest( '.item' ).removeClass( 'in-progress-item' );
						$form.closest( '.item' ).addClass( 'done-item' );
						$form.find( '.item-stats' ).html( responseText.data.doneDescription );
					}

					window.eoxiaJS.wpshop.doliSync.completed = true;
					jQuery( '.general-message' ).html( responseText.data.doneDescription );
					window.removeEventListener( 'beforeunload', window.eoxiaJS.wpshop.doliSync.safeExit );
				}
			}

			window.eoxiaJS.wpshop.doliSync.requestUpdate();
		}
	});
};

/**
 * Lancement du processus de mixe à jour: On prned le premier formulaire ayant la classe 'waiting-item'
 *
 * @return {void}
 */
window.eoxiaJS.wpshop.doliSync.requestUpdate = function() {
	if ( ! window.eoxiaJS.wpshop.doliSync.completed ) {
		var currentUpdateItemID = '#' + jQuery( '.waiting-item:first' ).attr( 'id' );

		jQuery( currentUpdateItemID ).addClass( 'in-progress-item' );
		jQuery( currentUpdateItemID ).find( 'form' ).submit();

	}
};

/**
 * Vérification avant la fermeture de la page si la mise à jour est terminée.
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @param  {WindowEventHandlers} event L'évènement de la fenêtre.
 * @return {string}
 */
window.eoxiaJS.wpshop.doliSync.safeExit = function( event ) {
	var confirmationMessage = taskManager.wpshopconfirmExit;
	if ( taskManager.wpshopUrlPage === event.currentTarget.adminpage ) {
		event.returnValue = confirmationMessage;
		return confirmationMessage;
	}
};

window.eoxiaJS.wpshop.doliSync.syncEntrySuccess = function( triggeredElement, response ) {
	if ( jQuery( '.wpeo-modal.modal-active' ).length > 0 ) {
		jQuery( '.wpeo-modal.modal-active' ).addClass( 'modal-force-display' );
		jQuery( '.wpeo-modal.modal-active' ).find( '.modal-content' ).html( response.data.view );
	} else {
		jQuery( triggeredElement ).closest( '.wps-sync' ).replaceWith( response.data.view );
	}
}
