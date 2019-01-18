<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2 id="top">
		<?php _e( 'Book a Room - Staff Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'View Staff Event', 'book-a-room' ); ?>
</h2>
<table class="tableMain">
	<tr>
		<td colspan="2">
			<?php _e( 'Event Information', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php _e( 'Event Title', 'book-a-room' ); ?>
		</td>
		<td>
			<?php echo $eventInfo['ev_title']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php _e( 'Date', 'book-a-room' ); ?>
		</td>
		<td>
			<span class="eventVal">
				<?php echo date( 'l, M. jS', strtotime( $eventInfo['ti_startTime'] ) ); ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Time', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventTime; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Branch', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventBranch; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Location', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventRoom; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Description', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventInfo['ev_desc']; echo ( empty( $eventInfo['ev_desc'] ) ) ? null : '<br />' . $eventInfo['ti_extraInfo']; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Categories', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventInfo['cats']; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader botBop">
				<?php _e( 'Age groups', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal botBop">
				<?php echo $eventInfo['ages']; ?>
			</span>
		</td>
	</tr>
</table><br>
<table class="tableMain">
	<tr>
		<td colspan="2">
			<?php _e( 'Presenter Information', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Presenter', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventInfo['ev_presenter']; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Website', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo ( empty( $eventInfo['ev_website'] ) ) ? '&nbsp;' : '<a href="' . $eventInfo['ev_website'] . '" target="_blank">' . $eventInfo['ev_website'] . '</a>'; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Contact Name', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $eventInfo['ev_publicName']; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Contact Email', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo ( empty( $eventInfo['ev_publicEmail'] ) ) ? '&nbsp;' : '<a href="mailto:' .  $eventInfo['ev_publicEmail'] . '">' .  $eventInfo['ev_publicEmail'] . '</a>'; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="eventHeader">
				<?php _e( 'Contact Phone', 'book-a-room' ); ?>
			</span>
		</td>
		<td>
			<span class="eventVal">
				<?php echo $nicePhone; ?>
			</span>
		</td>
	</tr>

</table>
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
<form id="form1" name="form1" method="post" action="?page=bookaroom_event_management_staff&action=viewEvent&eventID=<?php echo $eventInfo['ti_id']; ?>">
	<?php
	if ( $isSuccess == true ) {
		?>
		<br>
	<table class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Registration was successful', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>
					<?php _e( 'Your registration was entered successfully.', 'book-a-room' ); ?>
				</p>
				<p>
					<?php _e( 'You will not receive an email confirmation. Please print this for your records.', 'book-a-room' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<span class="eventHeader">
					<?php _e( 'Full Name', 'book-a-room' ); ?>
				</span>
			</td>
			<td>
				<span class="eventVal">
					<?php echo $externals['fullName']; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="eventHeader">
					<?php _e( 'Phone number', 'book-a-room' ); ?>
				</span>
			</td>
			<td>
				<span class="eventVal">
					<?php echo $nicePhoneReg; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="eventHeader">
					<?php _e( 'Email address', 'book-a-room' ); ?>
				</span>
			</td>
			<td>
				<span class="eventVal">
					<?php echo $externals['email']; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="eventHeader">
					<?php _e( 'Notes', 'book-a-room' ); ?>
				</span>
			</td>
			<td>
				<span class="eventVal">
					<?php echo $externals['notes']; ?>
				</span>
			</td>
		</tr>
	</table>
	<?php
	} elseif ( $eventInfo[ 'ev_regType' ] == 'staff' ) {
		$regInfo = self::getRegInfo( $eventInfo[ 'ti_id' ] );
		if ( count( $regInfo ) < $eventInfo[ 'ev_maxReg' ] ) {
		?><br>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Registration', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Full Name', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
        <input name="fullName" type="text" id="fullName" value="<?php echo $externals['fullName']; ?>" />
      </span>
			</td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Phone number', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
        <input name="phone" type="text" id="phone" value="<?php echo $externals['phone']; ?>" size="15" maxlength="15" />
      </span>
			</td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Email address', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
        <input name="email" type="text" id="email" value="<?php echo $externals['email']; ?>" />
      </span>
			</td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Notes', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
        <textarea name="notes" id="notes" cols="45" rows="5"><?php echo $externals['notes']; ?></textarea>
      </span>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><span class="eventVal">
        <input name="action" type="hidden" id="action" value="checkReg" />
        <input name="eventID" type="hidden" id="eventID" value="<?php echo $eventInfo['ti_id']; ?>" />
        <input name="bookaroom_RegFormSub" type="hidden" id="bookaroom_RegFormSub" value="<?php echo $externals['bookaroom_RegFormSub']; ?>" />
        <input type="submit" name="button" id="button" value="Submit" />
      </span>
			</td>
		</tr>
	</table>
	<?php
		} elseif ( count( $regInfo ) < ( $eventInfo[ 'ev_maxReg' ] + $eventInfo[ 'ev_waitingList' ] ) ) {
		?>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Waiting List', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php _e( 'This event is full. Please enter your information below if you would like to be added to the waiting list. You will be notified if anyone cancels and a spot becomes available.', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Full Name', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
      <input name="fullName" type="text" id="fullName" value="<?php echo $externals['fullName']; ?>" />
    </span>
			</td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Phone number', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
      <input name="phone" type="text" id="phone" value="<?php echo $externals['phone']; ?>" size="15" maxlength="15" />
    </span>
			</td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Email address', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
      <input name="email" type="text" id="email" value="<?php echo $externals['email']; ?>" />
    </span>
			</td>
		</tr>
		<tr>
			<td><span class="eventHeader"><?php _e( 'Notes', 'book-a-room' ); ?></span>
			</td>
			<td><span class="eventVal">
      <textarea name="notes" id="notes" cols="45" rows="5"><?php echo $externals['notes']; ?></textarea>
    </span>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><span class="eventVal">
      <input name="action" type="hidden" id="action" value="checkReg" />
      <input name="eventID" type="hidden" id="eventID" value="<?php echo $eventInfo['ti_id']; ?>" />
      <input name="bookaroom_RegFormSub" type="hidden" id="bookaroom_RegFormSub" value="<?php echo $externals['bookaroom_RegFormSub']; ?>" />
      <input type="submit" name="button2" id="button2" value="Submit" />
    </span>
			</td>
		</tr>
	</table>
	<?php
		} else  {
		?>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Registration', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php _e( 'This event is currently full.', 'book-a-room' ); ?></td>
		</tr>
	</table>
	<?php
		}
	} else {
		?>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Registration', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php _e( 'This event doesn\'t require registration.', 'book-a-room' ); ?></td>
		</tr>
	</table>
	<?php
	}
	?>
</form>