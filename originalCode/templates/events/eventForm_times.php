<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script language="JavaScript" type="text/javascript">
	var rowNum = 0;

	function addRow( frm ) {
		rowNum++;
		var row = '<p id="rowNum' + rowNum + '"><input class="dates" type="text" name="addDateVals[]" value="' + frm.add_name.value + '" readOnly="true"> <input type="button" value="Remove" onclick="removeRow(' + rowNum + ');"></p>';
		jQuery( '#itemRows' ).append( row );
		frm.add_name.value = '';
	}

	function removeRow( rnum ) {
		jQuery( '#rowNum' + rnum ).remove();
	}


	$( function () {
		// Setup date drops
		$( '#eventStart, #weekly_endBy, #regDate, #add_name, #daily_endBy' ).datepicker( {
			dateFormat: 'mm/dd/yy'
		} );

		// If all day is checked, disable the times
		if ( $( 'input[name="allDay"]:checked' ).val() == 'true' ) {
			$( '#startTime, #endTime' ).prop( 'disabled', true );
		} else {
			$( '#startTime, #endTime' ).prop( 'disabled', false );
		}

		// All day active switch
		$( 'input[name=allDay]:radio' ).change( function () {
			if ( $( this ).val() == 'true' ) {
				$( '#startTime, #endTime' ).prop( 'disabled', true );
			} else {
				$( '#startTime, #endTime' ).prop( 'disabled', false );
			}
		} );

		// registration - switch
		$( 'input[name=registration]:radio' ).change( function () {
			if ( $( this ).val() == 'no' ) {
				$( '#waitingList, #maxReg, #regDate' ).prop( 'disabled', true );
			} else {
				$( '#waitingList, #maxReg, #regDate' ).prop( 'disabled', false );
			}
		} );

		// registration - on load

		if ( typeof $( 'input[name=registration]:checked' ) !== 'undefined' ) {
			if ( $( 'input[name=registration]:checked' ).val() == 'no' ) {
				$( '#waitingList, #maxReg, #regDate' ).prop( 'disabled', true );
			} else {
				$( '#waitingList, #maxReg, #regDate' ).prop( 'disabled', false );
			}
		}


		// Daily - if you focus on eveyNDays, change radio button
		$( '#dailyEveryNDaysVal' ).on( 'focus', function () {
			$( '#dailyEveryNDays' ).attr( 'checked', 'checked' );
		} );

		// Daily - if you select weekends or weekdays, disable everyNDays
		if ( typeof $( 'input[name=weeklyType]:checked' ).val() != 'undefined' ) {
			if ( $( 'input[name=dailyType]:radio' ).val() !== 'everyNDays' ) {
				$( '#dailyEveryNDaysVal' ).attr( 'disabled', true );
			};
		};

		$( 'input[name=dailyType]:radio' ).change( function () {
			if ( $( this ).val() == 'everyNDays' ) {
				$( '#dailyEveryNDaysVal' ).attr( 'disabled', false );
			} else {
				$( '#dailyEveryNDaysVal' ).attr( 'disabled', true );
			}
		} );

		$( 'input[name=dailyEndType]:radio' ).change( function () {
			if ( $( this ).val() == 'Occurrences' ) {
				$( '#daily_Occurrence' ).attr( 'disabled', false );
				$( '#daily_endBy' ).attr( 'disabled', true );
			} else {
				$( '#daily_Occurrence' ).attr( 'disabled', true );
				$( '#daily_endBy' ).attr( 'disabled', false );
			}
		} );

		if ( typeof $( 'input[name=dailyEndType]:checked' ).val() != 'undefined' ) {
			if ( $( 'input[name=dailyEndType]:checked' ).val() == 'Occurrences' ) {
				$( '#daily_Occurrence' ).attr( 'disabled', false );
				$( '#daily_endBy' ).attr( 'disabled', true );
			} else {
				$( '#daily_Occurrence' ).attr( 'disabled', true );
				$( '#daily_endBy' ).attr( 'disabled', false );
			}
		}

		$( '#daily_Occurrence' ).on( 'focus', function () {
			$( '#dailyEndTypeOccurrences' ).attr( 'checked', 'checked' );
			$( '#daily_endBy' ).attr( 'disabled', true );
		} );

		$( '#daily_endBy' ).on( 'focus', function () {
			$( '#dailyEndTypeEndby' ).attr( 'checked', 'checked' );
			$( '#daily_Occurrence' ).attr( 'disabled', true );
		} );

		// weekly - if Occurrences value is entered, disable end by
		if ( typeof $( 'input[name=weeklyType]:checked' ).val() != 'undefined' ) {
			if ( $( 'input[name=weeklyType]:checked' ).val() == 'Occurrences' ) {
				$( '#weekly_Occurrence' ).attr( 'disabled', false );
				$( '#weekly_endBy' ).attr( 'disabled', true );
			} else {
				$( '#weekly_Occurrence' ).attr( 'disabled', true );
				$( '#weekly_endBy' ).attr( 'disabled', false );
			}
		}
		$( 'input[name=weeklyType]:radio' ).change( function () {
			if ( $( this ).val() == 'Occurrences' ) {
				$( '#weekly_Occurrence' ).attr( 'disabled', false );
				$( '#weekly_endBy' ).attr( 'disabled', true );
			} else {
				$( '#weekly_Occurrence' ).attr( 'disabled', true );
				$( '#weekly_endBy' ).attr( 'disabled', false );
			}
		} );

		$( '#weekly_Occurrence' ).on( 'focus', function () {
			$( '#weeklyTypeOccurrences' ).attr( 'checked', 'checked' );
			$( '#weekly_endBy' ).attr( 'disabled', true );
		} );

		$( '#weekly_endBy' ).on( 'focus', function () {
			$( '#weeklyTypeEndBy' ).attr( 'checked', 'checked' );
			$( '#weekly_Occurrence' ).attr( 'disabled', true );
		} );

		// Recurrence drop down and hidden inputs
		$( '#recurrence' ).change( function () {
			switch ( $( this ).val() ) {
				case 'daily':
					$( '.daily' ).slideDown( 'fast' );
					$( '.weekly' ).slideUp( 'fast' );
					$( '.monthly' ).slideUp( 'fast' );
					$( '.addDates' ).slideUp( 'fast' );
					break;
				case 'weekly':
					$( '.daily' ).slideUp( 'fast' );
					$( '.weekly' ).slideDown( 'fast' );
					$( '.monthly' ).slideUp( 'fast' );
					$( '.addDates' ).slideUp( 'fast' );
					break;
				case 'monthly':
					$( '.daily' ).slideUp( 'fast' );
					$( '.weekly' ).slideUp( 'fast' );
					$( '.monthly' ).slideDown( 'fast' );
					$( '.addDates' ).slideUp( 'fast' );
					break;
				case 'addDates':
					$( '.daily' ).slideUp( 'fast' );
					$( '.weekly' ).slideUp( 'fast' );
					$( '.monthly' ).slideUp( 'fast' );
					$( '.addDates' ).slideDown( 'fast' );
					break;
				default:
					$( '.daily' ).slideUp( 'fast' );
					$( '.weekly' ).slideUp( 'fast' );
					$( '.monthly' ).slideUp( 'fast' );
					$( '.addDates' ).slideUp( 'fast' );
					break;
			};
		} );

		$( '#roomID' ).change( function () {
			$( '#action' ).val( '#changeAction#' );
			$( '#form' ).submit();
		} );
	} );

	function limitText( limitField, limitCount, limitNum ) {
		if ( limitField.value.length > limitNum ) {
			limitField.value = limitField.value.substring( 0, limitNum );
		} else {
			limitCount.value = limitNum - limitField.value.length;
		}
	}
