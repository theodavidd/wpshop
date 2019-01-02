<table class="wpeo-table">
	<thead>
		<tr>
			<td><?php echo esc_html( 'Nom du produit', 'wpshop'); ?></td>
			<td><?php echo esc_html( 'Prix Unite', 'wpshop'); ?></td>
			<td><?php echo esc_html( 'Quantite', 'wpshop'); ?></td>
			<td><?php echo esc_html( 'Prix TTC', 'wpshop'); ?></td>
			<td><?php echo esc_html( 'TVA'  ); ?> %</td>
		</tr>
	</thead>
	<tbody>

	<?php

		foreach ( $content_panier as $product ) :
	?>

			<tr id='table_listproduct' style='cursor : pointer'
			data-product-id='<?= $product->id ?>'
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'product_focus' ) ); ?>">
				<td><?php echo esc_html( $product->product_label ); ?></td>
				<td><?php echo esc_html( round( $product->subprice, 2, PHP_ROUND_HALF_ODD) ); ?> €</td>
				<td>
					<span class="action-attribute"
					data-action="update_quantity"
					data-update-quantity="-1"
					data-product-id='<?= $product->fk_product ?>'
					data-proposal-id='<?= $proposal_id ?>'
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'update_quantity' ) ); ?>">
					<i class="button-icon fas fa-minus"></i></span>

					<?php echo esc_html( $product->qty )?>

					<span class="action-attribute"
					data-action="update_quantity"
					data-update-quantity='1'
					data-product-id='<?= $product->fk_product ?>'
					data-proposal-id='<?= $proposal_id ?>'
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'update_quantity' ) ); ?>">
					<i class="button-icon fas fa-plus"></i></span>
				</td>
				<td><?php echo esc_html( round( $product->total_ttc, 2, PHP_ROUND_HALF_ODD)  ); ?> €</td>
				<td><?php echo esc_html( round( $product->tva_tx, 2, PHP_ROUND_HALF_ODD)  ); ?> %</td>
			</tr>

	<?php

		endforeach;

	?>
	</tbody>
</table>

<p> <?= esc_html( 'prix : ', 'wpshop' ) ?><?= round( $price_ttc_panier, 2, PHP_ROUND_HALF_ODD) ?><?= esc_html( '€', 'wpshop' ) ?></p>

<button
class="action-attribute" style='cursor : pointer'
data-action="achat_panier"
data-customer-name='<?= $customer_name ?>'
data-customer-id='<?= $customer_id ?>'
data-proposal-id='<?= $proposal_id ?>'
data-nonce="<?php echo esc_attr( wp_create_nonce( 'achat_panier' ) ); ?>">

<?php
	echo esc_html_e( 'Passer a l\'achat', 'wpshop' );
?>
</button>
