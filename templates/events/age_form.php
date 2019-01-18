<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events - Age Groups Admin', 'book-a-room' ); ?>
	</h2>
</div>
<?php
# Display Errors if there are any
if ( !empty( $errorMSG ) ) {
	?><p><h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3></p><?php 
}
?>
<h2>
	<?php _e( 'Active List of age groups', 'book-a-room' ); ?>
</h2>
<table width="100%" border="0" class="tableMain">
	<tr>
		<td width="85%">
			<?php _e( 'Name', 'book-a-room' ); ?>
		</td>
		<td width="5%">
			<?php _e( 'Edit', 'book-a-room' ); ?>
		</td>
		<td width="5%">
			<?php _e( 'Deactivate', 'book-a-room' ); ?>
		</td>
		<td width="5%">
			<?php _e( 'Order', 'book-a-room' ); ?>
		</td>
	</tr>
	<?php
	# groups list
	$nameList = self::getNameList();

	# ------------------------------------------------------
	# NOT active 
	# ------------------------------------------------------
	if ( count( $nameList[ 'active' ] ) == false ) {
		echo '<tr>\n<td colspan=\"4\">' . __( 'There are currently no active age groups.', 'book-a-room' ) . '</td></tr>';
	# ------------------------------------------------------
	# IS active 
	# ------------------------------------------------------		
	} else {
		$temp = NULL;
		$final = array();
		$keyList = array_values( array_intersect( $nameList[ 'order' ], array_keys( $nameList[ 'active' ] ) ) );
		foreach ( $keyList as $key => $val ) {
			$links = self::makeLinks( $val, $key, count( $keyList ) );
			echo '<tr><td>' . $nameList[ 'active' ][ $val ][ 'age_desc' ] . '</td><td>' . '<a href="?page=bookaroom_event_settings&action=edit&groupID=' . $nameList[ 'active' ][ $val ][ 'age_id' ] . '">' . __( 'Edit', 'book-a-room' ) . '</a></td>' . '<td><a href="?page=bookaroom_event_settings&action=deactivate&groupID=' . $nameList[ 'active' ][ $val ][ 'age_id' ] . '">' . __( 'Deactivate', 'book-a-room' ) . '</a></td><td nowrap>' . $links . '</td></tr>';
		}
	}

	?>
</table>
<form action="?page=bookaroom_event_settings" method="post" name="addAge" id="addAge">
	<h2>
		<?php _e( 'Add a new age group', 'book-a-room' ); ?>
	</h2>
	<table width="100%" border="0" class="tableMain">
		<tr>
			<td>
				<?php _e( 'Option', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Value', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Age group name', 'book-a-room' ); ?>
			</td>
			<td><input name="newName" type="text" id="newName" value="<?php echo @$externals['newName']; ?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input name="action" type="hidden" id="action" value="addCheck">
				<input type="submit" name="submit" id="button2" value="<?php _e( 'Submit', 'book-a-room' ); ?>">
			</td>
		</tr>
	</table>
</form>

<h2>
	<?php _e( 'Inactive age groups', 'book-a-room' ); ?>
</h2>
<table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableMain">
	<tr>
		<td width="90%">
			<?php _e( 'Group Name', 'book-a-room' ); ?>
		</td>
		<td width="5%" nowrap>
			<?php _e( 'Reactivate?', 'book-a-room' ); ?>
		</td>
	</tr>
	<?php
	# ------------------------------------------------------		
	# inactive
	# ------------------------------------------------------		
	if ( count( $nameList[ 'inactive' ] ) == false ) {
		echo '<tr>
      	<td colspan="2">' . __( 'There are no inactive age groups.', 'book-a-room' ) . '</td>
      	</tr>';
	} else  {
		asort( $nameList[ 'all' ] );
		$keyList = array_intersect( array_keys( $nameList[ 'all' ] ), array_keys( $nameList[ 'inactive' ] ) );
		foreach ( $keyList as $val ) {
			echo '<tr><td nowrap>' . $nameList[ 'inactive' ][ $val ][ 'age_desc' ] . '</td><td nowrap><a href="?page=bookaroom_event_settings&amp;action=reactivate&amp;groupID=' . $nameList[ 'inactive' ][ $val ][ 'age_id' ] . '">' . __( 'Reactivate', 'book-a-room' ) . '</a></td></tr>';
		}
	}
	?>
</table>