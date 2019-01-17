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
	} );

	function copyToClipboard( element ) {
		var $temp = $( "<input>" );
		$( "body" ).append( $temp );
		$temp.val( $( element ).val() ).select();
		document.execCommand( "copy" );
		$temp.remove();
	}
</script>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	_e( 'View Registrations', 'book-a-room' );
	?>
</h2>
<form name="form1" method="post" action="">
	<table width="100%" class="tableMain freeWidth">
		<tr>
			<td>
				<?php _e( 'Event ID', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Date/Time', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Title/Desc', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Branch/Room', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $eventInfo['ti_id']; ?>
			</td>
			<td nowrap="nowrap">
				<?php echo date('l, F jS, Y', strtotime( $eventInfo['ti_startTime'] ) ); ?><br/>
				<em>
					<?php echo $time; ?>
				</em>
			</td>
			<td>
				<strong>
					<?php echo $eventInfo['ev_title']; ?>
				</strong><br/>
				<?php echo $eventInfo['ev_desc']; ?>
			</td>
			<td nowrap="nowrap">
				<p>
					<strong>
						<?php echo $branch; ?>
					</strong><br/>
					<?php echo $room; ?>
				</p>
			</td>
		</tr>
	</table>
	<h3>
		<?php _e( 'Registrations', 'book-a-room' ); ?>
	</h3>
	<?php
	# check for registrations
	$emailListArr = array();
	if ( count( $registrations ) !== 0 ) {
		foreach ( $registrations as $key => $val ) {
			$trimmedEmail = trim( $val[ 'reg_email' ] );
			if ( !empty( $trimmedEmail ) ) {
				$emailListArr[] = $trimmedEmail;
			}
		}
		$emailList = implode( ',', $emailListArr );
		$emailListSemicolons = implode( ';', $emailListArr );
		?>
	<h3><?php _e( 'Using comma separation, for most mail programs.', 'book-a-room' ); ?></h3>
	<p>
		<a href="mailto:?bcc=<?php echo $emailList; ?>">
			<?php _e( 'Click here to send an email to everyone on the Registration and Waiting list.', 'book-a-room' ); ?>
		</a>
	</p>
	<input name="emailList" type="text" id="emailList" value="<?php echo $emailList; ?>">
	<a href="#" onclick="copyToClipboard('#emailList')">
		<?php _e( 'Copy registration emails to clipboard.', 'book-a-room' );?>
	</a><br>
	<h3><?php _e( 'Using semicolon separation, for Outlook.', 'book-a-room' ); ?></h3>
	<p>
		<a href="mailto:?bcc=<?php echo $emailListSemicolons; ?>">
			<?php _e( 'Click here to send an email to everyone on the Registration and Waiting list.', 'book-a-room' ); ?>
		</a>
	</p>
	<input name="emailListSemicolons" type="text" id="emailListSemicolons" value="<?php echo $emailListSemicolons; ?>">
	<a href="#" onclick="copyToClipboard('#emailListSemicolons')">
		<?php _e( 'Copy registration emails to clipboard.', 'book-a-room' );?>
	</a><br><br>
	<?php
	}
	?>
	<table class="tableMain">
		<tr>
			<td>
				<?php _e( 'Name', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Phone', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Email', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Notes', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Date', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Action', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		if ( count( $registrations ) == 0 ) {
			?>
		<tr>
			<td colspan="6">
				<?php _e( 'No registrations.', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		} else {
			foreach ( array_slice( $registrations, 0, $eventInfo[ 'ev_maxReg' ] ) as $key => $val ) {
				?>
		<tr>
			<td>
				<?php echo $val['reg_fullName']; ?>
			</td>
			<td>
				<?php echo $val['reg_phone']; ?>
			</td>
			<td>
				<?php
				if ( !empty( $val[ 'reg_email' ] ) ) {
					?>
				<a href="mailto:<?php echo $val['reg_email']; ?>">
					<?php echo $val['reg_email']; ?>
				</a>
				<?php
				}
				?>
			</td>
			<td>
				<?php echo $val['reg_notes']; ?>
			</td>
			<td>
				<?php echo date('m/d/Y', strtotime( $val['reg_dateReg'] ) ); ?><br/>
				<?php echo date('g:i:s a', strtotime( $val['reg_dateReg'] ) ); ?>
			</td>
			<td>
				<p>
					<a href="?page=bookaroom_event_management&amp;action=edit_registration&amp;regID=<?php echo $val['reg_id']; ?>&amp;eventID=<?php echo $eventInfo['ti_id']; ?>">
						<?php _e( 'Edit', 'book-a-room' ); ?>
					</a><br>
					<a href="?page=bookaroom_event_management&amp;action=delete_registration&amp;regID=<?php echo $val['reg_id']; ?>&amp;eventID=<?php echo $eventInfo['ti_id']; ?>">
						<?php _e( 'Delete', 'book-a-room' ); ?>
					</a>
				</p>
			</td>
		</tr>
		<?php
		}
		}
		?>
	</table>
	<br/>
	<h3>
		<?php _e( 'Waiting List', 'book-a-room' ); ?>
	</h3>

	<table class="tableMain">
		<tr>
			<td>
				<?php _e( 'Name', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Phone', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Email', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Notes', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Date', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Action', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		if ( count( array_slice( $registrations, $eventInfo[ 'ev_maxReg' ] ) ) == 0 ) {
			?>
		<tr>
			<td colspan="6">
				<?php _e( 'No waiting list.', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		} else {
			foreach ( array_slice( $registrations, $eventInfo[ 'ev_maxReg' ] ) as $key => $val ) {
				?>
		<tr>
			<td>
				<?php echo $val['reg_fullName']; ?>
			</td>
			<td>
				<?php echo  $val['reg_phone']; ?>
			</td>
			<td>
				<?php
				if ( !empty( $val[ 'reg_email' ] ) ) {
					?>
				<a href="mailto:<?php echo $val['reg_email']; ?>">
					<?php echo $val['reg_email']; ?>
				</a>
				<?php
				}
				?>
			</td>
			<td>
				<?php echo $val['reg_phone']; ?>
			</td>
			<td>
				<?php echo date('m/d/Y', strtotime( $val['reg_dateReg'] ) ); ?><br/>
				<?php echo date('g:i:s a', strtotime( $val['reg_dateReg'] ) ); ?>
			</td>
			<td>
				<p>
					<a href="?page=bookaroom_event_management&action=edit_registration&regID=<?php echo $val['reg_id']; ?>&eventID=<?php echo $eventInfo['ti_id']; ?>">
						<?php _e(' Edit' , 'book-a-room' ); ?>
					</a><br>
					<a href="?page=bookaroom_event_management&amp;action=delete_registration&amp;regID=<?php echo $val['reg_id']; ?>&amp;eventID=<?php echo $eventInfo['ti_id']; ?>">
						<?php _e( 'Delete', 'book-a-room' ); ?>
					</a>
				</p>
			</td>
		</tr>
		<?php
		}
		}
		?>
	</table>
</form>