<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Delete Registrations', 'book-a-room' ); ?>
</h2>
<h3>
	<?php _e( 'Registration deleted.', 'book-a-room' ); ?>
</h3>
<p>
	<?php _e( 'The next person on the waiting list has not entered an email address, so we cannot automatically contect them.', 'book-a-room' ); ?>
</p>
<p>
	<?php _e( 'Please contact this person at the following number:', 'book-a-room' ); ?>
</p>
<table class="tableMain">
	<tr>
		<td colspan="2">
			<?php _e( 'Next registrant information', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php _e( 'Name', 'book-a-room' ); ?>
		</td>
		<td>
			<?php echo $regInfo['reg_fullName']; ?>
		</td>
	</tr>

		<tr>
			<td>
				<?php _e( 'Phone number', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $regInfo['reg_phone']; ?>
			</td>
		
	</tr>
	<tr>
		<td>
			<?php _e( 'Notes', 'book-a-room' ); ?>
		</td>
		<td>
			<?php echo $regInfo['reg_notes']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php _e( 'Date registered', 'book-a-room' ); ?>
		</td>
		<td>
			<?php echo date( 'l, F jS, Y [g:i a]', strtotime( $regInfo['reg_dateReg'] ) ); ?>
		</td>
	</tr>
</table>
<p>
	<a href="?page=bookaroom_event_management_upcoming">
		<?php _e( 'Go back to search.', 'book-a-room' ); ?>
	</a>
</p>