<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events - Age Groups Admin - Deactivate', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Deactivate an age group', 'book-a-room' ); ?>
</h2>
<?php
if ( !empty( $errorMSG ) ) {
	?><p><h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3></p><?php 
}
?>
<table width="100%" border="0" class="tableMain">
	<tr>
		<td>
			<?php _e( 'Deactivate', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php 
		/* translators: This asks the user if they would like to deactivate an age group, the name of which is %s */ 			
		printf( __( 'Are you sure you want to deactivate %s? This is not permanent and any events that have used this age group will still display it. It will not be available for future events unless you reactivate it.', 'book-a-room' ), $groupName ); ?>
		</td>
	</tr>
	<tr>
		<td class="redBold"><a href="?page=bookaroom_event_settings&action=deactivateFinal&amp;groupID=<?php echo $groupID; ?>&hash=<?php echo $hash; ?>&time=<?php echo $time; ?>"><?php 
			/* translators: This asks the user if they would like to deactivate an age group, the name of which is %s */ 
			printf( __( 'Yes. Deactivate %s.', 'book-a-room' ), $groupName ); ?>
			</a>
		</td>
	</tr>
</table>
<p><a href="?page=bookaroom_event_settings"><?php _e( 'Cancel and return to Age Group management.', 'book-a-room' ); ?></a></p>
