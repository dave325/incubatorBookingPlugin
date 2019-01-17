<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events - Age Groups Admin - Reactivate', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Reactivate an age group', 'book-a-room' ); ?>
</h2>
<?php
if ( !empty( $errorMSG ) ) {
	?><p><h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3></p><?php 
}
?>
<table width="100%" border="0" class="tableMain">
	<tr>
		<td>
			<?php _e( 'Reactivate', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php 
		/* translators: This asks the user if they would like to reactivate an age group, the name of which is %s */ 			
		printf( __( 'Are you sure you want to reactivate %s? This will make it active to any new events being entered.', 'book-a-room' ), $groupName ); ?>
		</td>
	</tr>
	<tr>
		<td class="redBold"><a href="?page=bookaroom_event_settings&action=reactivateFinal&amp;groupID=<?php echo $groupID; ?>&hash=<?php echo $hash; ?>&time=<?php echo $time; ?>"><?php 
			/* translators: This asks the user if they would like to deactivate an age group, the name of which is %s */ 
			printf( __( 'Yes. Reactivate %s.', 'book-a-room' ), $groupName ); ?>
			</a>
		</td>
	</tr>
</table>
<p><a href="?page=bookaroom_event_settings"><?php _e( 'Cancel and return to Age Group management.', 'book-a-room' ); ?></a></p>                                                                                                                                                                                                                                                                                                                                                                                                                                                                               