</script>
<style>
	.daily,
	.weekly,
	.monthly,
	.addDates {
		display: none;
	}
	
	<?php
	if( !empty( $externals['recurrence'] ) ) {
	?>.<?php echo $externals['recurrence']; ?> {
		display: block;
	}
	<?php
	}
		?>
</style>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	switch ( $action ) {
		default:
			case 'checkInformation':
			_e( 'New Event', 'book-a-room' );
		break;
		case 'edit':
				_e( 'Edit event', 'book-a-room' );
			break;
	}
	?>
</h2>
<?php
# Display Errors if there are any
if ( !empty( $errorArr[ 'errorMSG' ] ) ) {
	?>
<p>
	<h3 style="color: red;"><strong><?php echo $errorArr['errorMSG']; ?></strong></h3>
</p> 
<?php
}
?>
<form action="?page=bookaroom_event_management" method="post" name="form" id="form">
	<table class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Location', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td width="150">
				<?php _e( 'Branch/Room', 'book-a-room' ); ?>
			</td>
			<td<?php if( !empty( $errorArr['errorBG']['location'] ) ) echo ' class="error"';?>>
				<select name="roomID" id="roomID">
					<?php
					$selected = ( empty( $externals[ 'roomID' ] ) ) ? ' selected="selected"' : NULL;
					?>
					<option value="NULL" <?php echo $selected; ?> disabled="disabled">
						<?php _e( 'Please choose a location', 'book-a-room' ); ?>
					</option>
					<?php
					foreach ( $branchList as $key => $val ) {
						# display a disabled branch name
						?>
					<option value="NULL" disabled="disabled">
						<?php echo $val['branchDesc']; ?>
					</option>
					<?php
						if ( true == $val[ 'branch_hasNoloc' ] ) {
							$selected = ( $externals[ 'roomID' ] == 'noloc-' . $val[ 'branchID' ] ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo 'noloc-'.$val['branchID']; ?>" <?php echo $selected; ?>>
						&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $val['branchDesc']; ?> - <?php _e( 'No location required', 'book-a-room' ); ?>
					</option>
						<?php
						}
						$curRoomList = empty( $roomContList['branch'][$val['branchID']] ) ? array() : $roomContList['branch'][$val['branchID']];
						foreach( $curRoomList as $roomContID ) {
							$selected = ( $externals['roomID'] == $roomContID ) ? ' selected="selected"' : NULL;
					?><option value="<?php echo $roomContID; ?>" <?php echo $selected; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $roomContList['id'][$roomContID]['desc']; ?>&nbsp;[<?php echo $roomContList['id'][$roomContID]['occupancy']; ?>]</option>
						<?php
						}
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Recurrence settings', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Event Dates', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['eventStart'] ) ) echo ' class="error"';?>><input name="eventStart" type="text" id="eventStart" value="<?php echo $externals['eventStart']; ?>" size="10" maxlength="10"/>
			</td>
		</tr>
		<tr>
			<td width="150"><?php _e( 'Recurrence', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['recurrence'] ) ) echo ' class="error"';?>><select name="recurrence" id="recurrence">
         <?php
			$goodArr = array( 'single' => __( 'Single', 'book-a-room' ), 'daily' => __( 'Daily', 'book-a-room' ), 'weekly' => __( 'Weekly', 'book-a-room' ), 'addDates' => __( 'Add Dates', 'book-a-room' ) );
				$selected = ( empty( $externals['recurrence'] ) ) ? 'selected="selected"' : NULL;
				?>
				<option value=""<?php echo $selected; ?>><?php _e( 'Choose one', 'book-a-room' ); ?></option>
				<?php			 
			foreach( $goodArr as $key => $val ) {
				$selected = ( $externals['recurrence'] === $key ) ? 'selected="selected"' : NULL;				
				?>
         		<option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>          
          <?php
			}
				?>			
        </select>
			</td>
		</tr>
	</table>
	<br/>
	<?php

	$checked_everyNDays	= $checked_weekdays	= $checked_weekends	= null;
	switch( $externals['dailyType'] ) {
		case 'everyNDays':
			$checked_everyNDays	= ' checked="checked"';
			break;
		case 'weekdays':
			$checked_weekdays	= ' checked="checked"';
			break;
		case 'weekends':
			$checked_weekends	= ' checked="checked"';
			break;
	}
	# daily type
	$checked_Occurrences = $checked_endBy = NULL;
	switch( $externals['dailyEndType'] ) {
		case 'Occurrences':
			$checked_Occurrences = ' checked="checked"';
			break;
		case 'endBy':
			$checked_endBy	= ' checked="checked"';
			break;
	}
