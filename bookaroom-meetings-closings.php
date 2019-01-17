<?php
class bookaroom_settings_closings {
	protected $roomContList = array();
	protected $roomList = array();
	protected $branchList = array();
	protected $amenityList = array();
	protected $externals = array();
	protected $closings = array();

	public static
	function bookaroom_admin_closings() {

		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList();
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		$closings = self::getClosingsList();
		# figure out what to do
		# first, is there an action?
		$externals = self::getExternalsClosings();
		$error = NULL;

		switch ( $externals[ 'action' ] ) {
			case 'add':
				self::showClosingsEdit( NULL, $roomList, $branchList, 'addCheck', 'Add' );
				break;

			case 'addCheck':
				if ( ( $errors = self::checkClosingsEdit( $externals, $roomList ) ) == NULL ) {
					if ( self::addClosing( $externals ) == FALSE ) {
						require( BOOKAROOM_PATH . 'templates/closings/addError.php' );
					} else {
						require( BOOKAROOM_PATH . 'templates/closings/addSuccess.php' );
					}
					break;
				}

				$externals[ 'errors' ] = $errors;
				# show edit screen
				self::showClosingsEdit( $externals, $roomList, $branchList, 'addCheck', 'Add' );

				break;

			case 'edit':
				if ( ( $closingInfo = self::getClosingInfo( $externals[ 'closingID' ] ) ) == FALSE ) {
					require( BOOKAROOM_PATH . 'templates/closings/IDerror.php' );
					break;
				}

				self::showClosingsEdit( $closingInfo, $roomList, $branchList, 'editCheck', 'Edit' );
				break;

			case 'editCheck':
				if ( ( $errors = self::checkClosingsEdit( $externals, $roomList ) ) == NULL ) {
					if ( self::editClosing( $externals ) == FALSE ) {
						require( BOOKAROOM_PATH . 'templates/closings/addError.php' );
					} else {
						require( BOOKAROOM_PATH . 'templates/closings/editSuccess.php' );
					}
					break;
				}
				break;

			case 'delete':
				if ( ( $closingInfo = self::getClosingInfo( $externals[ 'closingID' ] ) ) == FALSE ) {
					require( BOOKAROOM_PATH . 'templates/closings/IDerror.php' );
					break;
				}

				self::showClosingsDelete( $closingInfo, $roomList, $branchList );
				break;

			case 'deleteCheck':
				if ( ( $closingInfo = self::getClosingInfo( $externals[ 'closingID' ] ) ) == FALSE ) {
					require( BOOKAROOM_PATH . 'templates/closings/IDerror.php' );
					break;
				}

				self::deleteClosing( $closingInfo[ 'closingID' ] );
				require( BOOKAROOM_PATH . 'templates/closings/deleteSuccess.php' );
				break;

			case 'deleteMulti':
				# check for valid IDS
				$multiError = NULL;

				if ( self::checkDeleteMultiError( $externals[ 'closingMulti' ], $closings, $multiError ) == TRUE ) {
					self::showClosings( $closings, $roomContList, $roomList, $branchList, $amenityList, $multiError );
					break;
				}
				self::showDeleteMulti( $externals[ 'closingMulti' ], $closings, $roomContList, $roomList, $branchList, $amenityList );
				break;

			case 'deleteMultiCheck':
				$multiError = NULL;
				if ( self::checkDeleteMultiError( $externals[ 'closingMulti' ], $closings, $multiError ) == TRUE ) {
					self::showClosings( $closings, $roomContList, $roomList, $branchList, $amenityList, $multiError );
					break;
				}
				self::deleteClosingMulti( $externals[ 'closingMulti' ] );

				require( BOOKAROOM_PATH . 'templates/closings/deleteSuccess.php' );
				break;

			default:
				self::showClosings( $closings, $roomContList, $roomList, $branchList, $amenityList );
				break;

		}
	}

	protected static
	function addClosing( $externals )
	# add a new branch
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_closings";

