<?php
/**
 * Countries
 *
 * Returns an array of countries and codes.
 *
 * @package WPshop
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 *	Retournes un nombre utilisable pour gérer les prix.
 *
 *	@param	float	$amount			Amount to convert/clean
 *	@param	string	$rounding		''=No rounding
 * 									'MU'=Round to Max unit price (MAIN_MAX_DECIMALS_UNIT)
 *									'MT'=Round to Max for totals with Tax (MAIN_MAX_DECIMALS_TOT)
 *									'MS'=Round to Max for stock quantity (MAIN_MAX_DECIMALS_STOCK)
 *									Numeric = Nb of digits for rounding
 * 	@param	int		$alreadysqlnb	Put 1 if you know that content is already universal format number
 *	@return	string					Amount with universal numeric format (Example: '99.99999') or unchanged text if conversion fails. If amount is null or '', it returns ''.
 */
function price2num( $amount, $alreadysqlnb = 0 ) {
	// Round PHP function does not allow number like '1,234.56' nor '1.234,56' nor '1 234,56'
	// Numbers must be '1234.56'
	// Decimal delimiter for PHP and database SQL requests must be '.'
	$dec      = ',';
	$thousand = ' ';

	if ( $alreadysqlnb != 1 )	// If not a PHP number or unknown, we change format
	{
		//print 'PP'.$amount.' - '.$dec.' - '.$thousand.' - '.intval($amount).'<br>';

		// Convert amount to format with dolibarr dec and thousand (this is because PHP convert a number
		// to format defined by LC_NUMERIC after a calculation and we want source format to be like defined by Dolibarr setup.
		if ( is_numeric( $amount ) )
		{
			// We put in temps value of decimal ("0.00001"). Works with 0 and 2.0E-5 and 9999.10
			$temps   = sprintf( "%0.10F", $amount - intval( $amount ) );	// temps=0.0000000000 or 0.0000200000 or 9999.1000000000
			$temps   = preg_replace( '/([\.1-9])0+$/', '\\1', $temps ); // temps=0. or 0.00002 or 9999.1
			$nbofdec = max( 0, strlen( $temps ) -2 );	// -2 to remove "0."
			$amount  = number_format( $amount, $nbofdec, $dec, $thousand);
		}

		// Now make replace (the main goal of function)
		if ( $thousand != ',' && $thousand != '.' ) {
			$amount=str_replace( ',', '.', $amount );// To accept 2 notations for french users
		}

		$amount=str_replace( ' ', '', $amount );		// To avoid spaces
		$amount=str_replace( $thousand, '', $amount );	// Replace of thousand before replace of dec to avoid pb if thousand is .
		$amount=str_replace( $dec, '.', $amount );
	}

	return $amount;
}
