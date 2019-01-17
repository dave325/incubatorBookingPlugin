<?PHP
class bookaroom_events_staff
{
	public static function bookaroom_staffCalendar()
	{

		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-amenities.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-rooms.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-branches.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-roomConts.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-closings.php' );
		#require_once( BOOKAROOM_PATH . 'bookaroom-events-settings.php' );
		
		# vaiables from includes
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		$changeRoom = false;
		# first, is there an action? 
		$externals = self::getExternals();

		switch( $externals['action'] ) {
			case 'searchReturn':
				$_SESSION['bookaroom_temp_search_settings'] = $externals;
				$results = self::getEventList( $externals );
				self::showSearchedEvents( $externals, $results, true );
				break;

			case 'search':
				$_SESSION['bookaroom_temp_search_settings'] = $externals;
				$curTime = getdate( time() );
				$externals['startDate'] = date( 'm/d/Y', mktime( 0, 0, 0, $curTime['mon'], $curTime['mday'], $curTime['year'] ) );
				$externals['endDate'] = date( 'm/d/Y', mktime( 0, 0, 0, $curTime['mon'], $curTime['mday']+31, $curTime['year'] ) -1 );
				self::showSearchedEvents( $externals, array(), true );
				break;

			
			case 'checkReg':
				if( empty( $externals['eventID'] ) or ( $eventInfo = self::checkID( $externals['eventID'] ) ) == false ) {
					#self::
					echo 'error';
					break;				
				}

				# bad hash?

				if( empty( $_SESSION['bookaroom_RegFormSub'] ) or $externals['bookaroom_RegFormSub'] !== $_SESSION['bookaroom_RegFormSub'] ) {
					$errorMSG = __( 'Either there was a problem processing your form, or you are trying to refresh an already completed form. Please fill out the form again.', 'book-a-room' );
					# check event ID
					$_SESSION['bookaroom_RegFormSub'] = md5( rand( 1, 500000000000 ) );
					unset( $_POST );
					$externals = array( 'fullName' => NULL, 'phone' => NULL, 'email' => NULL, 'notes' => NULL );
					$externals['bookaroom_RegFormSub'] = $_SESSION['bookaroom_RegFormSub'];
					self::viewEvent( $eventInfo, $externals, $errorMSG );
					break;
				}
					
				if( ( $errorMSG = self::checkReg( $externals ) ) !== false ) {
					self::viewEvent( $eventInfo, $externals, $errorMSG );
					break;
				}
				
				self::addRegistration( $externals );
				
				self::viewEvent( $eventInfo, $externals, $errorMSG, true );
				unset( $_SESSION['bookaroom_RegFormSub'] );

				break;
				
			case 'viewEvent':
				# check event ID
				if( empty( $externals['eventID'] ) or ( $eventInfo = self::checkID( $externals['eventID'] ) ) == false ) {
					#self::
					echo 'error';
					break;				
				}
				$_SESSION['bookaroom_RegFormSub'] = md5( rand( 1, 500000000000 ) );
				
				$externals['bookaroom_RegFormSub'] = $_SESSION['bookaroom_RegFormSub'];
				
				
				self::viewEvent( $eventInfo, $externals );
				break;
				
			default:
				self::showCalendar( $externals['timestamp'], $externals['searchTerms'] );
				break;
				
		}
	}
	
	
	protected static function getExternals()
	# Pull in POST and GET values
	{
		$final = array();
		
		# setup GET variables
		$getArr = array(	'action'				=> FILTER_SANITIZE_STRING, 
							'eventID'				=> FILTER_SANITIZE_STRING, 
							'timestamp'				=> FILTER_SANITIZE_STRING, 
							'filter'				=> FILTER_SANITIZE_STRING, 
							'age'					=> FILTER_SANITIZE_STRING, 
							'category'				=> FILTER_SANITIZE_STRING,
							'searchTerms'			=> FILTER_SANITIZE_STRING,
							 );

		# pull in and apply to final
		if( $getTemp = filter_input_array( INPUT_GET, $getArr ) )
			$final = $getTemp;
			
		# setup POST variables
		$postArr = array(	'published'				=> FILTER_SANITIZE_STRING,
							'searchTerms'			=> FILTER_SANITIZE_STRING,
							'ageGroup'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'categoryGroup'				=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'branchID'				=> FILTER_SANITIZE_STRING,
							'startDate'				=> FILTER_SANITIZE_STRING,
							'endDate'				=> FILTER_SANITIZE_STRING,
							'action'				=> FILTER_SANITIZE_STRING,
							'bookaroom_RegFormSub'		=> FILTER_SANITIZE_STRING, 
							'eventID'				=> FILTER_SANITIZE_STRING, 
							'timestamp'				=> FILTER_SANITIZE_STRING,
							'fullName'				=> FILTER_SANITIZE_STRING,
							'phone'					=> FILTER_SANITIZE_STRING,
							'email'					=> FILTER_SANITIZE_STRING,
							'notes'					=> FILTER_SANITIZE_STRING,
							'roomChecked'			=> array(	'filter'    => FILTER_SANITIZE_STRING,
                           										'flags'     => FILTER_REQUIRE_ARRAY ) );
	

		# pull in and apply to final
		if( $postTemp = filter_input_array( INPUT_POST, $postArr ) )
			$final = array_merge( $final, $postTemp );

		$arrayCheck = array_unique( array_merge( array_keys( $getArr ), array_keys( $postArr ) ) );

		foreach( $arrayCheck as $key ) {
			if( empty( $final[$key] ) ) {
				$final[$key] = NULL;
			} elseif( is_array( $final[$key] ) ) {
				$final[$key] = $final[$key];
			} else {
				$final[$key] = trim( $final[$key] );
			}
		}
		
		return $final;	
	}
	
