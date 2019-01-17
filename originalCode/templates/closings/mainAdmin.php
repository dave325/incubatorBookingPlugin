<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
	function checkAll( typeCheck ) {
		typeFinal = "true";
		if ( typeCheck === undefined ) {
			typeFinal = null;
		}

		for ( i = 0; i < document.formRooms[ "closingMulti[]" ].length; i++ ) {
			document.formRooms[ "closingMulti[]" ][ i ].checked = typeFinal;
		}
	}
</script>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Closings', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'New Closing', 'book-a-room' ); ?>
</h2>
<p>
	<a href="?page=bookaroom_Settings_Closings&amp;action=add">
		<?php _e( 'Add a new closing.', 'book-a-room' ); ?>
	</a>
</p>
<p>&nbsp;</p>
<h2>
	<?php _e( 'Current Closings', 'book-a-room' ); ?>
</h2>
<?php
if ( empty( $closings[ 'live' ] ) ) {
	?>
<h3>
	<?php _e( 'There are currently no scheduled closings.', 'book-a-room' ); ?>
</h3>
<?php
} else {
	?>
<table class="tableMain freeWidth">
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
		<td>
			<?php _e( 'Actions', 'book-a-room' ); ?>
		</td>
	</tr>
	<?php
	foreach ( $closings[ 'times' ][ 'live' ] as $key => $val ) {
		$reoccuring = ( empty( $closings[ 'live' ][ $key ][ 'reoccuring' ] ) ) ? 'No' : 'Yes';
		$dateDesc = ( $closings[ 'live' ][ $key ][ 'reoccuring' ] == TRUE ) ? 'D, M jS' : 'D, M jS, Y';
		switch ( $closings[ 'live' ][ $key ][ 'type' ] ) {
			case 'range':
				$date = date_i18n( $dateDesc, $closings[ 'live' ][ $key ][ 'startTime' ] ) . ' - <br />' . date_i18n( $dateDesc, $closings[ 'live' ][ $key ][ 'endTime' ] );
				break;
			case 'date':
				$date = date_i18n( $dateDesc, $closings[ 'live' ][ $key ][ 'startTime' ] );
				break;
			default:
				$date = sprintf( __( 'Unknown date type: %s. ', 'book-a-room' ), $closings[ 'live' ][ $key ][ 'type' ] );
				break;
		}
		?>
	<tr>
		<td>
			<?php echo  $closings['live'][$key]['closingName']; ?>
		</td>
		<td nowrap="nowrap">
			<?php echo $date; ?>
		</td>
		<td>
			<?php echo ucfirst( $closings['live'][$key]['type'] ); ?>
		</td>
		<td>
			<?php echo $reoccuring; ?>
		</td>
		<td nowrap="nowrap">
			<?php
			switch ( $closings[ 'live' ][ $key ][ 'allClosed' ] ) {
				case false:
					$rooms = self::makeRooms( $closings[ 'live' ][ $key ][ 'roomsClosed' ] );
					break;
				default:
					$rooms = 'All';
					break;
			}
			echo $rooms;
				?>
		</td>
		<td align="right" nowrap="nowrap">
			<a href="?page=bookaroom_Settings_Closings&amp;closingID=<?php echo $closings[ 'live' ][$key]['closingID']; ?>&amp;action=edit">
				<?php _e( 'Edit', 'book-a-room' ); ?></a> |
			<a href="?page=bookaroom_Settings_Closings&amp;closingID=<?php echo $closings[ 'live' ][$key]['closingID']; ?>&amp;action=delete">
				<?php _e( 'Delete', 'book-a-room' ); ?>
			</a>
		</td>
	</tr>
	<?php
	}
	?>
</table>
<?php
}
?>
<p>&nbsp;</p>
<h2>
	<?php _e( 'Expired Closings', 'book-a-room' ); ?>
</h2>
<?php
if ( !empty( $multiError ) ) {
	?>
<p style="color:red">
	<strong>
		<?php echo $multiError; ?>
	</strong>
</p>
<?php
}

