<?php
/**
 * Affichage de la page mon compte
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

?>

<table class="wpeo-table">
  <thead>
    <tr>
      <th data-title="Proposal">Proposal</th>
      <th data-title="Date">Date</th>
      <th data-title="Status">Status</th>
      <th data-title="Total">Total</th>
      <th data-title="Total">Actions</th>
    </tr>
  </thead>
  <tbody>
	  <?php
	  if ( ! empty( $proposals ) ) :
	  	foreach ( $proposals as $proposal ) :
			?>
			<tr>
			  <th data-title="<?php echo esc_attr( $proposal->data['title'] ); ?>"><?php echo esc_html( $proposal->data['title'] ); ?></th>
			  <td data-title="<?php echo esc_attr( $proposal->data['datec']['rendered']['date'] ); ?>"><?php echo esc_html( $proposal->data['datec']['rendered']['date'] ); ?></td>
			  <td data-title="N/D">N/D</td>
			  <td data-title="<?php echo esc_attr( number_format( $proposal->data['total_ttc'], 2 ) ); ?>€"><?php echo esc_html( number_format( $proposal->data['total_ttc'], 2 ) ); ?>€</td>
			  <td data-title="View">
				<a target="_blank" href="<?php echo esc_attr( admin_url( 'admin-post.php?action=wps_download_proposal&proposal_id=' . $proposal->data['id'] ) ); ?>"><i class="fas fa-file-download"></i></a>
			  </td>
			</tr>
			<?php
	  	endforeach;
	  endif;
	  ?>

  </tbody>
</table>
