<div class="wpeo-modal modal-active modal-force-display wps-modal-alert">
	<div class="modal-container">

		<!-- Entête -->
		<div class="modal-header">
			<h2 class="modal-title"><?php esc_html_e( 'There is a problem connecting your account to our ERP', 'wpshop' ); ?></h2>
			<div class="modal-close"><i class="fas fa-times"></i></div>
		</div>

		<!-- Corps -->
		<div class="modal-content">
			<p><?php esc_html_e( sprintf( 'Please contact %s at %s to reactivate your account with this message: %s', get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ), $response['status_code'] . ' - ' . $response['status_message'] ), 'wpshop' ); ?></p>

			<p><a href="<?php echo wp_logout_url( home_url() ); ?>"><?php esc_html_e('Logout', 'wpshop' ); ?></a></p>

		</div>

		<!-- Footer -->
		<div class="modal-footer">
			<a class="wpeo-button button-grey button-uppercase modal-close"><span>Annuler</span></a>
			<a class="wpeo-button button-main button-uppercase modal-close"><span>Valider</span></a>
		</div>
	</div>
</div>
