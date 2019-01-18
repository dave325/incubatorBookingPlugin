<?php
class bookaroom_events_manage
{
	var $rowCount;
	
	public static function bookaroom_manageEvents()
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

		# first, is there an action? 
		$externals = self::getExternals();
		
		switch( $externals['action'] ) {
			case 'filterResults':
				$_SESSION['bookaroom_temp_search_settings'] = $externals;
				
				if( !empty( $externals['submit_count'] ) ) {
					update_option( 'bookaroom_search_events_page_num', 1 );
					update_option( 'bookaroom_search_events_per_page', $externals['submit_count'] );
				}
				
				if( !empty( $externals['submit'] ) and $externals['submit'] == 'Submit' ) {
					update_option( 'bookaroom_search_events_page_num', 1 );
				}

				if( !empty( $externals['pageNum'] ) ) {
					update_option( 'bookaroom_search_events_page_num', $externals['pageNum'] );
				}
				
				if( !empty( $externals['submit_nav'] ) ) {
					$pageNum = get_option( 'bookaroom_search_events_page_num');
					
					switch( $externals['submit_nav'] ) {
						case 'Prev':
							update_option( 'bookaroom_search_events_page_num', $pageNum - 1 );
							break;
						case 'Next':
							update_option( 'bookaroom_search_events_page_num', $pageNum + 1 );	
							break;
					}
				}				
				$externals['hideSearch'] = true;				
				$results = self::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				self::manageEvents( $externals, $results, true );
				break;
				
			default:
				$externals['hideSearch'] = false;
				$_SESSION['bookaroom_meetings_hash'] = NULL;
				$_SESSION['bookaroom_meetings_externalVals'] = NULL;
				update_option( 'bookaroom_search_events_page_num', 1 );
				self::manageEvents( $externals );
				break;
		}
	}
	
	protected static
	function checkID( $eventID )
	{
		# look up ID in database
		global $wpdb;
		
		$sql = "SELECT `ti`.`ti_id`, `ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_type`, `ti`.`ti_roomID`, `res`.`res_id`, `res`.`ev_desc`, 
					`res`.`ev_maxReg`, `res`.`ev_waitingList`, `res`.`ev_presenter`, `res`.`ev_privateNotes`, `res`.`ev_publicEmail`, 
					`res`.`ev_publicName`, `res`.`ev_publicPhone`, `res`.`ev_noPublish`, `res`.`ev_regStartDate`, `res`.`ev_regType`, 
					`res`.`ev_submitter`, `res`.`ev_title`, `res`.`ev_website`, `res`.`ev_webText`  
				FROM `{$wpdb->prefix}bookaroom_times` AS `ti`
				LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` AS `res` ON `ti`.`ti_extID` = `res`.`res_id` 
				WHERE `ti`.`ti_id` = '{$eventID}'";
		
		
		$cooked = $wpdb->get_row( $sql, ARRAY_A );
		if( $cooked == NULL ) {
			return false;
		}
		
		return $cooked;
	}
	
	public static
	function getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList )
	{
		global $wpdb;
		global $rowCount;
		
		$where = array();
		$where[] = '`ti`.`ti_type` = "event"';
		$whereFinal = NULL;
		
		$page_num		= self::get_clean_option( 'bookaroom_search_events_page_num' );
		$per_page		= self::get_clean_option( 'bookaroom_search_events_per_page' );
		$order_by		= self::get_clean_option( 'bookaroom_search_events_order_by' );
		$sort_order		= self::get_clean_option( 'bookaroom_search_events_sort_order' );
		
		# age group
		if( !empty( $externals['regType'] ) ) {
			if( $externals['regType'] == 'staff' ) {
				$where[] = "`res`.`ev_regType` = 'staff'";				
			} elseif( $externals['regType'] == 'yes' ) {
				$where[] = "`res`.`ev_regType` = 'yes'";
			}
		}
		
		# age group
		if( !empty( $externals['ageGroup'] ) ) {
			$where[] = "`ages`.`ea_ageID` IN (".implode( ',', $externals['ageGroup'] ).")";
		}
		
		# category group
		if( !empty( $externals['categoryGroup'] ) ) {
			$where[] = "`cats`.`ec_catID` IN (".implode( ',', $externals['categoryGroup'] ).")";
		}
		
		# check for no location
		if( !empty( $externals['noloc-branchID'] ) and array_key_exists( $externals['noloc-branchID'], $branchList ) ) {
			# find all rooms in branch
			$where[] = "(`ti`.`ti_noLocation_branch` = '{$externals['noloc-branchID']}')";
		}
		
		# check for room
		if( !empty( $externals['roomID'] ) and array_key_exists( $externals['roomID'], $roomContList['id'] ) ) {
			# find all rooms in branch
			$where[] = "(`ti`.`ti_roomID` = '{$externals['roomID']}')";
		}
		
		# check for branch
		if( !empty( $externals['branchID'] ) and array_key_exists( $externals['branchID'], $branchList ) ) {
			# find all rooms in branch
			$branchArr = implode( ',',  $roomContList['branch'][$externals['branchID']] );
			$where[] = "(`ti`.`ti_roomID` IN ( {$branchArr} ) or `ti`.`ti_noLocation_branch` = '{$externals['branchID']}')";
		}
		
		# start time
		if( !empty( $externals['startDate'] ) and ( $startTimestamp =  date( 'Y-m-d H:i:s', strtotime( $externals['startDate'] ) ) ) !== false ) {
			$where[] = "`ti`.`ti_startTime` >= '{$startTimestamp}'";
		}

		# end time
		if( !empty( $externals['endDate'] ) and ( $endTimestamp =  date( 'Y-m-d H:i:s', strtotime( $externals['endDate']." + 1 days") ) ) !== false ) {
			$where[] = "`ti`.`ti_endTime` <= '$endTimestamp'";
		}

		# search term
		$trimmedSearch = trim( $externals['searchTerms'] );
		if( !empty( $trimmedSearch ) ) {
			$where[] = " MATCH ( `res`.`ev_desc`, `res`.`ev_presenter`, `res`.`ev_privateNotes`, `res`.`ev_publicEmail`, `res`.`ev_publicName`, `res`.`ev_submitter`, `res`.`ev_title`, `res`.`ev_website`, `res`.`ev_webText` ) AGAINST ('{$externals['searchTerms']}' IN NATURAL LANGUAGE MODE )";
			$scoreWhere = "`score` DESC, ";
		} else {
			$scoreWhere = NULL;
		}

		# check for non-published
		if( $externals['published'] == 'noPublished' ) {
			$where[] = "`res`.`ev_noPublish` = 1";			
		} elseif( $externals['published'] == 'published' ) {
			$where[] = "`res`.`ev_noPublish` = 0";
		}
		
		#  check for and build WHERE statment
		if( count( $where ) > 0 ) {
			$whereFinal = 'WHERE '.implode( ' AND ', $where );
		}

		# build limit on page numbers
		$limit = NULL;
		
		if( $per_page !== __( 'All', 'book-a-room' ) ) {
			if( ( $page_num - 1 ) * $per_page  < 1 ) {
				$limit = ' LIMIT 0, '.$per_page;
			} else {
				$limit = ' LIMIT '.( ( ( $page_num - 1 ) * $per_page ) ).', '.$per_page;
			}
		}
		
		# sort type
		$sql = "SELECT SQL_CALC_FOUND_ROWS MATCH ( `res`.`ev_desc`, `res`.`ev_presenter`, `res`.`ev_privateNotes`, `res`.`ev_publicEmail`, `res`.`ev_publicName`, `res`.`ev_submitter`, `res`.`ev_title`, `res`.`ev_website`, `res`.`ev_webText` ) AGAINST ('{$externals['searchTerms']}' IN NATURAL LANGUAGE MODE ) as `score`, 
		`ti`.`ti_id`, `ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_type`, `res`.`ev_title`, `res`.`ev_desc`, `ti`.`ti_roomID`, `ti`.`ti_noLocation_branch`, `ti`.`ti_extraInfo`, `res`.`ev_regType`, `res`.`ev_maxReg`, `res`.`ev_noPublish`, COUNT( DISTINCT `tiCount`.`ti_id` ) as eventCount, `res`.`res_id` 
			FROM `{$wpdb->prefix}bookaroom_times` AS `ti`
			LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` AS `res` ON `res`.`res_id` = `ti`.`ti_extID`
			LEFT JOIN `{$wpdb->prefix}bookaroom_times` AS `tiCount` ON `tiCount`.`ti_extID` = `res`.`res_id` 
			LEFT JOIN `{$wpdb->prefix}bookaroom_eventAges` AS `ages` ON `ages`.`ea_eventID` = `res`.`res_id`
			LEFT JOIN `{$wpdb->prefix}bookaroom_eventCats` AS `cats` ON `cats`.`ec_eventID` = `res`.`res_id`
			{$whereFinal}
			GROUP BY `ti`.`ti_id`
			ORDER BY {$scoreWhere}`ti`.`ti_startTime`, `res`.`ev_title` 
			{$limit}";		
		$final['results'] = $cooked = $wpdb->get_results( $sql, ARRAY_A );
		$rowCountArr = $wpdb->get_row( 'SELECT FOUND_ROWS() as rowcount' );
		$final['rowCount'] = $rowCountArr->rowcount;				
		return $final;		
	}
	
	protected static
	function getExternals()
	# Pull in POST and GET values
	{
		$final = array();
		
		# setup GET variables
		$getArr = array(	'action'					=> FILTER_SANITIZE_STRING,
							'addDateVals'				=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'endDate'					=> FILTER_SANITIZE_STRING, 
							'eventID'					=> FILTER_SANITIZE_STRING, 
							'published'					=> FILTER_SANITIZE_STRING, 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'searchTerms'				=> FILTER_SANITIZE_STRING, 
							'startDate'					=> FILTER_SANITIZE_STRING,
							);

		# pull in and apply to final
		if( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final = array_merge( $final, $getTemp );
		}
		
		# setup POST variables
		$postArr = array(	
							'action'					=> FILTER_SANITIZE_STRING,
							'addDateVals'				=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ),
							'ageGroup'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'categoryGroup'				=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'endDate'					=> FILTER_SANITIZE_STRING, 
							'eventID'					=> FILTER_SANITIZE_STRING, 
							'pageNum'					=> FILTER_SANITIZE_STRING, 
							'published'					=> FILTER_SANITIZE_STRING, 
							'regType'					=> FILTER_SANITIZE_STRING, 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'searchTerms'				=> FILTER_SANITIZE_STRING, 
							'startDate'					=> FILTER_SANITIZE_STRING,
							'submit'					=> FILTER_SANITIZE_STRING,
							'submit_count'				=> FILTER_SANITIZE_STRING,
							'submit_nav'				=> FILTER_SANITIZE_STRING,
							);
			
		# pull in and apply to final
		if( $postTemp = filter_input_array( INPUT_POST, $postArr ) ) {
			$final = array_merge( $final, $postTemp );
		}

		$arrayCheck = array_unique( array_merge( array_keys( $postArr ), array_keys( $getArr ) ) );
		
		foreach( $arrayCheck as $key ) {
			if( empty( $final[$key] ) ) {
				$final[$key] = NULL;
			} elseif( is_array( $final[$key] ) ) {
				$final[$key] = $final[$key];
			} else {
				$final[$key] = trim( $final[$key] );
			}
		}		
		 
		if( !empty( $final['roomID'] ) && substr( $final['roomID'], 0, 7 ) == 'branch-' ) {			
			$final['branchID'] = substr( $final['roomID'], 7 );
			$final['roomID'] = NULL;
		} else {
			$final['branchID'] = NULL;
		}
		
		if( !empty( $final['roomID'] ) && substr( $final['roomID'], 0, 6 ) == 'noloc-' ) {
			$final['noloc-branchID'] = substr( $final['roomID'], 6 );
			$final['roomID'] = NULL;
		} else {
			$final['noloc-branchID'] = NULL;
		}

		return $final;
	}
	
	protected static
	function getRegInfo( $eventID  )
	{
		global $wpdb;		
		$sql = "	SELECT `reg_id`, `reg_fullName`, `reg_phone`, `reg_email`, `reg_notes`, `reg_dateReg` 
					FROM `{$wpdb->prefix}bookaroom_registrations` 
					WHERE `reg_eventID` = '{$eventID}' 
					ORDER BY `reg_dateReg`";
		$cooked = $wpdb->get_results( $sql, ARRAY_A );		
		return $cooked;
	}
	
	public static function manageEvents( $externals, $resultsArr =  array( 'results' => array(), 'rowCount' => false ), $searched = false, $errorMSG = NULL )
	{
		$results			= $resultsArr['results'];
		$rowCount			= $resultsArr['rowCount'];
		$page_num			= self::get_clean_option( 'bookaroom_search_events_page_num' );
		$per_page			= self::get_clean_option( 'bookaroom_search_events_per_page' );		
		if( $per_page == __( 'All', 'book-a-room' ) ) {
			$totalPages 	= 1;
		} else {
			$totalPages 	= ceil( $rowCount / $per_page );
		}
		
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );

		if( $page_num < 1 ) {
			$page_num = 1;
		} elseif( $page_num > $totalPages ) {
			$page_num = $totalPages;
		}
		
		# get template		
		require( BOOKAROOM_PATH . 'templates/events/eventList.php' );
	}
	
	public static function get_clean_option( $type )
	{
		switch( $type ) {		
			case 'bookaroom_search_events_page_num':
				$temp = get_option( 'bookaroom_search_events_page_num' );
				
				if( !is_numeric( $temp ) ) {
					return NULL;
				} else {
					$final = $temp;
				}				
				break;
				
			case 'bookaroom_search_events_per_page':
				$temp = get_option( 'bookaroom_search_events_per_page' );
				
				if( !in_array( $temp, array( __( 'All', 'book-a-room' ), '10', '25', '50', '100' ) ) ) {
					$final = '10';
				} else {
					$final = $temp;
				}				
				break;
			
			case 'bookaroom_search_events_order_by':
				$temp = get_option( 'bookaroom_search_events_order_by' );
				
				if( !in_array( $temp, array( 'event_id', 'ti_startTime', 'title', 'branch', 'room', 'registrations' ) ) ) {
					$final = 'event_id';
				} else {
					$final = $temp;
				}				
				break;
			
			case 'bookaroom_search_events_sort_order':
				$temp = get_option( 'bookaroom_search_events_order_by' );
				
				if( !in_array( $temp, array( 'asc', 'desc' ) ) ) {
					$final = 'desc';
				} else {
					$final = $temp;
				}				
				break;
								
			default:
				$final = NULL;
				break;
		}
		
		return $final;		
	}
}
?>