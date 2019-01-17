<?PHP
class bookaroom_customerSearch
{
	static $branchList;
	static $roomContList;
	
	public static function bookaroom_findUser()
	{
			require_once( BOOKAROOM_PATH . 'sharedFunctions.php' );
		
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-branches.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-roomConts.php' );
		
		# vaiables from includes
		self::$roomContList = bookaroom_settings_roomConts::getRoomContList();
		self::$branchList = bookaroom_settings_branches::getBranchList( TRUE );		

		# first, is there an action? 
		$externals = self::getExternals();

		switch( $externals['action'] ) {
			case 'filterResults':
				# check for errors
				$errorResult = self::getSearchErrorMessage( $externals );
				
				if( $errorResult['isError'] == true ) {
					self::showSearchForm( $externals, $errorResult['errorMSG'], $errorResult['bgArr'] );
				} else {
					# get search values
					$roomInfo = getRoomInfo( $externals['roomID'], self::$branchList, self::$roomContList, true, true );
					$results = self::searchRegistrations( $roomInfo, $externals['startDate'], $externals['endDate'], $externals['searchName'], $externals['searchEmail'], $externals['searchPhone'] );
					self::showSearchForm( $externals, NULL, NULL, $results );
				}
				
				break;
				
			default:
				self::showSearchForm( $externals );
				break;
		}
	}
	
	
	protected static function getExternals()
	# Pull in POST and GET values
	{
		$final = array();
		
		# setup GET variables
		$getArr = 	array(	'action'				=> FILTER_SANITIZE_STRING, 
				 	);

		# pull in and apply to final
		if( $getTemp = filter_input_array( INPUT_GET, $getArr ) )
			$final = $getTemp;
			
		# setup POST variables
		$postArr =	array(	'action'				=> FILTER_SANITIZE_STRING,
							'branchID'				=> FILTER_SANITIZE_STRING,
							'noloc-branchID'		=> FILTER_SANITIZE_STRING,
							'roomID'				=> FILTER_SANITIZE_STRING,
							'startDate'				=> FILTER_SANITIZE_STRING,
							'endDate'				=> FILTER_SANITIZE_STRING,
							'searchName'			=> FILTER_SANITIZE_STRING,
							'searchEmail'			=> FILTER_SANITIZE_STRING,
							'searchPhone'			=> FILTER_SANITIZE_STRING, 
					);
		
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
	
	public static function getSearchErrorMessage( &$externals )
	# look at incomping variables for the search and find any errors.
	{
		# get required vars
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-roomConts.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-branches.php' );
		$errorArr = $bgArr = array();
		
		# check dates
		# if start date is empty or invalid, clear it, else make it unix timestamp
		if( !empty( $externals['startDate'] ) and ( ( $startDate = strtotime( $externals['startDate'] ) ) == false ) or is_numeric( $externals['startDate'] ) ) {
			$errorArr[] = 'Your start date is invalid.';
			$bgArr[] = 'startDate';
			$startDate = NULL;
		}
		
		# if end date is empty or invalis, clear it, else make it unix timestamp
		if( !empty( $externals['endDate'] ) and ( ( $endDate = strtotime( $externals['endDate'] ) ) == false ) or is_numeric( $externals['endDate'] ) ) {
			$errorArr[] = 'Your end date is invalid.';			
			$bgArr[] = 'endDate';
			$endDate = NULL;
		}
				
		# check to see if two dates are selected
		if( !empty( $startDate ) and !empty( $endDate ) ) {
			if( $startDate > $endDate ) {
				$errorArr[] = 'Your start date comes after your end date.';
				$bgArr[] = 'startDate';
				$bgArr[] = 'endDate';
			}
		}
		
		# validate non-empty emails
		if( !empty( $externals['searchEmail'] ) and !filter_var( $externals['searchEmail'], FILTER_VALIDATE_EMAIL ) ) {
			$errorArr[] = 'Your email address is incorrectly formatted.';
			$bgArr[] = 'searchEmail';
		}

		# check phone	
		if( !empty( $externals['searchPhone'] ) ) {
			# if not empty, remove everything except digits.
			$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['searchPhone'] );
			# if there are 11 digits, check for a 1 at the start and remove it
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );				
			}
			
