<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Amenities', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	switch ( $action ) {
		case 'addCheck':
		case 'add':
			_e( 'Add Amenity', 'book-a-room' );
			break;
		case 'editCheck':
		case 'edit':
			_e( 'Edit Amenity', 'book-a-room' );
			break;
		default:
			wp_die( "ERROR: BAD ACTION on amenity add/edit: " . $action );
			break;
	}
	?>
</h2>
<?php
# Display Errors if there are any
if ( !empty( $amenityInfo[ 'errors' ] ) ) {
	?>
<p>
	<h3 style="color: red;"><strong><?php echo $amenityInfo['errors']; ?></strong></h3>
</p>
<?php
}
?>
<form id="form1" name="form1" method="post" action="?page=bookaroom_Settings_Amenities&amenityID=<?php echo $amenityInfo['amenityID']; ?>&action=<?php echo $action; ?>">
	<table border="0" cellspacing="1" cellpadding="2" class="tableMain">
		<tr>
			<td>
				<?php _e( 'Option', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Value', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Amenity Name', 'book-a-room' ); ?>
			</td>
			<td><input name="amenityDesc" type="text" id="amenityDesc" value="<?php echo $amenityInfo['amenityDesc']; ?>" maxlength="64"/>
			</td>
			<br/>
		</tr>
		<tr>
			<td>
				<?php _e( 'Reservable?', 'book-a-room' ); ?>
			</td>
			<td><input name="amenity_isReservable" type="checkbox" id="amenity_isReservable" value="true" <?php echo ( $amenityInfo[ 'amenity_isReservable'] ) ? ' checked="checked"' : NULL; ?> /></td>
			<br/>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/>
			</td>
		</tr>
	</table>
</form>
<p>
	<a href="?page=bookaroom_Settings_Amenities">
		<?php _e( 'Return to Amenities Home.', 'book-a-room' ); ?>
	</a>
</p>