	protected static function showCalendar( $timestampRaw = NULL, $filter = NULL, $age = NULL, $category = NULL )
	{
		# show page
		require BOOKAROOM_PATH . 'templates/events/calendar.php';		
	}

	protected static function getMonthEvents( $timestamp, $filter = NULL, $age = NULL, $category = NULL )
	{
		global $wpdb;
		
		if( empty( $timestamp )) {
			$timestamp = time();
		}

		$catList = self::getCatList();
		$ageList = self::getAgeList();
		
		# find first of the month
		$monthFirst = date( 'Y-m-01', $timestamp ) . ' 00:00:00';
		$monthLast = date( 'Y-m-t', $timestamp ) . ' 23:59:59';
		
		$where = array();
		
		# is there an age filter?
		if( !empty( $age ) ) {
			$curAgeList = explode( ',', $age );
		
			if( !is_null( $curAgeList ) ) {
				array_walk( $curAgeList, function( &$value, $index ) use ( $ageList ) {
					$value = trim( $value );

					if( ( $foundIt = array_search( strtolower( $value ), $ageList['all'] ) ) ) {
						$value = "'{$foundIt}'";
					} else {
						$value = NULL;
					}
				} );
				
				$curAgeList = array_filter( $curAgeList );
				
				if( count( $curAgeList ) > 0 ) {
					$where[] = "`ti`.`ti_extID` IN ( SELECT `ea`.`ea_eventID` FROM `{$wpdb->prefix}bookaroom_eventAges` as `ea` WHERE `ea`.`ea_ageID` IN (".implode(',', $curAgeList).") )";
				}
			}
		}
				
		# is there an category filter?
		if( !empty( $category ) ) {
			$curCatList = explode( ',', $category );
			
			array_walk( $curCatList, function( &$value, $index ) use ( $catList ) {
				$value = trim( $value );
	
				if( ( $foundIt = array_search( strtolower( $value ), $catList['all'] ) ) ) {
					$value = "'{$foundIt}'";
				} else {
					$value = NULL;
				}
			} );
			
			$curCatList = array_filter( $curCatList );
			
			if( count( $curCatList ) > 0 ) {
				$where[] = "`ti`.`ti_extID` IN ( SELECT `ec`.`ec_eventID` FROM `{$wpdb->prefix}bookaroom_eventCats` as `ec` WHERE `ec`.`ec_catID` IN (".implode(',', $curCatList).") )";
			}
		}
			

		# search term
		if( !empty( $filter ) ) {
			$where[] = " MATCH ( `ev`.`ev_desc`, `ev`.`ev_presenter`, `ev`.`ev_privateNotes`, `ev`.`ev_publicEmail`, `ev`.`ev_publicName`, `ev`.`ev_submitter`, `ev`.`ev_title`, `ev`.`ev_website`, `ev`.`ev_webText` ) AGAINST ('{$filter}' IN NATURAL LANGUAGE MODE )";
			$scoreWhere = "`score` DESC, ";
		} else {
			$scoreWhere = NULL;
		}

		if( count( $where ) > 0 ) {
			$whereFinal = ' AND '.implode( ' AND ', $where );
		} else {
			$whereFinal = NULL;
		}
						
		$sql = "SELECT MATCH ( `ev`.`ev_desc`, `ev`.`ev_presenter`, `ev`.`ev_privateNotes`, `ev`.`ev_publicEmail`, `ev`.`ev_publicName`, `ev`.`ev_submitter`, `ev`.`ev_title`, `ev`.`ev_website`, `ev`.`ev_webText` ) AGAINST ('{$filter}' IN NATURAL LANGUAGE MODE ) as `score`, 
		`ti`.`ti_id`, `ti`.`ti_extID`, `ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_roomID`, `ev`.`ev_desc`, `ev`.`ev_maxReg`, `ev`.`ev_waitingList`, `ev`.`ev_presenter`, `ev`.`ev_privateNotes`, `ev`.`ev_publicEmail`, `ev`.`ev_publicName`, `ev`.`ev_publicPhone`, `ev`.`ev_noPublish`, `ev`.`ev_regStartDate`, `ev`.`ev_regType`, `ev`.`ev_submitter`, `ev`.`ev_title`, `ev`.`ev_website`, `ev`.`ev_webText`, `ti`.`ti_extraInfo`, 
				group_concat(DISTINCT `ea`.`ea_ageID` separator ', ') as `ageID`, group_concat(DISTINCT `ages`.`age_desc` separator ', ') as `ages`, 
				group_concat(DISTINCT `ec`.`ec_catID` separator ', ') as `catID`, group_concat(DISTINCT `cats`.`categories_desc` separator ', ') as `cats`
				FROM  `{$wpdb->prefix}bookaroom_times` as `ti`
				LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` as `ev` ON `ti`.`ti_extID` = `ev`.`res_id`
				LEFT JOIN `{$wpdb->prefix}bookaroom_eventAges` as `ea` on `ea`.`ea_eventID` = `ti`.`ti_extID`
				LEFT JOIN `{$wpdb->prefix}bookaroom_event_ages` as `ages` on `ea`.`ea_ageID` = `ages`.`age_id`
				
				LEFT JOIN `{$wpdb->prefix}bookaroom_eventCats` as `ec` on `ec`.`ec_eventID` = `ti`.`ti_extID`
				LEFT JOIN `{$wpdb->prefix}bookaroom_event_categories` as `cats` on `ec`.`ec_catID` = `cats`.`categories_id`
				
				WHERE `ti`.`ti_type` =  'event'
				AND `ti`.`ti_startTime` >=  '{$monthFirst}'
				AND `ti`.`ti_endTime` <=  '{$monthLast}' 
				{$whereFinal} 
				AND `ev`.`ev_regType` = 'staff' 
				GROUP BY `ti`.`ti_id` 
				ORDER BY {$scoreWhere}`ti`.`ti_startTime`";

		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		$final = array();
		
		foreach( $cooked as $val ) {
			$dateInfo = getdate( strtotime( $val['ti_startTime'] ) );
			$final[$dateInfo['mday']][] = $val;
		}

		return $final;
	}
	
	protected static function getAgeList( )
	{
		global $wpdb;
		
		$sql = "SELECT `age_id`, `age_desc`, `age_order`, `age_active` FROM `{$wpdb->prefix}bookaroom_event_ages` ";
		
		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		
		$final = array( 'active' => array(), 'inactive' => array(), 'all' => array(), 'status' => array() , 'order' => array() );
			
		foreach( $cooked as $key => $val ) {
			$active = ( empty( $val['age_active'] ) ) ? 'inactive' : 'active';
			$final[$active][$val['age_id']]		= $val;
			$final['all'][$val['age_id']]		= strtolower( $val['age_desc'] );
			$final['status'][$val['age_id']]	= $val['age_active'];
			$final['order'][$val['age_order']]	= $val['age_id'];
		}
		
		ksort( $final['order'] );
		
		return $final;
	}
	
	public static function getCatList( )
	{
		global $wpdb;
		
		$sql = "SELECT `categories_id`, `categories_desc`, `categories_order`, `categories_active` FROM `{$wpdb->prefix}bookaroom_event_categories` ";
		
		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		
		$final = array( 'active' => array(), 'inactive' => array(), 'all' => array(), 'status' => array(), 'order' => array() );
			
		foreach( $cooked as $key => $val ) {
			$active = ( empty( $val['categories_active'] ) ) ? 'inactive' : 'active';
			$final[$active][$val['categories_id']]		= $val;
			$final['all'][$val['categories_id']]		= strtolower( $val['categories_desc'] );
			$final['status'][$val['categories_id']]	= $val['categories_active'];
			$final['order'][$val['categories_order']]	= $val['categories_id'];
		}
		
		ksort( $final['order'] );
		
		return $final;
	}
	
	protected static function getRegInfo( $eventID )
	{
		global $wpdb;
		
		$sql = "	SELECT `reg_id`, `reg_fullName`, `reg_phone`, `reg_email`, `reg_notes`, `reg_dateReg` 
					FROM `{$wpdb->prefix}bookaroom_registrations` 
					WHERE `reg_eventID` = '{$eventID}' 
					ORDER BY `reg_dateReg`";
					
		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		
		return $cooked;
		
	}
	
	protected static function checkID( $eventID )
	{
		global $wpdb;
				
		$sql = "SELECT `ti`.`ti_id`, `ti`.`ti_type`, `ti`.`ti_extID`, `ti`.`ti_created`, `ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_roomID`, 
`res`.`res_id`, `res`.`res_created`, `res`.`ev_desc`, `res`.`ev_maxReg`, `res`.`ev_presenter`, `res`.`ev_privateNotes`, `res`.`ev_publicEmail`, `res`.`ev_publicName`, `res`.`ev_publicPhone`, `res`.`ev_noPublish`, `res`.`ev_regStartDate`, `res`.`ev_regType`, `res`.`ev_submitter`, `res`.`ev_title`, `res`.`ev_website`, `res`.`ev_webText`, `res`.`ev_waitingList`, 
		`ti`.`ti_noLocation_branch`, `ti`.`ti_extraInfo`, 
		group_concat(DISTINCT `ea`.`ea_ageID` separator ', ') as `ageID`, group_concat(DISTINCT `ages`.`age_desc` separator ', ') as `ages`, 
		group_concat(DISTINCT `ec`.`ec_catID` separator ', ') as `catID`, group_concat(DISTINCT `cats`.`categories_desc` separator ', ') as `cats`
		FROM `{$wpdb->prefix}bookaroom_times` AS `ti` 
		LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` as `res` ON `ti`.`ti_extID` = `res`.`res_id` 
		LEFT JOIN `{$wpdb->prefix}bookaroom_eventAges` as `ea` on `ea`.`ea_eventID` = `ti`.`ti_extID`
		LEFT JOIN `{$wpdb->prefix}bookaroom_event_ages` as `ages` on `ea`.`ea_ageID` = `ages`.`age_id`
		
		LEFT JOIN `{$wpdb->prefix}bookaroom_eventCats` as `ec` on `ec`.`ec_eventID` = `ti`.`ti_extID`
		LEFT JOIN `{$wpdb->prefix}bookaroom_event_categories` as `cats` on `ec`.`ec_catID` = `cats`.`categories_id`		
		WHERE `ti`.`ti_type` = 'event' AND `ti`.`ti_id` = '{$eventID}'
		
		GROUP BY `ti`.`ti_id`";
		
		
		$eventInfo = $wpdb->get_results( $sql, ARRAY_A );

		if( $wpdb->num_rows == 0 ) {
			return FALSE;
		}

		return $eventInfo[0];
		
	}

