/**
 * Initialise l'objet "wpshop" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
window.eoxiaJS.wpshop.doliSynchro = {};
window.eoxiaJS.wpshop.doliSynchro.completed = false;

/**
 * La méthode appelée automatiquement par la bibliothèque EoxiaJS.
 *
 * @return {void}
 *
 * @since 1.0.0
 * @version 1.0.0
 */
window.eoxiaJS.wpshop.doliSynchro.init = function() {
	jQuery( document ).on( 'modal-opened', '.modal-sync', function() {
		if ( 0 < jQuery( '.waiting-item' ).length ) {
			window.eoxiaJS.wpshop.doliSynchro.declareUpdateForm();
			window.eoxiaJS.wpshop.doliSynchro.requestUpdate();
			window.addEventListener( 'beforeunload', window.eoxiaJS.wpshop.doliSynchro.safeExit );
		}
	});
};

/**
 * Déclare les formulaires pour les mises à jour et leur fonctionnement.
 *
 * @type {void}
 */
window.eoxiaJS.wpshop.doliSynchro.declareUpdateForm = function() {
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
				if ( ! window.eoxiaJS.wpshop.doliSynchro.completed ) {
					$form.find( '.item-stats' ).html( responseText.data.progression );
					$form.find( 'input[name="done_number"]' ).val( responseText.data.doneElementNumber );
					$form.find( '.item-progression' ).css( 'width', responseText.data.progressionPerCent + '%' );

					if ( responseText.data.done ) {
						$form.closest( '.item' ).removeClass( 'waiting-item' );
						$form.closest( '.item' ).removeClass( 'in-progress-item' );
						$form.closest( '.item' ).addClass( 'done-item' );
						$form.find( '.item-stats' ).html( responseText.data.doneDescription );
					}

					window.eoxiaJS.wpshop.doliSynchro.completed = true;
					jQuery( '.general-message' ).html( responseText.data.doneDescription );
					window.removeEventListener( 'beforeunload', window.eoxiaJS.wpshop.doliSynchro.safeExit );
				}
			}

			window.eoxiaJS.wpshop.doliSynchro.requestUpdate();
		}
	});
};

/**
 * Lancement du processus de mixe à jour: On prned le premier formulaire ayant la classe 'waiting-item'
 *
 * @return {void}
 */
window.eoxiaJS.wpshop.doliSynchro.requestUpdate = function() {
	if ( ! window.eoxiaJS.wpshop.doliSynchro.completed ) {
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
window.eoxiaJS.wpshop.doliSynchro.safeExit = function( event ) {
	var confirmationMessage = taskManager.wpshopconfirmExit;
	if ( taskManager.wpshopUrlPage === event.currentTarget.adminpage ) {
		event.returnValue = confirmationMessage;
		return confirmationMessage;
	}
};

/**
 * @todo: voir processus de MAJ des MU.
 *
 * @type {Object}
 */
window.eoxiaJS.wpshop.doliSynchro.requestUpdateFunc = {
	endMethod: []
};
