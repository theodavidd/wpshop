<?php
/**
 * Le contenu de la modal de synchronisation.
 *
 * @todo: template pas OK
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

<?php
$date_time_wp   = new \DateTime( date( 'Y-m-d H:i:s', $date_wp ) );
$date_time_doli = new \DateTime( date( 'Y-m-d H:i:s', $date_doli ) );

?>
<h3>
	<?php
	if ( $date_wp > $date_doli ) :
		$interval = $date_time_wp->diff( $date_time_doli );
		?>
		Les données du tier sont plus récentes de<strong style="font-weight: 700">
		<span>
			<?php
			if ( $interval->format( '%a' ) !== 0 ) :
				echo $interval->format( '%a jour(s) et ' );
			endif;

			if ( $interval->format( '%h' ) !== 0 ) :
				echo $interval->format( '%hh' );
			endif;

			echo $interval->format( '%imin' );
			?>
			</strong> sur
			<strong style="font-weight: 700">WordPress</strong>.
		</span>
		<?php
	else if ( $date_wp < $date_doli ) :
		$interval = $date_time_doli->diff( $date_time_wp );
		?>
		Les données du tier sont plus récentes de<strong style="font-weight: 700">
		<span>
			<?php
			if ( $interval->format( '%a' ) !== 0 ) :
				echo $interval->format( '%a jour(s) et ' );
			endif;

			if ( $interval->format( '%h' ) !== 0 ) :
				echo $interval->format( '%hh' );
			endif;

			echo $interval->format( '%imin' );
			?>
			</strong> sur <strong style="font-weight: 700">Dolibarr</strong>.
		</span>
		<?php
	else :
		?>
		Les données du tier sont identiques sur WordPress et Dolibarr
		<?php
	endif;
	?>
</h3>
