<script language="javascript">
	function disableYear( $checkedName, $dropName ) {
		checker = document.getElementById( $checkedName ).checked;
		drop = document.getElementById( $dropName );
		if ( checker == true ) {
			drop.disabled = true;
			drop.options[ 0 ].selected = true;
		} else {
			drop.disabled = false;
		}
	}

	function showHideRoomType() {
		dropType = document.getElementById( "roomType" ).value;
		divRoomList = document.getElementById( "roomList" );
		switch ( dropType ) {
			case 'all':
				divRoomList.className = "hiddenDiv";
				break;
			case 'choose':
				divRoomList.className = "visibleDiv";
				break;
			default:
				divRoomList.className = "hiddenDiv";
				break;
		}
	}

	function showHideSection() {
		dropType = document.getElementById( "selType" ).value;
		divDate = document.getElementById( "typeDate" );
		divRange = document.getElementById( "typeRange" );
		switch ( dropType ) {
			case 'date':
				divDate.className = "visibleDiv";
				divRange.className = "hiddenDiv";
				break;
			case 'range':
				divDate.className = "hiddenDiv";
				divRange.className = "visibleDiv";
				break;
			default:
				divDate.className = "hiddenDiv";
				divRange.className = "hiddenDiv";
				break;
		}
	}
</script>
<style type="text/css" media="screen">
	<!-- .hiddenDiv {
		display: none;
	}
	
	.visibleDiv {
		display: ;
	}
	
	-->
</style>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Closings', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php
	switch ( $action ) {
		case 'addCheck':
		case 'add':
			_e( 'Add a Closing', 'book-a-room' );
			break;
		case 'editCheck':
		case 'edit':
			_e( 'Edit a Closing', 'book-a-room' );
			break;
		default:
			wp_die( "ERROR: BAD ACTION on closing add/edit: " . $action );
			break;
	}
		?>
