
<button
	class="wpeo-button button-radius-3 action-attribute" style='cursor : pointer'
	data-customer-name = '<?= $customer_name ?>'
	data-customer-id = '<?= $customer_id ?>'
	data-proposal-id = '<?= $proposal_id ?>'
	data-action="validate_panier"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'validate_panier' ) ); ?>">

	<?= esc_html( 'Valider le panier', 'wpshop' ); ?>
</button>

<div id="panier">
 Clique sur un produit pour l'ajouter au panier
</div>
<br>

<div id='div_list_product'>

<?php
	if ( ! empty( $listProduits ) && ! isset( $listProduits->error )) :
		foreach ( $listProduits  as $product ) :
?>
	<button
	class="action-attribute" style='cursor : pointer'
    data-customer-product-id = '<?= $product->id ?>'
	data-customer-name = '<?= $customer_name ?>'
	data-customer-id = '<?= $customer_id ?>'
	data-proposal-id = '<?= $proposal_id ?>'
    data-action="choose_this_product"
    data-nonce="<?php echo esc_attr( wp_create_nonce( 'choose_product' ) ); ?>">

<?php
			echo esc_html( $product->label ) . ' | ' . esc_html( round($product->price, 2) ) . esc_html( '€', 'wpshop' );
?>
	</button>


<?php
	echo'<br><br>';
		endforeach;
	endif;
?>

</div> <!-- div_list_product -->
