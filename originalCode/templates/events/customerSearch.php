<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events - Search Registrations', 'book-a-room' ); ?>
	</h2>
</div>
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
	<?php _e( 'Search Registrations', 'book-a-room' ); ?>
</h2>
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
	} );
</script>
<form name="form1" method="post" action="?page=bookaroom_event_management_customerSearch">
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
						$selected = ( empty( $roomInfo['cur_roomID'] ) and empty( $roomInfo['cur_branchID'] ) ) ? ' selected="selected"' : NULL; ?>
					<option value=""<?php echo $selected; ?>><?php _e( 'Do not filter', 'book-a-room' ); ?></option>
					<?php
					foreach ( self::$branchList as $key => $val ) {
						$selected = ( $roomInfo['cur_branchID'] == $val['branchID'] ) ? ' selected="selected"' : NULL;
						?><option value="branch-<?php echo $key; ?>"<?php echo $selected; ?> class="disabled"><?php echo $val['branchDesc']; ?></option><?php					
						if( true == $val['branch_hasNoloc'] ) {
							$selected = ( $roomInfo['cur_noloc_branchID'] == $val['branchID'] ) ? ' selected="selected"' : NULL;
							?><option value="noloc-<?php echo $val['branchID']; ?>"<?php echo $selected; ?> class="noloc">
								<?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' . sprintf( __( '%s - No location required', 'book-a-room' ), $val['branchDesc'] ); ?>
							</option><?php
						}
						$curRoomList = empty( self::$roomContList['branch'][$val['branchID']] ) ? array() : self::$roomContList['branch'][$val['branchID']];
		
						if( empty( $curRoomList ) ) $curRoomList = array();
						
						# replace values for each room
						foreach( $curRoomList as $roomContID ) {
							$selected = ( $roomInfo['cur_roomID'] == $roomContID ) ? ' selected="selected"' : NULL;
							?><option value="<?php echo $roomContID; ?>"<?php echo $selected; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo self::$roomContList['id'][$roomContID]['desc']; ?>&nbsp;[<?php echo self::$roomContList['id'][$roomContID]['occupancy']; ?>]
								</option><?php
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="searchArea">
			<td><?php _e( 'Start Date', 'book-a-room' ); ?></td>
			<td><input name="startDate" type="text" id="startDate" value="<?php echo $externals['startDate']; ?>">
			</td>
		</tr>
		<tr class="searchArea">
			<td><?php _e( 'End Date', 'book-a-room' ); ?></td>
			<td><input name="endDate" type="text" id="endDate" value="<?php echo $externals['endDate']; ?>">
			</td>
		</tr>
		<tr class="searchArea">
			<td><?php _e( 'Name', 'book-a-room' ); ?></td>
			<td><input name="searchName" type="text" id="searchName" value="<?php echo $externals['searchName']; ?>"/>
			</td>
		</tr>
		<tr class="searchArea">
			<td><?php _e( 'Email', 'book-a-room' ); ?></td>
			<td><input name="searchEmail" type="text" id="searchEmail" value="<?php echo $externals['searchEmail']; ?>"/>
			</td>
		</tr>
		<tr class="searchArea">
			<td><?php _e( 'Phone number', 'book-a-room' ); ?></td>
			<td><input name="searchPhone" type="text" id="searchPhone" value="<?php echo $externals['searchPhone']; ?>">
			</td>
		</tr>
		<tr class="searchArea">
			<td colspan="2" align="center">
				<input name="action" type="hidden" id="action" value="filterResults">
				<input type="submit" name="button" id="button" value="Submit">
			</td>
		</tr>
	</table>
</form>
<?php 
if( $externals['action'] == 'filterResults' ) {
	?>
<br />
<h3><?php _e( 'Search Results', 'book-a-room' ); ?></h3>
<table width="100%" class="tableMain freeWidth">
	<tr>
		<td><?php _e( 'Event ID', 'book-a-room' ); ?></td>
		<td><?php _e( 'Name', 'book-a-room' ); ?></td>
		<td><?php _e( 'Date/Time', 'book-a-room' ); ?></td>
		<td><?php _e( 'Title/Desc', 'book-a-room' ); ?></td>
		<td><?php _e( 'Branch/Room', 'book-a-room' ); ?></td>
		<td><?php _e( 'Registrations', 'book-a-room' ); ?></td>
	</tr>
	<?php
	if ( $externals[ 'action' ] !== 'filterResults' ) {
	?>
	<tr>
		<td colspan="6"><?php _e( 'Nothing matches your search criteria.', 'book-a-room' ); ?></td>
	</tr>
	<?php		
	} else {
		foreach ( $results as $key => $val ) {
			if ( empty( $val[ 'ti_id' ] ) ) {
				continue;
			}
	?>
	<tr>
		<td><?php echo $val[ 'ti_id' ]; ?></td>
		<td nowrap="nowrap"><strong><?php echo $val[ 'reg_fullName' ]; ?></strong>
		<?php 
			#phone number
			if ( !empty( $val[ 'reg_phone' ] ) ) {
			?>
			<br/><?php echo $val[ 'reg_phone' ]; ?>
			<?php
			}
			# email address
			if ( !empty( $val[ 'reg_email' ] ) ) {
			?>
			<br/><?php echo $val[ 'reg_email' ]; ?>
			<?php
			}
			?>
		</td>
		<td nowrap="nowrap"><?php echo date( 'D, M jS Y', strtotime( $val[ 'ti_startTime' ] ) ); ?><br/>
			<em><?php echo date( 'g:i a', strtotime( $val[ 'ti_startTime' ] ) ) . ' to ' . date( 'g:i a', strtotime( $val[ 'ti_endTime' ] ) ); ?></em>
		</td>
		<td>
			<p><strong><?php echo $val[ 'ev_title' ]; ?></strong><br/><?php echo make_brief( $val[ 'ev_desc' ], 100 ); ?>
				<br/>
				<em><?php echo $val[ 'ti_extraInfo' ]; ?></em>
			</p>
		</td>
		<?php
			if ( !empty( $val[ 'ti_noLocation_branch' ] ) ) {
				$branchName = self::$branchList[ $val[ 'ti_noLocation_branch' ] ][ 'branchDesc' ];
				$roomName = 'No location required.';
			} else {
				$branchName = self::$branchList[ self::$roomContList[ 'id' ][ $val[ 'ti_roomID' ] ][ 'branchID' ] ][ 'branchDesc' ];
				$roomName = self::$roomContList[ 'id' ][ $val[ 'ti_roomID' ] ][ 'desc' ];
			}
			?>
		<td nowrap="nowrap">
			<p><strong><?php echo $branchName; ?></strong><br/><?php echo $roomName; ?>
			</p>
		</td>
		<td nowrap="nowrap">
			<p>
				<?php
			if ( $val[ 'regCount' ] > $val[ 'ev_maxReg' ] ) {
				?>
				<em style="color:red">
				<strong><?php printf( __( 'On waiting list [%s/%s]', 'book-a-room' ), $val[ 'regCount' ], $val[ 'ev_maxReg' ]); ?></strong></em>
				<?php
			} else {
				_e( 'Registered', 'book-a-room' );
			}
			?>
			<br/>
				<a href="?page=bookaroom_event_management&amp;action=manage_registrations&amp;eventID=<?php echo $val[ 'ti_id' ]; ?>" target="_blank"><?php _e( 'Manage Registrations', 'book-a-room' ); ?></a>
				</p>
		</td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	}
}
?>