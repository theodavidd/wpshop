<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $order->mode_reglement == 'Cheque' ) :
	echo stripslashes( nl2br( $payment_methods['cheque']['description'] ) );
endif;

if ( $order->mode_reglement == 'payment_in_shop' ) :
	echo stripslashes( nl2br( $payment_methods['cheque']['description'] ) );
endif;
?>