			# if there are not 10 digits, the phone number is invalid.
			if ( strlen( $cleanPhone ) !== 10 ) {
				$errorArr[] = 'Your phone number is incorrectly formatted. Please enter 10 digits, including area code.';
				$bgArr[] = 'searchPhone';
			} else {
				$externals['searchPhone'] = "(" . substr($cleanPhone, 0, 3) . ") " . substr($cleanPhone, 3, 3) . "-" . substr($cleanPhone, 6);
			}
		}
		
		# check empty search
		$errorMSG = NULL;
		$isError = false;
		
		if( count( $errorArr ) > 0 ) {
			$errorMSG = implode( '<br />', $errorArr );
			$isError = true;			
		}
		
		return array( 'isError' => $isError, 'errorMSG' => $errorMSG, 'bgArr' => $bgArr );
	}
	
	public static function searchRegistrations( $roomInfo = array(), $startDate = NULL , $endDate = NULL, $searchName = NULL, $searchEmail = NULL, $searchPhone = NULL )
	# search for registrations based on search terms
	# return an array of events based on those registrations
	#
	# $roomInfo is an array with 3 variables. Only one should be true (or none). They include:
	# 	$cur_roomID				= The current selected room ID
	# 	$cur_branchID			= If an entire branch is selected, this is the ID
	# 	$cur_noloc_branchID		= If a no-location is selected for a branch, this is the branchID
	#
	# other search filters:
	# 	$startDate		= UNIX timestamp of the start date (can be NULL)
	# 	$endDate		= UNIX timestamp of the end date (can be NULL)
	# 	$searchName		= Partial or full name to search
	# 	$seachEmail		= Full email address to search
	# 	$searchPhone	= Full phone number to search
	
	{
		global $wpdb;
		
		# start making the MySQL call
		#
		# the where array will build the WHERE filters based on incoming variables.
		$where		= NULL;
		$whereArr	= array();
		
		# search for name
		if( !empty( $searchName ) ) {
			$whereArr[] = "( MATCH ( `reg`.`reg_fullName` ) AGAINST ( \"{$searchName}\" IN NATURAL LANGUAGE MODE ) or `reg`.`reg_fullName` = \"{$searchName}\" )";
		}
		
		# check for roomID
		if( !empty( $roomInfo['cur_roomID'] ) ) {
			$whereArr[] = "`ti`.`ti_roomID` = '{$roomInfo['cur_roomID']}'";
		}
		
		# check for no location branch
		if( !empty( $roomInfo['cur_noLocation_branch'] ) ) {
			$whereArr[] = "`ti`.`ti_noLocation_branch` = '{$roomInfo['cur_noLocation_branch']}'";
		}
		# check for branch
		if( !empty( $roomInfo['cur_branchID'] ) ) {
			# find all room containers in that branch
			$roomList = implode( ',', self::$roomContList['branch'][$roomInfo['cur_branchID']] );
			# compare against imploded list of room containers in that branch
			$whereArr[] = "( `ti`.`ti_roomID` IN ( {$roomList} ) or `ti`.`ti_noLocation_branch` = '{$roomInfo['cur_branchID']}' )";
		}
		
		# start date
		if( !empty( $startDate ) ) {
			$startDateSQL = date( 'Y-m-d H:i:s', strtotime( $startDate ) );
			$whereArr[] = "`ti`.`ti_startTime` >= '{$startDateSQL}'";
		}

		# end date
		if( !empty( $endDate ) ) {
			$endDateSQL =  date( 'Y-m-d H:i:s', strtotime( $endDate ) + 86399 );
			$whereArr[] = "`ti`.`ti_startTime` <= '{$endDateSQL}'";
		}
		
		# email
		if( !empty( $searchEmail) ) {
			$whereArr[] = "`reg`.`reg_email` LIKE \"%{$searchEmail}%\" or `reg`.`reg_email` = \"{$searchEmail}\"";
		}
		
		# email
		if( !empty( $searchPhone) ) {		
			$cleanPhone = preg_replace( "/[^0-9]/", '', $searchPhone );
			
			# if there are 11 digits, check for a 1 at the start and remove it
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
				$searchPhone = "(" . substr($cleanPhone, 0, 3) . ") " . substr($cleanPhone, 3, 3) . "-" . substr($cleanPhone, 6);
			}
		
			$whereArr[] = "`reg`.`reg_phone` = \"{$searchPhone}\"";
		}
		
		# collapse where if not empty
		if( count( $whereArr ) > 0 ) {
			$where = 'WHERE '.implode( " and \r\n", $whereArr );
		} else {
			$where = NULL;
		}
		
