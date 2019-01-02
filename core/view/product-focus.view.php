
<?php if ( ! empty( $product_focus ) && ! isset( $product_focus->error )) : ?>

<div class="wpeo-modal modal-active" style='display : block'>
	<div class="modal-container">
		<p> <?= esc_html_e( 'Titre : ', 'wpshop' ) ?><?= $product_focus->label ? $product_focus->label : esc_html_e( 'Titre du produit vide', 'wpshop' ); ?>
			<br>
		<?= esc_html_e( 'Description en vente', 'wpshop' ) ?>
		<?= $product_focus->description ? $product_focus->description : esc_html_e( 'Description du produit vide', 'wpshop' ); ?>
		<br>
		<?= esc_html_e( 'Autre data', 'wpshop' ) ?>
		</p>
	</div>
</div>

<?php endif ?>
