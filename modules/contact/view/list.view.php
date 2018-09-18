<?php
/**
 * Affichage du listing des contact dans le tableau des tiers dans le backend.
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


<ul>
	<?php
	if ( ! empty( $contacts ) ) :
		?>
		<p><?php esc_html_e( 'Associated contact', 'wpshop' ); ?></p>
		<?php
		foreach ( $contacts as $contact ) :
			\eoxia\View_Util::exec( 'wpshop', 'contact', 'item', array(
				'contact' => $contact,
			) );
		endforeach;
	endif;
	?>
</ul>
