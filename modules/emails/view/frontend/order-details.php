<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wps_email_before_order_table', $order ); ?>

<h2>
	<?php

	/* translators: %s: Order ID. */
	echo wp_kses_post( sprintf( __( '[Order #%s]', 'wpshop' ) . ' (<time datetime="%s">%s</time>)', $order->ref, date( 'Y-m-d h:i:s', $order->date_commande ), date( 'Y-m-d h:i:s', $order->date_commande ) ) );
	?>
</h2>

<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align: center;"><?php esc_html_e( 'Product', 'wpshop' ); ?></th>
				<th class="td" scope="col" style="text-align: center;"><?php esc_html_e( 'Quantity', 'wpshop' ); ?></th>
				<th class="td" scope="col" style="text-align: center;"><?php esc_html_e( 'Price TTC', 'wpshop' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if ( ! empty( $order->lines ) ) :
					foreach ( $order->lines as $line ) :
						?>
						<tr>
							<td class="td" scope="col" style="text-align: center;"><?php echo $line->libelle ?> x <?php echo $line->qty; ?></td>
							<td class="td" scope="col" style="text-align: center;"><?php echo $line->qty; ?></td>
							<td class="td" scope="col" style="text-align: center;"><?php echo number_format( $line->price * $line->qty, 2, ',', '' ); ?>€</td>
						</tr>
						<?php
					endforeach;
				endif;
			?>
		</tbody>
		<tfoot>
			<tr>
				<th class="td" scope="row" colspan="2" style="text-align: center;">Total TTC</th>
				<td class="td" style="text-align: center;"><?php echo wp_kses_post( number_format( $order->total_ttc, 2, ',', '' ) ); ?>€</td>
			</tr>
		</tfoot>
	</table>
</div>

<?php do_action( 'wps_email_after_order_table', $order ); ?>
