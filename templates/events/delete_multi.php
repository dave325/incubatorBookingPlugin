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
		$( '#toggleInstances' ).click( function () {
			if ( $( this ).is( ':checked' ) ) {
				$( 'input[name=instance\\[\\]' ).prop( "checked", true );
			} else {
				$( 'input[name=instance\\[\\]' ).removeAttr( "checked" );
			}
		} );
	} );
</script>
<style>
	.daily,
	.weekly,
	.monthly,
	.addDates {
		display: none;
	}
	
	.#showName# {
		display: block;
	}
</style>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events', 'book-a-room' ); ?>
	</h2>
</div>
<form id="form1" name="form1" method="post" action="?page=bookaroom_event_management">
	<p>
		<?php _e( 'Choose which instances you wish to delete. The checkbox at the top will toggle all of the instances. The event information is underneath the instance list.', 'book-a-room' ); ?>
	</p>
	<?php
	if ( !empty( $errorMSG ) ) {
		?>
	<p>
		<h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3>
	</p>
	<?php 
	}
?>
	<h2>
		<?php _e( 'Event Instances', 'book-a-room' ); ?>
	</h2>
	<table class="tableMain">
		<tr>
			<td><input type="checkbox" name="toggleInstances" id="toggleInstances"/>
			</td>
			<td>
				<?php _e( 'Event Date', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Time', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Location', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		foreach ( $instances as $key => $val ) {
			if ( !empty( $val[ 'ti_noLocation_branch' ] ) ) {
				$instanceBranch = $branchList[ $val[ 'ti_noLocation_branch' ] ][ 'branchDesc' ];
				$instanceRoom = __( 'No location required', 'book-a-room' );
			} else {
				$branchID = self::branch_and_room_id( $val[ 'ti_roomID' ], $branchList, $roomContList );
				$instanceBranch = $branchList[ $branchID ][ 'branchDesc' ];
				$instanceRoom = $roomContList[ 'id' ][ $val[ 'ti_roomID' ] ][ 'desc' ];
			}
			?>
		<tr>
			<td><input type="checkbox" name="instance[]" id="instance[<?php echo $externals['eventID']; ?>]" value="<?php echo $val['ti_id']; ?>"/>
			</td>
			<td width="150">
				<?php echo date( 'm/d/Y', strtotime( $val['ti_startTime'] ) ); ?>
			</td>
			<td width="150">
				<?php echo date( 'g:i a', strtotime( $val['ti_startTime'] ) ) . ' - ' . date( 'g:i a', strtotime( $val['ti_endTime'] ) ); ?>
			</td>
			<td>
				<strong>
					<?php echo $instanceBranch; ?>
				</strong><br/>
				<?php echo $instanceRoom; ?>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="4" align="center"><input name="eventID" type="hidden" id="eventID" value="<?php echo $externals['eventID']; ?>"/>
				<input name="action" type="hidden" id="action" value="checkDeleteMulti"/> <input type="submit" name="button" id="button" value="Submit"/>
			</td>
		</tr>
	</table>
	<h2>
		<?php _e( 'Event Information', 'book-a-room' ); ?>
	</h2>
	<table class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Event settings', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td width="150">
				<?php _e( 'Event Title', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['eventTitle']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Event Description', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['eventDesc']; ?>
			</td>
		</tr>
		<?php
		switch ( $externals[ 'registration' ] ) {
			case 'staff':
				$registration = 'Staff';
				break;
			case 'true':
				$registration = 'Yes';
				break;
			default:
				$registration = 'No';
				break;
		}
		?>
		<tr>
			<td>
				<?php _e( 'Registration Required', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $registration; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Max. Registrations', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['maxReg']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Waiting List', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['waitingList']; ?>
			</td>
		</tr>
		<?php
		$regDate = null;
		if ( empty( $externals[ 'regDate' ] ) ) {
			$regDate = $externals[ 'regDate' ];
		}
		?>
		<tr>
			<td>
				<?php _e( 'Registration Begin Date', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $regDate; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Private Notes', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['privateNotes']; ?>
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Contact settings', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td width="150">
				<?php _e( 'Contact Name', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['publicName']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Contact Phone', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['publicPhone']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Contact Email', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['publicEmail']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Presenter', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['presenter']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Website (<em>http://url.com</em>)', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['website']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Website Text', 'book-a-room' ); ?>
			</td>
			<td>
				<?php echo $externals['websiteText']; ?>
			</td>
		</tr>
	</table>
	<br/>
	<table class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Amenities', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		if ( empty( $roomContList[ 'id' ][ $externals[ 'roomID' ] ][ 'rooms' ] ) ) {
			$roomContList[ 'id' ][ $externals[ 'roomID' ] ][ 'rooms' ] = array();
		}
		$amenitiesArr = array();

		foreach ( $roomContList[ 'id' ][ $externals[ 'roomID' ] ][ 'rooms' ] as $val ) {
			if ( !empty( $roomList[ 'id' ][ $val ][ 'amenity' ] ) && is_array( $roomList[ 'id' ][ $val ][ 'amenity' ] ) ) {
				$amenitiesArr = array_merge( $amenitiesArr, $roomList[ 'id' ][ $val ][ 'amenity' ] );
			}
		}
		$amenities = array_unique( $amenitiesArr );

		if ( count( $amenities ) == 0 ) {
			?>
		<tr>
			<td colspan="2">
				<?php _e( 'No amenities available for this location.', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		} else {
			foreach ( $amenities as $val ) {
				if ( empty( $amenityList[ $val ] ) ) continue;
				$checked = ( !empty( $externals[ 'amenity' ] ) && in_array( $val, $externals[ 'amenity' ] ) ) ? ' checked="checked"' : null;
				?>
		<tr>
			<td>
				<?php echo $amenityList[$val]; ?>
			</td>
			<td>
				<input disabled="disable" name="amenity[]" type="checkbox" id="amenity[<?php echo $val; ?>]" value="<?php echo $val; ?>" <?php echo $checked; ?> />
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
			<td colspan="4">Categories</td>
		</tr>
		<tr>
			<?php
			$categoryList = bookaroom_settings_categories::getNameList();

			if ( count( $categoryList[ 'active' ] ) == 0 ) {
				?>
			<tr>
				<td colspan="4">
					<?php _e( 'No categories available.', 'book-a-room' ); ?>
				</td>
			</tr>
			<?php
			} else {
				usort( $categoryList[ 'active' ], function ( $a, $b ) {
					return strcmp( $a[ 'categories_desc' ], $b[ 'categories_desc' ] );
				} );
				for ( $t = 0; $t < ceil( count( $categoryList[ 'active' ] ) / 2 ); $t++ ) {
					$curVal = ( $t == 0 ) ? current( $categoryList[ 'active' ] ) : next( $categoryList[ 'active' ] );
					$checked = ( !empty( $externals[ 'category' ] ) and in_array( $curVal[ 'categories_id' ], $externals[ 'category' ] ) ) ? ' checked="checked"' : null;
					$nextItem = next( $categoryList[ 'active' ] );
					if ( $nextItem ) {
						$curVal_right = current( $categoryList[ 'active' ] );
					} else {
						$curVal_right[ 'categories_id' ] = null;
						$curVal_right[ 'categories_desc' ] = '&nbsp;';
					}
					?>
			<tr>
				<td width="250" <?php if( !empty( $errorArr[ 'errorBG'][ 'category'] ) ) echo ' class="error"'; ?>>
					<label for="category[<?php echo $curVal['categories_id']; ?>]">
						<?php echo $curVal['categories_desc']; ?>
					</label>
				</td>
				<td width="50" <?php if( !empty( $errorArr[ 'errorBG'][ 'category'] ) ) echo ' class="error"'; ?>>
					<input disabled="disable" name="category[<?php echo $curVal['categories_id']; ?>]" type="checkbox" id="category[<?php echo $curVal['categories_id']; ?>]" value="<?php echo $curVal['categories_id']; ?>" <?php echo $checked; ?> />
				</td>
				<td width="250" <?php if( !empty( $errorArr[ 'errorBG'][ 'category'] ) ) echo ' class="error"'; ?>>
					<label for="category[<?php echo $curVal_right['categories_id']; ?>]">
						<?php echo $curVal_right['categories_desc']; ?>
					</label>
				</td>
				<td width="50" <?php if( !empty( $errorArr[ 'errorBG'][ 'category'] ) ) echo ' class="error"'; ?>>
					<?php
					if ( $nextItem ) {
						$checked = ( !empty( $externals[ 'category' ] ) and in_array( $curVal_right[ 'categories_id' ], $externals[ 'category' ] ) ) ? ' checked="checked"' : null;
						?>
					<input disabled="disable" name="category[<?php echo $curVal_right['categories_id']; ?>]" type="checkbox" id="category[<?php echo $curVal_right['categories_id']; ?>]" value="<?php echo $curVal_right['categories_id']; ?>" <?php echo $checked; ?>/>
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
			<td colspan="4">
				<?php _e( 'Age Group', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<?php
			$ageGroupList = bookaroom_settings_age::getNameList();

			if ( count( $ageGroupList[ 'active' ] ) == 0 ) {
				?>
			<td colspan="4">
				<?php _e( 'No age groups available.', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		} else {
			for ( $t = 0; $t < ceil( count( $ageGroupList[ 'active' ] ) / 2 ); $t++ ) {
				$curVal = ( $t == 0 ) ? current( $ageGroupList[ 'active' ] ) : next( $ageGroupList[ 'active' ] );
				$checked = ( !empty( $externals[ 'ageGroup' ] ) and in_array( $curVal[ 'age_id' ], $externals[ 'ageGroup' ] ) ) ? ' checked="checked"' : null;

				$nextItem = next( $ageGroupList[ 'active' ] );

				if ( $nextItem ) {
					$curVal_right = current( $ageGroupList[ 'active' ] );
				} else {
					$curVal_right[ 'age_id' ] = null;
					$curVal_right[ 'age_desc' ] = '&nbsp;';
				}
				?>
		<tr>
			<td width="250" <?php if( !empty( $errorArr[ 'errorBG'][ 'ageGroup'] ) ) echo ' class="error"'; ?>>
				<label for="ageGroup[<?php echo $curVal['age_id']; ?>]">
					<?php echo $curVal['age_desc']; ?>
				</label>
			</td>
			<td width="50"<?php if( !empty( $errorArr[ 'errorBG'][ 'ageGroup'] ) ) echo ' class="error"'; ?>><input disabled="disabled" name="ageGroup[<?php echo $curVal['age_id']; ?>]" type="checkbox" id="ageGroup[<?php echo $curVal['age_id']; ?>]" value="<?php echo $curVal['age_id']; ?>" <?php echo $checked; ?> />
			</td>
			<td width="250" <?php if( !empty( $errorArr[ 'errorBG'][ 'ageGroup'] ) ) echo ' class="error"'; ?>>
				<label for="ageGroup[<?php echo $curVal_right['age_id']; ?>]">
					<?php echo $curVal_right['age_desc']; ?>
				</label>
			</td>
			<td width="50" <?php if( !empty( $errorArr[ 'errorBG'][ 'ageGroup'] ) ) echo ' class="error"'; ?>>
				<?php
				$checked = ( !empty( $externals[ 'ageGroup' ] ) and in_array( $curVal_right[ 'age_id' ], $externals[ 'ageGroup' ] ) ) ? ' checked="checked"' : null;
				if ( $nextItem ) {
					?>
				<input disabled="disabled" name="ageGroup[<?php echo $curVal_right['age_id']; ?>]" type="checkbox" id="ageGroup[<?php echo $curVal_right['age_id']; ?>]" value="<?php echo $curVal_right['age_id']; ?>" <?php echo $checked; ?>/>
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
			<td colspan="2">
				<?php _e( 'Other options', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td width="150">
				<?php _e( 'Your name', 'book-a-room' ); ?>
			</td>
			<td<?php if( !empty( $errorArr[ 'errorBG'][ 'yourName'] ) ) echo ' class="error"';?>><input disabled="disabled" name="yourName" type="text" id="yourName" value="<?php echo $externals['yourName']; ?>" size="42"/>
				</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Internal/Do not publish?', 'book-a-room' ); ?>
			</td>
			<td><input disabled="disabled" name="doNotPublish" type="checkbox" id="doNotPublish" value="true" <?php echo ( !empty( $externals[ 'doNotPublish'] ) ) ? ' checked="checked"' : null; ?> /></td>
		</tr>
	</table>
</form>