		switch ( $externals[ 'selType' ] ) {
			case 'date':
				$userInfo = wp_get_current_user();
				$userName = $userInfo->display_name . ' [' . $userInfo->user_login . ']';

				$reoccuring = ( $externals[ 'date_reoccuring' ] ) ? TRUE : FALSE;

				# all rooms?
				switch ( $externals[ 'roomType' ] ) {
					case 'choose':
						$roomsClosed = serialize( $externals[ 'rooms' ] );
						$allClosed = FALSE;
						break;
					case 'all':
						$roomsClosed = NULL;
						$allClosed = TRUE;
						break;

					default:
						die( 'Error: wrong room type' );
						break;
				}

				$final = $wpdb->insert( $table_name, array(
					'reoccuring' => $reoccuring,
					'type' => $externals[ 'selType' ],
					'startDay' => $externals[ 'date_single_day' ],
					'startMonth' => $externals[ 'date_single_month' ],
					'startYear' => $externals[ 'date_single_year' ],
					'allClosed' => $allClosed,
					'roomsClosed' => $roomsClosed,
					'closingName' => $externals[ 'closingName' ],
					'username' => $userName ) );
				return TRUE;
				break;

			case 'range':
				$userInfo = wp_get_current_user();
				$userName = $userInfo->display_name . ' [' . $userInfo->user_login . ']';
				$reoccuring = ( $externals[ 'dateRange_reoccuring' ] ) ? TRUE : FALSE;
				# all rooms?
				switch ( $externals[ 'roomType' ] ) {
					case 'choose':
						$roomsClosed = serialize( $externals[ 'rooms' ] );
						$allClosed = FALSE;
						break;
					case 'all':
						$roomsClosed = NULL;
						$allClosed = TRUE;
						break;

					default:
						die( 'Error: wrong room type' );
						break;
				}

				$final = $wpdb->insert( $table_name, array(
					'reoccuring' => $reoccuring,
					'type' => $externals[ 'selType' ],
					'endDay' => $externals[ 'date_end_day' ],
					'endMonth' => $externals[ 'date_end_month' ],
					'endYear' => $externals[ 'date_end_year' ],
					'startDay' => $externals[ 'date_start_day' ],
					'startMonth' => $externals[ 'date_start_month' ],
					'startYear' => $externals[ 'date_start_year' ],
					'allClosed' => $allClosed,
					'roomsClosed' => $roomsClosed,
					'closingName' => $externals[ 'closingName' ],
					'username' => $userName ) );
				return TRUE;
				break;

			default:
				return FALSE;
				break;
		}
	}

	protected static
	function checkClosingsEdit( $externals, $roomList ) {
		# check for errors
		$errors = array();
		$final = NULL;
		# type selected
		# closing name
		if ( empty( $externals[ 'closingName' ] ) ) {
			$errors[] = __( 'You must enter a name for this closing.', 'book-a-room' );
		}

		switch ( $externals[ 'selType' ] ) {
			case 'date':
				self::checkDateError( $externals[ 'date_single_year' ], $externals[ 'date_single_month' ], $externals[ 'date_single_day' ], $externals[ 'date_reoccuring' ], 'date', $errors );
				break;

			case 'range':
				$startError = self::checkDateError( $externals[ 'date_start_year' ], $externals[ 'date_start_month' ], $externals[ 'date_start_day' ], $externals[ 'dateRange_reoccuring' ], 'start date', $errors );
				$endError = self::checkDateError( $externals[ 'date_end_year' ], $externals[ 'date_end_month' ], $externals[ 'date_end_day' ], $externals[ 'dateRange_reoccuring' ], 'end date', $errors );

				# start date is different and before end date
				# check for no errors in dates
				if ( ( $startError or $endError ) == FALSE ) {
					# make year

					if ( $externals[ 'dateRange_reoccuring' ] ) {
						$startYear = $endYear = 2012;
					} else {
						$startYear = $externals[ 'date_start_year' ];
						$endYear = $externals[ 'date_end_year' ];
					}
					# make times
					$startTime = mktime( 1, 1, 1, $externals[ 'date_start_month' ], $externals[ 'date_start_day' ], $startYear );
					$endTime = mktime( 1, 1, 1, $externals[ 'date_end_month' ], $externals[ 'date_end_day' ], $endYear );
					if ( $startTime == $endTime ) {
						$errors[] = __( 'Your dates are the same. Instead of a range, choose a single date.', 'book-a-room' );
					} elseif ( $startTime > $endTime ) {
						$errors[] = __( 'Your start time comes after your end time.', 'book-a-room' );
					}

				}
				break;
			default:
				$errors[] = __( 'You must select a valid closing type.', 'book-a-room' );
				break;

		}

		# check type - just in case!		
		$goodTypeArr = array( 'date' => 'Date', 'range' => 'Range' );

		if ( empty( $externals[ 'selType' ] ) or!array_key_exists( $externals[ 'selType' ], $goodTypeArr ) ) {
			$errors[] = __( 'You much choose a closing type.', 'book-a-room' );
		}


		# check for closings
		if ( empty( $externals[ 'roomType' ] ) ) {
			$errors[] = __( 'You must select a rooms type.', 'book-a-room' );
		} elseif ( $externals[ 'roomType' ] !== 'all' ) {
				if ( empty( $externals[ 'rooms' ] ) ) {
					$errors[] = __( 'You must choose at least one room to close.', 'book-a-room' );
				} elseif ( count( array_intersect( array_keys( $roomList[ 'id' ] ), $externals[ 'rooms' ] ) ) == 0 ) {
					$errors[] = __( 'Somehow, you\'ve chosed invalid rooms. Please try again.', 'book-a-room' );
				}
			}
			# closing types
		if ( count( $errors ) !== 0 ) {
			$finalErrors = implode( '<br /><br />', $errors );
			return $finalErrors;
		} else {
			return NULL;
		}

	}

	protected static
	function checkDateError( $year, $month, $day, $reoccuringName, $displayName, & $errors ) {
		$startErrors = count( $errors );

		# empty month
		if ( empty( $month ) or( !is_numeric( $month ) or $month < 1 or $month > 12 ) ) {
			$errors[] = sprintf( __( 'You must choose a month for your %s.', 'book-a-room' ), $displayName );
		}
		# empty day
		if ( empty( $day ) or( !is_numeric( $day ) or $day < 1 or $day > 31 ) ) {
			$errors[] = sprintf( __( 'You must choose a day for your %s.', 'book-a-room' ), $displayName );
		}
		# year if not reoccuring

		if ( empty( $reoccuringName ) && ( empty( $year ) or!is_numeric( $year ) ) ) {
			$errors[] = sprintf( __( 'If not reoccuring, you must choose a year for your %s.', 'book-a-room' ), $displayName );
		}

		# is there that many days in the average month
		#
		# first ignore reoccuring leap year by setting the year to a leap year

		if ( empty( $errors ) ) {
			$curYear = ( $reoccuringName == TRUE ) ? 2012 : $year;

			$numDaysMonth = date( 't', mktime( 1, 1, 1, $month, 1, $curYear ) );
			if ( $day > $numDaysMonth ) {
				$errors[] = sprintf( __( 'There aren\'t that many days in the month for your %s.', 'book-a-room' ) . $displayName );
			}
		}

		# if new errors, return TRUE, otherwise return FALSE
		return ( $startErrors !== count( $errors ) ) ? TRUE : FALSE;
	}

	protected static
	function checkDeleteMultiError( & $multiList, $closings, & $error ) {
		if ( !is_array( $multiList ) or count( $multiList ) == 0 ) {
			$error = __( 'You must select at least one closing to delete.', 'book-a-room' );
			return TRUE;
		}

		# is valid numbers?

		$realClosings = array_unique( array_keys( $closings[ 'times' ][ 'live' ] + $closings[ 'times' ][ 'expired' ] ) );
		$multiList = array_intersect( $multiList, $realClosings );
		if ( count( $multiList ) == 0 ) {
			$error = __( 'There are no valid IDs from the list you chose. Please try again.', 'book-a-room' );
			return TRUE;
		}

		return FALSE;

	}

	protected static
	function editClosing( $externals )
	# edit a closing
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "bookaroom_closings";

		switch ( $externals[ 'selType' ] ) {
			case 'date':
				$userInfo = wp_get_current_user();
				$userName = $userInfo->display_name . ' [' . $userInfo->user_login . ']';

				$reoccuring = ( $externals[ 'date_reoccuring' ] ) ? TRUE : FALSE;

				# all rooms?
				switch ( $externals[ 'roomType' ] ) {
					case 'choose':
						$roomsClosed = serialize( $externals[ 'rooms' ] );
						$allClosed = FALSE;
						break;
					case 'all':
						$roomsClosed = NULL;
						$allClosed = TRUE;
						break;

					default:
						die( 'Error: wrong room type' );
						break;
				}

				$final = $wpdb->update( $table_name, array(
						'reoccuring' => $reoccuring,
						'type' => $externals[ 'selType' ],
						'startDay' => $externals[ 'date_single_day' ],
						'startMonth' => $externals[ 'date_single_month' ],
						'startYear' => $externals[ 'date_single_year' ],
						'allClosed' => $allClosed,
						'roomsClosed' => $roomsClosed,
						'closingName' => $externals[ 'closingName' ],
						'username' => $userName ),
					array( 'closingID' => $externals[ 'closingID' ] ) );
				return TRUE;
				break;

			case 'range':
				$userInfo = wp_get_current_user();
				$userName = $userInfo->display_name . ' [' . $userInfo->user_login . ']';
				$reoccuring = ( $externals[ 'dateRange_reoccuring' ] ) ? TRUE : FALSE;
				# all rooms?
				switch ( $externals[ 'roomType' ] ) {
					case 'choose':
						$roomsClosed = serialize( $externals[ 'rooms' ] );
						$allClosed = FALSE;
						break;
					case 'all':
						$roomsClosed = NULL;
						$allClosed = TRUE;
						break;
					default:
						die( 'Error: wrong room type' );
						break;
				}

				$final = $wpdb->update( $table_name, array(
						'reoccuring' => $reoccuring,
						'type' => $externals[ 'selType' ],
						'endDay' => $externals[ 'date_end_day' ],
						'endMonth' => $externals[ 'date_end_month' ],
						'endYear' => $externals[ 'date_end_year' ],
						'startDay' => $externals[ 'date_start_day' ],
						'startMonth' => $externals[ 'date_start_month' ],
						'startYear' => $externals[ 'date_start_year' ],
						'allClosed' => $allClosed,
						'roomsClosed' => $roomsClosed,
						'closingName' => $externals[ 'closingName' ],
						'username' => $userName ),
					array( 'closingID' => $externals[ 'closingID' ] ) );
				return TRUE;
				break;

			default:
				return FALSE;
				break;
		}
	}

	protected static
	function deleteClosing( $closingID ) {
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_closings";

		$sql = "DELETE FROM `{$table_name}` WHERE `closingID` = '{$closingID}' LIMIT 1";
		$wpdb->query( $sql );

	}

	protected static
	function deleteClosingMulti( $multiList ) {
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_closings";
		$mutliString = implode( ',', $multiList );
		$limit = count( $multiList );
		$sql = "DELETE FROM `{$table_name}` WHERE `closingID` IN ({$mutliString}) LIMIT {$limit}";
		$wpdb->query( $sql );
	}

	protected static
	function getClosingInfo( $closingID ) {
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_closings";
		$sql = "SELECT `closingID` as `closingID`, `reoccuring`, `type` as `selType`, `startDay`, `startMonth`, `startYear`, `endDay`, `endMonth`, `endYear`, `closingName`, `allClosed`, `roomsClosed` FROM `$table_name` WHERE `closingID` = '{$closingID}'";

		$cooked = $wpdb->get_row( $sql, ARRAY_A );

		if ( empty( $cooked ) ) {
			return FALSE;
		}

		$final = array();

		# simple substitutions
		$easyArr = array( 'closingName', 'closingID', 'date_single_month', 'date_single_day', 'date_single_year', 'date_start_month', 'date_start_day', 'date_start_year', 'date_end_month', 'date_end_day', 'date_end_year', 'date_reoccuring', 'dateRange_reoccuring', 'selType' );

		foreach ( $easyArr as $key => $val ) {
			$final[ $val ] = ( empty( $cooked[ $val ] ) ) ? NULL : $cooked[ $val ];
		}

		# roomType = all rooms or selected rooms
		if ( $cooked[ 'allClosed' ] == TRUE ) {
			$final[ 'roomType' ] = 'all';
			$final[ 'rooms' ] = NULL;
		} else {
			$final[ 'roomType' ] = 'choose';
			$final[ 'rooms' ] = unserialize( $cooked[ 'roomsClosed' ] );
		}
		# dates
		switch ( $cooked[ 'selType' ] ) {
			case 'date':
				$final[ 'date_reoccuring' ] = ( $cooked[ 'reoccuring' ] == TRUE ) ? TRUE : FALSE;
				$final[ 'date_single_month' ] = $cooked[ 'startMonth' ];
				$final[ 'date_single_day' ] = $cooked[ 'startDay' ];
				$final[ 'date_single_year' ] = $cooked[ 'startYear' ];
				break;
			case 'range':
				$final[ 'dateRange_reoccuring' ] = ( $cooked[ 'reoccuring' ] == TRUE ) ? TRUE : FALSE;
				$final[ 'date_start_month' ] = $cooked[ 'startMonth' ];
				$final[ 'date_start_day' ] = $cooked[ 'startDay' ];
				$final[ 'date_start_year' ] = $cooked[ 'startYear' ];
				$final[ 'date_end_month' ] = $cooked[ 'endMonth' ];
				$final[ 'date_end_day' ] = $cooked[ 'endDay' ];
				$final[ 'date_end_year' ] = $cooked[ 'endYear' ];
				break;
		}
		return $final;
	}

	public static
	function getClosingsList()
	# get a list of all of the cllsings. Return NULL on no closings
	# otherwise, return an array with the closing information
	{
		global $wpdb;
		$weekArr = self::getWeekArray();

		$table_name = $wpdb->prefix . "bookaroom_closings";
		$sql = "SELECT `closingID`, `reoccuring`, `type`, `startDay`, `startMonth`, `startYear`, `endDay`, `endMonth`, `endYear`, `spWeek`, `spDay`, `spMonth`, `spYear`, `closingName`, `allClosed`, `roomsClosed` FROM `$table_name` ";

		$count = 0;

		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		if ( count( $cooked ) == 0 ) {
			return NULL;
		}

		foreach ( array( 'live', 'expired' ) as $status ) {
			$final[ 'times' ][ $status ] = array();
			$final[ $status ] = array();
		}

		foreach ( $cooked as $key => $val ) {
			switch ( $val[ 'type' ] ) {
				case 'date':
					$val[ 'startYear' ] = ( $val[ 'reoccuring' ] == TRUE ) ? date( 'Y' ) : $val[ 'startYear' ];
					$val[ 'startTime' ] = mktime( 0, 0, 0, $val[ 'startMonth' ], $val[ 'startDay' ], $val[ 'startYear' ] );
					break;
				case 'range':
					$val[ 'startYear' ] = ( $val[ 'reoccuring' ] == TRUE ) ? date( 'Y' ) : $val[ 'startYear' ];
					$val[ 'endYear' ] = ( $val[ 'reoccuring' ] == TRUE ) ? date( 'Y' ) : $val[ 'endYear' ];
					$val[ 'startTime' ] = mktime( 0, 0, 0, $val[ 'startMonth' ], $val[ 'startDay' ], $val[ 'startYear' ] );
					$val[ 'endTime' ] = mktime( 23, 59, 59, $val[ 'endMonth' ], $val[ 'endDay' ], $val[ 'endYear' ] );
					break;
			}
			$val[ 'niceStartTime' ] = date_i18n( 'l, F jS, Y', $val[ 'startTime' ] );
			$type = ( $val[ 'reoccuring' ] == FALSE && $val[ 'startTime' ] < time() ) ? 'expired' : 'live';
			# rooms			
			$final[ $type ][ ( string )$val[ 'closingID' ] ] = $val;
		}
		# sort

		foreach ( array( 'live', 'expired' ) as $status ) {
			$times = array();
			if ( empty( $final[ $status ] ) ) {
				continue;
			}
			foreach ( $final[ $status ] as $key => $val ) {
				$times[ $key ] = $val[ 'startTime' ];
			}
			asort( $times );
			$final[ 'times' ][ $status ] = $times;
		}
		return $final;
	}

	protected static
	function getExternalsClosings()
	# Pull in POST and GET values
	{
		$final = array();

		# setup GET variables
		$getArr = array( 'action' => FILTER_SANITIZE_STRING,
			'closingID' => FILTER_SANITIZE_STRING, );

		# pull in and apply to final
		if ( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final = $getTemp;
		}
		# setup POST variables
		$postArr = array( 'action' => FILTER_SANITIZE_STRING,
			'closingID' => FILTER_SANITIZE_STRING,
			'closingName' => FILTER_SANITIZE_STRING,
			'roomType' => FILTER_SANITIZE_STRING,
			'date_single_month' => FILTER_SANITIZE_STRING,
			'date_single_day' => FILTER_SANITIZE_STRING,
			'date_single_year' => FILTER_SANITIZE_STRING,
			'selType' => FILTER_SANITIZE_STRING,
			'date_start_month' => FILTER_SANITIZE_STRING,
			'date_start_day' => FILTER_SANITIZE_STRING,
			'date_start_year' => FILTER_SANITIZE_STRING,
			'date_end_month' => FILTER_SANITIZE_STRING,
			'date_end_day' => FILTER_SANITIZE_STRING,
			'date_end_year' => FILTER_SANITIZE_STRING,
			'date_reoccuring' => FILTER_SANITIZE_STRING,
			'dateRange_reoccuring' => FILTER_SANITIZE_STRING,
			'roomChecked' => array( 'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_REQUIRE_ARRAY ),
			'closingMulti' => array( 'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_REQUIRE_ARRAY ) );



		# pull in and apply to final
		if ( $postTemp = filter_input_array( INPUT_POST, $postArr ) ) {
			$final = array_merge( $final, $postTemp );
		}

		$rooms = array();
		# check rooms
		if ( !empty( $final[ 'roomChecked' ] ) ) {
			$rooms = array();
			foreach ( $final[ 'roomChecked' ] as $key => $val ) {
				if ( substr( $key, 0, 5 ) == 'room_' ) {
					$rooms[] = substr( $key, 5 );
				}
			}
		}
		# check multiple closings
		$closingMulti = array();
		if ( !empty( $final[ 'closingMulti' ] ) ) {
			$closingMulti = $final[ 'closingMulti' ];
		}

		unset( $final[ 'roomChecked' ] );

		$arrayCheck = array_unique( array_merge( array_keys( $getArr ), array_keys( $postArr ) ) );

		foreach ( $arrayCheck as $key ) {
			if ( empty( $final[ $key ] ) ) {
				$final[ $key ] = NULL;
			} elseif ( is_array( $final[ $key ] ) ) {
				$final[ $key ] = array_keys( $final[ $key ] );
			} else {
				$final[ $key ] = trim( $final[ $key ] );
			}
		}

		$final[ 'rooms' ] = $rooms;
		$final[ 'closingMulti' ] = $closingMulti;

		return $final;
	}

	protected static
	function getWeekArray()
	# return a list of common date related things.
	# day = key(1-7) = val('Sunday' - 'Satuday')
	# week = key(1-4) = val('First' - 'Fourth')
	# month = key(1-12) - val = month names
	{

		return array( 'day' => array( 1 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ), 'week' => array( 1 => 'First', 'Second', 'Third', 'Fourth' ), 'month' => array( 1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July ', 'August', 'September', 'October', 'November', 'December' ) );
	}

	protected static
	function makeRooms( $roomSer )
	# make a clean list of rooms (by branch) from the list of room IDs
	{
		# check if empty list
		if ( empty( $roomSer ) ) {
			return __( 'Error!', 'book-a-room' );
		}

		# unserialize and check for empty array
		$roomArr = unserialize( $roomSer );

		if ( empty( $roomArr ) ) {
			return __( 'No rooms!', 'book-a-room' );
		}

		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList();

		# check for valid rooms

		$finalArr = array_intersect( $roomArr, array_keys( $roomList[ 'id' ] ) );
		if ( empty( $roomArr ) ) {
			return __( 'No rooms!', 'book-a-room' );
		}

		# make nice list
		$temp = array();
		$branches = array();
		$final = array();

		foreach ( $finalArr as $key => $val ) {
			$temp[ $branchList[ $roomList[ 'id' ][ $val ][ 'branch' ] ] ][] = $roomList[ 'id' ][ $val ][ 'desc' ];
		}

		# clean branch list
		foreach ( $temp as $key => $val ) {
			$bigTemp = '<strong>' . $key . '</strong><br />';
			$bigTemp .= implode( '<br />', $val );
			$final[] = $bigTemp;
		}


		return implode( '<br />', $final );
	}

	protected static
	function showClosings( $closings, $roomContList, $roomList, $branchList, $amenityList, $multiError = NULL ) {
		require( BOOKAROOM_PATH . 'templates/closings/mainAdmin.php' );
	}

	protected static
	function showClosingsEdit( $closingInfo, $roomList, $branchList, $action, $actionName ) {
		$yearsAhead = 4;
		$weekArr = self::getWeekArray();
		$init_openArr = array();
		$finalRoom = array();

		foreach ( $branchList as $key => $val ) {
			$init_openArr[] = "\"branch_{$key}\"";
		}

		$init_open = implode( ', ', $init_openArr );

		require( BOOKAROOM_PATH . 'templates/closings/edit.php' );
	}

	protected static
	function showClosingsDelete( $closingInfo, $roomList, $branchList )
	# show the imfo and option to permanantly delete a closing
	{
		require( BOOKAROOM_PATH . 'templates/closings/delete.php' );
	}

	protected static
	function showDeleteMulti( $multiList, $closings, $roomContList, $roomList, $branchList, $amenityList ) {
		require( BOOKAROOM_PATH . 'templates/closings/deleteMulti.php' );
	}
}
?>