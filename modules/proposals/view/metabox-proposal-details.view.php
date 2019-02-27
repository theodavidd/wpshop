<?php
/**
 * Affichage des données de la commande
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

<div class="wpeo-gridlayout grid-3">
	<div>
		<ul>
			<li>Customer: <a href="<?php echo admin_url( 'admin.php?page=wps-third-party&id=' . $third_party->data['id'] ); ?>" target="_blank"><?php echo $third_party->data['title']; ?></a></li>

			<?php
			if ( ! empty( $link_proposal ) ) :
				?><li><a href="<?php echo esc_url( $link_proposal ); ?>" target="_blank"><?php esc_html_e( 'View proposal', 'wpshop' ); ?></a></li><?php
			endif;
			?>
		</ul>
	</div>
	<div>
		<strong>Facturation</strong>

		<ul>
			<li>Aucune adresse de facturation</li>
		</ul>
	</div>
	<div>
		<strong>Expédition</strong>

		<ul>
			<li><?php echo ! empty( $third_party->data['title'] ) ? $third_party->data['title'] : 'N/D'; ?></li>
			<li><?php echo ! empty( $third_party->data['address'] ) ? $third_party->data['address'] : 'N/D'; ?></li>
			<li>
				<?php echo ! empty( $third_party->data['zip']) ? $third_party->data['zip'] : 'N/D'; ?>
				<?php echo ! empty( $third_party->data['town']) ? $third_party->data['town'] : 'N/D'; ?>
			</li>
			<li>
				<strong>Adresse de messagerie:</strong>
				<p><?php echo ! empty( $third_party->data['email']) ? $third_party->data['email'] : 'N/D'; ?></p>
			</li>
			<li>
				<strong>Téléphone:</strong>
				<p><?php echo ! empty( $third_party->data['phone']) ? $third_party->data['phone'] : 'N/D'; ?></p>
			</li>
		</ul>
	</div>
</div>
