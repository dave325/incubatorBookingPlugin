<?php
class bookaroom_settings_branches {
	############################################
	#
	# Branch Management
	#
	############################################
	public static
	function bookaroom_admin_branches() {
		$branchList = self::getBranchList();
		# figure out what to do
		# first, is there an action?
		$externals = self::getExternalsBranch();

		switch ( $externals[ 'action' ] ) {
			case 'deleteCheck': # check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'branchID' ], $branchList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/branches/IDerror.php' );
				} else {
					# show delete screen
					$branchInfo = self::getBranchInfo( $externals[ 'branchID' ] );
					$roomContList = bookaroom_settings_roomConts::getRoomContList();
					$roomList = bookaroom_settings_rooms::getRoomList();
					$container = self::makeRoomAndContList( $branchInfo, $roomContList, $roomList );
					self::deleteBranch( $branchInfo, $container );
					require( BOOKAROOM_PATH . 'templates/branches/deleteSuccess.php' );
				}
				break;

			case 'delete':
				# check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'branchID' ], $branchList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/branches/IDerror.php' );
				} else {
					# show delete screen

					$branchInfo = self::getBranchInfo( $externals[ 'branchID' ] );
					$roomContList = bookaroom_settings_roomConts::getRoomContList();
					$roomList = bookaroom_settings_rooms::getRoomList();
					self::showBranchDelete( $branchInfo, $roomContList, $roomList );
				}

				break;

			case 'addCheck':
				# check entries
				if ( ( $errors = self::checkEditBranch( $externals, $branchList ) ) == NULL ) {
					self::addBranch( $externals );
					require( BOOKAROOM_PATH . 'templates/branches/addSuccess.php' );
					break;
				}

				$externals[ 'errors' ] = $errors;
				# show edit screen
				self::showBranchEdit( $externals, 'addCheck', 'Add' );

				break;

			case 'add':
				self::showBranchEdit( NULL, 'addCheck', 'Add' );
				break;

			case 'editCheck':
				# check entries
				if ( ( $errors = self::checkEditBranch( $externals, $branchList ) ) == NULL ) {
					self::editBranch( $externals );
					require( BOOKAROOM_PATH . 'templates/branches/editSuccess.php' );
					break;
				}

				$externals[ 'errors' ] = $errors;

