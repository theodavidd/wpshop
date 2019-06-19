<?php
/**
 * La page général
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

defined( 'ABSPATH' ) || exit;  ?>


<div class="wpeo-gridlayout grid-2">
	<form action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" method="POST" class="import-third-party" >
		<h3 style="text-align: center;"><?php esc_html_e( 'Import third party CSV', 'wpshop' ); ?></h3>
		<input type="hidden" name="action" value="import_third_party" />
		<?php wp_nonce_field( 'import_third_party' ); ?>

		<div>
			<span class="import-explanation" ><?php esc_attr_e( '', 'wpshop' ); ?></span>
		</div>
		<div>
			<progress value="0" max="100">0%</progress>
		</div>

		<div>
			<input type="file" name="file" id="file" />
			<label class="wpeo-button button-primary" ><?php esc_html_e( 'Importer', 'wpshop' ); ?></label><br />
			<span class="import-detail"></span>
		</div>
	</form>
</div>
