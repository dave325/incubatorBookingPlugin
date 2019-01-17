<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2 id="top">
		<?php _e( 'Book a Room - Manage Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Manage Events', 'book-a-room' ); ?>
</h2>
<!-- scripts and CSS - need to include outside -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script language="javascript">
	$( function () {
		// Setup date drops
		$( '#startDate, #endDate' ).datepicker( {
			dateFormat: 'mm/dd/yy'
		} );
		$( "#hideToggle" ).click( function () {
			$( ".searchArea" ).toggle();
		} );
		$( "#resetAge" ).click( function () {
			$( "#ageGroup option:selected" ).removeAttr( "selected" );

		} );
		$( "#resetCats" ).click( function () {
			$( "#categoryGroup option:selected" ).removeAttr( "selected" );
		} );
		if ( <?php echo ( $externals['hideSearch'] == true) ?  'true' : 'false'; ?> ) {
			$( ".searchArea" ).toggle();
		}
	} );
</script>
<!-- <body class="ui-dialog-titlebar-close"> -->
<form name="form1" method="post" action="?page=bookaroom_event_management_upcoming">
	<table width="100%" class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Search/Filter Settings', 'book-a-room' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<div style="float:right; cursor:pointer; text-decoration:underline" id="hideToggle">
					<?php _e( 'Hide/Show', 'book-a-room' ); ?>
				</div>
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Branch', 'book-a-room' ); ?>
			</td>
			<td>
				<select name="roomID" id="roomID">
					<?php
					# make branch list drop down
					$selected = ( empty( $externals[ 'branchID' ] ) ) ? ' selected="selected"' : NULL;
					?>
					<option value="" <?php echo $selected; ?>>
						<?php _e( 'Do not filter', 'book-a-room' ); ?>
					</option>
					<?php
					foreach ( $branchList as $key => $val ) {
						# Branch Names
						$selected = ( $externals[ 'branchID' ] == $val[ 'branchID' ] ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo 'branch-' . $key; ?>" <?php echo $selected; ?> class="disabled">
						<?php echo $val[ 'branchDesc' ]; ?>
					</option>
					<?php
					# No location if available
					if ( true == $val[ 'branch_hasNoloc' ] ) {
						$selected = ( $externals[ 'noloc-branchID' ] == $val[ 'branchID' ] ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo 'noloc-' . $val[ 'branchID' ]; ?>" <?php echo $selected; ?> class="noloc">&nbsp;&nbsp;&nbsp;&nbsp;
						<?php _e( 'No location required', 'book-a-room' ); echo ' - ' . $val['branchDesc']; ?>
					</option>
					<?php
					}

					# get all room conts
					$curRoomList = $roomContList[ 'branch' ][ $val[ 'branchID' ] ];
					foreach ( $curRoomList as $roomContID ) {
						$selected = ( $externals[ 'roomID' ] == $roomContID ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo $roomContID; ?>" <?php echo $selected; ?>> &nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo $roomContList[ 'id' ][ $roomContID ][ 'desc' ] . '&nbsp;[' . $roomContList[ 'id' ][ $roomContID ][ 'occupancy' ] . ']'; ?>
					</option>
					<?php
					}
					}

					?>
				</select>
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Start Date', 'book-a-room' ); ?>
			</td>
			<td><input name="startDate" type="text" id="startDate" value="<?php echo $externals['startDate']; ?>">
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'End Date', 'book-a-room' ); ?>
			</td>
			<td><input name="endDate" type="text" id="endDate" value="<?php echo $externals['endDate']; ?>">
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Published', 'book-a-room' ); ?>
			</td>
			<td>
				<select name="published" id="published">
					<?php
					# published status drop down
					foreach ( array( null => 'Do not filter', 'published' => 'Published', 'noPublished' => 'Not Published' ) as $key => $val ) {
						$selected = ( $externals[ 'published' ] == $key ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo $key; ?>" <?php echo $selected;?>>
						<?php echo $val; ?>
					</option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Registration type', 'book-a-room' ); ?>
			</td>
			<td>
				<select name="regType" id="regType">
					<?php
					# make regType list drop down
					$selected = ( empty( $externals[ 'regType' ] ) or $externals[ 'regType' ] == NULL ) ? ' selected="selected"' : NULL;
					?>
					<option value="" <?php echo $selected;?>>
						<?php _e( 'Do not filter', 'book-a-room' );  ?>
					</option>
					<?php
					foreach ( array( 'no' => 'No Registration Required', 'yes' => 'Registration Required', 'staff' => 'Staff event' ) as $key => $val ) {
						$selected = ( !empty( $externals[ 'regType' ] ) and $externals[ 'regType' ] == $key ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo $key; ?>" <?php echo $selected;?>>
						<?php echo $val; ?>
					</option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Search Terms', 'book-a-room' ); ?>
			</td>
			<td><input name="searchTerms" type="text" id="seachTerms" value="<?php echo $externals['searchTerms']; ?>">
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Age Group', 'book-a-room' ); ?><br/>
				<span style="font-size:.8em">
					<?php _e( 'Hold down control to select multiple entries.', 'book-a-room' ); ?>
				</span>
			</td>
			<td>
				<select name="ageGroup[]" size="4" multiple="multiple" id="ageGroup">
					<?php
					# make age groups
					$ageList = bookaroom_settings_age::getNameList();
					foreach ( $ageList[ 'active' ] as $key => $val ) {
						$selected = ( !empty( $externals[ 'ageGroup' ] ) and in_array( $val[ 'age_id' ], $externals[ 'ageGroup' ] ) ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo $val[ 'age_id' ]; ?>" <?php echo $selected; ?>>
						<?php echo $val[ 'age_desc' ]; ?>
					</option>
					<?php
					}
					?>
				</select>
				<input type="button" name="resetAge" id="resetAge" value="<?php _e( 'Clear', 'book-a-room' ); ?>"/>
			</td>
		</tr>
		<tr class="searchArea">
			<td>
				<?php _e( 'Categories', 'book-a-room' ); ?><br/>
				<span style="font-size:.8em">
					<?php _e( 'Hold down control to select multiple entries.', 'book-a-room' ); ?>
				</span>
			</td>
			<td>
				<select name="categoryGroup[]" size="4" multiple="multiple" id="categoryGroup">
					<?php
					# make category groups
					$categoryList = bookaroom_settings_categories::getNameList();
					foreach ( $categoryList[ 'active' ] as $key => $val ) {
						$selected = ( !empty( $externals[ 'categoryGroup' ] ) and in_array( $val[ 'categories_id' ], $externals[ 'categoryGroup' ] ) ) ? ' selected="selected"' : NULL;
						?>
					<option value="<?php echo $val['categories_id']; ?>" <?php echo $selected; ?>>
						<?php echo $val['categories_desc']; ?>
					</option>
					<?php
					}
					?>
				</select>
				<input type="button" name="resetCats" id="resetCats" value="<?php _e( 'Clear', 'book-a-room' ); ?>"/>
			</td>
		</tr>
		<tr class="searchArea">
			<td colspan="2" align="center"><input name="action" type="hidden" id="action" value="filterResults">
				<input type="submit" name="submit" id="submit_search" value="Submit">
			</td>
		</tr>
	</table>
</form>
<?php
# if no search end the page, don't show results
if ( $searched !== true ) {
	echo '</form>';
	return;
}

# Error message
if ( !empty( $errorMSG ) ) {
	?>
	<p>
		<h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3>
	</p> 
	<?php
}

# Search results
?>
<h1>
	<?php _e( 'Search Results' , 'book-a-room' ); ?>
</h1>
<form name="form2" method="post" action="?page=bookaroom_event_management_upcoming">
	<div style="width: 900px; white-space: nowrap;">
		<div style="display: inline-block; width: 30%; white-space: normal;">
			<h2><em><?php /* translators: This appears like [number of search results] result(s), per page: [number of items chosen per page] */ printf( __( '%s result(s), per page: %s', 'book-a-room' ), number_format( $rowCount ), $per_page ); ?></em></h2>
			<h3>
				<input type="submit" name="submit_count" id="submit_10" value="10">
				<input type="submit" name="submit_count" id="submit_25" value="25">
				<input type="submit" name="submit_count" id="submit_50" value="50">
				<input type="submit" name="submit_count" id="submit_100" value="100">
				<input type="submit" name="submit_count" id="submit_all" value="<?php _e( 'All', 'book-a-room' ); ?>">
			</h3>
		
		</div>
		<div style="display: inline-block; width: 70%; white-space: normal; text-align:right">
			<h3>
				<?php _e( 'Pages', 'book-a-room' ); ?>
			</h3>
			<h2><input type="submit" name="submit_nav" id="submit_nav_prev" value="Prev"<?php if( $page_num <= 1 ) echo ' disabled'; ?>>&nbsp;&nbsp;&nbsp;
			<select name="pageNum" id="pageNum">
		<?php
		for( $t=1; $t <= $totalPages; $t++ ) {
			$selected = ( $page_num == $t ) ? ' selected="selected"' : NULL;
			?><option value="<?php echo $t; ?>"<?php echo $selected;  ?>><?php echo $t; ?></option><?php
		}
		?>
			</select>/ <?php echo $totalPages; ?>
				<input name="action" type="hidden" id="action" value="filterResults">
				<input name="roomID" type="hidden" id="roomID" value="<?php echo $externals['roomID']; ?>">
				<input name="endDate" type="hidden" id="endDate" value="<?php echo $externals['endDate']; ?>">
				<input name="published" type="hidden" id="published" value="<?php echo $externals['published']; ?>">
				<input name="searchTerms" type="hidden" id="searchTerms" value="<?php echo $externals['searchTerms']; ?>">
				<input name="startDate" type="hidden" id="startDate" value="<?php echo $externals['startDate']; ?>">
		<?php
		# create arrays foro age and category
		#'ageGroup', 'categoryGroup',

		if( !empty( $externals['ageGroup'] ) and count( $externals['ageGroup'] > 0 ) ) {
			foreach( $externals['ageGroup'] as $key => $val ) {
				?><input name="ageGroup[]" type="hidden" id="ageGroup-<?php echo $val; ?>" value="<?php echo $val; ?>"<?php
			}
		}

		if( !empty( $externals['categoryGroup'] ) and count( $externals['categoryGroup'] > 0 ) ) {
			foreach( $externals['categoryGroup'] as $key => $val ) {
				?><input name="categoryGroup[]" type="hidden" id="categoryGroup-<?php echo $val; ?>" value="<?php echo $val; ?>"<?php
			}
		}
				?>
			<input name="regType"type="hidden" id="regType" value="<?php echo $externals['regType']; ?>">
			<input name="branchID" type="hidden" id="branchID" value="<?php echo $externals['branchID']; ?>">
			<input name="noloc-branchID" type="hidden" id="noloc-branchID" value="<?php echo $externals['noloc-branchID']; ?>">
			<input name="submit_nav" type="submit" id="submit_prev" title="Switch Page" value="<?php _e( 'Go!', 'book-a-room' ); ?>">&nbsp;&nbsp;&nbsp;
			<input type="submit" name="submit_nav" id="submit_nav_next" value="Next"<?php if( $page_num >= $totalPages ) echo ' disabled'; ?>>
		</div>
	</div>

	<table width="100%" class="tableMain freeWidth">
		<tr>
			<td><?php _e( 'Event ID', 'book-a-room' ); ?></td>
			<td><?php _e( 'Date/Time', 'book-a-room' ); ?></td>
			<td><?php _e( 'Title/Desc', 'book-a-room' ); ?></td>
			<td><?php _e( 'Branch/Room', 'book-a-room' ); ?></td>
			<td><?php _e( 'Registrations', 'book-a-room' ); ?></td>
			<td><?php _e( 'Actions', 'book-a-room' ); ?></td>
		</tr>
		<?php
		if( count( $results ) == 0 ) {
		?>
		<tr>
			<td colspan="6"><?php _e( 'Nothing matches your search criteria.', 'book-a-room' ); ?></td>
		</tr>
		
		<?php
		} else {
			foreach( $results as $key => $val ) {
			?>
		<tr>
			<td><?php echo $val['ti_id']; ?></td>
			<td nowrap="nowrap">
			<?php
				$startTime = strtotime( $val['ti_startTime'] );
				$endTime = strtotime( $val['ti_endTime'] );
				echo date( 'l, F jS, Y', $startTime );
				
				$startTime = date( 'g:i a', $startTime );
				$endTime = date( 'g:i a', $endTime );
					
				if( $startTime == '12:00 am' and $endTime == '11:59 pm' ) {
					$times = 'All Day';
				} else {
					$times = $startTime . ' to ' . $endTime;
				}
				
				?><br/><em><?php echo $times; ?></em></td>
		
			<td><p><strong><?php echo $val['ev_title'];	?></strong><br/> <?php echo $val['ev_desc']; ?><br/> <?php echo $val['ti_extraInfo'];?></p></td>
			
			<td nowrap="nowrap"><p><strong>
			<?php
				# branch and room
				if( empty( $val['ti_roomID'] ) and !empty( $val['ti_noLocation_branch'] ) ) {
						$room = 'No location required';
						$branch = $branchList[$val['ti_noLocation_branch']]['branchDesc'];
				} else {
						$room = ( empty( $roomContList['id'][$val['ti_roomID']]['desc'] ) ) ? 'None' : $roomContList['id'][$val['ti_roomID']]['desc'];
						$branch = ( empty( $roomContList['id'][$val['ti_roomID']] ) ) ? 'None' : $branchList[$roomContList['id'][$val['ti_roomID']]['branchID']]['branchDesc'];
				}
				
				echo $branch;
				?></strong><br/><?php echo $room; ?></p></td>
			
			<td nowrap="nowrap">
			<?php 
				#staff Event
				if( $val['ev_regType'] == 'staff' ) {
					?><strong>Staff Event<br /></strong><?php
				}
			
			# Registrations
				if( empty( $val['ev_maxReg'] ) ) {
					echo __( 'No registration required.', 'book-a-room' ) . '<br>';
				} else {

				echo count( self::getRegInfo( $val['ti_id'] ) ) . ' / ' . $val['ev_maxReg']; ?><br/>
				
				<a href="?page=bookaroom_event_management&amp;action=manage_registrations&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Manage Registrations', 'book-a-room' ); ?></a><br>				
				<?php
				}
				?>
				
				<a href="?page=bookaroom_event_management&amp;action=manage_attendance&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Attendance Data', 'book-a-room' ); ?></a><br/>
				
				<?php
				if( false == ( $eventLink = get_option( "bookaroom_eventLink" ) ) ) {
					$eventLink = NULL;
				}
				?><a href="<?php echo $eventLink; ?>?action=viewEvent&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'View & Register', 'book-a-room' ); ?></a><br/>
			</td>
			
			<td nowrap="nowrap">
			<?php
				
				
			if( $val['eventCount'] == 1 ) {
				?><a href="?page=bookaroom_event_management&action=edit_single&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Edit', 'book-a-room' ); ?></a><br/>
				<a href="?page=bookaroom_event_management&action=delete&amp;eventID=<?php echo $val['ti_id']; ?>"><?php _e( 'Delete', 'book-a-room' ); ?></a><br/><?php
			} else {
				?><a href="?page=bookaroom_event_management&amp;action=edit_instance&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Edit Instance', 'book-a-room' ); ?></a><br/>
				<a href="?page=bookaroom_event_management&amp;action=edit_event&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Edit Event', 'book-a-room' ); ?></a><br/>
				<a href="?page=bookaroom_event_management&amp;action=delete_instance&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Delete Instance', 'book-a-room' ); ?></a><br/>
				<a href="?page=bookaroom_event_management&amp;action=delete_multi&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new"><?php _e( 'Delete Event', 'book-a-room' ); ?></a><br/>
				<?php
			}
				?>
				<a href="?page=bookaroom_event_management&amp;action=copy&amp;eventID=<?php echo $val['ti_id']; ?>" target="_new">Copy</a>
			</td>			
		</tr>
			<?php 
			}
		}
		?>
	</table>
	<h2><a href="#top">Back to the top</a></h2>
</form>