?>	
	<div class="daily">
		<table class="tableMain">
			<tr>
				<td colspan="2"><?php _e( 'Daily Recurrence', 'book-a-room' ); ?></td>
			</tr>
			<tr>
				<td width="150" rowspan="3"><?php _e( 'Options', 'book-a-room' ); ?></td>
				<td<?php if( !empty( $errorArr['errorBG']['dailyType_everyNDays'] ) ) echo ' class="error"'; ?>>
					
					<input name="dailyType" type="radio" id="dailyEveryNDays" value="everyNDays"<?php echo $checked_everyNDays; ?>/>
					<?php printf( __( 'Every %s Days(s)', 'book-a-room' ), '<input name="dailyEveryNDaysVal" type="text" id="dailyEveryNDaysVal" size="3" maxlength="3" value="'.$externals['dailyEveryNDaysVal'].'"/>' ); ?>
				</td>
			</tr>
			<tr>
				<td<?php if( !empty( $errorArr['errorBG']['dailyType_weekdays'] ) ) echo ' class="error"'; ?>>
					<input type="radio" name="dailyType" id="dailyWeekdays" value="weekdays"<?php echo $checked_weekdays; ?>/>
					<label for="dailyWeekdays"><?php _e( 'Weekdays', 'book-a-room' ); ?></label>
				</td>
			</tr>
			<tr>
				<td<?php if( !empty( $errorArr['errorBG']['dailyType_weekends'] ) ) echo ' class="error"'; ?>><input type="radio" name="dailyType" id="dailyWeekends" value="weekends"<?php echo $checked_weekends; ?>/>
					<label for="dailyWeekends"><?php _e( 'Weekends', 'book-a-room' ); ?></label>
				</td>
			</tr>
			<tr>
				<td rowspan="2"><?php _e( 'End choice', 'book-a-room' ); ?></td>
				<td<?php if( !empty( $errorArr['errorBG']['dailyEndType_Occurrences'] ) ) echo ' class="error"'; ?>><input type="radio" name="dailyEndType" id="dailyEndTypeOccurrences" value="Occurrences"<?php echo $checked_Occurrences; ?>/>
					<input name="daily_Occurrence" type="text" id="daily_Occurrence" value="<?php echo $externals['daily_Occurrence']; ?>" size="3" maxlength="3"/> <?php _e( 'Occurrences', 'book-a-room' ); ?>
				</td>
			</tr>
			<tr>
				<td<?php if( !empty( $errorArr['errorBG']['dailyEndType_endBy'] ) ) echo ' class="error"'; ?>><input type="radio" name="dailyEndType" id="dailyEndTypeEndby" value="endBy"<?php echo $checked_endBy; ?>/><?php _e( 'End by', 'book-a-room' ); ?> 
					<input name="daily_endBy" type="text" id="daily_endBy" value="<?php echo $externals['daily_endBy']; ?>" size="10" maxlength="10"/></td>
			</tr>
		</table>
		<br/>
	</div>
	<?php
	# weekly type
	$checked_Occurrences = $checked_endBy = NULL;
	switch( $externals['weeklyEndType'] ) {
		case 'Occurrences':
			$checked_Occurrences = ' checked="checked"';
			break;
		case 'endBy':
			$checked_endBy	= ' checked="checked"';
			break;
	}
