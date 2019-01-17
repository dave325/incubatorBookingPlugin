<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Branches', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	switch ( $action ) {
		case 'addCheck':
		case 'add':
			_e( 'Add Branch', 'book-a-room' );
			break;
		case 'editCheck':
		case 'edit':
			_e( 'Edit Branch', 'book-a-room' );
			break;
		default:
			wp_die( "ERROR: BAD ACTION on branch add/edit: " . $action );
			break;
	}
	?>
</h2>
<?php
# Display Errors if there are any
if ( !empty( $branchInfo[ 'errors' ] ) ) {
	?>
<p>
	<h3 style="color: red;"><strong><?php echo $branchInfo['errors']; ?></strong></h3>
</p>
<?php
}
?>
<form id="form1" name="form1" method="post" action="?page=bookaroom_Settings_Branches&branchID=<?PHP echo $branchInfo['branchID']; ?>&action=<?php echo $action; ?>">
	<table class="tableMain">
		<tr>
			<td>
				<?php _e( 'Description', 'book-a-room' ); ?>
			</td>
			<td colspan="2">
				<?php _e( 'Setting', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Branch Name', 'book-a-room' ); ?>
			</td>
			<td colspan="2"><input name="branchDesc" type="text" id="branchDesc" value="<?php echo $branchInfo['branchDesc']; ?>" size="50" maxlength="64"/>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Address', 'book-a-room' ); ?>
			</td>
			<td colspan="2">
				<textarea name="branchAddress" cols="50" rows="3" id="branchAddress"><?php echo $branchInfo['branchAddress']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Map Link', 'book-a-room' ); ?>
			</td>
			<td colspan="2"><input name="branchMapLink" type="text" id="branchMapLink" value="<?php echo $branchInfo['branchMapLink']; ?>" size="50"/>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Image URL (580x140 pixels)', 'book-a-room' ); ?><br/>
				<?php _e( '(Gradient between 176x420 pixels)', 'book-a-room' ); ?>
			</td>
			<td colspan="2"><input name="branchImageURL" type="text" id="branchImageURL" value="<?php echo $branchInfo['branchImageURL']; ?>" size="50"/>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Available to the public', 'book-a-room' ); ?>
			</td>
			<td colspan="2"><input name="branch_isPublic" type="radio" id="branch_isPublic" value="true" <?php echo $branch_isPublicTrue; ?> />
				<?php _e( 'Yes', 'book-a-room' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="branch_isPublic" id="branch_isPublic" value="false" <?php echo $branch_isPublicFalse; ?> />
				<?php _e( 'No', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Available for Social Meetings', 'book-a-room' ); ?>
			</td>
			<td colspan="2"><input name="branch_isSocial" type="radio" id="branch_isSocial" value="true" <?php echo $branch_isSocialTrue; ?> />
				<?php _e( 'Yes', 'book-a-room' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="branch_isSocial" id="branch_isSocial" value="false" <?php echo $branch_isSocialFalse; ?> />
				<?php _e( 'No', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Show no social meeting notice on branch list', 'book-a-room' ); ?>
			</td>
			<td colspan="2"><input name="branch_showSocial" type="radio" id="branch_showSocial" value="true" <?php echo $branch_showSocialTrue; ?> />
				<?php _e( 'Yes', 'book-a-room' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="branch_showSocial" id="branch_showSocial" value="false" <?php echo $branch_showSocialFalse; ?> />
				<?php _e( 'No', 'book-a-room' ); ?>
			</td>
			</td>
		</tr>		
		<tr>
			<td>
				<?php _e( 'Has \'No Location\'', 'book-a-room' ); ?>
			</td>
			<td><input name="branch_hasNoloc" type="radio" id="branch_hasNolocTrue" value="true" <?php echo $branch_hasNolocTrue; ?> />
				<?php _e( 'Yes', 'book-a-room' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="branch_hasNoloc" id="branch_hasNolocFalse" value="false" <?php echo $branch_hasNolocFalse; ?> />
				<?php _e( 'No', 'book-a-room' ); ?>
			</td>
			<td>&nbsp;</td>
			<!-- -------------------------------------------------------------------------- -->
			<!-- days start -->
			<?php
			$days = array( 0 => __( 'Sunday Open/Close (HH:MM)', 'book-a-room' ), __( 'Monday Open/Close', 'book-a-room' ), __( 'Tuesday Open/Close', 'book-a-room' ), __( 'Wednesday Open/Close', 'book-a-room' ), __( 'Thursday Open/Close', 'book-a-room' ), __( 'Friday Open/Close', 'book-a-room' ), __( 'Saturday Open/Close', 'book-a-room' ) );
			foreach ( $days as $num => $dayName ) {
			?>
		</tr>
		<tr>
			<td>
				<?php echo $dayName; ?>
			</td>
			<td><input name="branchOpen_<?php echo $num; ?>" type="text" id="branchOpen_<?php echo $num; ?>" value="<?php echo $branchInfo["branchOpen_{$num}"]; ?>" size="5" maxlength="5"/>
				<input type="checkbox" name="branchOpen_<?php echo $num; ?>PM" id="branchOpen_<?php echo $num; ?>PM" <?php echo ( !empty( $branchInfo[ "branchOpen_{$num}PM"] ) ) ? ' checked="checked"' : NULL; ?> />
				<?php _e( 'PM', 'book-a-room' ); ?>
			</td>
			<td><input name="branchClose_<?php echo $num; ?>" type="text" id="branchClose_<?php echo $num; ?>" value="<?php echo $branchInfo["branchClose_{$num}"]; ?>" size="5" maxlength="5"/>
				<input type="checkbox" name="branchClose_<?php echo $num; ?>PM" id="branchClose_<?php echo $num; ?>PM" <?php echo ( !empty( $branchInfo[ "branchClose_{$num}PM"] ) ) ? ' checked="checked"' : null; ?> />
				<?php _e( 'PM', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		}
		?>
		<!-- -------------------------------------------------------------------------- -->
		<!-- days end -->
		<tr>
			<td colspan="3"><input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/>
			</td>
		</tr>
	</table>
</form>
<p>
	<a href="?page=bookaroom_Settings_Branches">
		<?php _e( 'Return to Branches Home.', 'book-a-room' ); ?>
	</a>
</p>