</h2>
<?php
# Display Errors if there are any
if ( !empty( $closingInfo[ 'errors' ] ) ) {
	?>
<p>
	<h3 style="color: red;"><strong><?php echo $closingInfo['errors']; ?></strong></h3>
</p>
<?php
}
?>
<form name="form1" method="post" action="?page=bookaroom_Settings_Closings">
	<table class="tableMain">
		<tr>
			<td colspan="2">
				<?php _e( 'Closing Information', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>
					<?php _e( 'Please use <em>dd/mm/yyyy</em> as your date format.', 'book-a-room' ); ?>
				</p>
				<p>
					<?php _e( '<strong>For reoccuring dates</strong>, you do not have to enter a year.', 'book-a-room' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Closing Name', 'book-a-room' ); ?>
			</td>
			<td><input name="closingName" type="text" id="closingName" value="<?php echo $closingInfo['closingName']; ?>" size="60"/>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Closing Type', 'book-a-room' ); ?>
			</td>
			<td>
				<select name="selType" id="selType" onchange="showHideSection()">
					<?php
					foreach ( array( null => __( 'Choose One', 'book-a-room' ), 'date' => __( 'Date', 'book-a-room' ), 'range' => __( 'Range', 'book-a-room' ) ) as $key => $val ) {

						if ( $closingInfo[ 'selType' ] == $key ) {
							$selected = ' selected="selected"';
						} else  {
							$selected = null;
						}
					?>
					<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
						<?php echo $val; ?>
					</option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Rooms Type', 'book-a-room' ); ?>
			</td>
			<td>
				<select name="roomType" id="roomType" onchange="showHideRoomType()">
					<?php
					foreach ( array( null => __( 'Room Closing Type', 'book-a-room' ), 'all' => __( 'All rooms', 'book-a-room' ), 'choose' => __( 'Choose rooms', 'book-a-room' ) ) as $key => $val ) {
						$selected = ( $closingInfo[ 'roomType' ] == $key ) ? ' selected="selected"' : null;

					?>
					<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
						<?php echo $val; ?>
					</option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<div class="hiddenDiv" id="typeDate">
		<table class="tableMain">
			<tr>
				<td colspan="2">
					<?php _e( 'Date', 'book-a-room' ); ?>
					</th>
			</tr>
			<tr>
				<td>
					<?php _e( 'Date (dd/mm/yy)', 'book-a-room' ); ?>
				</td>
				<td>
					<select name="date_single_month" id="date_single_month">
						<?php
						$monArr = array( null => __( 'Month', 'book-a-room' ) ) + $weekArr[ 'month' ];
						foreach ( $monArr as $key => $val ) {
							$selected = ( $closingInfo[ "date_single_month" ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
					<select name="date_single_day" id="date_single_day">
						<?php
						$dayArr = array( null => __( 'Day', 'book-a-room' ) );
						for ( $d = 1; $d <= 31; $d++ ) {
							$dayStamp = mktime( 1, 1, 1, 10, $d, 2012 );
						$dayArr[ $d ] = date_i18n( 'jS', $dayStamp );
						}
						foreach ( $dayArr as $key => $val ) {
							$selected = ( $closingInfo[ 'date_single_day' ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected;?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
					<select name="date_single_year" id="date_single_year">
						<?php
						$yearArr = array( null => __( 'Year', 'book-a-room' ) );
						$thisYear = date( 'Y' );
						$firstYear = ( !empty( $closingInfo[ 'date_single_year' ] ) ) ? $closingInfo[ 'date_single_year' ] : date( 'Y' );
						for ( $y = $firstYear; $y <= $yearsAhead + $thisYear; $y++ ) {
							$yearArr[ $y ] = $y;
						}
						foreach ( $yearArr as $key => $val ) {
							$selected = ( $closingInfo[ 'date_single_year' ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Reoccuring?', 'book-a-room' ); ?>
				</td>
				<td><input name="date_reoccuring" type="checkbox" id="date_reoccuring" value="TRUE" onclick="disableYear('date_reoccuring','date_single_year');" <?php echo ( $closingInfo[ 'date_reoccuring'] == true ) ? ' checked="checked"' : null; ?>/>
					<?php _e( 'Yes', 'book-a-room' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="hiddenDiv" id="typeRange">
		<table class="tableMain">
			<tr>
				<td colspan="2">
					<?php _e( 'Date Range', 'book-a-room' ); ?>
					</th>
			</tr>
			<tr>
				<td>
					<?php _e( 'Start Date', 'book-a-room' ); ?>
				</td>
				<td>
					<select name="date_start_month" id="date_start_month">
						<?php
						$monArr = array( null => __( 'Month', 'book-a-room' ) ) + $weekArr[ 'month' ];
						foreach ( $monArr as $key => $val ) {
							$selected = ( $closingInfo[ "date_start_month" ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
					<select name="date_start_day" id="date_start_day">
						<?php
						$dayArr = array( null => __( 'Day', 'book-a-room' ) );
						for ( $d = 1; $d <= 31; $d++ ) {
							$dayStamp = mktime( 1, 1, 1, 10, $d, 2012 );
						$dayArr[ $d ] = date_i18n( 'jS', $dayStamp );
						}
						foreach ( $dayArr as $key => $val ) {
							$selected = ( $closingInfo[ 'date_start_day' ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected;?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
					<select name="date_start_year" id="date_start_year">
						<?php
						$yearArr = array( null => __( 'Year', 'book-a-room' ) );
						$thisYear = date( 'Y' );
						for ( $y = $thisYear; $y <= $yearsAhead + $thisYear; $y++ ) {
							$yearArr[ $y ] = $y;
						}
						foreach ( $yearArr as $key => $val ) {
							$selected = ( $closingInfo[ 'date_start_year' ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'End Date', 'book-a-room' ); ?>
				</td>
				<td>
					<select name="date_end_month" id="date_end_month">
						<?php
						$monArr = array( null => __( 'Month', 'book-a-room' ) ) + $weekArr[ 'month' ];
						foreach ( $monArr as $key => $val ) {
							$selected = ( $closingInfo[ "date_end_month" ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
					<select name="date_end_day" id="date_end_day">
						<?php
						$dayArr = array( null => __( 'Day', 'book-a-room' ) );
						for ( $d = 1; $d <= 31; $d++ ) {
							$dayStamp = mktime( 1, 1, 1, 10, $d, 2012 );
							$dayArr[ $d ] = date_i18n( 'jS', $dayStamp );
						}
						foreach ( $dayArr as $key => $val ) {
							$selected = ( $closingInfo[ 'date_end_day' ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected;?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
					<select name="date_end_year" id="date_end_year">
						<?php
						$yearArr = array( null => __( 'Year', 'book-a-room' ) );
						$thisYear = date( 'Y' );
						for ( $y = $thisYear; $y <= $yearsAhead + $thisYear; $y++ ) {
							$yearArr[ $y ] = $y;
						}
						foreach ( $yearArr as $key => $val ) {
							$selected = ( $closingInfo[ 'date_end_year' ] == $key ) ? ' selected="selected"' : null;
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
							<?php echo $val; ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Reoccuring?', 'book-a-room' ); ?>
				</td>
				<td><input name="dateRange_reoccuring" type="checkbox" id="dateRange_reoccuring" value="TRUE" onclick="disableYear('dateRange_reoccuring','date_start_year');  disableYear('dateRange_reoccuring','date_end_year');" <?php echo ( $closingInfo[ 'dateRange_reoccuring'] == true ) ? ' checked="checked"' : null; ?>/>
					<?php _e( 'Yes', 'book-a-room' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="hiddenDiv" id="roomList">
		<table class="tableMain">
			<tr>
				<td>
					<?php _e( 'Rooms', 'book-a-room' ); ?>
					</th>
			</tr>
			<tr>
				<td>
					<div id="demo1" class="demo">
						<ul>
							<li id="roomsAll">
								<a href="#">
									<?php _e( 'All', 'book-a-room' ); ?>
								</a>
								<?php
								foreach ( $branchList as $key => $val ) {
									?>
								<ul>
									<li id="branch_<?php echo $key; ?>"> <a href="#"><?php echo $val; ?></a>
										<ul>
											<?php
											if( !empty(  $roomList['room'][$key] ) ) {
											   foreach( $roomList['room'][$key] as $key2 => $val2 ) {
											?>
											<li id="room_<?php echo $key2; ?>"> <a href="#"><?php echo $roomList['id'][$key2]['desc']; ?></a>
											</li>
											<?php
											   }
											}
											?>
										</ul>
									</li>
								</ul>
								<?php
								}
								?>
							</li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<table class="tableMain">
		<tr>
			<td colspan="2"><input name="action" type="hidden" id="action" value="<?php echo $action; ?>"/>
				<input name="closingID" type="hidden" id="closingID" value="<?php echo $closingInfo['closingID']; ?>"/> <input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/>
			</td>
		</tr>
	</table>
</form>
<p><a href="?page=bookaroom_Settings_Closings"><?php _e( 'Return to Closings Home.', 'book-a-room' ); ?></a></p>
<link rel="stylesheet" href="<?php echo plugins_url(); ?>/book-a-room/js/jstree/themes/default-dark/style.min.css" />
<script type="text/javascript" class="source below">
	jQuery( document ).ready( function ( $ ) {
		// TO CREATE AN INSTANCE
		// select the tree container using jQuery
		$( "#demo1" )
			// call `.jstree` with the options object
			.jstree( {
				// the `plugins` array allows you to configure the active plugins on this instance
				"plugins": [ "themes", "html_data", "ui", "checkbox" ],
				// each plugin you have included can have its own config object
				"core": {
					"initially_open": [ <?php echo $init_open; ?> ]
				},
				// it makes sense to configure a plugin only if overriding the defaults
				"checkbox": {
					"real_checkboxes": true,
					"real_checkboxes_names": function ( n ) {
						return [ ( "roomChecked[" + ( n[ 0 ].id || Math.ceil( Math.random() * 10000 ) ) ) + "]", 1 ];
					}
				}
			} )
			// EVENTS
			// each instance triggers its own events - to process those listen on the container
			// all events are in the `.jstree` namespace
			// so listen for `function_name`.`jstree` - you can function names from the docs
			.bind( "loaded.jstree", function ( event, data ) {
				// you get two params - event & data - check the core docs for a detailed description
				<?php
				if( !empty( $closingInfo['rooms'] ) and count( $closingInfo['rooms'] ) >= 1 ) {
					foreach( $closingInfo['rooms'] as $key => $val ) {
				?>$( '#demo1' ).jstree( "check_node", "#room_<?php echo $val; ?>" );<?php
					}
				}
				?>
			} );


	} );
</script>
<script language="javascript">
	showHideSection();
	showHideRoomType();
	disableYear( 'date_reoccuring', 'date_single_year' );
	disableYear( 'dateRange_reoccuring', 'date_start_year' );
	disableYear( 'dateRange_reoccuring', 'date_end_year' );
</script>