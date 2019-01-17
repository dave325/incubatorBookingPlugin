<?PHP
class bookaroom_public
# main settings functions
{
	# main funcion - initialize and search for action variable.
	# if no action, return the regular content
	public static function form( $val ){
		$calendarPage = get_option( 'bookaroom_reservation_URL' );
		$perms = get_option('permalink_structure');
		
		$myURLRaw = parse_url( $_SERVER['REQUEST_URI'] );
		$myURL = $myURLRaw['path'];
		if( $perms ) {
			if( $myURL !== $calendarPage ) {
				return $val;
			}
		} else {
			parse_str( $myURLRaw['query'], $queryVals );
			if( $queryVals['page_id'] !== $calendarPage ) {
				return $val;
			}
		}
	}
	
	public static function mainForm()
	{
		# get external variables from GET and POST
		$externals = self::getExternalsPublic();
		
		# includes
		require_once( BOOKAROOM_PATH . '/bookaroom-meetings-amenities.php' );
		require_once( BOOKAROOM_PATH . '/bookaroom-meetings-rooms.php' );
		require_once( BOOKAROOM_PATH . '/bookaroom-meetings-branches.php' );
		require_once( BOOKAROOM_PATH . '/bookaroom-meetings-roomConts.php' );
		require_once( BOOKAROOM_PATH . '/bookaroom-meetings-closings.php' );
		require_once( BOOKAROOM_PATH . '/bookaroom-meetings-cityManagement.php' );
		
		# vaiables from includes
		$roomContList = bookaroom_settings_roomConts::getRoomContList( true );
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE, TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		$realAmenityList = bookaroom_settings_amenities::getAmenityList( true );
		$cityList = bookaroom_settings_cityManagement::getCityList( );
		$stateList = self::getStates();
		
		# check action
		switch( $externals['action'] ) {
			case 'checkForm':
				
				if( ( $errorMSG = self::showForm_checkHoursError( $externals['startTime'], $externals['endTime'], $externals['roomID'], $roomContList, $branchList ) ) == TRUE ) {
					
					return self::showForm_hoursError( $errorMSG, $externals );
				} elseif( FALSE !== ( $errorArr = self::showForm_checkErrors( $externals, $branchList, $roomContList, $roomList, $amenityList, $cityList ) ) ) {	
					return self::showForm_publicRequest( $externals['roomID'], $branchList, $roomContList, $roomList, $realAmenityList, $cityList, $externals, $errorArr );
				} else {
					self::sendAlertEmail( $externals, $amenityList, $roomContList, $branchList );
					self::showForm_insertNewRequest( $externals, NULL, $cityList );
					return self::sendCustomerReceiptEmail( $externals, $amenityList, $roomContList, $branchList );					
				}

				break;
			
			case 'fillForm':
				$externals['action'] = 'checkForm';
				# setup times
				$hoursList = array_filter( $externals['hours'], 'strlen' );
				$baseIncrement = get_option( 'bookaroom_baseIncrement' );
				$externals['startTime'] = current( $hoursList );
				$externals['endTime'] = end( $hoursList ) + ( $baseIncrement * 60 );
				
				# check for SESSION variables
							
				if( 'usa' ==  get_option( 'bookaroom_addressType' ) ) {
					$externals['contactState'] = array_search( get_option( 'bookaroom_defaultState_name' ), $stateList );
				} else {
					$externals['contactState'] = get_option( 'bookaroom_defaultState_name' );	
				}

				$externals['nonProfit'] = FALSE;
				
				if( !empty( $_SESSION['savedMeetingFormVals']['sessionGo'] ) ) {
					self::getSession( $externals );
				}
				
				if( ( $errorMSG = self::showForm_checkHoursError( $externals['startTime'], $externals['endTime'], $externals['roomID'], $roomContList, $branchList ) ) == TRUE ) {
					return self::showForm_hoursError( $errorMSG, $externals );
				} else {
					return self::showForm_publicRequest( $externals['roomID'], $branchList, $roomContList, $roomList, $realAmenityList, $cityList, $externals );
				}
				break;
				
			case 'calDate':
				$timestamp = self::makeTimestamp( $externals );
				return self::showRooms_day( $externals['roomID'], $externals['branchID'], $timestamp, $branchList, $roomContList, $roomList, $amenityList, $cityList );
				break;
			
			case 'reserve':
				if( empty( $externals['roomID'] ) ) {
					return self::showRooms( $branchList, $roomContList, $roomList, $amenityList );
					break;
				}
				return self::showRooms_day( $externals['roomID'], $externals['branchID'], $externals['timestamp'], $branchList, $roomContList, $roomList, $amenityList, $cityList );
				
				break;
					
			default:
				$_SESSION['savedMeetingFormVals'] = NULL;
				return self::showRooms( $branchList, $roomContList, $roomList, $amenityList );
				break;
		}
	}
	
	public static function showForm_checkHoursError( $startTime, $endTime, $roomContID, $roomContList, $branchList, $res_id = NULL )
	{
		global $wpdb;
		$final = FALSE;
		
		# hours bad?
		# check for common logical errors
		################################################################
		$validStart		= strtotime( date( 'Y-m-d' ) ) + ( get_option( 'bookaroom_reserveBuffer' ) * 24 * 60 * 60 );
		$validEnd		= $validStart + ( get_option( 'bookaroom_reserveAllowed' ) * 24 * 60 * 60 );
			  
		# find room's settings
		
		$branchID		= $branchList[$roomContList['id'][$roomContID]['branchID']];
		$dayOfWeek		= date( 'w', $startTime );
		
		$branchStart	= strtotime( date( 'Y-m-d', $startTime).' '. $branchID['branchOpen_'.$dayOfWeek] );
		$branchEnd		= strtotime( date( 'Y-m-d', $endTime).' '.$branchID['branchClose_'.$dayOfWeek] );

		$admin = is_user_logged_in();
		# start or end empty or invalid? 
		if( ( !is_numeric( $startTime ) || !is_numeric( $endTime ) ) ) {
			$final = __( 'Your start and/or end time is invalid. Please return to the time selection page and try again.', 'book-a-room' ); 
		} elseif( ( $startTime <= $validStart || $endTime <= $validStart ) && empty( $res_id) && $admin == false ) {
			# start or end come before valid start date (using reserveBuffer)?
			$final = __( 'Your start and/or end time comes before the allowed Start Date to reserve. Please return to the time selection page and try again.', 'book-a-room' ); 
		} elseif( ( $startTime >= $validEnd || $endTime >= $validEnd ) && empty( $res_id) && $admin == false ) {
			# start or end come after valid end date (using reserveAllowed)?
			$final = __( 'Your start and/or end time comes after the allowed End Date to reserve. Please return to the time selection page and try again.', 'book-a-room' ); 
		} elseif( $startTime < $branchStart || $endTime < $branchStart ) {
			# start or end come before branch open date?
			$final = __( 'Your start and/or end time comes after the allowed End Date to reserve. Please return to the time selection page and try again.', 'book-a-room' );
		} elseif( $startTime > $branchEnd || $endTime > $branchEnd ) {
			# start or end come after branch close date?
			$final = __( 'Your start and/or end time comes after the branch is closed. Please return to the time selection page and try again.', 'book-a-room' );
		} elseif( $startTime == $endTime ) {
			# start or end date identical?
			$final = __( 'Your start and end times are the same. Please return to the time selection page and try again.', 'book-a-room' );
		} elseif( $startTime > $endTime ) {
			# start come after end date?
			$final = __( 'Your start time comes after your end time. Please return to the time selection page and try again.', 'book-a-room' ); 
		}
		
		$reservations = self::getReservations( $roomContID, $startTime, $endTime, $res_id );
		
		if( count( $reservations ) !== 0 ) {
			$final = __( 'There is a meeting at that time. Please return to the time selection page and try again.', 'book-a-room' ); 
		}
		
		if( !empty( $final ) && empty( $res_id ) && $admin == false ) {
			$final .= '<br />' . __( 'Your form data has been saved. When you choose a new time, your form will retain the information you have already filled out.', 'book-a-room' );
		}
		
		return $final;
	}
	
	protected static function getExternalsPublic()
	# Pull in POST and GET values
	{
		$final = array();
		
		# setup GET variables
		$getArr = array(	'action'				=> FILTER_SANITIZE_STRING,
							'roomID'				=> FILTER_SANITIZE_STRING, 
							'branchID'				=> FILTER_SANITIZE_STRING,
							'calMonth'				=> FILTER_SANITIZE_STRING,
							'calYear'				=> FILTER_SANITIZE_STRING,	
							'timestamp'				=> FILTER_SANITIZE_STRING,
							'resID'					=> FILTER_SANITIZE_STRING, 
							'hash'					=> FILTER_SANITIZE_STRING, );
		
		# pull in and apply to final
		if( $getTemp = filter_input_array( INPUT_GET, $getArr ) )
			$final = $getTemp;
		# setup POST variables
		$postArr = array(	'action'					=> FILTER_SANITIZE_STRING, 
							'amenity' 					=> array(	'filter' => FILTER_SANITIZE_STRING,
																	'flags'  => FILTER_FORCE_ARRAY ),
							'contactAddress1'			=> FILTER_SANITIZE_STRING, 
							'contactAddress2'			=> FILTER_SANITIZE_STRING, 
							'contactCity'				=> FILTER_SANITIZE_STRING, 
							'contactCityDrop'			=> FILTER_SANITIZE_STRING, 
							'contactEmail'				=> FILTER_SANITIZE_STRING, 
							'contactName'				=> FILTER_SANITIZE_STRING, 
							'contactPhonePrimary'		=> FILTER_SANITIZE_STRING, 
							'contactPhoneSecondary'		=> FILTER_SANITIZE_STRING, 
							'contactState'				=> FILTER_SANITIZE_STRING, 
							'contactWebsite'			=> FILTER_SANITIZE_STRING, 
							'contactZip'				=> FILTER_SANITIZE_STRING, 
							'desc'						=> FILTER_SANITIZE_STRING, 
							'endTime'					=> FILTER_SANITIZE_STRING, 
							'eventName'					=> FILTER_SANITIZE_STRING, 
							'hours' 					=> array(	'filter' => FILTER_SANITIZE_STRING,
																	'flags'  => FILTER_FORCE_ARRAY ),
							'libcardNum'				=> FILTER_SANITIZE_STRING, 
							'nonProfit'					=> FILTER_SANITIZE_STRING, 
							'notes'						=> FILTER_SANITIZE_STRING, 
							'numAttend'					=> FILTER_SANITIZE_STRING, 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'session'					=> FILTER_SANITIZE_STRING, 
							'isSocial'					=> FILTER_SANITIZE_STRING, 
							'startTime'					=> FILTER_SANITIZE_STRING, 
							'timestamp'					=> FILTER_SANITIZE_STRING );
	
	
		# pull in and apply to final
		if( $postTemp = filter_input_array( INPUT_POST, $postArr ) )
			$final = array_merge( $final, $postTemp );
		
		$arrayCheck = array_unique( array_merge( array_keys( $getArr ), array_keys( $postArr ) ) );
		
		foreach( $arrayCheck as $key ):
			if( empty( $final[$key] ) ):
				$final[$key] = NULL;
			elseif( is_array( $final[$key] ) ):
				$final[$key] = $final[$key];
			else:			
				$final[$key] = trim( $final[$key] );
			endif;
		endforeach;
		
		return $final;
	}
	
	public static function getClosings( $roomID, $timestamp, $roomContList )
	{
		global $wpdb;
		# create date information
		$dateInfo = getdate( $timestamp );
		
		$table_name = $wpdb->prefix . "bookaroom_closings";
		
		$sql = "SELECT `allClosed` , `roomsClosed`, `closingName` 
					FROM `{$table_name}` 
					WHERE 
(
					`type` = 'range' AND
				 	`reoccuring` = '0' AND 
UNIX_TIMESTAMP( CONCAT_WS( '-', CAST( startYear AS CHAR ), LPAD( CAST( startMonth AS CHAR ), 2, '00'), LPAD( CAST( startDay AS CHAR ), 2, '00') ) ) <= UNIX_TIMESTAMP( CONCAT_WS( '-', CAST( '{$dateInfo['year']}' AS CHAR ), LPAD( CAST( '{$dateInfo['mon']}' AS CHAR ), 2, '00'), LPAD( CAST( '{$dateInfo['mday']}' AS CHAR ), 2, '00') ) ) AND
UNIX_TIMESTAMP( CONCAT_WS( '-', CAST( endYear AS CHAR ), LPAD( CAST( endMonth AS CHAR ), 2, '00'), LPAD( CAST( endDay AS CHAR ), 2, '00') ) ) >=
UNIX_TIMESTAMP( CONCAT_WS( '-', CAST( '{$dateInfo['year']}' AS CHAR ), LPAD( CAST( '{$dateInfo['mon']}' AS CHAR ), 2, '00'), LPAD( CAST( '{$dateInfo['mday']}' AS CHAR ), 2, '00') ) ) 
					) OR 					
					(	
								`type` = 'date' AND
							 	`reoccuring` = '0' AND 
								`startDay` = '{$dateInfo['mday']}' AND 
								`startMonth` = '{$dateInfo['mon']}' AND 
								`startYear` = '{$dateInfo['year']}') OR 
					(`type` = 'date' AND `reoccuring` ='1' AND `startDay` = '{$dateInfo['mday']}' AND `startMonth` = '{$dateInfo['mon']}') OR 
					(`type` = 'range' AND `reoccuring` ='0' AND ('{$dateInfo['mday']}' >= `startDay` AND '{$dateInfo['mday']}' <= `endDay`) AND ('{$dateInfo['mon']}' >= `startMonth` AND '{$dateInfo['mon']}' <= `endMonth`) AND ('{$dateInfo['year']}' >= `startYear` AND '{$dateInfo['year']}' <= `endYear`) ) OR
(`type` = 'range' AND `reoccuring` ='1' AND ('{$dateInfo['mday']}' >= `startDay` AND '{$dateInfo['mday']}' <= `endDay`) AND ('{$dateInfo['mon']}' >= `startMonth` AND '{$dateInfo['mon']}' <= `endMonth`))";
		
		$raw = $wpdb->get_results( $sql, ARRAY_A );
		foreach( $raw as $key => $val ) {
			if( $val['allClosed'] == '1' ) {
				return $val['closingName'];
			}
			
			$rooms = unserialize( $val['roomsClosed'] );
			if( count( array_intersect( $roomContList['id'][$roomID]['rooms'], $rooms ) ) !== 0 ) {
				return $val['closingName'];
			}
		}
		
		return false;
	}
	
	
	public static function getReservations( $roomID, $timestamp, $timeEnd = NULL, $res_id = NULL )
	# use timestamp only to get all day's reservation.
	# use timestamp as start time and timeEnd as end time to find reservations in a certain range.
	{
		global $wpdb;
		
		if( empty( $timeEnd ) ) {
			$timeInfo = getdate( $timestamp );
		
			$startTime		= date( 'Y-m-d H:i:s', mktime( 0, 0, 0, $timeInfo['mon'], $timeInfo['mday'], $timeInfo['year'] ) );
			$endTime		= date( 'Y-m-d H:i:s', mktime( 0, 0, 0, $timeInfo['mon'], $timeInfo['mday']+1, $timeInfo['year'] ) - 1 );
		} else {
			$increment	= get_option( 'bookaroom_baseIncrement' );
			$setupInc	= get_option( 'bookaroom_setupIncrement' );			
			$cleanupInc	= get_option( 'bookaroom_cleanupIncrement' );
			$startTime		= date( 'Y-m-d H:i:s', $timestamp + ($increment * 60 * $setupInc) );
			$endTime		= date( 'Y-m-d H:i:s', $timeEnd + ($increment * 60 * $cleanupInc) );
		}
		
		if( !empty( $res_id ) ) {
			$where = " AND `res`.`res_id` != '{$res_id}'";
		} else {
			$where = NULL;
		}
			
		$sql = "SELECT `res`.*, `ti`.* FROM `{$wpdb->prefix}bookaroom_times` as `ti` 
				LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` as `res` ON `ti`.`ti_extID` = `res`.`res_id` 
				WHERE ( `ti`.`ti_startTime`< '{$endTime}' AND `ti`.`ti_endTime` > '{$startTime}' )
				AND `ti`.`ti_roomID` IN (
					SELECT DISTINCT `roomMembers`.`rcm_roomContID` 
					FROM `{$wpdb->prefix}bookaroom_roomConts_members` as `roomMembers` 
					WHERE 	( ( `ti`.`ti_type` = 'meeting' AND ( `res`.me_status != 'archived' and `res`.`me_status` != 'denied')  ) OR 
							( `ti`.`ti_type` = 'event') )	AND 
					`rcm_roomID` IN ( 
						SELECT `roomMembers`.`rcm_roomID` FROM 					
						`{$wpdb->prefix}bookaroom_roomConts_members` as `roomMembers` 
						WHERE rcm_roomContID = '{$roomID}'{$where} ) );";
		$final = $wpdb->get_results( $sql, ARRAY_A );
		
		
		if( get_option('bookaroom_obfuscatePublicNames') == true ) {
			foreach( $final as $key => &$val ) {
				if( $val['ti_type'] !== 'event' ) {
					$val['me_eventName'] = __( 'In use', 'book-a-room' );
				}
			}
		}
		return $final;
	}
	
	protected static function getSession( &$externals )
	{
		$valArr = array( 'action', 'amenity', 'contactAddress1', 'contactAddress2', 'contactCity', 'contactEmail', 'contactName', 'contactPhonePrimary', 'contactPhoneSecondary', 'contactState', 'contactWebsite', 'contactZip', 'desc', 'notes', 'eventName', 'nonProfit', 'numAttend', 'roomID' );
		
		foreach( $valArr as $key ) {
			if(!empty( $_SESSION['savedMeetingFormVals'][$key] ) ) {
				$externals[$key] = $_SESSION['savedMeetingFormVals'][$key];
			} else {
				$externals[$key] = NULL;
			}
		}
		
		$_SESSION['savedMeetingFormVals']['sessionGo'] = FALSE;
		
		return $externals;
	}
	
	public static function getStates()
	{
		return array(	'AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming");
	
	}
	
	protected static function makeTimestamp( $externals )
	{
		$curTimeInfo = getdate( current_time('timestamp') );
		# check for month
		if( empty( $externals['calMonth'] ) || !is_numeric( $externals['calMonth'] ) || ( $externals['calMonth'] < 1 || $externals['calMonth'] > 12 ) ) {
			$month = $curTimeInfo['mon'];
		} else {
			$month = $externals['calMonth'];
		}
		
		# check year
		if( empty( $externals['calYear'] ) || !is_numeric( $externals['calYear'] ) || ( $externals['calYear'] < $curTimeInfo['year']-1 || $externals['calYear'] > $curTimeInfo['year'] +3 ) ) {
			$year = $curTimeInfo['year'];
		} else {
			$year = $externals['calYear'];
		}
		
		$timestamp = mktime( 0, 0, 0, $month, 1, $year );		
		return $timestamp;
		
	}
	
	public static function showForm_checkErrors( &$externals, $branchList, $roomContList, $roomList, $amenityList, $cityList )
	{
		$final = array();
		# library card
		# is social true
		$libcardRegex = get_option( 'bookaroom_libcardRegex' );
		
		if( $externals['isSocial'] ) {
			if( empty( $externals['libcardNum'] ) ) {
				$final['classes']['libcardNum'] = 'error';
				$final['errorMSG'][] = __( 'You must enter your <em>Library Card Number</em>.', 'book-a-room' );
			} elseif( !empty( $libcardRegex ) and FALSE == preg_match( $libcardRegex, $externals['libcardNum'] ) ) {
				$final['classes']['libcardNum'] = 'error';
				$final['errorMSG'][] = __( 'You must enter a valid <em>Library Card Number</em>.', 'book-a-room' );
			}
		}
		
		# event name
		if( empty( $externals['eventName'] ) ) {
			$final['classes']['eventName'] = 'error';
			$final['errorMSG'][] = __( 'You must enter an event name in the <em>Event/Organization name</em> field.', 'book-a-room' );
		} elseif( !mb_check_encoding( $externals['eventName'], 'ASCII') ) {
				$final['errorMSG'][] = __( 'Your <em>Event/Organization name</em> field contains invalid characters. Make sure, if you are copying from Word, you clean up your quotes, single quotes and apostrophes.', 'book-a-room' );
				$final['classes']['eventName'] = 'error';
		}
		
		
		# event name
		if( empty( $externals['numAttend'] ) ) {
			$final['classes']['numAttend'] = 'error';
			$final['errorMSG'][] = __( 'You must enter how many people you expect to attend in <em>Number of attendees</em> field.', 'book-a-room' );
		} elseif( !is_numeric( $externals['numAttend'] )) {
			$final['classes']['numAttend'] = 'error';
			$final['errorMSG'][] = __( 'You must enter valid number <em>Number of attendees</em> field.', 'book-a-room' ); 
		} elseif( $externals['numAttend'] < 1 or $externals['numAttend'] > $roomContList['id'][$externals['roomID']]['occupancy'] ) {
			$final['classes']['numAttend'] = 'error';
			$final['errorMSG'][] = sprintf( __( 'The maximum occupancy of this room is <em>%s</em>. Please enter a valid <em>Number of attendees</em> field.', 'book-a-room' ),  $roomContList['id'][$externals['roomID']]['occupancy'] );			
		}
		
		# purposr of meeting (desc)
		if( empty( $externals['desc'] ) ) {
			$final['classes']['desc'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a value in the <em>Purpose of meeting</em> field.', 'book-a-room' );
		} elseif( !mb_check_encoding( $externals['desc'], 'ASCII') ) {
				$final['errorMSG'][] = __( 'Your <em>Purpose of meeting</em> contains invalid characters. Make sure, if you are copying from Word, you clean up your quotes, single quotes and apostrophes.', 'book-a-room' );
				$final['classes']['desc'] = 'error';
		}
		
		# contact name
		if( empty( $externals['contactName'] ) ) {
			$final['classes']['contactName'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a value in the <em>Contact name</em> field.', 'book-a-room' ); 
		}
		# clean phone
		#$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['contactPhonePrimary'] );
		#if ( strlen( $cleanPhone ) == 11 ) $cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
		# contact primary phone
		# ignore if regex is empty
		$phoneRegex = get_option( 'bookaroom_phone_regex' );
		$phoneExample = get_option( 'bookaroom_phone_example' );
	    
		$cleanPhone = str_replace( array( '(', ')', ' ' ), '', $externals['contactPhonePrimary'] );
		if( empty( $cleanPhone ) ) {
			$final['classes']['contactPhonePrimary'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a value in the <em>Primary phone</em> field.', 'book-a-room' );
		} elseif( !empty( $phoneRegex ) ) {
		# check main phone against regex
			if( !preg_match( $phoneRegex, $cleanPhone ) ) {
				$final['classes']['contactPhonePrimary'] = 'error';
				$final['errorMSG'][] = sprintf( __( 'You must enter a valid phone number in the <em>Primary phone</em> field. Example: %s', 'book-a-room' ), $phoneExample );
			}
		}
			
		$cleanPhone = str_replace( array( '(', ')', ' ' ), '', $externals['contactPhoneSecondary'] );
		if( !empty( $cleanPhone ) and !empty( $phoneRegex ) ) {
		# check main phone against regex
			if( !preg_match( $phoneRegex, $cleanPhone ) ) {
				$final['classes']['contactPhoneSecondary'] = 'error';
				$final['errorMSG'][] = sprintf( __( 'You must enter a valid phone number in the <em>Secondary phone</em> field. Example: %s', 'book-a-room' ), $phoneExample );
			}
		}		
		
		# street address
		if( empty( $externals['contactAddress1'] ) ) {
			$final['classes']['contactAddress1'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a value in the <em>Street Address</em> field.', 'book-a-room' );
		}
		# city
		#
		# first, is social selected
		if( empty( $externals['isSocial'] ) ) {
			if( empty( $externals['contactCity'] ) ) {
				$final['classes']['contactCity'] = 'error';
				$final['errorMSG'][] = __( 'You must enter a value in the <em>City</em> field.', 'book-a-room' );
			}
		} else {
			if( empty( $externals['contactCityDrop'] )) {
				$final['classes']['contactCity'] = 'error';
				$final['errorMSG'][] = __( 'You must choose a <em>City</em> from the drop down.', 'book-a-room' );
			} elseif( $externals['contactCityDrop'] == 'other' ) {
				$final['classes']['contactCity'] = 'error';
				$final['errorMSG'][] = __( 'To use our meetings rooms for social events, you have to live in one of the cities listed in the <em>City</em> drop down. Please refer to our guidelines for more information.', 'book-a-room' );
			} elseif( !array_key_exists( $externals['contactCityDrop'], $cityList ) ) {
				$final['classes']['contactCity'] = 'error';
				$final['errorMSG'][] = __( 'You must choose a valid <em>City</em> from the drop down.', 'book-a-room' );
			}
		}
		
		# state
		if( get_option( 'bookaroom_addressType' ) ==  'usa' ) {
			if( empty( $externals['contactState'] ) ) {
				$final['classes']['contactState'] = 'error';
				$final['errorMSG'][] = __( 'You must choose an item from the <em>State</em> drop-down.', 'book-a-room' );
			} elseif( !array_key_exists( $externals['contactState'], self::getStates() ) ) {
				$final['classes']['contactState'] = 'error';
				$final['errorMSG'][] = __( 'That state is invalid. Please choose a valid item from the <em>State</em> drop-down.', 'book-a-room' );
			}
		} elseif( empty( $externals['contactState'] ) ) {
				$final['classes']['contactState'] = 'error';
				$stateName = get_option( 'bookaroom_state_name' );
				$final['errorMSG'][] = sprintf( __( 'You must enter a value in the <em>%s</em> drop-down.', 'book-a-room' ), $stateName );
		}
		
		# zip code
		$zipRegex = get_option( 'bookaroom_zip_regex' );
		$zipExample = get_option( 'bookaroom_zip_example' );
		
		if( empty( $externals['contactZip'] ) ) {
			$final['classes']['contactZip'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a value in the <em>Zip code</em> field.', 'book-a-room' ); 
		} elseif( !empty( $zipRegex ) ) {
			if( !preg_match( $zipRegex, $externals['contactZip'] ) ) {
				$final['classes']['contactZip'] = 'error';
				$final['errorMSG'][] = sprintf( __( 'You must enter a valid postal code. Example: %s', 'book-a-room' ), $zipExample );
			}
		}
		
		# email address
		if( empty( $externals['contactEmail'] ) ) {
			$final['classes']['contactEmail'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a value in the <em>Email address</em> field.', 'book-a-room' ); 
		} elseif( !filter_var( $externals['contactEmail'], FILTER_VALIDATE_EMAIL ) ) {
			$final['classes']['contactEmail'] = 'error';
			$final['errorMSG'][] = __( 'You must enter a valid address in the <em>Email address</em> field.', 'book-a-room' ); 
		}
		
		# NEW
		if( empty( $final ) ) $final = FALSE;
		return $final;
	}	

	
	protected static function showForm_hoursError( $errorMSG, $externals )
	{
		# save values into session data
		$valArr = array( 'action', 'amenity', 'contactAddress1', 'contactAddress2', 'contactCity', 'contactEmail', 'contactName', 'contactPhonePrimary', 'contactPhoneSecondary', 'contactState', 'contactWebsite', 'contactZip', 'desc', 'notes', 'eventName', 'nonProfit', 'numAttend', 'roomID' );
		
		foreach( $valArr as $key ) {
			$_SESSION['savedMeetingFormVals'][$key] = $externals[$key];
		}
		
		$_SESSION['savedMeetingFormVals']['sessionGo'] = TRUE;
		
		ob_start();        
        require( BOOKAROOM_PATH . 'templates/public/error.php' );        
        $final = ob_get_contents();        
        ob_end_clean();
        
        return $final;        
	}
	
	public static function showForm_insertNewRequest( $externals, $event = NULL, $cityList )
	{
		global $wpdb;
        
        $currentTimeMySQL = date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
		
		$social = ( !empty( $externals['isSocial'] ) ) ? true : false;
		if( $social == true ) {
			$cityName = $cityList[$externals['contactCityDrop']];
		} else {
			$cityName = esc_textarea( $externals['contactCity'] );
		}
		# nonprofit
		$nonProfit = ( empty(  $externals['nonProfit'] ) ) ? 0 : 1;
		
		if( empty( $externals['amenity'] ) ) {
			$amenity = "";
		} else {
			$amenity = serialize( $externals['amenity'] );
		}
		
		if( !empty( $event ) ) {
			$event = TRUE;
			$pending = 'approved';
		} else {
			$event = TRUE;
			$pending = 'approved';
		}
		
		$table_name = $wpdb->prefix . "bookaroom_reservations";
		$final = $wpdb->insert( $table_name, array( 
            'res_created'               => $currentTimeMySQL,
			'me_numAttend'				=> $externals['numAttend'],
			'me_eventName'				=> esc_textarea( $externals['eventName'] ),
			'me_desc'					=> esc_textarea( $externals['desc'] ),
			'me_contactName'			=> esc_textarea( $externals['contactName'] ),
			'me_contactPhonePrimary'	=> $externals['contactPhonePrimary'],
			'me_contactPhoneSecondary'	=> $externals['contactPhoneSecondary'],
			'me_contactAddress1'		=> esc_textarea( $externals['contactAddress1'] ),
			'me_contactAddress2'		=> esc_textarea( $externals['contactAddress2'] ),
			'me_contactCity'			=> $cityName, 
			'me_contactState'			=> $externals['contactState'],
			'me_contactZip'				=> $externals['contactZip'],
			'me_contactEmail'			=> esc_textarea( $externals['contactEmail'] ),
			'me_contactWebsite'			=> esc_textarea( $externals['contactWebsite'] ),
			'me_nonProfit'				=> $nonProfit,
			'me_amenity'				=> $amenity, 
			'me_notes'					=> esc_textarea( $externals['notes'] ),
			'me_libcardNum'				=> esc_textarea( $externals['libcardNum'] ),
			'me_social'					=> $social,
			'me_status'					=> $pending ) );
		$table_name = $wpdb->prefix . "bookaroom_times";
		$final = $wpdb->insert( $table_name, array( 
			'ti_created'                => $currentTimeMySQL,
            'ti_startTime'				=> date( 'Y-m-d H:i:s', $externals['startTime'] ),#
			'ti_endTime'				=> date( 'Y-m-d H:i:s', $externals['endTime'] ),
			'ti_roomID'  				=> $externals['roomID'], 
			'ti_type'                   => 'meeting', 
			'ti_extID'                  => $wpdb->insert_id ) );
		return TRUE;
	}
	
	public static function showForm_publicRequest( $roomContID, $branchList, $roomContList, $roomList, $amenityList, $cityList, $externals, $errorArr = NULL, $edit = NULL )
	{
		if( get_option( 'bookaroom_addressType' ) !== 'usa' ) {
			$address1_name 	= get_option( 'bookaroom_address1_name');
			$address2_name 	= get_option( 'bookaroom_address2_name');
			$city_name 		= get_option( 'bookaroom_city_name');
			$state_name 	= get_option( 'bookaroom_state_name');
			$zip_name 		= get_option( 'bookaroom_zip_name');
		} else {
			$address1_name 	= 'Street Address';
			$address2_name 	= 'Address';
			$city_name 		= 'City';			
			$state_name 	= 'State';
			$zip_name 		= 'Zip Code';
		}		
		
		$NPyesChecked = $NPnoChecked = NULL;
				
		if( !empty( $externals['nonProfit'] ) ) {
			$NPyesChecked = ' checked="checked"';
		} else {
			$NPnoChecked = ' checked="checked"';
		}
		
		$SOyesChecked = $SOnoChecked = null;
			
		if(  true == ( $externals['isSocial'] ) ) {
			$SOyesChecked = ' checked="checked"';
		} else {
			$SOnoChecked = ' checked="checked"';	
		}
		
        ob_start();
        
		if( !empty( $edit ) ) {
			require( BOOKAROOM_PATH . 'templates/meetings/pending_edit.php' );
		} else {
			require( BOOKAROOM_PATH . 'templates/public/publicRequest.php' );
		}
        
        $final = ob_get_contents();
        ob_end_clean();
        
        return $final;  
	}
	
	protected static function showRooms( $branchList, $roomContList, $roomList, $amenityList )
	{
        ob_start();        
		require( BOOKAROOM_PATH . 'templates/public/publicShowRooms.php' );
        $final = ob_get_contents();
        ob_end_clean();
        
        return $final;  
		
	}
	public static function showRooms_day( $roomID, $branchID, $timestamp, $branchList, $roomContList, $roomList, $amenityList, $cityList )
	{
		# get branch and room
		# if Room ID is not empty and is valid
		if( !empty( $roomContList ) and !empty( $roomID ) and in_array( $roomID, array_keys( $roomContList['id'] ) ) ) {
			# find that room's branch
			# if there is no valid branch, both are null (can't have a 
			# valid room that has no branch)
			if( empty( $roomContList['id'][$roomID]['branchID'] ) ) {
				$branchID = NULL;
				$roomID = NULL;
			# if valid branch, map it.
			} else {
				$branchID = $roomContList['id'][$roomID]['branchID'];
			}
		# if the room is empty, check for a branch ID, make sure it's valid
		} elseif( !empty( $branchID ) and in_array( $branchID, array_keys( $branchList ) ) ) {
			# since we have a valid branch, lets see if there are any rooms (make sure that
			# the room list is an array and not empty )
			if( !empty( $roomContList['branch'][$branchID] ) and is_array( $roomContList['branch'][$branchID] ) ) {
				# make the room ID the first room
				$roomID = current( $roomContList['branch'][$branchID] );
			} else {
				# if no rooms, show no available rooms
				$roomID = NULL;
			}
		} else {
			$branchID = NULL;
			$roomID = NULL;
		}
        ob_start();
		require( BOOKAROOM_PATH . 'templates/public/publicShowRooms_day.php' );
		$final = ob_get_contents();
        ob_end_clean();
        
        return $final;  
	}
	
	public static function sendAlertEmail( $externals, $amenityList, $roomContList, $branchList, $admin = false )
	{
		$filename = BOOKAROOM_PATH . 'templates/public/adminNewRequestAlert.html';	
		$handle = fopen( $filename, "r" );
		$contents = fread( $handle, filesize( $filename ) );
		fclose( $handle );
		
		$contents = str_replace( '#pluginLocation#', plugins_url( '', __FILE__ ), $contents );		
		
		# Amenities
		if( empty( $externals['amenity'] ) ) {
			$externals['amenityVal'] = __( 'No amenities', 'book-a-room' );
		} else {
			$amentiyArr = array();
			foreach( $externals['amenity'] as $key => $val ) {
				$amenityArr[] = $amenityList[$val];
			}
			$externals['amenityVal'] = implode( ', ', $amenityArr );
		}
		if( $externals['nonProfit'] == TRUE ) {
			$externals['nonProfitDisp'] = 'Yes';
			$externals['roomDeposit'] = get_option( 'bookaroom_nonProfitDeposit' );
			$costIncrement =  get_option( 'bookaroom_nonProfitIncrementPrice' );
		} else {
			$externals['nonProfitDisp'] = 'No';
			$externals['roomDeposit'] = get_option( 'bookaroom_profitDeposit' );
			$costIncrement =  get_option( 'bookaroom_profitIncrementPrice' );
		}
		
		$roomCount = 1;
		
		$externals['roomCost'] = ( ( ( ( $externals['endTime'] - $externals['startTime'] ) / 60 ) / get_option( 'bookaroom_baseIncrement' ) ) * $costIncrement ) * $roomCount;
		$externals['roomTotal'] = $externals['roomCost'] + $externals['roomDeposit'];
		
		# times
		$externals['formDate'] = date_i18n( 'l, F jS, Y', $externals['startTime'] );
		$externals['startTimeDisp'] = date( 'g:i a', $externals['startTime'] );
		$externals['endTimeDisp'] = date( 'g:i a', $externals['endTime'] );
		
		# branch name
		$roomID = $externals['roomID'];
		
		$externals['roomName'] = $roomContList['id'][$externals['roomID']]['desc'];
		$externals['branchName'] = $branchList[$roomContList['id'][$externals['roomID']]['branchID']]['branchDesc'];
		
		# payment link
		$externals['paymentLink'] = 'test.php';
		
		# array of all values
		$valArr = array( 'amenityVal','branchName','branchNames','contactAddress1','contactAddress2','contactCity','contactEmail','contactName','contactPhonePrimary','contactPhoneSecondary','contactState','contactWebsite','contactZip','desc','endTimeDisp','eventName','formDate','nonProfitDisp','numAttend', 'paymentLink', 'roomCost','roomDeposit','roomID','roomName','roomTotal','startTimeDisp' );
		
		foreach( $valArr as $key => $val ) {
			$contents = @str_replace( "#{$val}#", $externals[$val], $contents );
		}
	
		$email = get_option( 'bookaroom_alertEmail' );;
		
		$fromName	= get_option( 'bookaroom_alertEmailFromName' );
		$fromEmail	= get_option( 'bookaroom_alertEmailFromEmail' );
		
		$replyToOnly	= ( true == get_option( 'bookaroom_emailReplyToOnly' ) ) ? "From: {$fromName}\r\nReply-To: {$fromName} <{$fromEmail}>\r\n" : "From: {$fromName} <{$fromEmail}>\r\n";
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					$replyToOnly .
				    'X-Mailer: PHP/' . phpversion();
		/* translators: Book a Room Event Created: BRANCH_NAME [ROOM_NAME] by CONTACT_NAME on START_DATE from START_TIME to END_TIME */
		$subject = sprintf( __( 'Book a Room Event Created: %s [%s] by %s on %s from %s to %s', 'book-a-room' ), $externals['branchName'], $externals['roomName'], $externals['contactName'], $externals['formDate'], $externals['startTimeDisp'], $externals['endTimeDisp'] );
		
		mail( $email, $subject, $contents, $headers );
		
		if( $admin == true ) {
			$subject = sprintf( __( 'Your Book a Room Event Details: %s [%s] by %s on %s from %s to %s', 'book-a-room' ), $externals['branchName'], $externals['roomName'], $externals['contactName'], $externals['formDate'], $externals['startTimeDisp'], $externals['endTimeDisp'] );
			mail( $contactEmail, $subject, $contents, $headers );
		}
	}
	
	public static function sendCustomerReceiptEmail( $externals, $amenityList, $roomContList, $branchList, $internal = false )
	{
		$fromName	= get_option( 'bookaroom_alertEmailFromName' );
		$fromEmail	= get_option( 'bookaroom_alertEmailFromEmail' );
		
		$replyToOnly	= ( true == get_option( 'bookaroom_emailReplyToOnly' ) ) ? "From: {$fromName}\r\nReply-To: {$fromName} <{$fromEmail}>\r\n" : "From: {$fromName} <{$fromEmail}>\r\n";
		
		if( $internal == TRUE ) {
			$subject	= get_option( 'bookaroom_newInternal_subject' );
			$contents	= html_entity_decode( get_option( 'bookaroom_newInternal_body' ) );
		} else {
			$subject	= get_option( 'bookaroom_newAlert_subject' );
			$contents	= html_entity_decode( get_option( 'bookaroom_newAlert_body' ) );
		}
		
		$subject	= self::replaceItems( $subject, $externals, $amenityList, $roomContList, $branchList );
		$contents	= self::replaceItems( $contents, $externals, $amenityList, $roomContList, $branchList );
		
		$option['bookaroom_profitDeposit']				= get_option( 'bookaroom_profitDeposit' );
		$option['bookaroom_nonProfitDeposit']			= get_option( 'bookaroom_nonProfitDeposit' );
		$option['bookaroom_profitIncrementPrice']		= get_option( 'bookaroom_profitIncrementPrice' );
		$option['bookaroom_nonProfitIncrementPrice']	= get_option( 'bookaroom_nonProfitIncrementPrice' );
		$option['bookaroom_baseIncrement']				= get_option( 'bookaroom_baseIncrement' );
				
		$roomCount = count( $roomContList['id'][$externals['roomID']]['rooms'] );
				
		if( empty( $externals['nonProfit'] ) ) {
			# find how many increments
			$minutes = ( ( $externals['endTime'] - $externals['startTime'] ) / 60 ) / $option['bookaroom_baseIncrement'] ;
			$roomPrice = $minutes * $option['bookaroom_profitIncrementPrice'] * $roomCount;
			$deposit = 	intval( $option['bookaroom_profitDeposit'] );
		} else {
			# find how many increments
			$minutes = ( ( $externals['endTime'] - $externals['startTime'] ) / 60 ) / $option['bookaroom_baseIncrement'] ;
			$roomPrice = $minutes * $option['bookaroom_nonProfitIncrementPrice'] * $roomCount;
			$deposit = 	intval( $option['bookaroom_nonProfitDeposit'] );	
		}
		
		$contents = str_replace( '{deposit}', $deposit, $contents );
		$contents = str_replace( '{roomPrice}', $roomPrice, $contents );
		$contents = str_replace( '{totalPrice}', $roomPrice+$deposit, $contents );
		
		# create email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					$replyToOnly . 				
				    'X-Mailer: PHP/' . phpversion();

		mail( $externals['contactEmail'], $subject, $contents, $headers );

		return $contents;		
	}
	
	public static function replaceItems( $contents, $externals, $amenityList, $roomContList, $branchList )
	{
		$oldExternals = $externals;
		
		# Amenities
		if( empty( $externals['amenity'] ) ) {
			$externals['amenity'] = __( 'No amenities', 'book-a-room' );
		} else {
			$amentiyArr = array();
			foreach( $externals['amenity'] as $key => $val ) {
				$amenityArr[] = $amenityList[$val];
			}
			$externals['amenity'] = implode( ', ', $amenityArr );
		}
		
		if( $externals['nonProfit'] == TRUE ) {
			$externals['nonProfit'] = 'Yes';
		} else {
			$externals['nonProfit'] = 'No';
		}

		# date 1 - two weeks from now
		$timeArr = getdate( time() );
		$date1 = mktime( 0,0,0, $timeArr['mon'], $timeArr['mday']+14, $timeArr['year'] );
		$date3 = mktime( 0,0,0, $timeArr['mon'], $timeArr['mday']+1, $timeArr['year'] );
		
		# two weeks before event		
		$timeArr = getdate( $externals['startTime'] );
		$date2 = mktime( 0,0,0, $timeArr['mon'], $timeArr['mday']-14, $timeArr['year'] );
		
		
		# check dates
		$mainDate = min( $date1, $date2 );
		
		if( $mainDate < $date3 ) {
			$mainDate = $date3;
		}

		$externals['paymentDate'] = date_i18n( 'l, F jS, Y', $mainDate );
		
		$externals['paymentLink'] = 'test.php';
				

		# times
		$externals['date'] = date_i18n( 'l, F jS, Y', $externals['startTime'] );
		$externals['startTime'] = date( 'g:i a', $externals['startTime'] );
		$externals['endTime'] = date( 'g:i a', $externals['endTime'] );
		
		# branch name
		$roomID = $externals['roomID'];
		
		$externals['roomName'] = $roomContList['id'][$externals['roomID']]['desc'];
		$externals['branchName'] = $branchList[$roomContList['id'][$externals['roomID']]['branchID']]['branchDesc'];

		# costs
		if( empty( $oldExternals['nonProfit'] ) ) {
			$externals['roomDeposit'] = get_option( 'bookaroom_profitDeposit' );
			$costIncrement =  get_option( 'bookaroom_profitIncrementPrice' );
		} else {
			$externals['roomDeposit'] = get_option( 'bookaroom_nonProfitDeposit' );
			$costIncrement =  get_option( 'bookaroom_nonProfitIncrementPrice' );
		}
		
		$externals['roomCost'] = ( ( ( ( $oldExternals['endTime'] - $oldExternals['startTime'] ) / 60 ) / get_option( 'bookaroom_baseIncrement' ) ) * $costIncrement );
		$externals['roomTotal'] = $externals['roomCost'] + $externals['roomDeposit'];
		
		# final			
		$goodArr = array( 'amenity','branchName','contactAddress1','contactAddress2','contactCity','contactEmail','contactName','contactPhonePrimary','contactPhoneSecondary','contactState','contactWebsite','contactZip','date','desc','endTime','eventName','nonProfit','numAttend','paymentDate','paymentLink','roomCost','roomDeposit','roomName','roomTotal','startTime','resID');
		
		#
		foreach( $goodArr as $val ) {
			$name = '{'.$val.'}';
			$contents = str_replace( $name, $externals[$val], $contents );
		}		
		
		return nl2br( $contents );
	}
}
?>