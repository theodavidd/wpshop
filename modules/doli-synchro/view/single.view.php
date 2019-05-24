<?php
/**
 * Le contenu de la modal de synchronisation.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Templates
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit; ?>

<p>Ce <?php echo esc_html( $text ); ?> n'est pas associé à un <?php echo esc_html( $text ); ?> de dolibarr.</p>

<p>Veuillez choisir le <?php echo esc_html( $text ); ?> à associer dans les éléments suivant:</p>

<p><?php echo ucfirst( esc_html( $text ) ); ?> de votre ERP</p>

<input type="hidden" name="entry_id" />
<input type="hidden" name="wp_id" value="<?php echo $wp_id; ?>" />
<input type="hidden" name="route" value="<?php echo $route; ?>" />

<input type="text" class="filter-entry" />
<ul class="select">
	<?php
	if ( ! empty( $entries ) ) :
		foreach ( $entries as $entry ) :
			?>
			<li data-id="<?php echo esc_attr( $entry->id ); ?>">#<?php echo $entry->id . ' ' . ( ! empty( $entry->name ) ? $entry->name : $entry->label ); ?></li>
			<?php
		endforeach;
	endif;
	?>
</ul>
