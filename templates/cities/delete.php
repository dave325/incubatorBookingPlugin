<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Cities', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Delete city', 'book-a-room' ); ?>
</h2>
<table border="0" cellspacing="1" cellpadding="2" class="tableMain">
  <tr>
    <td><strong><?php _e( 'City Name', 'book-a-room' ); ?></strong></td>
  </tr>
  <tr>
    <td><?PHP echo $cityInfo['cityDesc'];?></td>
  </tr>
</table>
<p><?php _e( 'Deleting a city is permanent and cannot be undone.', 'book-a-room' ); ?></p>
<p><a class="errorText" href="?page=bookaroom_Settings_cityManagement&cityID=<?PHP echo $cityInfo['cityID'] ?>&action=deleteCheck"><?php _e( 'Click here to permanantly delete this city.', 'book-a-room' ); ?></a></p><br />
<p><a href="?page=bookaroom_Settings_cityManagement"><?php _e( 'Return to Cities Home.', 'book-a-room' ); ?></a></p>