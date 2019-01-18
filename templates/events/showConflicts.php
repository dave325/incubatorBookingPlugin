<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Event Conflicts', 'book-a-room' ); ?>
</h2>
<form name="form1" method="post" action="">
	<table class="tableMain freeWidth">
		<tr>
			<td><?php _e( 'Date', 'book-a-room' ); ?></td>
			<td><?php _e( 'Time', 'book-a-room' ); ?></td>
			<td><?php _e( 'Type', 'book-a-room' ); ?></td>
			<td><?php _e( 'Desc', 'book-a-room' ); ?></td>
			<td><?php _e( 'Location', 'book-a-room' ); ?></td>
			<td><?php _e( 'Action', 'book-a-room' ); ?></td>
		</tr>
		<?php
		foreach( $dateList as $key => $val ) {
			if( !empty( $val['conflicts'] ) ) {
				if( empty( $val['usedRoomsClosings'] ) ) {
					$val['usedRoomsClosings'] = array();
				}
				$rowCount = count( $val['conflicts'] );
				$firstRow = true;
				foreach( $val['conflicts'] as $cat => $mouse ) {
					$timeStamp = strtotime( $mouse['startTime'] );
					$time = ( !empty( $mouse['allDay'] ) ) ? 'All Day' : date( 'g:i a', $timeStamp ). ' - ' .date( 'g:i a', strtotime( $mouse['endTime'] ) );					
					if( is_array( $mouse['roomID'] ) ) {
						$location 		= self::makeClosingRoomList( $mouse['roomID'], $mouse['allClosed'], $branchList, $roomContList, $roomList );
					} else {
						$room 			= $roomContList['id'][$mouse['roomID']]['desc'];
						$branch 		= $branchList[$roomContList['id'][$mouse['roomID']]['branchID']]['branchDesc'];
						$location		= '<strong>'.$branch.'</strong><br />'.$room;
					}
					if( $firstRow ) {
						$firstRow = false;
					?>
		<tr>
			<td><?php echo date( 'm/d/y', $timeStamp); ?></td>
			<td nowrap="nowrap"><?php echo $time; ?></td>
			<td><?php echo $mouse['type']; ?></td>
			<td><?php echo $mouse['desc']; ?></td>
			<td><?php echo $location; ?></td>
			<td rowspan="<?php echo $rowCount; ?>" nowrap="nowrap">
				<a href="?page=bookaroom_event_management&action=edit_event&eventID=<?php echo $mouse['eventID']; ?>" target="_blank"><?php _e( 'View Event', 'book-a-room' ); ?></a>
				<!-- <input name="delete[< ?php echo date( 'm/d/y', $timeStamp); ?>]" type="checkbox" value="< ?php echo date( 'm/d/y', $timeStamp); ?>"/>< ?php _e( 'Delete', 'book-a-room' ); ?>-->
				
			</td>
		</tr>
		<?php
					} else {
		?>
		<tr>
			<td><?php echo date( 'm/d/y', $timeStamp); ?></td>
			<td nowrap="nowrap"><?php echo $time; ?></td>
			<td><?php echo $mouse['type']; ?></td>
			<td><?php echo $mouse['desc']; ?></td>
			<td><?php echo $location; ?></td>
		</tr>
		<?php
					}
				}
			} elseif( !empty( $delete ) and array_search( date( 'm/d/y', $val['start'] ), $delete )  ) {
				
		?>
		<tr>
			<td><?php echo date( 'm/d/y', $val['start'] ); ?></td>
			<td colspan="4" align="center" nowrap="nowrap"><?php _e( 'No conflict - Deleted', 'book-a-room' ); ?></td>
			<td nowrap="nowrap">
				<input name="delete[<?php echo date( 'm/d/y', $val['start']); ?>]" type="checkbox" value="<?php echo date( 'm/d/y', $val['start']); ?>" checked="checked" /> <?php _e( 'Delete', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php		
			} else {
		?>
		<tr>
			<td><?php echo date( 'm/d/y', $val['start'] ) ?></td>
			<td colspan="5" align="center"><?php _e( 'No conflict', 'book-a-rooom' ); ?></td>
		</tr>
		<?php
				}
			}
		
		?> 
		<!--<tr>
			<td colspan="6" align="center">
				<input type="hidden" name="recurrence" id="recurrence" value="<?php echo $sessionVars['recurrence']; ?>"/>
				<input type="hidden" name="action" id="action" value="checkConflicts"/>
				<input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' );?>">
			</td>
		</tr>-->
	</table>
</form>