				# check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'branchID' ], $branchList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/branches/IDerror.php' );
				} else {
					# show edit screen
					self::showBranchEdit( $externals, 'editCheck', 'Edit', $externals );
				}

				break;

			case 'edit':

				# check that there is an ID and it is valid

				if ( bookaroom_settings::checkID( $externals[ 'branchID' ], $branchList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/branches/IDerror.php' );
				} else {
					# show edit screen
					$branchInfo = self::getBranchInfo( $externals[ 'branchID' ] );

					self::showBranchEdit( $branchInfo, 'editCheck', 'Edit', $externals );
				}

				break;

			default:
				self::showBranchList( $branchList );
				break;
		}

	}

	# sub functions:
	############################################

	public static
	function addBranch( $externals )
	# add a new branch
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_branches";

		$finalTime = array();

		foreach ( array( 'Open', 'Close' ) as $type ) {
			for ( $d = 0; $d <= 6; $d++ ) {
				#open time
				$name = "branch{$type}_{$d}";
				$pmName = $name . 'PM';

				if ( empty( $externals[ $name ] ) ) {
					$finalTime[ $type ][ $d ] = NULL;
				} else {
					list( $h, $m ) = explode( ":", $externals[ $name ] );
					$timeVal = ( $h * 60 ) + $m;

					if ( !empty( $externals[ $pmName ] ) ) {
						$timeVal += 720;
					}

					$finalTime[ $type ][ $d ] = date( 'G:i:s', strtotime( '1/1/2000 00:00:00' ) + ( $timeVal * 60 ) );
				}

			}
		}

		if ( $externals[ 'branch_isPublic' ] == 'true' ) {
			$branch_isPublic = 1;
		} else {
			$branch_isPublic = 0;
		}

		if ( $externals[ 'branch_hasNoloc' ] == 'true' ) {
			$branch_hasNoloc = 1;
		} else {
			$branch_hasNoloc = 0;
		}


		$final = $wpdb->insert( $table_name,
			array( 'branchDesc' => $externals[ 'branchDesc' ],
				'branchAddress' => $externals[ 'branchAddress' ],
				'branchMapLink' => $externals[ 'branchMapLink' ],
				'branchImageURL' => $externals[ 'branchImageURL' ],
				'branch_isPublic' => $branch_isPublic,
				'branch_hasNoloc' => $branch_hasNoloc,
				'branchOpen_0' => $finalTime[ 'Open' ][ 0 ],
				'branchOpen_1' => $finalTime[ 'Open' ][ 1 ],
				'branchOpen_2' => $finalTime[ 'Open' ][ 2 ],
				'branchOpen_3' => $finalTime[ 'Open' ][ 3 ],
				'branchOpen_4' => $finalTime[ 'Open' ][ 4 ],
				'branchOpen_5' => $finalTime[ 'Open' ][ 5 ],
				'branchOpen_6' => $finalTime[ 'Open' ][ 6 ],
				'branchClose_0' => $finalTime[ 'Close' ][ 0 ],
				'branchClose_1' => $finalTime[ 'Close' ][ 1 ],
				'branchClose_2' => $finalTime[ 'Close' ][ 2 ],
				'branchClose_3' => $finalTime[ 'Close' ][ 3 ],
				'branchClose_4' => $finalTime[ 'Close' ][ 4 ],
				'branchClose_5' => $finalTime[ 'Close' ][ 5 ],
				'branchClose_6' => $finalTime[ 'Close' ][ 6 ] ) );

	}

	public static
	function checkEditBranch( & $externals, $branchList )
	# check the name for duplicates, the times for correct format and non-equal
	# or close after open
	{

		# check times
		$timeArr = array();
		$dayname = array( 0 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
		$final = NULL;
		$error = array();

		foreach ( $externals as $key => $val ) {
			$day = NULL;
			$type = NULL;
			$timeVal = NULL;
			# check for open or close
			if ( stristr( $key, 'branchOpen' ) or stristr( $key, 'branchClose' ) ) {

				switch ( substr( $key, 0, 10 ) ) {
					case 'branchOpen':
						$type = 'open';
						$errorType = 'opening';
						break;
					case 'branchClos':
						$type = 'close';
						$errorType = 'closing';
						break;
					default:
						die( 'Error!' );
						break;
				}
				# is checkbox?

				if ( substr( $key, -2 ) == 'PM' ) {
					$day = substr( $key, -3, 1 );
					if ( !is_null( $val ) ) {
						# get day val
						$timeVal = 720;
					}

					if ( $externals[ substr( $key, 0, -2 ) ] == '12:00' ) {
						$timeVal = NULL;
					}
				} else {
					#find day of the week
					$day = substr( $key, -1, 1 );
					# is valid value?
					if ( empty( $val ) ) {
						continue;
					}

					if ( count( explode( ":", $val ) ) !== 2 ) {
						$error[] = "The {$errorType} time for {$dayname[$day]} is invalid.";
						continue;
					}


					list( $h, $m ) = explode( ":", $val );

					# not numeric?
					if ( !is_numeric( $h ) or!is_numeric( $m ) ) {
						$error[] = "The {$errorType} time for {$dayname[$day]} is invalid.";
						continue;
					}

					# invalid times?
					if ( ( $h <= 12 and $h >= 0 ) and( $m <= 59 and $m >= 0 ) ) {
						# get day
						$timeVal = ( $h * 60 ) + $m;
					} else {
						$error[] = "The {$errorType} time for {$dayname[$day]} is invalid.";
						continue;
					}
				}

				# check for, and create, array entry if empty
				if ( empty( $timeArr[ $day ][ $type ] ) ) {
					$timeArr[ $day ][ $type ] = $timeVal;
				} else {
					$timeArr[ $day ][ $type ] += $timeVal;
				}
			}
		}

		# check close-before-opens and closed days
		for ( $d = 0; $d <= 6; $d++ ) {
			# first, clear check if empty
			foreach ( array( 'Open', 'Close' ) as $val ) {
				$name = "branch{$val}_{$d}";
				$namePM = $name . 'PM';

				if ( empty( $externals[ $name ] ) ) {
					$externals[ $namePM ] = NULL;
					$typeName = strtolower( $val );
					$timeArr[ $d ][ $typeName ] = NULL;
				}
			}

			if ( !$timeArr[ $d ][ 'close' ]and!$timeArr[ $d ][ 'open' ] ) {
				#
			} elseif ( empty( $timeArr[ $d ][ 'close' ] ) or empty( $timeArr[ $d ][ 'open' ] ) ) {
				$error[] = "Your must enter both a close and open time on {$dayname[$d]} or leave both blank if the branch is closed.";
			} elseif ( $timeArr[ $d ][ 'close' ] <= $timeArr[ $d ][ 'open' ] ) {
				$error[] = "Your close time must come after your opening time on {$dayname[$d]}.";
			}
		}

		# check for public
		if ( empty( $externals[ 'branch_isPublic' ] ) ) {
			$error[] = 'You must choose if this branch is availble for public scheduling.';
		}

		# check for noloc
		if ( empty( $externals[ 'branch_hasNoloc' ] ) ) {
			$error[] = 'You must choose if this branch is has a "No location" option.';
		}

		# check for empty branch name
		if ( empty( $externals[ 'branchAddress' ] ) ) {
			$error[] = 'You must enter an address.';
		}

		# check for empty branch name
		if ( empty( $externals[ 'branchMapLink' ] ) ) {
			$error[] = 'You must enter a map link.';
		}

		# check for empty branch name
		if ( empty( $externals[ 'branchDesc' ] ) ) {
			$error[] = 'You must enter a branch name.';
		}

		# check dupe name		
		if ( bookaroom_settings::dupeCheck( $branchList, $externals[ 'branchDesc' ], $externals[ 'branchID' ] ) == 1 ) {
			$error[] = 'That branch name is already in use. Please choose another.';
		}

		# if errors, implode and return error messages

		if ( count( $error ) !== 0 ) {
			$final = implode( "<br />", $error );
		}

		return $final;

	}

	public static
	function deleteBranch( $branchInfo, $container )
	# add a new branch
	{
		global $wpdb;


		$table_name = $wpdb->prefix . "bookaroom_branches";

		$sql = "DELETE FROM `{$table_name}` WHERE `branchID` = '{$branchInfo['branchID']}' LIMIT 1";
		$wpdb->query( $sql );

		$finalRooms = array();
		foreach ( $container as $key => $val ) {
			$finalRooms = array_unique( array_merge( $finalRooms, $val[ 'rooms' ] ) );
		}

		if ( !empty( $finalRooms ) ) {
			$table_name = $wpdb->prefix . "bookaroom_rooms";

			$finalRoomsImp = implode( ',', $finalRooms );
			$sql = "DELETE FROM `{$table_name}` WHERE `roomID` IN ({$finalRoomsImp}) LIMIT 1";
			$wpdb->query( $sql );
		}

		unset( $container[ NULL ] );

		$finalRoomConts = array_keys( $container );

		if ( !empty( $finalRoomConts ) ) {
			$table_name = $wpdb->prefix . "bookaroom_roomConts";

			$finalRoomsContsImp = implode( ',', $finalRoomConts );
			$sql = "DELETE FROM `{$table_name}` WHERE `roomCont_ID` IN ({$finalRoomsContsImp}) LIMIT 1";
			$wpdb->query( $sql );
		}

		return FALSE;
	}

	public static
	function editBranch( $externals )
	# change the branch settings
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_branches";

		$finalTime = array();

		foreach ( array( 'Open', 'Close' ) as $type ) {
			for ( $d = 0; $d <= 6; $d++ ) {
				#open time
				$name = "branch{$type}_{$d}";
				$pmName = $name . 'PM';
				if ( empty( $externals[ $name ] ) ) {
					$finalTime[ $type ][ $d ] = NULL;
					$typeCast[ $type ][ $d ] = NULL;
				} else {
					list( $h, $m ) = explode( ":", $externals[ $name ] );
					# check for noon
					$timeVal = ( $h * 60 ) + $m;
					if ( !empty( $externals[ $pmName ] ) ) {
						if ( $h !== '12' ) {
							$timeVal += 720;
						}
					}
					$finalTime[ $type ][ $d ] = date( 'G:i:s', strtotime( '1/1/2000 00:00:00' ) + ( $timeVal * 60 ) );
					$typeCast[ $type ][ $d ] = '%s';
				}
			}
		}

		if ( $externals[ 'branch_isSocial' ] == 'true' ) {
			$branch_isSocial = 1;
		} else {
			$branch_isSocial = 0;
		}
		
		if ( $externals[ 'branch_showSocial' ] == 'true' ) {
			$branch_showSocial = 1;
		} else {
			$branch_showSocial = 0;
		}
		
		if ( $externals[ 'branch_isPublic' ] == 'true' ) {
			$branch_isPublic = 1;
		} else {
			$branch_isPublic = 0;
		}

		if ( $externals[ 'branch_hasNoloc' ] == 'true' ) {
			$branch_hasNoloc = 1;
		} else {
			$branch_hasNoloc = 0;
		}

		$final = $wpdb->update( $table_name,
			array( 'branchDesc' => $externals[ 'branchDesc' ],
				'branchAddress' => $externals[ 'branchAddress' ],
				'branchMapLink' => $externals[ 'branchMapLink' ],
				'branchImageURL' => $externals[ 'branchImageURL' ],
				'branch_isPublic' => $branch_isPublic,
				'branch_isSocial' => $branch_isSocial,
				'branch_showSocial' => $branch_showSocial,
				'branch_hasNoloc' => $branch_hasNoloc,
				'branchOpen_0' => $finalTime[ 'Open' ][ 0 ],
				'branchOpen_1' => $finalTime[ 'Open' ][ 1 ],
				'branchOpen_2' => $finalTime[ 'Open' ][ 2 ],
				'branchOpen_3' => $finalTime[ 'Open' ][ 3 ],
				'branchOpen_4' => $finalTime[ 'Open' ][ 4 ],
				'branchOpen_5' => $finalTime[ 'Open' ][ 5 ],
				'branchOpen_6' => $finalTime[ 'Open' ][ 6 ],
				'branchClose_0' => $finalTime[ 'Close' ][ 0 ],
				'branchClose_1' => $finalTime[ 'Close' ][ 1 ],
				'branchClose_2' => $finalTime[ 'Close' ][ 2 ],
				'branchClose_3' => $finalTime[ 'Close' ][ 3 ],
				'branchClose_4' => $finalTime[ 'Close' ][ 4 ],
				'branchClose_5' => $finalTime[ 'Close' ][ 5 ],
				'branchClose_6' => $finalTime[ 'Close' ][ 6 ] ),
			array( 'branchID' => $externals[ 'branchID' ] ),
			array( '%s', '%s', '%s', '%s', '%s', '%s',
				$typeCast[ 'Open' ][ 0 ],
				$typeCast[ 'Open' ][ 1 ],
				$typeCast[ 'Open' ][ 2 ],
				$typeCast[ 'Open' ][ 3 ],
				$typeCast[ 'Open' ][ 4 ],
				$typeCast[ 'Open' ][ 5 ],
				$typeCast[ 'Open' ][ 6 ],
				$typeCast[ 'Close' ][ 0 ],
				$typeCast[ 'Close' ][ 1 ],
				$typeCast[ 'Close' ][ 2 ],
				$typeCast[ 'Close' ][ 3 ],
				$typeCast[ 'Close' ][ 4 ],
				$typeCast[ 'Close' ][ 5 ],
				$typeCast[ 'Close' ][ 6 ] ) );

	}

	public static
	function getBranchInfo( $branchID )
	# get information about branch from daabase based on the ID
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_branches";

		$final = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `branchID` = %d", $branchID ) );

		$branchInfo = array( 'branch_hasNoloc' => $final->branch_hasNoloc, 'branch_isPublic' => $final->branch_isPublic, 'branch_isSocial' => $final->branch_isSocial, 'branch_showSocial' => $final->branch_showSocial, 'branchDesc' => $final->branchDesc, 'branchID' => $final->branchID, 'branchAddress' => $final->branchAddress, 'branchMapLink' => $final->branchMapLink, 'branchImageURL' => $final->branchImageURL );


		# parse the times and convert from 24:00:00 to a 12:00 with a bit for PM
		foreach ( $final as $key => $val ) {
			if ( !in_array( substr( $key, 0, 10 ), array( 'branchOpen', 'branchClos' ) ) ) {
				continue;
			}

			if ( empty( $val ) || $val == '00:00:00' ) {
				$branchInfo[ $key ] = NULL;
			} else {
				# make name for PM
				$name = $key . 'PM';
				$convTime = strtotime( '1/1/2000 ' . $val );

				$branchInfo[ $key ] = date( "g:i", $convTime );
				$branchInfo[ $name ] = date( "a", $convTime ) == 'pm' ? TRUE : FALSE;
			}
		}

		if ( true == $branchInfo[ 'branch_isPublic' ] ) {
			$branchInfo[ 'branch_isPublic' ] = 'true';
		} else {
			$branchInfo[ 'branch_isPublic' ] = 'false';
		}

		if ( true == $branchInfo[ 'branch_isSocial' ] ) {
			$branchInfo[ 'branch_isSocial' ] = 'true';
		} else {
			$branchInfo[ 'branch_isSocial' ] = 'false';
		}
		
		if ( true == $branchInfo[ 'branch_showSocial' ] ) {
			$branchInfo[ 'branch_showSocial' ] = 'true';
		} else {
			$branchInfo[ 'branch_showSocial' ] = 'false';
		}		
		
		if ( true == $branchInfo[ 'branch_hasNoloc' ] ) {
			$branchInfo[ 'branch_hasNoloc' ] = 'true';
		} else {
			$branchInfo[ 'branch_hasNoloc' ] = 'false';
		}
		
		return $branchInfo;
	}

	public static
	function getBranchList( $full = NULL, $branch_isPublic = false )
	# get a list of all of the branches. Return NULL on no branches
	# otherwise, return an array with the unique ID of each branch
	# as the key and the description as the val
	{
		global $wpdb;
		$final = array();

		if ( $branch_isPublic == true ) {
			$where = 'WHERE `branch_isPublic` = 1 ';
		} else {
			$where = NULL;
		}

		$table_name = $wpdb->prefix . "bookaroom_branches";
		$sql = "SELECT `branchID`, `branchDesc`, `branchAddress`, `branchMapLink`, `branchImageURL`, `branch_isPublic`, `branch_isSocial`, `branch_showSocial`, `branch_hasNoloc`, `branchOpen_0`, `branchOpen_1`, `branchOpen_2`, `branchOpen_3`, `branchOpen_4`, `branchOpen_5`, `branchOpen_6`, `branchClose_0`, `branchClose_1`, `branchClose_2`, `branchClose_3`, `branchClose_4`, `branchClose_5`, `branchClose_6` FROM `$table_name` {$where}ORDER BY `branchDesc`";

		$count = 0;

		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		if ( count( $cooked ) == 0 ) {
			return array();
		}

		foreach ( $cooked as $key => $val ) {
			if ( $full ) {
				$final[ $val[ 'branchID' ] ] = $val;
			} else {
				$final[ $val[ 'branchID' ] ] = $val[ 'branchDesc' ];
			}
		}

		return $final;
	}

	public static
	function getExternalsBranch()
	# Pull in POST and GET values
	{
		$final = array();

		# setup GET variables
		$getArr = array( 'branchID' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING );

		# pull in and apply to final
		if ( $getTemp = filter_input_array( INPUT_GET, $getArr ) )
			$final += $getTemp;

		# setup POST variables
		$postArr = array( 'action' => FILTER_SANITIZE_STRING,
			'branchID' => FILTER_SANITIZE_STRING,
			'branchDesc' => FILTER_SANITIZE_STRING,
			'branch_isPublic' => FILTER_SANITIZE_STRING,
			'branch_isSocial' => FILTER_SANITIZE_STRING,
			'branch_showSocial' => FILTER_SANITIZE_STRING,
			'branch_hasNoloc' => FILTER_SANITIZE_STRING,
			'branchAddress' => FILTER_SANITIZE_STRING,
			'branchMapLink' => FILTER_SANITIZE_STRING,
			'branchImageURL' => FILTER_SANITIZE_STRING,
			'branchOpen_0' => FILTER_SANITIZE_STRING,
			'branchOpen_0PM' => FILTER_SANITIZE_STRING,
			'branchClose_0' => FILTER_SANITIZE_STRING,
			'branchClose_0PM' => FILTER_SANITIZE_STRING,
			'branchOpen_1' => FILTER_SANITIZE_STRING,
			'branchOpen_1PM' => FILTER_SANITIZE_STRING,
			'branchClose_1' => FILTER_SANITIZE_STRING,
			'branchClose_1PM' => FILTER_SANITIZE_STRING,
			'branchOpen_2' => FILTER_SANITIZE_STRING,
			'branchOpen_2PM' => FILTER_SANITIZE_STRING,
			'branchClose_2' => FILTER_SANITIZE_STRING,
			'branchClose_2PM' => FILTER_SANITIZE_STRING,
			'branchOpen_3' => FILTER_SANITIZE_STRING,
			'branchOpen_3PM' => FILTER_SANITIZE_STRING,
			'branchClose_3' => FILTER_SANITIZE_STRING,
			'branchClose_3PM' => FILTER_SANITIZE_STRING,
			'branchOpen_4' => FILTER_SANITIZE_STRING,
			'branchOpen_4PM' => FILTER_SANITIZE_STRING,
			'branchClose_4' => FILTER_SANITIZE_STRING,
			'branchClose_4PM' => FILTER_SANITIZE_STRING,
			'branchOpen_5' => FILTER_SANITIZE_STRING,
			'branchOpen_5PM' => FILTER_SANITIZE_STRING,
			'branchClose_5' => FILTER_SANITIZE_STRING,
			'branchClose_5PM' => FILTER_SANITIZE_STRING,
			'branchOpen_6' => FILTER_SANITIZE_STRING,
			'branchOpen_6PM' => FILTER_SANITIZE_STRING,
			'branchClose_6' => FILTER_SANITIZE_STRING,
			'branchClose_6PM' => FILTER_SANITIZE_STRING );



		# pull in and apply to final
		if ( $postTemp = filter_input_array( INPUT_POST, $postArr ) ) {
			$final += $postTemp;
		}

		$arrayCheck = array_unique( array_merge( array_keys( $getArr ), array_keys( $postArr ) ) );

		foreach ( $arrayCheck as $key ) {
			if ( !isset( $final[ $key ] ) ) {
				$final[ $key ] = NULL;
			} else {
				$final[ $key ] = trim( $final[ $key ] );
			}
		}

		return $final;
	}

	public static
	function makeRoomAndContList( $branchInfo, $roomContList, $roomList ) {
		$branchID = $branchInfo[ 'branchID' ];
		$container = array();

		# rooms and room containers
		# cycle through each room and map to container (or none), then

		# cycle through any containers that don't have rooms.

		# first cycle containers
		$containers = array();
		$doneRoomList = array();
		if ( !empty( $roomContList[ 'names' ][ $branchID ] ) && count( $roomContList[ 'names' ][ $branchID ] ) !== 0 ) {
			foreach ( $roomContList[ 'names' ][ $branchID ] as $key => $val ) {
				$container[ $key ][ 'name' ] = $val;
				$container[ $key ][ 'rooms' ] = $roomContList[ 'id' ][ $key ][ 'rooms' ];
				$doneRoomList = array_merge( $doneRoomList, $roomContList[ 'id' ][ $key ][ 'rooms' ] );
				sort( $container[ $key ][ 'rooms' ] );
			}
			$doneRoomList = array_unique( $doneRoomList );
			sort( $doneRoomList );
		}

		# check for any rooms not in final room list
		$allRoomsBranch = array();
		if ( !empty( $roomList[ 'room' ][ $branchID ] ) ) {
			$allRoomsBranch = array_keys( $roomList[ 'room' ][ $branchID ] );
		}
		$unknown = array_diff( $allRoomsBranch, $doneRoomList );

		if ( count( $unknown ) !== 0 ) {
			$container[ NULL ] = array( 'name' => 'No container', 'rooms' => $unknown );
		}

		return $container;
	}

	public static
	function showBranchDelete( $branchInfo, $roomContList, $roomList )
	# show delete page and fill with values
	{
		# setup times
		$timeDisp = array();
		$am = __( 'AM', 'book-a-room' );
		$pm = __( 'PM', 'book-a-room' );

		for ( $d = 0; $d <= 6; $d++ ) {
			# find if closed
			if ( empty( $branchInfo[ "branchOpen_{$d}" ] ) or empty( $branchInfo[ "branchClose_{$d}" ] ) ) {
				$timeDisp[ $d ] = 'Closed';
			} else {
				# get open and close time
				$openTime = $branchInfo[ "branchOpen_{$d}" ];
				$openTime .= ( empty( $branchInfo[ "branchOpen_{$d}PM" ] ) ) ? " {$am}" : " {$pm}";
				$closeTime = $branchInfo[ "branchClose_{$d}" ];
				$closeTime .= ( empty( $branchInfo[ "branchClose_{$d}PM" ] ) ) ? " {$am}" : " {$pm}";
				$timeDisp[ $d ] = $openTime . ' to ' . $closeTime;
			}
		}

		require( BOOKAROOM_PATH . 'templates/branches/delete.php' );
	}

	public static
	function showBranchEdit( $branchInfo, $action, $actionName, $externals = array() )
	# show edit page and fill with values
	{
		if ( !empty( $branchInfo[ 'branch_isPublic' ] ) and $branchInfo[ 'branch_isPublic' ] == 'true' ) {
			$branch_isPublicTrue = ' checked="checked"';
			$branch_isPublicFalse = NULL;
		} else {
			$branch_isPublicTrue = NULL;
			$branch_isPublicFalse = ' checked="checked"';
		}

		if ( !empty( $branchInfo[ 'branch_isSocial' ] ) and $branchInfo[ 'branch_isSocial' ] == 'true' ) {
			$branch_isSocialTrue = ' checked="checked"';
			$branch_isSocialFalse = NULL;
		} else {
			$branch_isSocialTrue = NULL;
			$branch_isSocialFalse = ' checked="checked"';
		}
		
		if ( !empty( $branchInfo[ 'branch_showSocial' ] ) and $branchInfo[ 'branch_showSocial' ] == 'true' ) {
			$branch_showSocialTrue = ' checked="checked"';
			$branch_showSocialFalse = NULL;
		} else {
			$branch_showSocialTrue = NULL;
			$branch_showSocialFalse = ' checked="checked"';
		}
		
		if ( !empty( $branchInfo[ 'branch_hasNoloc' ] ) and $branchInfo[ 'branch_hasNoloc' ] == 'true' ) {
			$branch_hasNolocTrue = ' checked="checked"';
			$branch_hasNolocFalse = NULL;
		} else {
			$branch_hasNolocTrue = NULL;
			$branch_hasNolocFalse = ' checked="checked"';
		}

		require( BOOKAROOM_PATH . 'templates/branches/edit.php' );
	}

	public static
	function showBranchList( $branchList )
	# show a list of branches with edit and delete links, or, if none 
	# a message stating there are no branches
	{
		require( BOOKAROOM_PATH . 'templates/branches/mainAdmin.php' );

	}
}
?>