<?php
#date				
$reoccuring = __( 'No', 'book-a-room' );
switch ( $closingInfo[ 'selType' ] ) {
	case 'range':
		if ( $closingInfo[ 'dateRange_reoccuring' ] == TRUE ) {
			$dateDesc = 'D, M jS';
			$year = date( 'y' );
			$reoccuring = __( 'Yes', 'book-a-room');
		} else {
			$dateDesc = 'D, M jS Y';
			$year = $closingInfo[ 'date_end_year' ];
		}
		$startTime = mktime( 1, 1, 1, $closingInfo[ 'date_start_month' ], $closingInfo[ 'date_start_day' ], $year );
		$endTime = mktime( 1, 1, 1, $closingInfo[ 'date_end_month' ], $closingInfo[ 'date_end_day' ], $year );
		$date = date_i18n( $dateDesc, $startTime ) . ' - <br />' . date_i18n( $dateDesc, $endTime );
		break;
	case 'date':
		if ( $closingInfo[ 'date_reoccuring' ] == TRUE ) {
			$dateDesc = 'D, M jS';
			$year = date( 'y' );
			$reoccuring = _e( 'Yes', 'book-a-room' );
		} else {
			$dateDesc = 'D, M jS Y';
			$year = $closingInfo[ 'date_single_year' ];
		}
		$startTime = mktime( 1, 1, 1, $closingInfo[ 'date_single_month' ], $closingInfo[ 'date_single_day' ], $year );
		$date = date_i18n( $dateDesc, $startTime );
		break;
	default:
		$date = sprintf( __( 'Unknown date type: %s.', 'book-a-room' ), $closingInfo['type'] );
		break;
}
#rooms 
switch ( $closingInfo[ 'roomType' ] ) {
	case 'all':
		$rooms = 'All';
		break;
	case 'choose':
		$rooms = self::makeRooms( serialize( $closingInfo[ 'rooms' ] ) );
		break;
}
?>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Closings', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Delete a closing', 'book-a-room' ); ?>
</h2>
<table class="tableMain">
	<tr>
		<td>
			<?php _e( 'Reason', 'book-a-room' ); ?>
		</td>
		<td>
			<?php _e( 'Date', 'book-a-room' ); ?>
		</td>
		<td>
			<?php _e( 'Type', 'book-a-room' ); ?>
		</td>
		<td>
			<?php _e( 'Reoccuring', 'book-a-room' ); ?>
		</td>
		<td>
			<?php _e( 'Rooms', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td valign="top"><?php echo $closingInfo[ 'closingName' ]; ?></td>
		<td valign="top"><?php echo $date; ?></td>
		<td valign="top"><?php echo ucfirst( $closingInfo[ 'selType' ] ); ?></td>
		<td valign="top"><?php echo $reoccuring; ?></td>
		<td valign="top"><?php echo $rooms; ?></td>
	</tr>
</table>
<p>
	<?php _e( 'Deleting a closing is permanent and cannot be undone.', 'book-a-room' );?>
</p>
<p>
	<a class="errorText" href="?page=bookaroom_Settings_Closings&closingID=<?php echo $closingInfo['closingID']; ?>&amp;action=deleteCheck">
		<?php _e( 'Click here to permanantly delete this closing.', 'book-a-room' ); ?>
	</a>
</p>
<p>
	<a href="?page=bookaroom_Settings_Closings">
		<?php _e( 'Return to Closings Home.', 'book-a-room' ); ?>
	</a>
</p>