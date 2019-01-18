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
	<?php _e( 'The next person on the waiting list has entered an email address. <strong>An automatic confirmation has been sent. </strong>', 'book-a-room' ); ?>
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
			<?php _e( 'Email', 'book-a-room' ); ?>
		</td>
		<td>
			<a href="mailto:<?php echo $regInfo['reg_email']; ?>">
				<?php echo $regInfo['reg_email']; ?>
			</a>
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