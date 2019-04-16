<?php
/**
 * Le contenu de la modal de synchronisation.
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

<p>Ce tier n'est pas associé à un tier de dolibarr.</p>

<p>Veuillez choisir le tier à associer dans les éléments suivant:</p>

<p>Tier de votre ERP</p>

<input type="hidden" name="entry_id" />
<input type="hidden" name="wp_id" value="<?php echo $wp_id; ?>" />
<input type="hidden" name="route" value="<?php echo $route; ?>" />

<input type="text" class="filter-entry" />
<ul class="select">
	<?php
	if ( ! empty( $third_parties ) ) :
		foreach ( $third_parties as $third_party ) :
			?>
			<li data-id="<?php echo esc_attr( $third_party->id ); ?>">#<?php echo $third_party->id . ' ' . ( ! empty( $third_party->name ) ? $third_party->name : $third_party->label ); ?></li>
			<?php
		endforeach;
	endif;
	?>
</ul>
