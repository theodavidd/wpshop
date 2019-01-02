<?php

	if ( ! empty( $content_panier ) && ! isset( $content_panier->error )) :
		echo '<div class="wpeo-gridlayout grid-3">';
		foreach ( $content_panier as $product_panier ) :
			?>

			<div><?= $product_panier->product_label ?> | <?= $product_panier->qty ?></div>

		<?php endforeach;
		echo '</div>';
	else:?>


	<p> Panier vide :( </p>

	<?php
	endif;
?>
