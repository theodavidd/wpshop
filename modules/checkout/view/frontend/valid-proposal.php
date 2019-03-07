<?php
/**
 * Le formulaire pour créer son adresse de livraison
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<?php esc_html_e( 'Thanks, your quotation has bee received', 'wpshop' ); ?>

<ul>
	<li><?php esc_html_e( 'Quotation number', 'wpshop' ); ?> : <strong><?php echo esc_html( $proposal->data['title'] ); ?></strong></li>
	<li><?php esc_html_e( 'Date', 'wpshop' ); ?> : <strong><?php echo esc_html( $proposal->data['datec']['rendered']['date'] ); ?></strong></li>
	<li><?php esc_html_e( 'Total', 'wpshop' ); ?> : <strong><?php echo esc_html( number_format( $proposal->data['total_ttc'], 2 ) ); ?>€</strong></li>
</ul>

<h2><?php esc_html_e( 'Quotation detail', 'wpshop' ); ?></h2>

<table class="wpeo-table">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'wpshop' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'wpshop' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if ( ! empty( $proposal->data['lines'] ) ) :
				foreach ( $proposal->data['lines'] as $line ) :
					?>
					<tr>
						<td class="product-name"><?php echo $line['libelle'] ?> x <?php echo $line['qty']; ?></td>
						<td class="product-total"><?php echo number_format( $line['price'] * $line['qty'], 2 ); ?>€</td>
					</tr>
					<?php
				endforeach;
			endif;
		?>
		<tr>
			<td><?php esc_html_e( 'Total', 'wpshop' ); ?></td>
			<td><?php echo number_format( $proposal->data['total_ttc'], 2 ); ?>€</td>
		</tr>
	</tbody>
</table>

<a href="<?php echo Pages_Class::g()->get_account_link(); ?>proposals/" class="wpeo-button button-main">
	<span><?php esc_html_e( 'See my quotations', 'wpshop' ); ?></span>
</a>
