
 <?php if ( ! empty( $listProduits ) && ! isset( $listProduits->error )) : ?>

	<table class="wpeo-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Name', 'wpshop' ); ?></th>
				<th><?php esc_html_e( 'Price', 'wpshop' ); ?></th>
				<th><?php esc_html_e( 'Quantite reel', 'wpshop' ); ?></th>
				<th><?php esc_html_e( 'Quantite theorique', 'wpshop' ); ?></th>
				<th><?php esc_html_e( 'Prix ttc', 'wpshop' ); ?></th>
				<th><?php esc_html_e( 'Tva tx', 'wpshop' ); ?></th>
				<th><?php esc_html_e( 'Supprimer', 'wpshop' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				require_once( \eoxia\Config_Util::$init['wpshop']->core->path . '/view/product-table.view.php' ) ;
			?>
		</tbody>
	</table>

	<p> <?= esc_html_e( 'Tva 7% : ', 'wpshop' ); ?> <?= $totalTVA[7] | 0 ?><?= esc_html_e( '€', 'wpshop' ); ?></p>
	<p> <?= esc_html_e( 'Tva 20% : ', 'wpshop' ); ?><?= $totalTVA[20] | 0 ?><?= esc_html_e( '€', 'wpshop' ); ?></p>
	<p> <?= esc_html_e( 'Prix TTC : ', 'wpshop' ); ?><?= $total_price | 0 ?><?= esc_html_e( '€', 'wpshop' ); ?></p>
	<p> <?= esc_html_e( 'Prix Total : ', 'wpshop' ); ?><?= $total_price_ttc | 0 ?><?= esc_html_e( '€', 'wpshop' ); ?></p>
	<?php /*<p> <?= esc_html_e( 'Frais de livraison : ', 'wpshop' ); ?><?= $totalFraisDeLivraison | 0 ?><?= esc_html_e( '€', 'wpshop' ); ?></p>*/ ?>

	<?php
		else :
	?>
	<div>
		<?= esc_html_e( 'Aucun produit dans le panier', 'wpshop' ); ?>
	</div>

	<?php
		endif;

	?>
	<div class="parent-container">

		<!-- Le bouton déclenchant louverture de la popup -->
		<a class="wpeo-button button-radius-3 button-main wpeo-modal-event"
			data-parent="parent-container"
			data-target="wpeo-modal"><i class="button-icon fal fa-hand-pointer"></i> <span>
			<?=	esc_html_e( 'Ajouter', 'wpshop' ) ?> </span></a>

		<!-- Structure -->
		<div class="wpeo-modal">

			<div class="modal-container">
				<h2 style="text-align : center"> <?php esc_html_e( 'Ajouter un produit', 'wpshop' ); ?></h2>

				<form class="wpeo-grid grid-2 grid-padding-1 wpeo-form" action="" method="" style="margin-top : -30px">
					<div class="form-element">
						<span class="form-label"><?= esc_html_e( 'Nom', 'wpshop' ) ?></span>
						<label class="form-field-container">
							<input type="text" class="form-field" name="title"/>
						</label>
					</div>

					<div class="form-element">
						<span class="form-label"><?= esc_html_e( 'Description', 'wpshop' ) ?></span>
						<label class="form-field-container">
							<textarea class="form-field" rows="1" name="description"></textarea>
						</label>
					</div>

					<div>
						<div class="form-element">
							<span class="form-label"><?= esc_html_e( 'Prix ( € )', 'wpshop' ) ?></span>
							<label class="form-field-container">
								<input type="number" class="form-field" name="price" placeholder="00,00"/>
							</label>
						</div>
					</div>
					<div>
						<div class="form-element">
							<span class="form-label"><?= esc_html_e( 'Quantité', 'wpshop' ) ?></span>
							<label class="form-field-container">
								<input type="number" class="form-field" name="quantity" placeholder="0"/>
							</label>
						</div>
					</div>
					<div>
						<div class="form-element">
							<span class="form-label"><?= esc_html_e( 'Taxe ( % )', 'wpshop' ) ?></span>
							<label class="form-field-container">
								<input type="number" class="form-field" name="quantity" placeholder="0"/>
							</label>
						</div>
					</div>

					<div>
						<div class="form-element form-align-horizontal">
							<span class="form-label"><?= esc_html_e( 'Mettre en vente', 'wpshop' ) ?></span>
							<label class="form-field-container"  style="text-align : center">
								<div class="form-field-inline">
									<input type="radio" id="radio1" class="form-field" name="type" checked value="radio1">
									<label for="radio1"><?= esc_html_e( 'Activé', 'wpshop' ) ?></label>
								</div>
								<div class="form-field-inline">
									<input type="radio" id="radio2" class="form-field" name="type" value="radio2">
									<label for="radio2"><?= esc_html_e( 'Désactivé', 'wpshop' ) ?></label>
								</div>
							</label>
						</div>
					</div>

					<span class="event-footer">
						<a class="wpeo-button button-secondary action-input"
						data-action="add_product"
						data-parent="wpeo-form"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'add_product' ) ); ?>"><?php esc_html_e( 'Creer', 'wpshop' ); ?></a>
					</span>
				</form>
				<div id="success_add_product" style='display : none; top : -50px'>
					<h2><?= esc_html_e( 'Produit ajouté avec succés', 'wpshop' ); ?></h2>
				</div>
			</div>
		</div>
	</div>

	<div id="product_focus">

	</div>
