<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Add/Edit Reservations', 'book-a-room' ); ?>
	</h2>
</div>
<?php
# Display Errors if there are any
if ( !empty( $errorMSG ) ) {
	?>
	<p>
		<h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3>
	</p>
	<?php
}
?>
<form name="form1" method="post" action="?page=bookaroom_meetings">
	<p>
		<table border="0" class="tableMain">
			<tr>
				<td>
					<?php _e( 'Name', 'book-a-room' ); ?>
				</td>
				<td>
					<?php _e( 'Value', 'book-a-room' ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Branch and Room', 'book-a-room' ); ?>
				</td>
				<td>
					<strong>
						<?php echo $branchList[$roomContList['id'][$roomContID]['branchID']]['branchDesc']; ?>
					</strong><br/>
					<em>
						<?php echo $roomContList['id'][$roomContID]['desc']; ?>
					</em>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Date', 'book-a-room' ); ?>
				</td>
				<td>
					<?php echo date( 'l, F jS, Y', $externals['startTime'] ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Requested times', 'book-a-room' ); ?>
				</td>
				<td>
					<strong>
						<?php echo date( 'g:i a', $externals['startTime'] ); ?>
					</strong> -
					<strong>
						<?php echo date( 'g:i a', $externals['endTime'] ); ?>
					</strong>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Change Reservation', 'book-a-room' ); ?>
				</td>
				<td>
					<a href="?page=bookaroom_meetings&amp;action=changeReservationSetup&amp;res_id=<?php echo $edit; ?>">
						<?php _e( 'Change Reservation (Time and/or Room)', 'book-a-room' ); ?>
					</a>
				</td>
			</tr>
			<tr>
				<td>
					<label for="eventName">
						<?php _e( 'Event / Organization name', 'book-a-room' ); ?> *
					</label>
				</td>
				<td<?php if( !empty( $errorArr[ 'errorBG'][ 'eventName'] ) ) echo ' class="error"'; ?>><input name="eventName" type="text" id="eventName" value="<?php echo $externals['eventName']; ?>" size="64" maxlength="255"/>
					</td>
			</tr>
			<tr>
				<td>
					<label for="numAtend">
						<?php _e( 'Number of attendees', 'book-a-room' ); ?> *
					</label>
				</td>
				<td<?php if( !empty( $errorArr[ 'errorBG'][ 'numAttend'] ) ) echo ' class="error"'; ?>><input name="numAttend" type="text" id="numAttend" value="<?php echo $externals['numAttend']; ?>" size="3" maxlength="3"/>
					</td>
			</tr>
			<tr>
				<td>
					<label for="notes">
						<?php _e( 'Purpose of meeting', 'book-a-room' ); ?> *
					</label>
				</td>
				<td<?php if( !empty( $errorArr[ 'errorBG'][ 'desc'] ) ) echo ' class="error"'; ?>>
					<textarea cols="60" rows="5" id="desc" name="desc" style="resize: vertical"><?php echo htmlspecialchars_decode( $externals['desc'] ); ?>
					</textarea>
					</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td><input name="startTime" type="hidden" id="startTime" value="<?php echo  $externals['startTime']; ?>"/>
					<input name="endTime" type="hidden" id="endTime" value="<?php echo  $externals['endTime']; ?>"/>
					<input name="roomID" type="hidden" id="roomID" value="<?php echo $roomContID; ?>"/>
					<input name="action" type="hidden" id="action" value="editCheck"/>
					<input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/>
				</td>
			</tr>
		</table>
</form>