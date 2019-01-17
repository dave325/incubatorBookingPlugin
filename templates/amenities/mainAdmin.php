<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Amenities', 'book-a-room' ); ?>
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
<h2>
	<?php _e( 'New amenity', 'book-a-room' ); ?>
</h2>
<p>
	<a href="?page=bookaroom_Settings_Amenities&amp;action=add">
		<?php _e( 'Create a new amenity.', 'book-a-room' ); ?>
	</a>
</p>
<p>&nbsp;</p>
<h2>
	<?php _e( 'Current Amenities', 'book-a-room' ); ?>
</h2>
<?php
if ( count( $amenityList ) == 0 ) {
	# if none, replace with none line
	?>
<p>
	<?php _e( 'You haven\'t created any amenities.' ); ?>
</p>
<?php
} else {
	# if some, replace with some line and loop to display each one
	?>
	<table class="tableMain">
		<tr>
			<td>
				<?php _e( 'Amenity Name', 'book-a-room' ); ?>
			</td>
			<td>
				<?php /* translators: This is a checkbox - is this item reservable? */ _e( 'Reservable?', 'book-a-room' ); ?>
			</td>
			<td width="150">
				<?php  /* translators: These are available actions, like edit and delete */ _e( 'Actions', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		$count = 0;

		foreach ( $amenityList[ 'id' ] as $key => $val ) {
			?>
		<tr>
			<td>
				<?php echo $val['amenityDesc']; ?>
			</td>
			<td>
				<?php echo ( $val['amenity_isReservable'] ) ? 'Yes' : 'No'; ?>
			</td>
			<td>
				<a href="?page=bookaroom_Settings_Amenities&amp;action=edit&amp;amenityID=<?php echo $key; ?>">
					<?php _e( 'Edit', 'book-a-room' ); ?>
				</a> |
				<a href="?page=bookaroom_Settings_Amenities&amp;action=delete&amp;amenityID=<?php echo $key; ?>">
					<?php _e( 'Delete', 'book-a-room' ); ?>
				</a>
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}
?>
<p><a href="?page=bookaroom_Settings">Return to Meeting Room Administration Home.</a> </p>