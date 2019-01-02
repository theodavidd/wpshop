<div id="downloadpdf">
	<button
	class="action-attribute" style='cursor : pointer'
    data-action="downloadpdf"
	data-customer-product-id='<?= $product_id ?>'
	data-customer-name='<?= $customer_name ?>'
	data-customer-id='<?= $customer_id ?>'
	data-proposal-id='<?= $proposal_id ?>'
	data-invoice-path='<?= $path_pdf ?>'
    data-nonce="<?php echo esc_attr( wp_create_nonce( 'downloadpdf' ) ); ?>">

	<?php
		echo esc_html_e( 'Télécharger le pdf', 'wpshop' );
	?>
	</button>
</div>
