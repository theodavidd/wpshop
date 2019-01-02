<?php

	if ( ! empty( $listProduits ) && ! isset( $listProduits->error )) :

		foreach ( $listProduits as $product ) :
			if ( $product->label == "frais de livraison"){
				continue;
			}

			?>

	<tr id='table_listproduct' class="action-attribute" style='cursor : pointer'
	data-product-id='<?= $product->id ?>'
	data-action="product_focus"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'product_focus' ) ); ?>">
		<td><?php echo esc_html( $product->label ); ?></td>
		<td><?php echo esc_html( round( $product->price, 2, PHP_ROUND_HALF_ODD) ); ?> €</td>
		<td><?php echo esc_html( $product->stock_reel ); ?></td>
		<td><?php echo esc_html( $product->stock_theorique ); ?></td>
		<td><?php echo esc_html( round( $product->price_ttc, 2, PHP_ROUND_HALF_ODD)  ); ?> €</td>
		<td><?php echo esc_html( round( $product->tva_tx, 2, PHP_ROUND_HALF_ODD)  ); ?> %</td>
		<td style='display : block; text-align : center'>
			<span class="event-footer">
				<a class="wpeo-button button-square-40 button-rounded action-attribute"
				data-action="delete_product"
				data-product-id='<?= $product->id ?>'
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_product' ) ); ?>">
					<i class="button-icon fas fa-times"></i>
				</a>
			</span>
		</td>
	</tr>

	<?php

		endforeach;
	endif;

?>
