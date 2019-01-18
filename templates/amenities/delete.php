<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Amenities', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Delete amenity', 'book-a-room' ); ?>
</h2>
<table border="0" cellspacing="1" cellpadding="2" class="tableMain">
  <tr>
    <td><strong><?php _e( 'Amenity Name', 'book-a-room' ); ?></strong></td>
    <td><strong><?php _e( 'Reservable', 'book-a-room' ); ?></strong></td>
  </tr>
  <tr>
    <td><?php echo $amenityInfo['amenityDesc']; ?></td>
    <td><?php echo ( $amenityInfo['amenity_isReservable'] ) ? 'Yes' : 'No'; ?></td>
  </tr>
</table>
<p><?php _e( 'Deleting an amenity is permanent and cannot be undone.', 'book-a-room' ); ?></p>
<p><a class="errorText" href="?page=bookaroom_Settings_Amenities&amp;amenityID=<?php echo $amenityInfo['amenityID'] ?>&amp;action=deleteCheck">
<?php _e( 'Click here to permanantly delete this amenity.', 'book-a-room' ); ?></a></p><br />
<p><a href="?page=bookaroom_Settings_Amenities"><?php _e( 'Return to Amenities Home.', 'book-a-room' ); ?></a></p>