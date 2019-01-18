<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2 id="top">
		<?php _e( 'Book a Room - Attendance', 'book-a-room' ); ?>
	</h2>
</div>
<form name="form1" method="post" action="?page=bookaroom_event_management">
  <table class="tableMain wider">
    <tr>
      <td><?php _e( 'Event ID', 'book-a-room' ); ?></td>
      <td><?php _e( 'Date/Time', 'book-a-room' ); ?></td>
      <td><?php _e( 'Title/Desc', 'book-a-room' ); ?></td>
      <td><?php _e( 'Branch/Room', 'book-a-room' ); ?></td>
      <td><?php _e( 'Registrations', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td><?php echo $eventInfo['ti_id']; ?></td>
      <td nowrap="nowrap"><?php echo date('l, F jS, Y', strtotime( $eventInfo['ti_startTime'] ) ); ?><br />
        <em><?php echo $time; ?></em></td>
      <td><strong><?php echo $eventInfo['ev_title']; ?></strong><br />
        <?php echo $eventInfo['ev_desc']; ?></td>
      <td nowrap="nowrap"><p><strong><?php echo $branch;?></strong><br />
          <?php echo $room; ?></p></td>
      <td nowrap="nowrap"><?php echo count( $registrations ) . ' / ' . $eventInfo['ev_maxReg']; ?></td>
    </tr>
  </table>
  <br />
  <?php
	# Display Errors if there are any
	if ( !empty( $errorMSG ) ) {
	?><p><h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3></p><?php 
	}
	?>
  <table class="tableMain">
    <tr>
      <td colspan="2"><?php _e( 'Attendance Information', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td><?php _e( 'Num. Attended', 'book-a-room' ); ?></td>
      <td><input name="attCount" type="text" id="attCount" value="<?php echo $attCount; ?>" /></td>
    </tr>
    <tr>
      <td><?php _e( 'Notes', 'book-a-room' ); ?></td>
      <td><textarea name="attNotes" id="attNotes" cols="45" rows="5"><?php echo $attNotes; ?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="eventID" type="hidden" id="eventID" value="<?php echo $eventInfo['ti_id']; ?>" />
        <input name="action" type="hidden" id="action" value="edit_attendance" />
        <input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>" /></td>
    </tr>
  </table>
  <p><a href="?page=bookaroom_event_management_upcoming"><?php _e( 'Cancel and go back to search.', 'book-a-room' ); ?></a></p>
</form>