if ( empty( $closings[ 'expired' ] ) ) {
	?>
	<h3>
		<?php _e( 'There are currently no expired closings.', 'book-a-room' ); ?>
	</h3>
	<?php
} else {
		?>
	<form id="formRooms" name="formRooms" method="post" action="?page=bookaroom_Settings_Closings">
		<table class="tableMain freeWidth">
			<tr>
				<td><?php _e( 'Reason', 'book-a-room' ); ?></td>
				<td><?php _e( 'Date', 'book-a-room' ); ?></td>
				<td><?php _e( 'Type', 'book-a-room' ); ?></td>
				<td><?php _e( 'Reoccuring', 'book-a-room' ); ?></td>
				<td><?php _e( 'Rooms', 'book-a-room' ); ?></td>
				<td><?php _e( 'Actions', 'book-a-room' ); ?></td>
				<td><?php _e( 'Delete', 'book-a-room' ); ?></td>
			</tr>
			<?php
			foreach ( $closings['times']['expired'] as $key => $val ) {
				switch( $closings[ 'expired' ][$key]['type'] ) {
					case 'range':						
						$date = date_i18n( $dateDesc, $closings[ 'expired' ][$key]['startTime'] ) . ' - <br />' . date( $dateDesc, $closings[ 'expired' ][$key]['endTime'] );
						break;
					case 'date':
						$date = date_i18n( $dateDesc, $closings[ 'expired' ][$key]['startTime'] );
						break;						
					default:
						$date = sprintf( __( 'Unknown date type: %s.', 'book-a-room' ), $closings[ 'expired' ][$key]['type'] );
						break;						
				}
				
				switch( $closings[ 'expired' ][$key]['allClosed'] ) {
					case false:
						$rooms = self::makeRooms( $closings[ 'expired' ][$key]['roomsClosed'] );
						break;						
					default:
						$rooms = 'All';
						break;						
				}
				$reoccuring = ( empty( $closings[ 'expired' ][$key]['reoccuring'] ) ) ? 'No' : 'Yes';
				?>
			<tr>
				<td><label for="closingID_<?php echo $closings[ 'expired' ][$key]['closingID']; ?>"><?php echo $closings[ 'expired' ][$key]['closingName']; ?></label>
				</td>
				<td nowrap="nowrap"><label for="closingID_<?php echo $closings[ 'expired' ][$key]['closingID']; ?>"><?php echo $date; ?></label>
				</td>
				<td><label for="closingID_<?php echo $closings[ 'expired' ][$key]['closingID']; ?>"><?php echo ucfirst( $closings[ 'expired' ][$key]['type'] ); ?></label>
				</td>
				<td><label for="closingID_<?php echo $closings[ 'expired' ][$key]['closingID']; ?>"><?php echo $reoccuring; ?></label>
				</td>
				<td nowrap="nowrap"><label for="closingID_<?php echo $closings[ 'expired' ][$key]['closingID']; ?>"><?php echo $rooms; ?></label>
				</td>
				<td align="right" nowrap="nowrap"><a href="?page=bookaroom_Settings_Closings&amp;closingID=<?php echo $closings[ 'expired' ][$key]['closingID']; ?>&amp;action=edit"><?php _e( 'Edit', 'book-a-room' ); ?></a>
				</td>
				<td><input name="closingMulti[]" type="checkbox" id="closingMulti_<?php echo $closings[ 'expired' ][$key]['closingID']; ?>" value="<?php echo $closings[ 'expired' ][$key]['closingID']; ?>"/>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="7" align="center"><input name="action" type="hidden" id="action" value="deleteMulti"/>
					<input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="button_checkAll" id="button_checkAll" value="<?php _e( 'Check All', 'book-a-room' ); ?>" onclick="checkAll('true');"/> &nbsp;&nbsp;
					<input type="button" name="button_clearAll" id="button_clearAll" value="<?php _e( 'Clear', 'book-a-room' ); ?>" onclick="checkAll();"/> </td>
			</tr>
		</table>
	</form>
<?php
}
?>