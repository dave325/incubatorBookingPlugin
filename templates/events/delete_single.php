<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events', 'book-a-room' ); ?>
	</h2>
</div>
<br>
<h2>
	<?php _e( 'Delete Event/Instance', 'book-a-room' ); ?>
</h2>
<table class="tableMain">
	<tr>
		<td>
			<?php _e( 'Are you sure?', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td width="150">
			<a href="?page=bookaroom_event_management&amp;action=<?php echo $action; ?>&amp;eventID=<?php echo $externals['eventID']; ?>&amp;hash=<?php echo $hash; ?>&amp;time=<?php echo $time; ?>">
				<?php _e( 'Delete this event?', 'book-a-room' ); ?>
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<a href="?page=bookaroom_event_management_upcoming">
				<?php _e( 'Back to search', 'book-a-room' ); ?>
			</a>
		</td>
	</tr>
</table>
<br>
<h2>
	<?php _e( 'Event Information', 'book-a-room' ); ?>
</h2>
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
		<td>
			<strong>
				<?php echo $branchName; ?>
			</strong><br/>
			<?php echo $roomName; ?>
		</td>
	</tr>
</table>
<br/>
<table class="tableMain">
	<tr>
		<td colspan="2">
			<?php _e( 'Date and Time', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php _e( 'Event Date', 'book-a-room' ); ?>
		</td>
		<td>
			<?php echo $externals['eventStart']; ?>
		</td>
	</tr>
	<tr>
		<td width="150">
			<?php _e( 'Event Time', 'book-a-room' ); ?>
		</td>
		<td nowrap="nowrap">
			<?php echo $eventTimes; ?>
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
		<td><?php echo $externals['eventTitle']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Event Description', 'book-a-room' ); ?></td>
		<td><?php echo $externals['eventDesc']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Registration Required', 'book-a-room' ); ?></td>
		<td><?php echo $registration; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Max. Registrations', 'book-a-room' ); ?></td>
		<td><?php echo $externals['maxReg']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Waiting List', 'book-a-room' ); ?></td>
		<td><?php echo $externals['waitingList']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Registration Begin Date', 'book-a-room' ); ?></td>
		<td><?php echo $regDate; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Private Notes', 'book-a-room' ); ?></td>
		<td><?php echo $externals['privateNotes']; ?></td>
	</tr>
</table>
<br/>
<table class="tableMain">
	<tr>
		<td colspan="2"><?php _e( 'Contact settings', 'book-a-room' ); ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Contact Name', 'book-a-room' ); ?></td>
		<td><?php echo $externals['publicName']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Contact Phone', 'book-a-room' ); ?></td>
		<td><?php echo $externals['publicPhone']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Contact Email', 'book-a-room' ); ?></td>
		<td><?php echo $externals['publicEmail']; ?></td>
	</tr>
	<tr>
		<td width="150"><?php _e( 'Presenter', 'book-a-room' ); ?></td>
		<td><?php echo $externals['presenter']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Website (<em>http://url.com</em>)', 'book-a-room' ); ?></td>
		<td><?php echo $externals['website']; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Website Text', 'book-a-room' ); ?></td>
		<td><?php echo $externals['websiteText']; ?></td>
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
			<td width="150">
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
				<input disabled="disable" name="category[<?php echo $curVal['categories_id']; ?>]" type="checkbox" id="category[<?php echo $curVal['categories_id']; ?>]" value="<?php echo $curVal['categories_id']; ?>"<?php echo $checked; ?> />
			</td>
			<td width="250"<?php if( !empty( $errorArr['errorBG']['category'] ) ) echo ' class="error"'; ?>>
				<label for="category[<?php echo $curVal_right['categories_id']; ?>]"><?php echo $curVal_right['categories_desc']; ?></label>
			</td>
			<td width="50"<?php if( !empty( $errorArr['errorBG']['category'] ) ) echo ' class="error"'; ?>>
				<?php
				if( $nextItem ) {
					$checked = ( !empty( $externals['category'] ) and in_array( $curVal_right['categories_id'], $externals['category'] ) ) ? ' checked="checked"' : null;
				?>
				<input disabled="disable" name="category[<?php echo $curVal_right['categories_id']; ?>]" type="checkbox" id="category[<?php echo $curVal_right['categories_id']; ?>]" value="<?php echo $curVal_right['categories_id']; ?>"<?php echo $checked; ?>/>
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
			<td width="50"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>><input disabled="disabled" name="ageGroup[<?php echo $curVal['age_id']; ?>]" type="checkbox" id="ageGroup[<?php echo $curVal['age_id']; ?>]" value="<?php echo $curVal['age_id']; ?>"<?php echo $checked; ?> />
			</td>
			<td width="250"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>><label for="ageGroup[<?php echo $curVal_right['age_id']; ?>]"><?php echo $curVal_right['age_desc']; ?></label>
			</td>
			<td width="50"<?php if( !empty( $errorArr['errorBG']['ageGroup'] ) ) echo ' class="error"'; ?>>
				<?php					
					$checked = ( !empty( $externals['ageGroup'] ) and in_array( $curVal_right['age_id'], $externals['ageGroup'] ) ) ? ' checked="checked"' : null;
					if ( $nextItem ) {
						?>
				<input disabled="disabled" name="ageGroup[<?php echo $curVal_right['age_id']; ?>]" type="checkbox" id="ageGroup[<?php echo $curVal_right['age_id']; ?>]" value="<?php echo $curVal_right['age_id']; ?>"<?php echo $checked; ?>/>
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
  <br />
  <table class="tableMain">
    <tr>
      <td colspan="2"><?php _e( 'Other options', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td width="150"><?php _e( 'Your name', 'book-a-room' ); ?></td>
      <td<?php if( !empty( $errorArr[ 'errorBG'][ 'yourName'] ) ) echo ' class="error"';?>><input disabled="disabled" name="yourName" type="text" id="yourName" value="<?php echo $externals['yourName']; ?>" size="42" /></td>
    </tr>
    <tr>
      <td><?php _e( 'Internal/Do not publish?', 'book-a-room' ); ?></td>
      <td><input disabled="disabled" name="doNotPublish" type="checkbox" id="doNotPublish" value="true"<?php echo ( !empty( $externals['doNotPublish'] ) ) ? ' checked="checked"' : null; ?> /></td>
    </tr>
  </table>