#		$sql = "	SELECT `reg`.*, `ti`.`ti_startTime`, `res`.`ev_desc`
		$sql = "	SELECT 	MATCH ( `reg`.`reg_fullName` ) AGAINST ( \"{$searchName}\" IN NATURAL LANGUAGE MODE ) as score, 
							`reg`.`reg_fullName`, 
							`reg`.`reg_phone`, 
							`reg`.`reg_email`, 
							`reg`.`reg_eventID`, 
							`ti`.`ti_startTime`, 
							`ti`.`ti_endTime`, 
							`ti`.`ti_roomID`, 
							`ti`.`ti_noLocation_branch`,  
							`ti`.`ti_extraInfo`, 
							`ti`.`ti_id`, 
							`res`.`res_id`, 
							`res`.`ev_desc`, 
							`res`.`ev_title`, 
							`res`.`ev_maxReg`,  
							`rc`.`reg_dateReg`, 
							COUNT( `rc`.`reg_id` ) as `regCount`, 
							
							COUNT( DISTINCT `regCount2`.`reg_id` ) + 1 as `regCount` 

					FROM `{$wpdb->prefix}bookaroom_registrations` AS `reg` 
					LEFT JOIN `{$wpdb->prefix}bookaroom_times` AS `ti` ON `reg`.`reg_eventID` = `ti`.`ti_id`					
					LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` AS `res` ON `ti`.`ti_extID` = `res`.`res_id`
					LEFT JOIN `{$wpdb->prefix}bookaroom_registrations` as `rc` ON `reg`.`reg_eventID` = `rc`.`reg_eventID` 
					
					LEFT JOIN `{$wpdb->prefix}bookaroom_registrations` as `regCount2` ON ( `reg`.`reg_eventID` = `regCount2`.`reg_eventID` AND UNIX_TIMESTAMP( `reg`.`reg_dateReg` ) > UNIX_TIMESTAMP( `regCount2`.`reg_dateReg` ) )
					
					{$where} 
					GROUP BY `reg`.`reg_id` 
					ORDER BY score DESC, `reg`.`reg_fullName`, `reg`.`reg_email`, `reg`.`reg_phone`, `ti`.`ti_startTime` 
					
					";
		
		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		
		if( count( $cooked ) == 0 ) {
			return array();
		} else {
			return $cooked;
		}
		
	}
	
	public static function showSearchForm( $externals, $errorMSG = NULL, $errorBG = array(), $results = array() )
	# grab the template file and replace things to make a pretty page.
	# this includes the branch drop, search options, error messages and
	# form fields
	{
		$roomInfo = getRoomInfo( $externals['roomID'], self::$branchList, self::$roomContList, true, true );

		require BOOKAROOM_PATH . 'templates/events/customerSearch.php';
	}
}