?>		
	<div class="weekly">
		<table class="tableMain">
			<tr>
				<td colspan="2"><?php _e( 'Weekly Recurrence', 'book-a-room' ); ?></td>
			</tr>
			<tr>
				<td width="150" rowspan="2"><?php _e( 'Options', 'book-a-room' ); ?></td>
				<td<?php if( !empty( $errorArr['errorBG']['everyNWeeks'] ) ) echo ' class="error"'; ?>><?php printf( __( 'Every %s Weeks on', 'book-a-room' ), '<input name="everyNWeeks" type="text" id="everyNWeeks" value="' . $externals['everyNWeeks'] . '" size="3" maxlength="3"/>' ); ?></td>
			</tr>
			<tr>
				<td<?php if( !empty( $errorArr['errorBG']['weeklyDay'] ) ) echo ' class="error"'; ?>>
					<table width="100%">
						<tr>
							<td><?php _e( 'Su', 'book-a-room' ); ?></td>
							<td><?php _e( 'Mo', 'book-a-room' ); ?></td>
							<td><?php _e( 'Tu', 'book-a-room' ); ?></td>
							<td><?php _e( 'We', 'book-a-room' ); ?></td>
							<td><?php _e( 'Th', 'book-a-room' ); ?></td>
							<td><?php _e( 'Fr', 'book-a-room' ); ?></td>
							<td><?php _e( 'Sa', 'book-a-room' ); ?></td>
						</tr>
						<tr>
							<td><input name="weeklyDay[]" type="checkbox" value="sunday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'sunday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
							<td><input name="weeklyDay[]" type="checkbox" value="monday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'monday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
							<td><input name="weeklyDay[]" type="checkbox" value="tuesday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'tuesday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
							<td><input name="weeklyDay[]" type="checkbox" value="wednesday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'wednesday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
							<td><input name="weeklyDay[]" type="checkbox" value="thursday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'thursday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
							<td><input name="weeklyDay[]" type="checkbox" value="friday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'friday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
							<td><input name="weeklyDay[]" type="checkbox" value="saturday"<?php 
									   if( !empty( $externals['weeklyDay'] ) and in_array( 'saturday', $externals['weeklyDay'] ) ) echo ' checked="checked"';
									   ?>/></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td rowspan="2"><?php _e( 'End choice', 'book-a-room' ); ?></td>
				<td<?php if( !empty( $errorArr['errorBG']['weeklyEndType_Occurrences'] ) ) echo ' class="error"'; ?>>
					<input type="radio" name="weeklyEndType" id="weeklyTypeOccurrences" value="Occurrences"<?php echo $checked_Occurrences; ?>/>
					<input name="weekly_Occurrence" type="text" id="weekly_Occurrence" value="<?php echo $externals['weekly_Occurrence']; ?>" size="3" maxlength="3"/> <?php _e( 'Occurrences', 'book-a-room' ); ?>
				</td>
			</tr>
			<tr>
				<td<?php if( !empty( $errorArr['errorBG']['weeklyEndType_endBy'] ) ) echo ' class="error"'; ?>><input type="radio" name="weeklyEndType" id="weeklyTypeEndBy" value="endBy"<?php echo $checked_endBy; ?>/>
					 <?php _e( 'End by', 'book-a-room' ); ?> <input name="weekly_endBy" type="text" id="weekly_endBy" value="<?php echo $externals['weekly_endBy'];?>" size="10" maxlength="10"/></td>
			</tr>
		</table>
		<br/>
	</div>
	<div class="addDates">
		<table class="tableMain">
			<tr>
				<td colspan="2"><?php _e( 'Additional Dates', 'book-a-room' ); ?></td>
			</tr>
			<tr>
				<td width="150"><?php _e( 'Dates<br/> (You <strong>must</strong> click Add Date to add the date.)', 'book-a-room' ); ?></td>
				<td<?php if( !empty( $errorArr['errorBG']['addDates'] ) ) echo ' class="error"'; ?>>
					<div id="itemRows">
						<input type="text" name="add_name" id="add_name"/>
						<input onclick="addRow(this.form);" type="button" value="Add Date"/> 
						<?php
						if( !empty( $externals['addDateVals'] ) ) {
							$count = 0;
								foreach( $externals['addDateVals'] as $val ) {
						?>
						<p id="rowNum<?php echo $count; ?>">
							<input type="text" name="addDateVals[]" value="<?php echo $val; ?>" readOnly="true">
							<input type="button" value="<?php _e( 'Remove', 'book-a-room' ); ?>" onclick="removeRow(<?php echo $count++; ?>);">
						</p>
						<?php
								}
						}
						?>
					</div>
				</td>
			</tr>
		</table>
		<br/>
	</div>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Time settings', 'book-a-room' ); ?></td>
		</tr>
		<?php
		if( $externals['allDay'] == 'true' ) {
			$allDayTrue = ' checked="checked"';
			$allDayFalse = NULL;
		} else {
			$allDayTrue = NULL;
			$allDayFalse = ' checked="checked"';
		}
		?>
		<tr>
			<td width="150"><?php _e( 'All Day?', 'book-a-room' ); ?></td>
			<td><input name="allDay" id="allDayTrue" type="radio" value="true"<?php echo $allDayTrue; ?>/> <?php _e( 'Yes', 'book-a-room' ); ?>
				<input name="allDay" type="radio" id="allDayFalse" value="false"<?php echo $allDayFalse; ?>/> <?php _e( 'No', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Start Time (hh:mm am/pm)', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['startTime'] ) ) echo ' class="error"'; ?>><input name="startTime" type="text" id="startTime" value="<?php echo $externals['startTime']; ?>" size="8" maxlength="8" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'End Time (hh:mm am/pm)', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['endTime'] ) ) echo ' class="error"'; ?>><input name="endTime" type="text" id="endTime" value="<?php echo $externals['endTime']; ?>" size="8" maxlength="8" />
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Event settings', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td width="150"><?php _e( 'Event Title', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['eventTitle'] ) ) echo ' class="error"'; ?>><input name="eventTitle" type="text" id="eventTitle" value="<?php echo $externals['eventTitle']; ?>" size="55"/>
			</td>
		</tr>
		<tr>
			<td>
				<p><?php _e( 'Event Description', 'book-a-room' ); ?><br>
					<em> <?php _e( '(300 char. max)', 'book-a-room' ); ?></em>
				</p>
			</td>
			<td<?php if( !empty( $errorArr['errorBG']['eventDesc'] ) ) echo ' class="error"'; ?>><textarea name="eventDesc" cols="55" rows="6" id="eventDesc" onKeyDown="limitText(this.form.eventDesc,this.form.eventDesc_countdown,300);" onKeyUp="limitText(this.form.eventDesc,this.form.eventDesc_countdown,300);"><?php echo $externals['eventDesc']; ?></textarea>
				<br>
				<input readonly type="text" name="eventDesc_countdown" size="3" value="300" id="numbCount"> <?php /* translators: this appears as "n Characters left" under a text box. */ _e( 'Characters left.', 'book-a-room' ); ?></td>
		</tr>
		<?php
		$reg_true = $reg_false = $reg_staff = null;	
		switch( $externals['registration'] ) {
			case 'yes':
				$reg_true = ' checked="checked"';						
				break;
			case 'staff':
				$reg_staff = ' checked="checked"';
				break;
			case 'no':
			default:
				$reg_false = ' checked="checked"';
				break;
		}
		?>
		<tr>
			<td><?php _e( 'Registration Required', 'book-a-room' ); ?></td>
			<td><input type="radio" name="registration" id="registrationTrue" value="yes"<?php echo $reg_true; ?>/> <?php _e( 'Yes', 'book-a-room' ); ?>
				<input type="radio" name="registration" id="registrationFalse" value="no"<?php echo $reg_false; ?>/> <?php _e( 'No', 'book-a-room' ); ?>
				<input type="radio" name="registration" id="registrationStaff" value="staff"<?php echo $reg_staff; ?>/> <?php _e( 'Staff Only', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Max. Registrations', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['maxReg'] ) ) echo ' class="error"'; ?>><input name="maxReg" type="text" id="maxReg" value="<?php echo $externals['maxReg']; ?>" size="3" maxlength="3" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Waiting List', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['maxReg'] ) ) echo ' class="error"'; ?>><input name="waitingList" type="text" id="waitingList" value="<?php echo $externals['waitingList']; ?>" size="3" maxlength="3" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Registration Begin Date', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['regDate'] ) ) echo ' class="error"'; ?>><input name="regDate" type="text" id="regDate" value="<?php echo $externals['regDate']; ?>" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Private Notes', 'book-a-room' ); ?></td>
			<td><textarea name="privateNotes" id="privateNotes" cols="55" rows="6"><?php echo $externals['privateNotes']; ?></textarea>
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Contact settings', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Contact Name', 'book-a-room' ); ?></td>
			<td><input name="publicName" type="text" id="publicName" value="<?php echo $externals['publicName']; ?>" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Contact Phone', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['publicPhone'] ) ) echo ' class="error"'; ?>><input name="publicPhone" type="text" id="publicPhone" value="<?php echo $externals['publicPhone']; ?>" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Contact Email', 'book-a-room' ); ?></td>
			<td><input name="publicEmail" type="text" id="publicEmail" value="<?php echo $externals['publicEmail']; ?>"/>
			</td>
		</tr>
		<tr>
			<td width="150"><?php _e( 'Presenter', 'book-a-room' ); ?></td>
			<td><input name="presenter" type="text" id="presenter" value="<?php echo $externals['presenter']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Website (<em>http://url.com</em>)', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['website'] ) ) echo ' class="error"'; ?>><input name="website" type="text" id="website" value="<?php echo $externals['website']; ?>" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Website Text', 'book-a-room' ); ?></td>
			<td><input name="websiteText" type="text" id="websiteText" value="<?php echo $externals['websiteText']; ?>"/>
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Amenities', 'book-a-room' ); ?></td>
		</tr>
		<?php
		if( empty( $roomContList['id'][$externals['roomID']]['rooms'] ) ) {
			$roomContList['id'][$externals['roomID']]['rooms'] = array();
		}
		
		$amenitiesArr = array();
			
		foreach( $roomContList['id'][$externals['roomID']]['rooms'] as $val ) {
			if( !empty( $roomList['id'][$val]['amenity'] ) && is_array( $roomList['id'][$val]['amenity'] ) ) {
				$amenitiesArr = array_merge( $amenitiesArr, $roomList['id'][$val]['amenity'] );
			}
		}
		
		$amenities = array_unique( $amenitiesArr );
		
		if( count( $amenities ) == 0 ) {
			
		?>
		<tr>
			<td colspan="2"><?php _e( 'No amenities available for this location.', 'book-a-room' ); ?></td>
		</tr>
		<?php
		} else {
		?>
		<tr>		
			<?php 
			foreach( $amenities as $val ) {
				if( empty( $amenityList[$val] ) ) continue;
				$checked = ( !empty( $externals['amenity'] ) && in_array( $val, $externals['amenity'] ) ) ? ' checked="checked"' : null;
			?>
			<td width="150"><?php echo $amenityList[$val]; ?></td>
			<td>
				<input name="amenity[]" type="checkbox" id="amenity[<?php echo $val; ?>]" value="<?php echo $val; ?>"<?php echo $checked; ?> />
			</td>
		</tr>
			<?php
			}
		}
		?>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="4"><?php _e( 'Categories', 'book-a-room' ); ?></td>
		</tr>
		<?php
		$categoryList = bookaroom_settings_categories::getNameList();
		
		if( count( $categoryList['active'] ) == 0 ) {
		?>
		<tr>
			<td colspan="4"><?php _e( 'No categories available.', 'book-a-room' ); ?></td>
		</tr>
		<?php
		} else {
			usort( $categoryList['active'], function ($a, $b) {
				return strcmp($a['categories_desc'], $b['categories_desc']);
			});
			for( $t=0; $t < ceil( count( $categoryList['active'] )/2); $t++) {
				$curVal = ( $t== 0 ) ? current( $categoryList['active'] ) : next( $categoryList['active'] );				
				$checked = ( !empty( $externals['category'] ) and in_array( $curVal['categories_id'], $externals['category'] ) ) ? ' checked="checked"' : null;				
				$nextItem =  next( $categoryList['active'] );
				if( $nextItem ) {
					$curVal_right = current( $categoryList['active'] );
				} else {
					$curVal_right['categories_id'] 		= null;
					$curVal_right['categories_desc'] 	= '&nbsp;';
				}
		?>
		<tr>
			<td width="250"<?php if( !empty( $errorArr['errorBG']['category'] ) ) echo ' class="error"'; ?>>
				<label for="category[<?php echo $curVal['categories_id']; ?>]"><?php echo $curVal['categories_desc']; ?></label>
			</td>
			<td width="50"<?php if( !empty( $errorArr['errorBG']['category'] ) ) echo ' class="error"'; ?>>
				<input name="category[<?php echo $curVal['categories_id']; ?>]" type="checkbox" id="category[<?php echo $curVal['categories_id']; ?>]" value="<?php echo $curVal['categories_id']; ?>"<?php echo $checked; ?> />
			</td>
			<td width="250"<?php if( !empty( $errorArr['errorBG']['category'] ) ) echo ' class="error"'; ?>>
				<label for="category[<?php echo $curVal_right['categories_id']; ?>]"><?php echo $curVal_right['categories_desc']; ?></label>
			</td>
			<td width="50"<?php if( !empty( $errorArr['errorBG']['category'] ) ) echo ' class="error"'; ?>>
				<?php
				if( $nextItem ) {
					$checked = ( !empty( $externals['category'] ) and in_array( $curVal_right['categories_id'], $externals['category'] ) ) ? ' checked="checked"' : null;
				?>
				<input name="category[<?php echo $curVal_right['categories_id']; ?>]" type="checkbox" id="category[<?php echo $curVal_right['categories_id']; ?>]" value="<?php echo $curVal_right['categories_id']; ?>"<?php echo $checked; ?>/>
				<?php
				} else {
					echo '&nbsp;';				
				}
				?>
			</td>
		</tr>
		<?php
			}
		}		
		?>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="4"><?php _e( 'Age Group', 'book-a-room' ); ?></td>
		</tr>
		<tr>
		<?php
			$ageGroupList = bookaroom_settings_age::getNameList();
			
			if( count( $ageGroupList['active'] ) == 0 ) {
			?>
			<td colspan="4"><?php _e( 'No age groups available.', 'book-a-room' ); ?></td>
		</tr>
		<?php
			} else {
				for( $t=0; $t < ceil( count( $ageGroupList['active'] )/2); $t++) {
					$curVal = ( $t== 0 ) ? current( $ageGroupList['active'] ) : next( $ageGroupList['active'] );
					$checked = ( !empty( $externals['ageGroup'] ) and in_array( $curVal['age_id'], $externals['ageGroup'] ) ) ? ' checked="checked"' : null;
					
					$nextItem =  next( $ageGroupList['active'] );

					if( $nextItem ) {
						$curVal_right = current( $ageGroupList['active'] );						
					} else {
						$curVal_right['age_id'] 		= null;
						$curVal_right['age_desc'] 		= '&nbsp;';
					}
		?>
		<tr>
			<td width="250"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>><label for="ageGroup[<?php echo $curVal['age_id']; ?>]"><?php echo $curVal['age_desc']; ?></label>
			</td>
			<td width="50"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>><input name="ageGroup[<?php echo $curVal['age_id']; ?>]" type="checkbox" id="ageGroup[<?php echo $curVal['age_id']; ?>]" value="<?php echo $curVal['age_id']; ?>"<?php echo $checked; ?> />
			</td>
			<td width="250"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>><label for="ageGroup[<?php echo $curVal_right['age_id']; ?>]"><?php echo $curVal_right['age_desc']; ?></label>
			</td>
			<td width="50"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>>
				<?php					
					$checked = ( !empty( $externals['ageGroup'] ) and in_array( $curVal_right['age_id'], $externals['ageGroup'] ) ) ? ' checked="checked"' : null;
					if ( $nextItem ) {
						?>
				<input name="ageGroup[<?php echo $curVal_right['age_id']; ?>]" type="checkbox" id="ageGroup[<?php echo $curVal_right['age_id']; ?>]" value="<?php echo $curVal_right['age_id']; ?>"<?php echo $checked; ?>/>
				<?php
					} else {
						echo '&nbsp;';
					}
					?>
			</td>
		</tr>
		<?php
				}
			}		
		?>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2"><?php _e( 'Other options', 'book-a-room' ); ?></td>
		</tr>
		<tr>
			<td width="150"><?php _e( 'Your name', 'book-a-room' ); ?></td>
			<td<?php if( !empty( $errorArr['errorBG']['yourName'] ) ) echo ' class="error"'; ?>><input name="yourName" type="text" id="yourName" value="<?php echo $externals['yourName']; ?>" size="42" />
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Internal/Do not publish?', 'book-a-room' ); ?></td>
			<td><input name="doNotPublish" type="checkbox" id="doNotPublish" value="true"<?php echo ( !empty( $externals['doNotPublish'] ) ) ? ' checked="checked"' : null; ?> />
			</td>
		</tr>
	</table>
	<h3><?php _e( 'Submit when complete to check for conflicts', 'book-a-room' ); ?></h3>
	<?php
	$disabled = ( !empty( $dateList ) ) ? ' disabled="disabled"' : null;
	$message = ( !empty( $dateList ) ) ? __( 'Please resolve conflicts at the top of the page before continuing.', 'book-a-room' ) : null;
	?>	
	<table class="tableMain">
		<tr>
			<td><input name="action" type="hidden" id="action" value="<?php echo $action; ?>"/>
				<input name="eventID" type="hidden" id="eventID" value="<?php echo $externals['eventID']; ?>"/>
				<input type="submit" name="SubmitButton" id="SubmitButton" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/><?php echo $message; ?>
			</td>
		</tr>
	</table>
</form>