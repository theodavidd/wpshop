
<p id="text_information"><?php esc_html_e( 'Etape 1 - Identifiez vous', 'wpshop' ); ?></p>
<div class="div_add_customer">
	<form>
		<div class="form-element">
			<span class="form-label"><?= esc_html_e( 'Nom Utilisateur', 'wpshop' ) ?></span>
			<label class="form-field-container">
				<input type="text" class="form-field" name="customer_name"/>
			</label>
		</div>
	<br>
		<span class="event-footer">
			<a class="wpeo-button button-secondary action-input"
			data-action="add_customer"
			data-parent="div_add_customer"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'add_customer' ) ); ?>"><?php esc_html_e( 'Ajouter', 'wpshop' ); ?></a>
		</span>
	</form>
</div> <!-- #div_add_customer -->

<div class="div_add_product" style='display : none'>

</div> <!-- #div_add_product -->

<div id="achat_panier" style='display : block'>

</div>

<div id="div_finish" style='display : none'>

</div> <!-- #div_finish -->

<div id="div_downloadpdf" style='display : none'>

</div> <!-- #div_finish -->
