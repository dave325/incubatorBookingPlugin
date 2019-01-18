<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events - Category Admin - Edit', 'book-a-room' ); ?>
	</h2>
</div>
<?php
if ( !empty( $errorMSG ) ) {
	?><p><h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3></p><?php 
}
?>
<h2>
	<?php _e( 'Edit a category', 'book-a-room' ); ?>
</h2>
<form action="?page=bookaroom_event_settings_categories" method="post" name="addCategory" id="addCategory">
  <table width="100%" border="0" class="tableMain">
    <tr>
      <td><?php _e( 'Option', 'book-a-room' ); ?></td>
      <td><?php _e( 'Value', 'book-a-room' ); ?></td>
    </tr>
    <tr>
      <td><?php _e( 'Category name', 'book-a-room' ); ?></td>
      <td><input name="newName" type="text" id="newName" value="<?php echo $newName; ?>"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input name="groupID" type="hidden" id="groupID" value="#groupID#"><input name="action" type="hidden" id="action" value="editCheck">        <input type="submit" name="button2" id="button2" <?php _e( 'Submit', 'book-a-room' ); ?>></td>
    </tr>
  </table>
</form>
<p><a href="?page=bookaroom_event_settings_categories"><?php _e( 'Cancel and return to Category management.', 'book-a-room' ); ?></a></p>
