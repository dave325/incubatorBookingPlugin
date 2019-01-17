<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - Events - Category Admin', 'book-a-room' ); ?>
	</h2>
</div>
<?php
if ( !empty( $errorMSG ) ) {
	?><p><h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3></p><?php
}
?>
<h2>
	<?php _e( 'Active List of categories', 'book-a-room' ); ?>
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
	# ------------------------------------------------------		
	# inactive
	# ------------------------------------------------------
	if ( count( $nameList[ 'active' ] ) == false ) {
	?>
	<tr>
		<td colspan="4">
			<?php _e( 'There are currently no active categories.', 'book-a-room' ); ?>
		</td>
	</tr>
	<?php
	} else {
		$keyList = array_values( array_intersect( $nameList[ 'order' ], array_keys( $nameList[ 'active' ] ) ) );
		foreach ( $keyList as $key => $val ) {
			$linkLine = self::makeLinks( $val, $key, count( $keyList ) );
	?>
	<tr>
		<td>
			<?php echo $nameList[ 'active' ][ $val ][ 'categories_desc' ]; ?>
		</td>
		<td>
			<a href="?page=bookaroom_event_settings_categories&action=edit&groupID=<?php echo $nameList[ 'active' ][ $val ][ 'categories_id' ]; ?>">
				<?php _e( 'Edit', 'book-a-room' ); ?>
			</a>
		</td>
		<td>
			<a href="?page=bookaroom_event_settings_categories&action=deactivate&groupID=<?php echo $nameList[ 'active' ][ $val ][ 'categories_id' ]; ?>">
				<?php _e( 'Deactivate', 'book-a-room' ); ?>
			</a>
		</td>
		<td nowrap>
			<?php echo $linkLine; ?>
		</td>
	</tr>
	<?php
		}
	}
	?>
</table>
<form action="?page=bookaroom_event_settings_categories" method="post" name="addCategory" id="addCategory">
	<h2>
		<?php _e( 'Add a new category', 'book-a-room' ); ?>
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
				<?php _e( 'Category name', 'book-a-room' ); ?>
			</td>
			<td><input name="newName" type="text" id="newName" value="<?php echo $externals['newName']; ?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input name="action" type="hidden" id="action" value="addCheck"> <input type="submit" name="button2" id="button2" value="<?php _e( 'Submit', 'book-a-room' ); ?>">
			</td>
		</tr>
	</table>
</form>

<h2>
	<?php _e( 'Inactive categories', 'book-a-room' ); ?>
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
	if ( count( $nameList[ 'inactive' ] ) == false ) {
		?>
	<tr>
		<td colspan="2">
			<?php _e( 'There are no inactive categories.', 'book-a-room' ); ?>
		</td>
	</tr>
	<?php
	} else  {
		asort( $nameList[ 'all' ] );
		$keyList = array_intersect( array_keys( $nameList[ 'all' ] ), array_keys( $nameList[ 'inactive' ] ) );
		foreach ( $keyList as $val ) {
		?>
	<tr>
		<td nowrap>
			<?PHP echo $nameList['inactive'][$val]['categories_desc']; ?>
		</td>
		<td nowrap><a href="?page=bookaroom_event_settings_categories&amp;action=reactivate&amp;groupID=<?php echo $nameList['inactive'][$val]['categories_id'];?>"><?php _e( 'Reactivate', 'book-a-room' ); ?></a>
		</td>
	</tr>
	<?php
		}
	}
	?>
</table>