	protected static function viewEvent( $eventInfo, $externals, $errorMSG = NULL, $isSuccess = false )
	{

		# include appropriate datau
		$ageList		= self::getAgeList();
		$catList		= self::getCatList();

		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		
		# create nicely formatted time
		if( date( 'g:i a', strtotime( $eventInfo['ti_startTime'] ) ) == '12:00 am' ) {
			$eventTime		= __( 'All Day', 'book-a-room' );
		} else {
			$eventTime		= date( 'g:i a', strtotime( $eventInfo['ti_startTime'] ) ) . ' - ' . date( 'g:i a', strtotime( $eventInfo['ti_endTime'] ) );
		}

		# branch and room
		if( !empty( $eventInfo['ti_noLocation_branch'] ) ) {
			$eventBranch	= $branchList[$eventInfo['ti_noLocation_branch']]['branchDesc'];
			$eventRoom		= _e( 'No location specified', 'book-a-room' );
		} else {
			$eventBranch	= $branchList[$roomContList['id'][$eventInfo['ti_roomID']]['branchID']]['branchDesc'];
			$eventRoom		= $roomContList['id'][$eventInfo['ti_roomID']]['desc'];
		}
		
		# phone number
		if( empty( $eventInfo['ev_publicPhone'] ) ) {
			$nicePhone = '&nbsp;';
		} else {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $eventInfo['ev_publicPhone'] );
			$nicePhone = "(" . substr($cleanPhone, 0, 3) . ") " . substr($cleanPhone, 3, 3) . "-" . substr($cleanPhone, 6);
		}
		
