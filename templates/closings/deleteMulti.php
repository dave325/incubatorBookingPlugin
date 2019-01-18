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
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Closings', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Delete Expired Closings', 'book-a-room' ); ?>
</h2>
<p>
	<?php _e( 'Deleting closings is permanent. Please double check the closings if you want to continue.', 'book-a-room' ); ?>
</p>
<form id="formRooms" name="formRooms" method="post" action="?page=bookaroom_Settings_Closings">
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
				<?php _e( 'Rooms', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		foreach ( $multiList as $key => $val ) {
			switch( $closings['expired'][$val]['type'] ) {
				case 'date':
					$dateStamp = mktime( 0, 0, 0,  $closings['expired'][$val]['startMonth'],  $closings['expired'][$val]['startDay'], $closings['expired'][$val]['startYear'] );	
					$dateDisp =  ( $closings['expired'][$val]['reoccuring'] == TRUE ) ? date( 'D, M jS', $dateStamp ) : date( 'D, M jS, Y', $dateStamp );					
					break;				
				case 'range':
					$startStamp = mktime( 0, 0, 0,  $closings['expired'][$val]['startMonth'],  $closings['expired'][$val]['startDay'],  $closings['expired'][$val]['startYear'] );
					$endStamp = mktime( 23, 59, 59,  $closings['expired'][$val]['endMonth'],  $closings['expired'][$val]['endDay'], $closings['expired'][$val]['endYear'] );
					$startTime =  ( $closings['expired'][$val]['reoccuring'] == TRUE ) ? date( 'D, M jS', $startStamp ) : date( 'D, M jS, Y', $startStamp );
					$endTime =  ( $closings['expired'][$val]['reoccuring'] == TRUE ) ? date( 'D, M jS', $endStamp ) : date( 'D, M jS, Y', $endStamp );
					$dateDisp = $startTime.' - <br />'.$endTime;
					break;
					
				default:
					die( 'Wrong date type in meeting-room-closings > showDeleteMulti['.$closings['expired'][$val]['type'].']' );
					break;
			}
			if( $closings['expired'][$val]['allClosed'] == TRUE ) {
				$rooms = 'All';
			} else {
				$rooms = self::makeRooms( $closings['expired'][$val]['roomsClosed'] );
			}
			?>
		<tr>
			<td><?php echo $closings['expired'][$val]['closingName']; ?></td>
			<td nowrap="nowrap"><?php echo $dateDisp; ?></td>
			<td><?php echo ucfirst( $closings['expired'][$val]['type'] ); ?></td>
			<td nowrap="nowrap"><?php echo $rooms; ?>
				<input name="closingMulti[]" type="hidden" id="closingMulti_<?php echo $closings['expired'][$val]['closingID']; ?>" value="<?php echo $closings['expired'][$val]['closingID']; ?>"/>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="4" align="center"><input name="action" type="hidden" id="action" value="deleteMultiCheck"/>
				<input type="submit" name="button" id="button" value="<?php _e( 'Continue and Delete', 'book-a-room' ); ?>"/>
			</td>
		</tr>
	</table>
</form>
<p><a href="?page=bookaroom_Settings_Closings"><?php _e( 'Return to Closings Home.', 'book-a-room' ); ?></a>
</p>