<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Cities', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'New City', 'book-a-room' ); ?>
</h2>
<p>
	<a href="?page=bookaroom_Settings_cityManagement&amp;action=add">
		<?php _e( 'Create a new city.', 'book-a-room' ); ?>
	</a>
</p>
<p>&nbsp;</p>
<h2>
	<?php _e( 'Current Cities', 'book-a-room' ); ?>
</h2>
<?php 
if( count( $cityList ) == 0 ) {
	?>
<p>
	<?php _e( 'You haven\'t added any cities.', 'book-a-room' ); ?>
</p>
<?php
} else {
	?>
	<table class="tableMain">
		<tr>
			<td>
				<?php _e( 'City Name', 'book-a-room' ); ?>
			</td>
			<td width="150">
				<?php _e( 'Actions', 'book-a-room' ); ?>
			</td>
		</tr>
		<?php
		foreach ( $cityList as $key => $val ) {
			?>
		<tr>
			<td>
				<?php echo $val; ?>
			</td>
			<td>
				<a href="?page=bookaroom_Settings_cityManagement&action=edit&cityID=<?php echo $key; ?>">
					<?php _e( 'Edit', 'book-a-room' ); ?>
				</a> |
				<a href="?page=bookaroom_Settings_cityManagement&action=delete&cityID=<?php echo $key; ?>">
					<?php _e( 'Delete', 'book-a-room' ); ?>
				</a>
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}
?>