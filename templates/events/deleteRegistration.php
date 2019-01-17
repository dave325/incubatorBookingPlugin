<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Events', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	_e( 'Delete Registration', 'book-a-room' );
	?>
</h2>
<form name="form1" method="post" action="?page=bookaroom_event_management">
  <table class="tableMain">
    <tr>
      <td><?php _e( 'Event ID', 'book-a-room' ); ?></td>
      <td><?php _e( 'Date/Time', 'book-a-room' ); ?></td>
      <td><?php _e( 'Title/Desc', 'book-a-room' ); ?></td>
      <td><?php _e( 'Branch/Room', 'book-a-room' ); ?></td>
      <td><?php _e( 'Registrations', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td><?php echo $eventInfo[ 'ti_id' ]; ?></td>
      <td nowrap="nowrap"><?php echo date( 'l, F jS, Y', strtotime( $eventInfo[ 'ti_startTime' ] ) ); ?><br />
        <em><?php echo $time; ?></em></td>
      <td><strong><?php echo $eventInfo[ 'ev_title' ]; ?></strong><br />
        <?php echo $eventInfo[ 'ev_desc' ]; ?></td>
      <td nowrap="nowrap"><p><strong><?php echo $branch; ?></strong><br />
          <?php echo $room; ?></p></td>
      <td nowrap="nowrap"><?php echo count( $registrations ); ?> / <?php echo $eventInfo[ 'ev_maxReg' ]; ?></td>
    </tr>
  </table>
  <h3><?php _e( 'Registration', 'book-a-room' ); ?></h3>
  <table class="tableMain">
    <tr>
      <td><?php _e( 'Name', 'book-a-room' ); ?></td>
      <td><?php _e( 'Phone', 'book-a-room' ); ?></td>
      <td><?php _e( 'Email', 'book-a-room' ); ?></td>
      <td><?php _e( 'Notes', 'book-a-room' ); ?></td>
      <td><?php _e( 'Date', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td><?php echo $registrations[ $regID ][ 'reg_fullName' ]; ?></td>
      <td><?php echo $regPhone; ?></td>
      <td>
      <?php
		  if ( !empty( $registrations[ $regID ][ 'reg_email' ] ) ) {
		  ?><a href="mailto:<?php echo $registrations[ $regID ][ 'reg_email' ]; ?>"><?php echo $registrations[ $regID ][ 'reg_email' ]; ?></a>
     <?php
		  }
		  ?>
     </td>
      <td><?php echo $registrations[ $regID ][ 'reg_notes' ]; ?></td>
      <td><?php echo date( 'm/d/Y', strtotime( $registrations[ $regID ][ 'reg_dateReg' ] ) ); ?><br />
        <?php echo date( 'g:i a', strtotime( $registrations[ $regID ][ 'reg_dateReg' ] ) ); ?></td>
    </tr>
  </table>
  <br />
  <table class="tableMain">
    <tr>
      <td><?php _e( 'Are you sure you want to delete this registraion?', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td align="center"><input name="eventID" type="hidden" id="eventID" value="<?php echo $eventInfo[ 'ti_id' ]; ?>" />
        <input name="hash" type="hidden" id="hash" value="<?php echo $hash; ?>" />
        <input name="hashTime" type="hidden" id="hashTime" value="<?php echo $hashtime; ?>" />
        <input name="regID" type="hidden" id="regID" value="<?php echo $regID; ?>" />
        <input name="action" type="hidden" id="action" value="delete_registration_final" />
        <input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>" /></td>
    </tr>
  </table>
  <p><a href="?page=bookaroom_event_management_upcoming"><?php _e( 'Cancel and go back to search.', 'book-a-room' ); ?></a></p>
</form>