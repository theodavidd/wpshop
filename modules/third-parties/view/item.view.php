<?php
/**
 * Affichage d'un tier dans le listing de la page des tiers (wps-third-party)
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

<tr>
	<td><input type="checkbox" /></td>
	<td><?php echo esc_html( $third_party->data['id'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['external_id'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['title'] ); ?></td>
	<td><?php Contact_Class::g()->display( $third_party ); ?></td>
	<td><?php echo esc_html( $third_party->data['forme_juridique'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['code_fournisseur'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['address'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['zip'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['country'] ); ?></td>
	<td><?php echo esc_html( $third_party->data['state'] ); ?></td>
</tr>
