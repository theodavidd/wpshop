<?php
/**
 * La vue principale de la page de migration des données de WPShop 1.x.x vers
 * WPShop 2.x.x
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

<div class="wrap transfert-data">
	<input type="hidden" name="number_customers" value="<?php echo esc_attr( $number_customers ); ?>" />
	<input type="hidden" name="key_query" value="1" />
	<input type="hidden" name="index" value="0" />

	<ul class="output"></ul>

	<ul class="errors"></ul>
</div>
