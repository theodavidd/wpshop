<input type='text' placeholder="Identifiant Bancaire">
<input type='text' placeholder="Cryptogramme">

<p><?= esc_attr('Prix : ', 'wpshop') ?> <?= esc_html( round( $price_ttc_panier, 2, PHP_ROUND_HALF_ODD)  );?><?= esc_html( '€', 'wpshop') ?></p>
<button
class="action-attribute" style='cursor : pointer'
data-action="validation_banque"
data-proposal-id='<?= $proposal_id ?>'
data-nonce="<?php echo esc_attr( wp_create_nonce( 'validation_banque' ) ); ?>">
 Acheter </button>
