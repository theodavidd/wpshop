<?php
/**
 * Résultat de l'association
 *
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

<div class="wpeo-notice notice-success">
	<ul class="notice-content">
		<?php
		if ( ! empty( $messages ) ) :
			foreach ( $messages as $message ) :
				?>
				<li><?php echo $message; ?></li>
				<?php
			endforeach;
		endif;
		?>
	</ul>
</div>

<div class="wpeo-notice notice-warning">
	<ul class="notice-content">
	<?php
	if ( ! empty( $errors->get_error_messages() ) ) :
		foreach ( $errors->get_error_messages() as $message ) :
			?>
			<li><?php echo $message; ?></li>
			<?php
		endforeach;
	endif;
	?>
</ul>
</div>

<a href="<?php echo esc_attr( $url ); ?>" class="wpeo-button button-main">
	<span>Rafraichir la page</span>
</a>