		# registration phone number
		if( empty( $externals['phone'] ) ) {
			$nicePhoneReg = '&nbsp;';
		} else {
			$cleanPhoneReg = preg_replace( "/[^0-9]/", '', $externals['phone'] );
			$nicePhoneReg = "(" . substr($cleanPhoneReg, 0, 3) . ") " . substr($cleanPhoneReg, 3, 3) . "-" . substr($cleanPhoneReg, 6);
		}

		require( BOOKAROOM_PATH . 'templates/events/viewEvent.php' );
	}

	protected static function checkReg( $externals )
	{
		$error = array();
		
		# check for empty values
		if( empty( $externals['fullName'] ) ) {
			$error[] = __( 'You must enter the full name of the person who is registering.', 'book-a-room' );
		}
		
		if( empty( $externals['phone'] ) and empty( $externals['email'] ) ) {
			$error[] = __( 'You must enter contact information; either a phone number or email address where you can be reached.', 'book-a-room' );
		} else {
			if( !empty( $externals['phone'] ) ) {
				$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['phone'] );
				if ( strlen( $cleanPhone ) == 11 ) $cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
				if( !is_numeric( $cleanPhone ) || strlen( $cleanPhone ) !== 10 ) {
					$error[] = __( 'You must enter a valid phone number.', 'book-a-room' );
				}				
			}
			
			if( !empty( $externals['email'] ) and !filter_var( $externals['email'], FILTER_VALIDATE_EMAIL ) ) {
				$error[] = __( 'Please enter a valid email address.', 'book-a-room' );
			}
		}
		
		if( count( $error )!== 0 ) {
			return implode( "<br />", $error );
		} else {
			return false;
		}		
		
	}
	
	protected static function addRegistration( $externals )
	{
		global $wpdb;
			
		if( empty( $externals['phone'] ) ) {
			$cleanPhone = NULL;
		} else {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['phone'] );
			if ( strlen( $cleanPhone ) == 11 ) $cleanPhone = preg_replace( "/^1/", '', $cleanPhone );

			$cleanPhone = "(" . substr($cleanPhone, 0, 3) . ") " . substr($cleanPhone, 3, 3) . "-" . substr($cleanPhone, 6);	
		}
		
		$final = $wpdb->insert( $wpdb->prefix . "bookaroom_registrations",
			array( 	'reg_eventID' => $externals['eventID'], 
					'reg_fullname' => $externals['fullName'], 
					'reg_phone' => $cleanPhone, 
					'reg_email' => $externals['email'], 
					'reg_notes' => $externals['notes'], 					
					 ) );
		
	}
		
}
?>