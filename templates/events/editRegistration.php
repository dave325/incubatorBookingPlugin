<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	_e( 'Edit Registration', 'book-a-room' );
	?>
</h2>
<form name="form1" method="post" action="?page=bookaroom_event_management">
	<table class="tableMain">
		<tr>
			<td>
				<?php _e( 'Event ID', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Date/Time', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Title/Desc', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Branch/Room', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Registrations', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $eventInfo['ti_id']; ?>
			</td>
			<td nowrap="nowrap">
				<?php echo date('l, F jS, Y', strtotime( $eventInfo['ti_startTime'] ) ); ?><br/>
				<em>
					<?php echo $time; ?>
				</em>
			</td>
			<td>
				<strong>
					<?php echo $eventInfo['ev_title']; ?>
				</strong><br/>
				<?php echo make_brief( $eventInfo['ev_desc'], 50 ); ?>
			</td>
			<td nowrap="nowrap">
				<p><strong><?php echo $branch; ?></strong><br/> <?php echo $room; ?>
				</p>
			</td>
			<td nowrap="nowrap"><?php echo  count( $registrations ); ?> / <?php echo $eventInfo['ev_maxReg']; ?></td>
		</tr>
	</table>
	<h3><?php _e( 'Registration', 'book-a-room' ); ?></h3>
	<?php
	if( !empty( $errorMSG ) ) {
		$regName	= $externals['regName'];
		$regPhone	= $externals['regPhone'];
		$regEmail	= $externals['regEmail'];
		$regNotes	= $externals['regNotes'];
	?>
	<span style="color:red;font-weight:bold"><?php echo $errorMSG; ?></span>
	<?php	
	} else {
		$regName	= $registrations[$externals['regID']]['reg_fullName'];
		$regPhone	= $registrations[$externals['regID']]['reg_phone'];
		$regEmail	= $registrations[$externals['regID']]['reg_email'];
		$regNotes	= $registrations[$externals['regID']]['reg_notes'];
	}
	?>
	<table class="tableMain">
		<tr>
			<td><?php _e( 'Name', 'book-a-room' ); ?></td>
			<td><?php _e( 'Phone', 'book-a-room' ); ?></td>
			<td><?php _e( 'Email', 'book-a-room' ); ?></td>
			<td><?php _e( 'Notes', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td>
				<input name="regName" type="text" id="regName" value="<?php echo $regName; ?>">
			</td>
			<td><input name="regPhone" type="text" id="regPhone" value="<?php echo $regPhone; ?>">
			</td>
			<td><input name="regEmail" type="text" id="regEmail" value="<?php echo $regEmail; ?>">
			</td>
			<td><textarea name="regNotes" type="text" id="regNotes"><?php echo $regNotes; ?></textarea>
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td><?php _e( 'Are you sure you want to edit this registraion?', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td align="center"><input name="eventID" type="hidden" id="eventID" value="<?php echo $eventInfo['ti_id']; ?>"/>
				<input name="hash" type="hidden" id="hash" value="<?php echo $hash; ?>"/>
				<input name="hashTime" type="hidden" id="hashTime" value="<?php echo $hashTime; ?>"/>
				<input name="regID" type="hidden" id="regID" value="<?php echo $externals['regID']; ?>"/>
				<input name="action" type="hidden" id="action" value="edit_registration_final"/>
				<input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/>
			</td>
		</tr>
	</table>
	<p><a href="?page=bookaroom_event_management_upcoming"><?php _e( 'Cancel and go back to search.', 'book-a-room' ); ?></a>
	</p>
</form>