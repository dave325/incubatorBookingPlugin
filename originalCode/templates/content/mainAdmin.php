<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<?php
require( BOOKAROOM_PATH . 'templates/mainSettings/helpTableSetup.php' );
?>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Content', 'book-a-room' ); ?>
	</h2>
</div>
<div class="mainText">
	<p>
		<?php _e( 'In the content settings, please insert your Meeting Room contract information. The text below will pop up when the user clicks on Reserve and needs to be accepted before they continue.', 'book-a-room' ); ?>
	</p>
	<p>
		<?php _e( 'HTML is accepted.', 'book-a-room' ); ?>
	</p>
</div>
<form id="form1" name="form1" method="post" action="">
	<div class="helpFormMain">
		<?php 
	if( !empty( $errorMSG[ 'contract' ] ) ) {
		?>
		<p style="color:red; font-weight:bold">
			<?php echo $errorMSG[ 'contract' ]; ?>
		</p>
		<?php
		}
		?>
		<table class="tableMain">
			<tr>
				<td>
					<?php _e( 'Meeting Room Contract', 'book-a-room' ); ?>
					<div id="helpButton" onclick="showHelp('emailHelpContract')" style="float:right; clear:left;">Help!</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php wp_editor( $externals['bookaroom_content_contract'] , 'bookaroom_content_contract', $settings = array( 'editor_id' => 'bookaroom_content_contract' ) ); ?>
				</td>
			</tr>
			<tr>
				<td><input name="action" type="hidden" id="action" value="updateContent"/>
					<input type="submit" name="submit" id="submit" value="Save Changes"/>
				</td>
			</tr>
		</table>
	</div>
<?php
	# set a name for the DIV ID so it can hide and show it.
	$helpID = 'emailHelpContract';
	require( BOOKAROOM_PATH . 'templates/mainSettings/helpTable.php' );
	?>
</form>