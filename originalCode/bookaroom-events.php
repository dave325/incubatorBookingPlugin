<?php
class bookaroom_events {
	public static

	function bookaroom_adminEvents() {
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-amenities.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-rooms.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-branches.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-roomConts.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-closings.php' );
		require_once( BOOKAROOM_PATH . 'bookaroom-meetings-cityManagement.php' );

		# vaiables from includes
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		$cityList = bookaroom_settings_cityManagement::getCityList();

		$changeRoom = false;
		# first, is there an action? 
		$externals = self::getExternals();
		if ( empty( $_SESSION[ 'bookaroom_meetings_externalVals' ] ) and in_array( $externals[ 'action' ], array( 'checkBad', 'checkConflicts' ) ) ) {
			$externals[ 'action' ] = NULL;
		}

		$res_id = $externals[ 'res_id' ];
		$instance = false;

		switch ( $externals[ 'action' ] ) {
			case 'copy':
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, array(), true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				$externals = self::fixSavedEventInfo( $eventInfo, $externals );
				$_SESSION[ 'bookaroom_meetings_hash' ] = NULL;
				$_SESSION[ 'bookaroom_meetings_externalVals' ] = NULL;
				$externals[ 'eventStart' ] = false;
				$externals[ 'endTime' ] = false;
				$externals[ 'startTime' ] = false;
				$externals[ 'regDate' ] = false;
				self::showEventForm_times( $externals );
				break;

			case 'edit_attendance':
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, array(), true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				if ( false != ( $errorMSG = self::checkAttendance( $externals ) ) ) {
					self::showAttendance( $eventInfo, $externals, $externals[ 'attCount' ], $externals[ 'attNotes' ], $errorMSG );
				}
				self::editAttendance( $externals );
				self::showAttendanceSuccess();
				break;

			case 'manage_attendance':
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				self::showAttendance( $eventInfo, $externals, $eventInfo[ 'ti_attendance' ], $eventInfo[ 'ti_attNotes' ] );
				break;

			case 'edit_registration_final':
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check hash
				if ( empty( $externals[ 'hash' ] ) or empty( $externals[ 'hashTime' ] ) or empty( $externals[ 'regID' ] ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That was a problem editing that registration. Please try again.', 'book-a-room' ) );
					break;
				}

				$newHash = md5( $externals[ 'hashTime' ] . $externals[ 'regID' ] . $externals[ 'eventID' ] );
				if ( $newHash !== $externals[ 'hash' ] ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That was a problem deleting that registration. Please try again.', 'book-a-room' ) );
				}

				if ( ( $errorMSG = self::checkReg( $externals ) ) !== false ) {
					self::showEditRegistrations( $eventInfo, $externals, $branchList, $roomContList, $errorMSG );
					break;
				}

				self::editRegistration( $externals );
				self::manageRegistrations( $eventInfo, $branchList, $roomContList );
				break;

			case 'edit_registration':
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				self::showEditRegistrations( $eventInfo, $externals, $branchList, $roomContList );
				break;

			case 'delete_registration_final':
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check hash
				if ( empty( $externals[ 'hash' ] ) or empty( $externals[ 'hashTime' ] ) or empty( $externals[ 'regID' ] ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That was a missing value while deleting that registration. Please try again.', 'book-a-room' ) );
					break;
				}
				
				$newHash = md5( $externals[ 'hashTime' ] . $externals[ 'regID' ] . $externals[ 'eventID' ] );
				
				if ( $newHash !== $externals[ 'hash' ] ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That was a problem deleting that registration. Please try again.', 'book-a-room' ) );
				}
				$delResult = self::deleteReg( $externals, $eventInfo );
				if ( $delResult[ 'status' ] !== true ) {
					$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
					bookaroom_events_manage::manageEvents( $externals, $results, true, $delResult[ 'errorMSG' ] );
					break;
				}
				if ( !empty( $delResult[ 'alertID' ] ) ) {
					# phone or email
					self::alertReg( $delResult[ 'alertID' ], $eventInfo );
				} else {
					self::delReg_showSuccess( $delResult[ 'alertID' ] );
				}
				break;

			case 'delete_registration':
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				self::showDeleteRegistrations( $eventInfo, $externals[ 'regID' ], $branchList, $roomContList );
				break;

			case 'manage_registrations':
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				self::manageRegistrations( $eventInfo, $branchList, $roomContList );
				break;

			case 'checkDeleteMulti':
				if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ] ) ) {
					foreach ( array( 'startDate', 'endDate', 'published', 'searchTerms', 'branchID' ) as $val ) {
						if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ] ) ) {
							$externals[ $val ] = $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ];
						} else {
							$externals[ $val ] = NULL;
						}
					}
				}
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check if single				
				$externals = self::fixSavedEventInfo( $eventInfo, $externals );
				$instances = $instanceKeys = self::getInstances( $eventInfo[ 'res_id' ] );
				array_walk( $instanceKeys, function ( & $value, $index ) {
					$value = $value[ 'ti_id' ];
				} );

				if ( empty( $externals[ 'instance' ] ) or!is_array( $externals[ 'instance' ] ) ) {
					$externals[ 'instance' ] = array();
				}
				$finalDelete = array_intersect( $instanceKeys, $externals[ 'instance' ] );
				if ( count( $finalDelete ) == 0 ) {
					self::showDeleteMulti( $externals, $instances, __( 'You haven\'t selected any instances to delete.', 'book-a-room' ) );
				}
				self::deleteMulti( $eventInfo, $externals, $finalDelete, $instanceKeys );
				unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
				unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
				unset( $_POST );
				unset( $_GET );
				unset( $externals );
				self::deleteSuccess();
				break;

			case 'delete_multi':
				# get search settings
				if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ] ) ) {
					foreach ( array( 'startDate', 'endDate', 'published', 'searchTerms', 'branchID' ) as $val ) {
						if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ] ) ) {
							$externals[ $val ] = $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ];
						} else {
							$externals[ $val ] = NULL;
						}
					}
				}
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check if single				
				$externals = self::fixSavedEventInfo( $eventInfo, $externals );
				$instances = self::getInstances( $eventInfo[ 'res_id' ] );
				self::showDeleteMulti( $externals, $instances );
				break;

			case 'delete_instance':
				$instance = true;
			case 'delete':
				# get search settings
				if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ] ) ) {
					foreach ( array( 'startDate', 'endDate', 'published', 'searchTerms', 'branchID' ) as $val ) {
						if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ] ) ) {
							$externals[ $val ] = $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ];
						} else {
							$externals[ $val ] = NULL;
						}
					}
				}
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				$externals = self::fixSavedEventInfo( $eventInfo, $externals );
				self::showDeleteSingle( $externals, $instance );
				break;
				
			case 'deleteCheckInstance':
				$instance = true;
			case 'deleteCheck':
				# check hash
				if ( self::checkHash( $externals ) == false ) {
					$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'An error has occured. Please try again.', 'book-a-room' ) );
					break;
				}
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, array(), true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				self::deleteSingle( $eventInfo, $instance );
				self::deleteSuccess();
				break;
				
			case 'checkEvent':
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				if ( true == ( $result = self::checkFormEvent( $externals, $roomContList, $branchList, $amenityList ) ) ) {
					self::showEventForm_event( $externals, $result, $eventInfo );
					break;
				}
				$_SESSION[ 'bookaroom_meetings_hash' ] = md5( serialize( $externals ) );
				$_SESSION[ 'bookaroom_meetings_externalVals' ] = $externals;
				self::editEventReccuring( $externals );
				unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
				unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
				unset( $_POST );
				unset( $_GET );
				unset( $externals );
				self::editEventSuccess();
				break;

			case 'edit_event_changeRoom':
				$changeRoom = true;
			case 'edit_event':
				# get search settings
				if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ] ) ) {
					foreach ( array( 'startDate', 'endDate', 'published', 'searchTerms', 'branchID' ) as $val ) {
						if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ] ) ) {
							$externals[ $val ] = $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ];
						} else {
							$externals[ $val ] = NULL;
						}
					}
				}
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}

				if ( $changeRoom == false ) {
					$externals = self::fixSavedEventInfo( $eventInfo, $externals );
				}
				# check if single
				if ( $eventInfo[ 'tiCount' ] == 1 ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That event has a single date and cannot be edited as a multiple date date event.', 'book-a-room' ) );
					break;
				}
				self::showEventForm_event( $externals, NULL, $eventInfo );
				break;

			case 'checkInstanceEdit':
				# first check for errors					
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, NULL, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check if single				
				if ( $eventInfo[ 'tiCount' ] == 1 ) {
					bookaroom_events_manage::manageEvents( $externals, NULL, true, __( 'That event has a single date and cannot be edited as a multiple date event.', 'book-a-room' ) );
					break;
				}
				if ( true == ( $result = self::checkFormInstance( $externals, $roomContList, $branchList, $amenityList ) ) ) {
					self::showEventForm_instance( $externals, $result, $eventInfo );
					break;
				}
				$externalsTemp = self::fixSavedEventInfo( $eventInfo, $externals );
				$externalsTemp[ 'recurrence' ] = 'single';
				$externalsTemp[ 'startTime' ] = $externals[ 'startTime' ];
				$externalsTemp[ 'endTime' ] = $externals[ 'endTime' ];
				$externalsTemp[ 'roomID' ] = $externals[ 'roomID' ];
				$externalsTemp[ 'eventStart' ] = $externals[ 'eventStart' ];
				$externalsTemp[ 'eventID' ] = $externals[ 'eventID' ];
				$externalsTemp[ 'allDay' ] = $externals[ 'allDay' ];
				$_SESSION[ 'bookaroom_meetings_hash' ] = md5( serialize( $externalsTemp ) );
				$_SESSION[ 'bookaroom_meetings_externalVals' ] = $externalsTemp;
				if ( self::checkConflicts( $externalsTemp, $externalsTemp[ 'roomID' ], $dateList, array(), array(), $externalsTemp[ 'eventID' ] ) ) {
					self::showConflicts( $_SESSION[ 'bookaroom_meetings_externalVals' ], $dateList, $externals[ 'delete' ] );
					self::showEventForm_instance( $externals, NULL, $eventInfo );
				} else {
					self::editEventInstance( $externals );
					unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
					unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
					unset( $_POST );
					unset( $_GET );
					unset( $externals );
					self::editEventSuccess();
				}
				break;

			case 'edit_instance_changeRoom':
				$changeRoom = true;
			case 'edit_instance':
				# get search settings
				if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ] ) ) {
					foreach ( array( 'startDate', 'endDate', 'published', 'searchTerms', 'branchID' ) as $val ) {
						if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ] ) ) {
							$externals[ $val ] = $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ];
						} else {
							$externals[ $val ] = NULL;
						}
					}
				}
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				if ( $changeRoom == false ) {
					$externals[ 'startTime' ] = $eventInfo[ 'ti_startTime' ];
					$externals[ 'endTime' ] = $eventInfo[ 'ti_endTime' ];
					$externals[ 'roomID' ] = $eventInfo[ 'ti_roomID' ];
					$externals[ 'eventStart' ] = $eventInfo[ 'ti_startTime' ];
					$externals[ 'extraInfo' ] = $eventInfo[ 'ti_extraInfo' ];
				}
				# check if single
				if ( $eventInfo[ 'tiCount' ] == 1 ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That event has a single date and cannot be edited as a multiple date date event.', 'book-a-room' ) );
					break;
				}
				if ( $changeRoom == false ) {
					#$externals[''] 
				}
				self::showEventForm_instance( $externals, NULL, $eventInfo );
				break;

			case 'checkSingleEdit':
				# first check for errors					
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check if single				
				if ( $eventInfo[ 'tiCount' ] > 1 ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, 'That event has multiple dates and cannot be edited as a single date event.' );
					break;
				}
				if ( true == ( $result = self::checkForm( $externals, $roomContList, $branchList, $amenityList ) ) ) {
					self::showEventForm_times( $externals, $result, 'Edit', 'checkSingleEdit', 'edit_single_changeRoom' );
					break;
				}
				$_SESSION[ 'bookaroom_meetings_hash' ] = md5( serialize( $externals ) );
				$_SESSION[ 'bookaroom_meetings_externalVals' ] = $externals;
				if ( self::checkConflicts( $externals, $_SESSION[ 'bookaroom_meetings_externalVals' ][ 'roomID' ], $dateList, array(), array(), $externals[ 'eventID' ] ) ) {
					self::showEventForm_times( $externals, array( 'errorMSG' => __( 'There is a conflict at this time. Please try another time or room.', 'book-a-room' ) ), 'Edit', 'checkSingleEdit', 'edit_single_changeRoom' );
				} else {
					self::editEvent( $dateList );
					unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
					unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
					unset( $_POST );
					unset( $_GET );
					unset( $externals );
					self::editEventSuccess();
				}
				break;

			case 'edit_single_changeRoom':
				$changeRoom = true;
			case 'edit_single':
                $externals['recurrence'] = 'single';
				# get search settings
				if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ] ) ) {
					foreach ( array( 'startDate', 'endDate', 'published', 'searchTerms', 'branchID' ) as $val ) {
						if ( !empty( $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ] ) ) {
							$externals[ $val ] = $_SESSION[ 'bookaroom_temp_search_settings' ][ $val ];
						} else {
							$externals[ $val ] = NULL;
						}
					}
				}
				$results = bookaroom_events_manage::getEventList( $externals, $amenityList, $branchList, $roomContList, $roomList );
				#check id	
				if ( false == ( $eventInfo = self::checkID( $externals[ 'eventID' ] ) ) ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That ID is invalid. Please try again.', 'book-a-room' ) );
					break;
				}
				# check if single				
				if ( $eventInfo[ 'tiCount' ] > 1 ) {
					bookaroom_events_manage::manageEvents( $externals, $results, true, __( 'That event has multiple dates and cannot be edited as a single date event.', 'book-a-room' ) );
					break;
				}
				if ( $changeRoom == false ) {
					$externals = self::fixSavedEventInfo( $eventInfo, $externals );
				}
				self::showEventForm_times( $externals, NULL, 'Edit', 'checkSingleEdit', 'edit_single_changeRoom' );
				break;

			case 'checkBad':
				if ( self::checkConflicts( $externals, $_SESSION[ 'bookaroom_meetings_externalVals' ][ 'roomID' ], $dateList ) ) {
					self::showConflicts( $_SESSION[ 'bookaroom_meetings_externalVals' ], $dateList );
				} else {
					self::addEvent( $dateList );
					unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
					unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
					unset( $_POST );
					unset( $_GET );
					unset( $externals );
					self::addEventSuccess();
				}

				break;

			case 'checkConflicts':
				if ( self::checkConflicts( $_SESSION[ 'bookaroom_meetings_externalVals' ], $_SESSION[ 'bookaroom_meetings_externalVals' ][ 'roomID' ], $dateList, $externals[ 'delete' ], $externals[ 'newVal' ] ) ) {
					self::showConflicts( $_SESSION[ 'bookaroom_meetings_externalVals' ], $dateList, $externals[ 'delete' ] );					
					self::showEventForm_times( $_SESSION[ 'bookaroom_meetings_externalVals' ], null, 'New', 'checkInformation', 'changeRooms', $dateList );
				} else {
					self::addEvent( $dateList, $externals[ 'delete' ] );
					unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
					unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
					unset( $_POST );
					unset( $_GET );
					unset( $externals );
					self::addEventSuccess();
				}
				break;

			case 'checkLocation':
				if ( empty( $externals[ 'roomID' ] ) or!array_key_exists( $externals[ 'roomID' ], $roomContList[ 'id' ] ) ) {
					self::showEventForm_times( $externals, $roomContList, $branchList, __( 'Please choose a valid room.', 'book-a-room' ) );
					break;
				}
				$externals[ 'recurrence' ] = 'none';
				$externals[ 'registration' ] = 'false';
				$externals[ 'waitingList' ] = get_option( 'bookaroom_waitingListDefault' );
				self::showEventForm_times( $externals, $roomContList, $branchList );
				break;

			case 'checkInformation':
				# first check for errors
				if ( true == ( $result = self::checkForm( $externals, $roomContList, $branchList, $amenityList ) ) ) {
					self::showEventForm_times( $externals, $result );
					break;
				}

				$_SESSION[ 'bookaroom_meetings_hash' ] = md5( serialize( $externals ) );
				$_SESSION[ 'bookaroom_meetings_externalVals' ] = $externals;
				if ( self::checkConflicts( $externals, $_SESSION[ 'bookaroom_meetings_externalVals' ][ 'roomID' ], $dateList ) ) {
					self::showConflicts( $_SESSION[ 'bookaroom_meetings_externalVals' ], $dateList );
					self::showEventForm_times( $_SESSION[ 'bookaroom_meetings_externalVals' ], null, 'New', 'checkInformation', 'changeRooms', $dateList );
				} else {
					self::addEvent( $dateList );
					unset( $_SESSION[ 'bookaroom_meetings_externalVals' ] );
					unset( $_SESSION[ 'bookaroom_meetings_hash' ] );
					unset( $_POST );
					unset( $_GET );
					unset( $externals );
					self::addEventSuccess();
				}
				break;

			case 'makeReservation':
				# check hours
				$baseIncrement = get_option( 'bookaroom_baseIncrement' );
				if ( !empty( $externals[ 'hours' ] ) ) {
					$externals[ 'startTime' ] = current( $externals[ 'hours' ] );
				} else {
					$externals[ 'startTime' ] = $externals[ 'timestamp' ];
				}

				if ( !empty( $externals[ 'hours' ] ) ) {
					$externals[ 'endTime' ] = end( $externals[ 'hours' ] ) + ( $baseIncrement * 60 );
				} else {
					$externals[ 'endTime' ] = $externals[ 'timestamp' ];
				}

				if ( ( $errorMSG = bookaroom_public::showForm_checkHoursError( $externals[ 'startTime' ], $externals[ 'endTime' ], $externals[ 'roomID' ], $roomContList, $branchList, $externals[ 'res_id' ], TRUE ) ) == TRUE ) {
					self::showEventForm_times( $externals );
					break;
				}
				$requestInfo[ 'startTime' ] = $externals[ 'startTime' ];
				$requestInfo[ 'endTime' ] = $externals[ 'endTime' ];
				$requestInfo[ 'amenity' ] = unserialize( $externals[ 'amenity' ] );
				bookaroom_public::showForm_publicRequest( $externals[ 'roomID' ], $branchList, $roomContList, $roomList, $amenityList, $cityList, $requestInfo, array(), TRUE );
				break;

			default:
				$externals[ 'waitingList' ] = get_option( 'bookaroom_waitingListDefault' );
				$_SESSION[ 'bookaroom_meetings_hash' ] = NULL;
				$_SESSION[ 'bookaroom_meetings_externalVals' ] = NULL;
				self::showEventForm_times( $externals );
				break;

			case 'changeRoom':
				self::showEventForm_times( $externals );
				#self::showEventForm( $externals, $roomContList, $branchList );
				break;
		}
	}

	protected static function editEventInstance( $externals )
	{
		global $wpdb;		
		$table_name = $wpdb->prefix . "bookaroom_times";

		if( !empty( $externals['allDay'] ) and $externals['allDay'] == 'true' ) {
			$startTime = date('Y-m-d H:i:s', strtotime( $externals['eventStart'] . ' 00:00:00' ) );
			$endTime = date('Y-m-d H:i:s', strtotime( $externals['eventStart'] . ' 23:59:59' ) );
		} else {		
			$startTime = date('Y-m-d H:i:s', strtotime( $externals['eventStart'] . ' ' . $externals['startTime'] ) );
			$endTime = date('Y-m-d H:i:s', strtotime( $externals['eventStart'] . ' ' . $externals['endTime'] ) );
		}

		if( substr( $externals['roomID'], 0, 6 ) == 'noloc-' ) {
			$roomInfo = explode( '-', $externals['roomID'] );			
			$branchID = $roomInfo[1];
			$roomSQL = " `ti_roomID` = '0', `ti_noLocation_branch` = '{$branchID}'";
		} else {
			$roomSQL = " `ti_roomID` = '{$externals['roomID']}'";
		}
		
		$sql = "UPDATE `$table_name` SET `ti_extraInfo` = '{$externals['extraInfo']}', `ti_startTime` = '{$startTime}', `ti_endTime` = '{$endTime}',{$roomSQL}
		WHERE `ti_id` = '{$externals['eventID']}'";
		$wpdb->query( $sql );				
	}
	
	protected static function delReg_showPhone( $regInfo )
	{
		require( BOOKAROOM_PATH . 'templates/events/delReg_showPhone.php' );
	}
	
	protected static function delReg_showSuccess( $regInfo )
	{
		require( BOOKAROOM_PATH . 'templates/events/delReg_showSuccess.php' );
	}
	
	protected static function delReg_showMail( $regInfo )
	{
		require( BOOKAROOM_PATH . 'templates/events/delReg_showEmail.php' );
	}
	
	protected static function alertReg( $regID, $eventInfo )
	{
		
		$registrations = self::getRegistrations( $eventInfo['ti_id'] );
		
		if( empty( $registrations[$regID]['reg_email'] ) ) {
			self::delReg_showPhone( $registrations[$regID] );
			return false;
		}
		
		$fromName	= get_option( 'bookaroom_alertEmailFromName' );	
		$fromEmail	= get_option( 'bookaroom_alertEmailFromEmail' );
		
		$replyName	= get_option( 'bookaroom_alertEmailReplyName' );	
		$replyEmail	= get_option( 'bookaroom_alertEmailReplyEmail' );
		
		$CCEmail	= get_option( 'bookaroom_alertEmailCC' );	
		$BCEmail	= get_option( 'bookaroom_alertEmailBCC' );
		
		$subject	= get_option( 'bookaroom_regChange_subject' );
		$body		= nl2br( get_option( 'bookaroom_regChange_body' ) );
		
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		
		
		$body = str_replace( '{eventName}', $eventInfo['ev_title'], $body );
		$body = str_replace( '{date}', date_i18n( 'l, F jS, Y', strtotime( $eventInfo['ti_startTime'] ) ), $body );
		$body = str_replace( '{startTime}', date( 'g:i a', strtotime( $eventInfo['ti_startTime'] ) ), $body );
		
		if( !empty( $eventInfo['ti_noLocation_branch'] ) ) {
			$branch = $branchList[$eventInfo['ti_noLocation_branch']]['branchDesc'];
		} else {
			$branch = $branchList[$roomContList['id'][$eventInfo['ti_roomID']]['branchID']]['branchDesc'];
		}
		
		$body = str_replace( '{branchName}', $branch, $body );
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					"From: {$fromName} <{$fromEmail}>" . "\r\n";
		if( !empty( $replyName ) and !empty( $replyEmail ) ) {
			$headers .= "Reply-To: {$replyName} <{$replyEmail}>" . "\r\n";
		}
		if( !empty( $CCEmail ) ) {
			$headers .= "CC: {$CCEmail}" . "\r\n";
		}
		if( !empty( $BCCEmail ) ) {
			$headers .= "BCC: {$BCCEmail}" . "\r\n";
		}
		$headers .=	'X-Mailer: PHP/' . phpversion();
				
		wp_mail( $registrations[$regID]['reg_email'], $subject, $body, $headers );
		
		self::delReg_showMail( $registrations[$regID] );
		
		return true;
	}
	
	protected static function editEventReccuring( )
	{
		global $wpdb;
		
		if( $_SESSION['bookaroom_meetings_externalVals']['doNotPublish'] == 'true' ) {
			$doNotPublish = '1';
		} else {
			$doNotPublish = '0';
		}
		
		# get event main info
		$eventInfo = self::checkID( $_SESSION['bookaroom_meetings_externalVals']['eventID'] );
		
		# insert event
		$table_name = $wpdb->prefix . "bookaroom_reservations";
		
		if( !empty( $_SESSION['bookaroom_meetings_externalVals']['publicPhone'] ) ) {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $_SESSION['bookaroom_meetings_externalVals']['publicPhone'] );
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
			}
		} else {
			$cleanPhone = NULL;
		}
		
		# amenity
		$amenity = ( empty( $_SESSION['bookaroom_meetings_externalVals']['amenity'] ) ) ? NULL : serialize( $_SESSION['bookaroom_meetings_externalVals']['amenity'] );
		
		$regDate = date('Y-m-d H:i:s', strtotime( $_SESSION['bookaroom_meetings_externalVals']['regDate'] ) );
		
		$sql = "UPDATE `$table_name` SET 
			`ev_desc` = '".$_SESSION['bookaroom_meetings_externalVals']['eventDesc']."', 
			`ev_maxReg` = '".$_SESSION['bookaroom_meetings_externalVals']['maxReg']."', 
			`ev_amenity` = '{$amenity}', 
			`ev_waitingList` = '".$_SESSION['bookaroom_meetings_externalVals']['waitingList']."', 
			`ev_presenter` = '".$_SESSION['bookaroom_meetings_externalVals']['presenter']."', 
			`ev_privateNotes` = '".$_SESSION['bookaroom_meetings_externalVals']['privateNotes']."', 
			`ev_publicEmail` = '".$_SESSION['bookaroom_meetings_externalVals']['publicEmail']."', 
			`ev_publicName` = '".$_SESSION['bookaroom_meetings_externalVals']['publicName']."', 
			`ev_publicPhone` = '{$cleanPhone}', 
			`ev_noPublish` = '{$doNotPublish}', 
			`ev_regStartDate` = '{$regDate}', 
			`ev_regType` = '".$_SESSION['bookaroom_meetings_externalVals']['registration']."',  
			`ev_submitter` = '".$_SESSION['bookaroom_meetings_externalVals']['yourName']."', 
			`ev_title` = '".$_SESSION['bookaroom_meetings_externalVals']['eventTitle']."', 
			`ev_website` = '".$_SESSION['bookaroom_meetings_externalVals']['website']."', 
			`ev_webText` = '".$_SESSION['bookaroom_meetings_externalVals']['websiteText']."' 
			WHERE `res_id` = '{$eventInfo['res_id']}'";
		$wpdb->query( $sql );

		# ages		
		$agesArr = array();
		foreach( $_SESSION['bookaroom_meetings_externalVals']['ageGroup'] as $key => $age ) {
			$agesArr[] = "('{$eventInfo['res_id']}', '{$age}')";
		}

		$table_name = $wpdb->prefix . "bookaroom_eventAges";
		
		$finalAges = implode( ', ', $agesArr );
		$sql = "DELETE FROM `{$table_name}` WHERE `ea_eventID` = '{$eventInfo['res_id']}'";
		
		$wpdb->query( $sql );
		
		$sql = "INSERT INTO `{$table_name}` (`ea_eventID`, `ea_ageID`) VALUES {$finalAges}";
		
		$wpdb->query( $sql );

		# categories

		$table_name = $wpdb->prefix . "bookaroom_eventCats";
		
		$catsArr = array();
		foreach( $_SESSION['bookaroom_meetings_externalVals']['category'] as $key => $cat ) {
			$catsArr[] = "('{$eventInfo['res_id']}', '{$cat}')";
		}
		
		$table_name = $wpdb->prefix . "bookaroom_eventCats";
		
		$finalCats = implode( ', ', $catsArr );
		
		$sql = "DELETE FROM `{$table_name}` WHERE `ec_eventID` = '{$eventInfo['res_id']}'";
		$wpdb->query( $sql );
		
		$sql = "INSERT INTO `$table_name` (`ec_eventID`, `ec_catID`) VALUES {$finalCats}";
		$wpdb->query( $sql );
	
		
	}
	
	protected static function editEvent( $dateList, $delete = array() )
	{
		global $wpdb;
		
		if( $_SESSION['bookaroom_meetings_externalVals']['doNotPublish'] == 'true' ) {
			$doNotPublish = '1';
		} else {
			$doNotPublish = '0';
		}
		
		# get event main info
		$eventInfo = self::checkID( $_SESSION['bookaroom_meetings_externalVals']['eventID'] );
		
		# insert event
		$table_name = $wpdb->prefix . "bookaroom_reservations";
		
		if( !empty( $_SESSION['bookaroom_meetings_externalVals']['publicPhone'] ) ) {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $_SESSION['bookaroom_meetings_externalVals']['publicPhone'] );
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
			}
		} else {
			$cleanPhone = NULL;
		}
		
		# amenity
		$amenity = ( empty( $_SESSION['bookaroom_meetings_externalVals']['amenity'] ) ) ? NULL : serialize( $_SESSION['bookaroom_meetings_externalVals']['amenity'] );
		
		$regDate = date('Y-m-d H:i:s', strtotime( $_SESSION['bookaroom_meetings_externalVals']['regDate'] ) );
		
		$sql = "UPDATE `$table_name` SET 
			`ev_desc` = '".$_SESSION['bookaroom_meetings_externalVals']['eventDesc']."', 
			`ev_maxReg` = '".$_SESSION['bookaroom_meetings_externalVals']['maxReg']."', 
			`ev_amenity` = '{$amenity}', 
			`ev_waitingList` = '".$_SESSION['bookaroom_meetings_externalVals']['waitingList']."', 
			`ev_presenter` = '".$_SESSION['bookaroom_meetings_externalVals']['presenter']."', 
			`ev_privateNotes` = '".$_SESSION['bookaroom_meetings_externalVals']['privateNotes']."', 
			`ev_publicEmail` = '".$_SESSION['bookaroom_meetings_externalVals']['publicEmail']."', 
			`ev_publicName` = '".$_SESSION['bookaroom_meetings_externalVals']['publicName']."', 
			`ev_publicPhone` = '{$cleanPhone}', 
			`ev_noPublish` = '{$doNotPublish}', 
			`ev_regStartDate` = '{$regDate}', 
			`ev_regType` = '".$_SESSION['bookaroom_meetings_externalVals']['registration']."',  
			`ev_submitter` = '".$_SESSION['bookaroom_meetings_externalVals']['yourName']."', 
			`ev_title` = '".$_SESSION['bookaroom_meetings_externalVals']['eventTitle']."', 
			`ev_website` = '".$_SESSION['bookaroom_meetings_externalVals']['website']."', 
			`ev_webText` = '".$_SESSION['bookaroom_meetings_externalVals']['websiteText']."' 
			WHERE `res_id` = '{$eventInfo['res_id']}'";

		$wpdb->query( $sql );

		
		$table_name = $wpdb->prefix . "bookaroom_times";
		
		foreach( $dateList as $val ) {
			if( array_search( date( 'm/d/y', $val['start'] ), $delete ) ) {
				continue;
			}
			$startTime = date('Y-m-d H:i:s', $val['start'] );
			$endTime = date('Y-m-d H:i:s', $val['end'] );
			$roomID = $_SESSION['bookaroom_meetings_externalVals']['roomID'];
			
			if( substr( $roomID, 0, 6 ) == 'noloc-' ) {
				$roomInfo = explode( '-', $roomID );
				
				$branchID = $roomInfo[1];
				$roomID = NULL;
			} else {
				$goodRoomID = $roomID;
				$branchID = NULL;
			}
			$sql = "UPDATE `{$table_name}` SET 
				`ti_type` = 'event', 
				`ti_extID` = '{$eventInfo['res_id']}', 
				`ti_startTime` = '{$startTime}', 
				`ti_endTime` = '{$endTime}', 
				`ti_roomID` = '{$roomID}', 
				`ti_noLocation_branch` = '{$branchID}' 
				WHERE `ti_id` = '{$_SESSION['bookaroom_meetings_externalVals']['eventID']}'";

			$wpdb->query( $sql );	
		}
		# ages
		
		$agesArr = array();
		foreach( $_SESSION['bookaroom_meetings_externalVals']['ageGroup'] as $key => $age ) {
			$agesArr[] = "('{$eventInfo['res_id']}', '{$age}')";
		}

		$table_name = $wpdb->prefix . "bookaroom_eventAges";
		
		$finalAges = implode( ', ', $agesArr );
		$sql = "DELETE FROM `{$table_name}` WHERE `ea_eventID` = '{$eventInfo['res_id']}'";
		
		$wpdb->query( $sql );
		
		$sql = "INSERT INTO `{$table_name}` (`ea_eventID`, `ea_ageID`) VALUES {$finalAges}";
		
		$wpdb->query( $sql );

		# categories

		$table_name = $wpdb->prefix . "bookaroom_eventCats";
		
		$catsArr = array();
		foreach( $_SESSION['bookaroom_meetings_externalVals']['category'] as $key => $cat ) {
			$catsArr[] = "('{$eventInfo['res_id']}', '{$cat}')";
		}
		
		$table_name = $wpdb->prefix . "bookaroom_eventCats";
		
		$finalCats = implode( ', ', $catsArr );
		
		$sql = "DELETE FROM `{$table_name}` WHERE `ec_eventID` = '{$eventInfo['res_id']}'";
		$wpdb->query( $sql );
		
		$sql = "INSERT INTO `$table_name` (`ec_eventID`, `ec_catID`) VALUES {$finalCats}";
		$wpdb->query( $sql );
	
		
	}
		
	protected static function addEvent( $dateList, $delete = array() )
	{
		global $wpdb;
		
        $currentTimeMySQL = date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
                
		if( $_SESSION['bookaroom_meetings_externalVals']['doNotPublish'] == 'true' ) {
			$doNotPublish = '1';
		} else {
			$doNotPublish = '0';
		}
		# insert event
		$table_name = $wpdb->prefix . "bookaroom_reservations";
		
		if( !empty( $_SESSION['bookaroom_meetings_externalVals']['publicPhone'] ) ) {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $_SESSION['bookaroom_meetings_externalVals']['publicPhone'] );
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
			}
		} else {
			$cleanPhone = NULL;
		}
		
		# amenity
		$amenity = ( empty( $_SESSION['bookaroom_meetings_externalVals']['amenity'] ) ) ? NULL : serialize( $_SESSION['bookaroom_meetings_externalVals']['amenity'] );
		
		if( !empty( $_SESSION['bookaroom_meetings_externalVals']['regDate'] ) ) {
			$regDate = date('Y-m-d H:i:s', strtotime( $_SESSION['bookaroom_meetings_externalVals']['regDate'] ) );
		} else {
			$regDate = NULL;
		}
		
       
        
		$sql = "INSERT INTO `$table_name` ( `res_created`, `ev_desc`, `ev_maxReg`, `ev_amenity`, `ev_waitingList`, `ev_presenter`, `ev_privateNotes`, `ev_publicEmail`, `ev_publicName`, `ev_publicPhone`, `ev_noPublish`, `ev_regStartDate`, `ev_regType`, `ev_submitter`, `ev_title`, `ev_website`, `ev_webText` ) 
			VALUES(
            '".$currentTimeMySQL."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['eventDesc']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['maxReg']."', 
			'{$amenity}', 
			'".$_SESSION['bookaroom_meetings_externalVals']['waitingList']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['presenter']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['privateNotes']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['publicEmail']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['publicName']."', 
			'{$cleanPhone}', 
			'{$doNotPublish}', 
			'{$regDate}', 
			'".$_SESSION['bookaroom_meetings_externalVals']['registration']."',  
			'".$_SESSION['bookaroom_meetings_externalVals']['yourName']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['eventTitle']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['website']."', 
			'".$_SESSION['bookaroom_meetings_externalVals']['websiteText']."' )";
		$wpdb->query( $sql );

		$insertID = $wpdb->insert_id;
        
		if( empty( $insertID ) ) {
			die( 'Mysql Error' );
		}
		
		$table_name = $wpdb->prefix . "bookaroom_times";
		

		foreach( $dateList as $val ) {
			if( array_search( date( 'm/d/y', $val['start'] ), $delete ) ) {
				continue;
			}
			$startTime = date('Y-m-d H:i:s', $val['start'] );
			$endTime = date('Y-m-d H:i:s', $val['end'] );
			$roomID = $_SESSION['bookaroom_meetings_externalVals']['roomID'];
			
			if( substr( $roomID, 0, 6 ) == 'noloc-' ) {
				$roomInfo = explode( '-', $roomID );
				
				$branchID = $roomInfo[1];
				$roomID = NULL;
			} else {
				$goodRoomID = $roomID;
				$branchID = NULL;
			}
			$sql = "INSERT INTO `{$table_name}` ( `ti_created`, `ti_type`, `ti_extID`, `ti_startTime`, `ti_endTime`, `ti_roomID`, `ti_noLocation_branch` ) VALUES( '".$currentTimeMySQL."', 'event', '{$insertID}', '{$startTime}', '{$endTime}', '{$roomID}', '{$branchID}' )";
			$wpdb->query( $sql );

			$t_insertID = $wpdb->insert_id;
            
            if( empty( $t_insertID ) ) {
				die( 'Mysql Error' );
			}
		}
		# ages		
		$agesArr = array();
		foreach( $_SESSION['bookaroom_meetings_externalVals']['ageGroup'] as $key => $age ) {
			$agesArr[] = "('{$insertID}', '{$age}')";
		}
		
		$table_name = $wpdb->prefix . "bookaroom_eventAges";
		
		$finalAges = implode( ', ', $agesArr );
		
		$sql = "INSERT INTO `$table_name` (`ea_eventID`, `ea_ageID`) VALUES {$finalAges}";
		
        $wpdb->query( $sql );
		
		# categories
		$table_name = $wpdb->prefix . "bookaroom_eventCats";
		
		$catsArr = array();
		foreach( $_SESSION['bookaroom_meetings_externalVals']['category'] as $key => $cat ) {
			$catsArr[] = "('{$insertID}', '{$cat}')";
		}
		
		$table_name = $wpdb->prefix . "bookaroom_eventCats";
		
		$finalCats = implode( ', ', $catsArr );
		
		$sql = "INSERT INTO `$table_name` (`ec_eventID`, `ec_catID`) VALUES {$finalCats}";
		
		$wpdb->query( $sql );
        
	}
	
	protected static function editEventSuccess()
	{
		require( BOOKAROOM_PATH . 'templates/events/eventForm_edit_success.php' );		
	}
		
	protected static function addEventSuccess()
	{
		require( BOOKAROOM_PATH . 'templates/events/eventForm_success.php' );
	}
	
	public static function branch_and_room_id( &$roomID, $branchList, $roomContList )
	{
		
		if( !empty( $roomID ) and array_key_exists( $roomID, $roomContList['id'] ) ) {
			$branchID = $roomContList['id'][$roomID]['branchID'];
		# else check branchID
		} else {
			$branchID = NULL;
		}
		
		if( !array_key_exists( $roomID, $roomContList['id'] ) ) {
			$roomID = NULL;
		}
		
		return $branchID;		
	}

	protected static function checkAttendance( $externals )
	{
		$errorMSG = false;
		
		if( empty( $externals['attCount'] ) and empty( $externals['attNotes'] ) ) {
			$errorMSG = __( 'You must enter either an attendance amount or a note.', 'book-a-room' );
		} elseif( !empty( $externals['attCount'] ) and !is_numeric( $externals['attCount'] ) ) {
			$errorMSG = __( 'You must enter a valid number for attendance amount.', 'book-a-room' );
		}
		
		return $errorMSG;
	}
	protected static function checkConflicts( $externals, $roomID, &$dateList, $delete = array(), $newVal = array(), $editID = NULL )
	{
		# find which kind of recurrence it is
		global $wpdb;
		
		$usedRooms = array();
		
		switch( $externals['recurrence'] ) {
			case 'single':
				$dateList = self::getDates_single();
				break;

			case 'daily':
				$dateList = self::getDates_daily();
				break;
				
			case 'weekly':
				$dateList = self::getDates_weekly();
				break;

			case 'addDates':
				$dateList = self::getDates_addDates();
				break;				
				
			default:
				echo 'error: wrong recurrance';
				die();
				break;
		}
		
		# vaiables from includes
		$realRoomContList = bookaroom_settings_roomConts::getRoomContList();
		$allRoomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		
		# get room list from container ID
		
		$roomInfo = self::getRoomContListByRoomID( $roomID, $roomContList, $allRoomList );

		$allRooms = array_keys( $allRoomList['id'] );
		
		$closings = bookaroom_settings_closings::getClosingsList();
		
		$conflicts = array();

 		# check for closing
		if( empty( $closings['live'] ) or !is_array( $closings['live'] ) ) {
			$closing['live'] = array();
		} else {		
			foreach( $closings['live'] as $val ) {
				$closingRooms = unserialize( $val['roomsClosed'] );				
				switch( $val['type'] ) {				
					case 'range':
						$startTime = $val['startTime'];
						$endTime = $val['endTime'];
						$range = true;
						break;
					
					case 'date':
						$startTime = $val['startTime'];
						$endTime = $val['startTime'] + 86399;
						$range = false;
						break;
				}
									
				if( !is_array( $dateList ) ) {
					$dateList = array();
				}
				
				foreach( $dateList as $key => $dates) {
					# check for deletes
					if( $range == true ) {
						# range stuff
					} else {
						$quickStart = date( 'm/d/y', $dates['start'] );
						
						if( is_array( $delete ) and in_array( $quickStart, $delete ) ) {
							continue;
						}	
					}
					# check for new vals

					if( $val['allClosed'] == true or array_intersect( $roomInfo['finalRoomConts'], $closingRooms ) ) {
					# if the room is valid, lets check the date
	
						$temp = array();
						if( $dates['start'] >= $startTime and $dates['end'] <= $endTime ) {
							$temp['desc'] = $val['closingName'];
							$temp['type'] = 'Closing';
							$temp['startTime'] = date('Y-m-d H:i:s', $startTime );
							$temp['endTime'] = date('Y-m-d H:i:s', $endTime );
							$temp['allDay'] = true;
							
							# all rooms closed
							if( $val['allClosed'] == true ) {
								$temp['roomID'] = array();
								$temp['allClosed'] = true;
								$closingRoomsTemp = $allRooms;
							# non numeric - find partial closes
							} else {
								$temp['roomID'] = unserialize( $val['roomsClosed'] );
								$temp['allClosed'] = false;
								$closingRoomsTemp = $closingRooms;
							}
							
							$dateList[$key]['conflicts'][] = $temp;
							$dateList[$key]['usedRoomsClosings'] = $closingRoomsTemp;
							
							if ( !empty( $cval['ti_roomID'] ) ) {
								$conflicts[]= $cval['ti_roomID'];
							}
						}
					}
				}
			}
		}
		
		if( substr( $externals['roomID'], 0, 6 ) == 'noloc-' ) {
			return $conflicts;
		}
		foreach( $dateList as $key => $val ) {
			$quickStart = date( 'm/d/y', $val['start'] );
			
			if( is_array( $delete ) and in_array( $quickStart, $delete ) ) {
				continue;
			}
			
			if( !empty( $editID ) ) {
				$where = " and `ti`.`ti_id` != '{$editID}'";
			} else {
				$where = NULL;
			}
			
			if( empty( $roomInfo['roomContList'] ) ) {
				$roomInfo['roomContList'] = "''";
			}
			
			$sql = "SELECT `ti`.`ti_id`, `ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_type`, `res`.`me_eventName`, `res`.`ev_desc`, `ti`.`ti_roomID` 
					FROM `{$wpdb->prefix}bookaroom_times` AS `ti`
					LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` AS `res` ON `ti`.`ti_extID` = `res`.`res_id` 
					WHERE ( ( `res`.me_status != 'archived' and `res`.`me_status` != 'denied') AND `ti`.`ti_roomID` in ( {$roomInfo['roomContList']}) ) AND (`ti`.`ti_startTime` < '".date('Y-m-d H:i:s', $val['end'])."' && `ti`.`ti_endTime` > '".date('Y-m-d H:i:s', $val['start'])."' ){$where}";
			$cooked = $wpdb->get_results( $sql, ARRAY_A );

			# find all bAd
			$sql = "SELECT `ti`.`ti_id`, `ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_type`, `res`.`me_eventName`, `res`.`ev_desc`, `ti`.`ti_roomID` 
					FROM `{$wpdb->prefix}bookaroom_times` AS `ti`
					LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` AS `res` ON `ti`.`ti_extID` = `res`.`res_id` 
					WHERE ( ( `res`.me_status != 'archived' and `res`.`me_status` != 'denied') AND `ti`.`ti_startTime` < '".date('Y-m-d H:i:s', $val['end'])."' && `ti`.`ti_endTime` > '".date('Y-m-d H:i:s', $val['start'])."' )";

			$usedRooms = array();
			
			foreach( $wpdb->get_results( $sql, ARRAY_A ) as $badKey => $badVal ) {
				$usedRooms[] = $badVal['ti_roomID'];
			}
			

			$dateList[$key]['usedRooms'] = @array_unique( $usedRooms );
			
			if( count( $cooked ) !== 0 ) {
				foreach( $cooked as $ckey => $cval ) {
					$temp = array();
					switch( $cval['ti_type'] ) {
						case 'meeting':
							$temp['desc'] = $cval['me_eventName'];
							$temp['type'] = 'Meeting';
							break;
							
						case 'event':
							$temp['desc'] = $cval['ev_desc'];
							$temp['type'] = 'Event';
							break;
					}
					
					$temp['eventID'] = $cval['ti_id'];
					$temp['startTime'] = $cval['ti_startTime'];
					$temp['endTime'] = $cval['ti_endTime'];
					$temp['roomID'] = $cval['ti_roomID'];
					$dateList[$key]['conflicts'][] = $temp;
					$conflicts[]= $cval['ti_roomID'];
				}
			}
		}
		# find unused rooms.
		return $conflicts;
	}
	
	protected static function checkDate( $date, $name )
	{
		if( empty( $date ) ) {
			return "You must enter an {$name}.";
		}
		
		$dateArr = date_parse( $date );
		if( checkdate( $dateArr["month"], $dateArr["day"], $dateArr["year"] ) ) {
			return NULL;
		} else {
		    return sprintf( __( 'You must enter a valid date in the %s field.', 'book-a-room' ), $name );
		}
	}
	
	protected static function checkFormInstance( &$externals, $roomContList, $branchList, $amenityList )
	{
		$final = array();
		$errorBG = array();
		
		# Follow Form Flow
		#
		##############################################
		
		# check event date
		###############################################
		if( true == ( $error = self::checkDate( $externals['eventStart'], 'event date' ) ) ) {
			$final[] = $error;
			$errorBG['eventStart'] = true;
			unset( $error );
		}		
	
		# time settings
		########################################
		# not all day 
		$goodStart = true;
		$goodEnd = true;
		
		if( !(!empty( $externals['allDay'] ) && $externals['allDay'] == 'true') ) {			
			# check start and end times
			if( true == ( $error = self::checkTime( $externals['startTime'], 'start time' ) ) ) {
				$final[] = $error;
				$errorBG['startTime'] = true;
				$goodStart = false;
				unset( $error );
			}			
			if( true == ( $error = self::checkTime( $externals['endTime'], 'end time' ) ) ) {
				$final[] = $error;
				$errorBG['endTime'] = true;
				$goodEnd = false;
				unset( $error );
			}
			if( ( $goodStart and $goodEnd ) and ( ( strtotime( $externals['startTime'] ) >= strtotime( $externals['endTime'] ) ) ) ) {
				$final[] = 'Your end time must come after your start time.';
				$errorBG['startTime'] = $errorBG['endTime'] = true;
			}
		}		
		# errors?
		if( count( $final ) == 0 ) {
			return false;
		} else {
			return array( 'errorMSG' => implode( '<br />', $final ), 'errorBG' => $errorBG );
		}
	}	

	protected static function checkFormEvent( &$externals, $roomContList, $branchList, $amenityList )
	{
		$final = array();
		$errorBG = array();
		
		# event title
		if( empty( $externals['eventTitle'] ) ) {
			$final[] = __( 'You must enter an event title.', 'book-a-room' );
			$errorBG['eventTitle'] = true;
		} elseif( !mb_check_encoding( $externals['eventTitle'], 'ASCII') ) {
			$final[] = __( 'Your event title contains invalid characters. Make sure, if you are copying from Word, you clean up your quotes, single quotes and apostrophes.', 'book-a-room' );
			$errorBG['eventTitle'] = true;
		}
		
		# event desc
		if( empty( $externals['eventDesc'] ) ) {
			$final[] = __( 'You must enter an event description.', 'book-a-room' );
			$errorBG['eventDesc'] = true;
		} elseif( !mb_check_encoding( $externals['eventDesc'], 'ASCII') ) {
			$final[] = __( 'Your event description contains invalid characters. Make sure, if you are copying from Word, you clean up your quotes, single quotes and apostrophes.', 'book-a-room' );
			$errorBG['eventDesc'] = true;		
		}
		
		# registration
		if( empty( $externals['registration'] ) or !in_array( $externals['registration'], array( 'yes', 'no', 'staff' ) ) ) {
			$final[] = __( 'You must choose if this event requires registration.', 'book-a-room' );
			$errorBG['registration'] = true;
		}
		
		# registration options
		if( $externals['registration'] !== 'no' ) {
			# max reg number?
			if( empty( $externals['maxReg'] ) ) {
				$final[] = __( 'You must enter a number for maximum registration.', 'book-a-room' ); 
				$errorBG['maxReg'] = true;	
			} elseif( self::NaN( $externals['maxReg'] ) ) {
				$final[] = __( 'You must enter a valid number for maximum registration.', 'book-a-room' );
				$errorBG['maxReg'] = true;	
			}
			
			# waiting list
			if( !empty( $externals['maxReg'] ) and self::NaN( $externals['maxReg'] ) ) {
				$final[] = __( 'You must enter a valid number for your waiting list.', 'book-a-room' );
				$errorBG['waitingList'] = true;	
			}
						
			# reg date
			if( empty( $externals['regDate'] ) ) {
				$final[] = __( 'You must enter a registration begin date.', 'book-a-room' );
				$errorBG['regDate'] = true;	
			} elseif( true == ( $error = self::checkDate( $externals['regDate'], 'registration date' ) ) ) {
				$final[] = $error;
				$errorBG['regDate'] = true;
				unset( $error );
			}
		}
		
		# url for contact website
		if( !empty( $externals['website'] ) and !filter_var( $externals['website'], FILTER_VALIDATE_URL ) ) {
			$final[] = __( 'You must enter a valid URL.', 'book-a-room' );
			$errorBG['website'] = true;
		}

		# categories
		if( count( $externals['category'] ) < 1 ) {
			$final[] = __( 'You must choose at least one category.', 'book-a-room' );
			$errorBG['category'] = true;
		}
		
		# age groups
		if( count( $externals['ageGroup'] ) < 1 ) {
			$final[] = __( 'You must choose at least one age group.', 'book-a-room' );
			$errorBG['ageGroup'] = true;
		}
		
		# your name
		if( empty( $externals['yourName'] ) ) {
			$final[] = __( 'You must enter your name.', 'book-a-room' );
			$errorBG['yourName'] = true;
		}
		
		if( !empty( $externals['publicPhone'] ) ) {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['publicPhone'] );
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
			}
			
			if( !is_numeric( $cleanPhone ) || strlen( $cleanPhone ) !== 10 ) {
				$final[] = __( 'You must enter a valid contact phone number.', 'book-a-room' );
				$errorBG['publicPhone'] = true;
			}
		}
		
		# errors?
		if( count( $final ) == 0 ) {
			return false;
		} else {
			return array( 'errorMSG' => implode( '<br />', $final ), 'errorBG' => $errorBG );
		}
	}
	
	protected static
	function checkForm( &$externals, $roomContList, $branchList, $amenityList )
	{
		$final = array();
		$errorBG = array();
		
		if( empty( $externals['roomID'] ) && empty( $externals['branchID'] ) ) {
			$final[] = __( 'You must choose a location.', 'book-a-room' );
			$errorBG['location'] = true;
		}
		
		# Follow Form Flow
		#
		##############################################
		
		# check event date
		###############################################
		if( true == ( $error = self::checkDate( $externals['eventStart'], 'event date' ) ) ) {
			$final[] = $error;
			$errorBG['eventStart'] = true;
			unset( $error );
		}		
		# check recurrence
		self::checkRecurrence( $externals, $final, $errorBG );		
		
		# time settings
		########################################
		# not all day 
		$goodStart = true;
		$goodEnd = true;		
		if( !(!empty( $externals['allDay'] ) && $externals['allDay'] == 'true') ) {			
			# check start and end times

			if( true == ( $error = self::checkTime( $externals['startTime'], 'start time' ) ) ) {
				$final[] = $error;
				$errorBG['startTime'] = true;
				$goodStart = false;
				unset( $error );
			}
			
			if( true == ( $error = self::checkTime( $externals['endTime'], 'end time' ) ) ) {
				$final[] = $error;
				$errorBG['endTime'] = true;
				$goodEnd = false;
				unset( $error );
			}
		

			if( ( $goodStart and $goodEnd ) and ( ( strtotime( $externals['startTime'] ) >= strtotime( $externals['endTime'] ) ) ) ) {
				$final[] = __( 'Your end time must come after your start time.', 'book-a-room' );
				$errorBG['startTime'] = $errorBG['endTime'] = true;
			}
		}				
		# event title
		if( empty( $externals['eventTitle'] ) ) {
			$final[] = __( 'You must enter an event title.', 'book-a-room' ); 
			$errorBG['eventTitle'] = true;
		} elseif( !mb_check_encoding( $externals['eventTitle'], 'ASCII') ) {
				$final[] = __( 'Your event title contains invalid characters. Make sure, if you are copying from Word, you clean up your quotes, single quotes and apostrophes.', 'book-a-room' );
				$errorBG['eventTitle'] = true;
		}
		
		# event desc
		if( empty( $externals['eventDesc'] ) ) {
			$final[] = __( 'You must enter an event description.', 'book-a-room' );
			$errorBG['eventDesc'] = true;
		} elseif( !mb_check_encoding( $externals['eventDesc'], 'ASCII') ) {
				$final[] = __( 'Your event description contains invalid characters. Make sure, if you are copying from Word, you clean up your quotes, single quotes and apostrophes.', 'book-a-room' );
				$errorBG['eventDesc'] = true;
		}
		
		# registration
		if( empty( $externals['registration'] ) or !in_array( $externals['registration'], array( 'yes', 'no', 'staff' ) ) ) {
			$final[] = 'You must choose if this event requires registration.';
			$errorBG['registration'] = true;
		}
		
		# registration options
		if( $externals['registration'] !== 'no' ) {
			# max reg number?
			if( empty( $externals['maxReg'] ) ) {
				$final[] = __( 'You must enter a number for maximum registration.', 'book-a-room' );
				$errorBG['maxReg'] = true;	
			} elseif( self::NaN( $externals['maxReg'] ) ) {
				$final[] = __( 'You must enter a valid number for maximum registration.', 'book-a-room' );
				$errorBG['maxReg'] = true;	
					# Registration lower than total number
			} elseif( substr( $externals['roomID'], 0, 6 ) !== 'noloc-' and ( $externals['maxReg'] > $roomContList['id'][$externals['roomID']]['occupancy'] ) ) {
				$final[] = sprintf( __( 'You entered a maximum registration (%s) that is larger than the maximum occupancy of the room (%s).', 'book-a-room'), $externals['maxReg'], $roomContList['id'][$externals['roomID']]['occupancy'] );
				$errorBG['maxReg'] = true;
			}
			
			# waiting list
			if( !empty( $externals['maxReg'] ) and self::NaN( $externals['maxReg'] ) ) {
				$final[] = __( 'You must enter a valid number for your waiting list.', 'book-a-room' );
				$errorBG['waitingList'] = true;	
			}
						
			# reg date
			if( empty( $externals['regDate'] ) ) {
				$final[] = __( 'You must enter a registration begin date.', 'book-a-room' ); 
				$errorBG['regDate'] = true;	
			} elseif( true == ( $error = self::checkDate( $externals['regDate'], 'registration date' ) ) ) {
				$final[] = $error;
				$errorBG['regDate'] = true;
				unset( $error );
			}
		}		
		# url for contact website
		if( !empty( $externals['website'] ) and !filter_var( $externals['website'], FILTER_VALIDATE_URL ) ) {
			$final[] = __( 'You must enter a valid URL.', 'book-a-room' ); 
			$errorBG['website'] = true;
		}

		# categories
		if( count( $externals['category'] ) < 1 ) {
			$final[] = __( 'You must choose at least one category.', 'book-a-room' ); 
			$errorBG['category'] = true;
		}
		
		# age groups
		if( count( $externals['ageGroup'] ) < 1 ) {
			$final[] = __( 'You must choose at least one age group.', 'book-a-room' );
			$errorBG['ageGroup'] = true;
		}
		
		# your name
		if( empty( $externals['yourName'] ) ) {
			$final[] = __( 'You must enter your name.', 'book-a-room' );
			$errorBG['yourName'] = true;
		} 
		
		if( !empty( $externals['publicPhone'] ) ) {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['publicPhone'] );
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
			}			
			if( !is_numeric( $cleanPhone ) || strlen( $cleanPhone ) !== 10 ) {
				$final[] = __( 'You must enter a valid contact phone number.', 'book-a-room' ); 
				$errorBG['publicPhone'] = true;
			}
		}				
		# errors?
		if( count( $final ) == 0 ) {
			return false;
		} else {
			return array( 'errorMSG' => implode( '<br />', $final ), 'errorBG' => $errorBG );
		}
	}
	
	protected static
	function checkHash( $externals )
	{
		# is hash or time empty	
		if( empty( $externals['time'] ) or empty( $externals['hash'] ) or empty( $externals['eventID'] ) ) {
			return false;
		}		
		if( md5( $externals['time'].$externals['eventID'] ) !== $externals['hash'] ) {
			return false;
		}		
		return true;
	}
	
	protected static
	function checkID( $eventID )
	{
		global $wpdb;		
		
		$sql = "SELECT `ti`.`ti_id`, `ti`.`ti_type`, `ti`.`ti_extID`, `ti`.`ti_created`, `ti`.`ti_extraInfo`, 
			`ti`.`ti_startTime`, `ti`.`ti_endTime`, `ti`.`ti_roomID`, 
			`ti`.`ti_noLocation_branch`,`ev`.`res_id`, `ev`.`res_created`, 
			`ev`.`ev_desc`, `ev`.`ev_maxReg`, `ev`.`ev_amenity`, `ev`.`ev_waitingList`, 
			`ev`.`ev_presenter`, `ev`.`ev_privateNotes`, `ev`.`ev_publicEmail`, `ev`.`ev_publicName`, 
			`ev`.`ev_publicPhone`, `ev`.`ev_noPublish`, `ev`.`ev_regStartDate`, `ev`.`ev_regType`, 
			`ev`.`ev_submitter`, `ev`.`ev_title`, `ev`.`ev_website`, `ev`.`ev_webText`, `ti`.`ti_attendance`, 
			`ti`.`ti_attNotes`, 
			COUNT( DISTINCT `tiCheck`.`ti_id` ) as `tiCount`, 
			GROUP_CONCAT( DISTINCT `ages`.`ea_ageID` SEPARATOR  ',' ) as 'ageGroup', 
			GROUP_CONCAT( DISTINCT `cats`.`ec_catID` SEPARATOR  ',' ) as 'category' 
			FROM `{$wpdb->prefix}bookaroom_times` as `ti` 
			LEFT JOIN `{$wpdb->prefix}bookaroom_reservations` as `ev` ON `ev`.`res_id` = `ti`.`ti_extID` 
			LEFT JOIN `{$wpdb->prefix}bookaroom_times` AS `tiCheck` ON `tiCheck`.`ti_extID` = `ev`.`res_id` 
			LEFT JOIN `{$wpdb->prefix}bookaroom_eventAges` AS `ages` ON  `ages`.`ea_eventID` = `ev`.`res_id`
			LEFT JOIN `{$wpdb->prefix}bookaroom_eventCats` AS `cats` ON  `cats`.`ec_eventID` = `ev`.`res_id`
			WHERE `ti`.`ti_id` = '{$eventID}' 
			GROUP BY `ti`.`ti_id`";
		
		$cooked = ( $wpdb->get_row( $sql, ARRAY_A ) );
		
		if( $cooked == NULL ) {
			return false;
		}
		
		$cooked['ageGroup'] = explode( ',', $cooked['ageGroup'] );
		$cooked['category'] = explode( ',', $cooked['category'] );
		
		switch( $cooked['ev_regType'] ) {
			case 'yes':
				$cooked['registration'] = 'true';
				break;
			case 'no':
				$cooked['registration'] = 'false';
				break;				
		}		
		return $cooked;
	}
	
	protected static
	function checkRecurrence_addDates( &$externals, &$final, &$errorBG )
	{
		$errorDate = false;		
		# count dates to see if empty
		if( !is_array( $externals['addDateVals'] ) ) {
			$externals['addDateVals'] = array();
		}		
		$externals['addDateVals'] = array_filter( array_unique( $externals['addDateVals'] ) );
		if( false == count( $externals['addDateVals'] ) ) {
			$final[] = __( 'You must add at least one <em>valid date</em> to the Add Date recurrence entry.', 'book-a-room' );
			$errorBG['addDates'] = true;
		}		
		$count = 0;
		foreach( $externals['addDateVals'] as $key => $val ) {
			if( true == self::checkDate( $val, NULL ) ) {
				$errorDate = true;
				$errorBG['addDateVals'][$count] = true;
			}
			$count++;
		}		
		if( $errorDate ) {
			$final[] = __( 'At least one of your dates in the Add Dates option is invalid. Please check your dates.', 'book-a-room' );
		}		
	}
	
	protected static function checkRecurrence_daily( &$externals, &$final, &$errorBG )
	{
		# first, check that there is an option (valid) checked.
		$validOptions = array( 'everyNDays', 'weekends', 'weekdays' );		
		if( empty( $externals['dailyType'] ) or !in_array( $externals['dailyType'], $validOptions ) ) {
			$final[] = __( 'You must choose a Daily Recurrence option.', 'book-a-room' );
			$errorBG['dailyType_everyNDays'] = $errorBG['dailyType_weekends'] = $errorBG['dailyType_weekdays'] = true;
		}		
		# if Every N Days is selected, make sure the dailyEveryNDaysVal isn't empty or non numeric
		if( !empty( $externals['dailyType'] ) and $externals['dailyType'] == 'everyNDays' ) {
			if( empty( $externals['dailyEveryNDaysVal'] ) or self::NaN( $externals['dailyEveryNDaysVal'] ) ) {
				$final[] = __( 'You must enter a valid number for the <em>every n days</em> setting in Daily Recurrence.', 'book-a-room' );
				$errorBG['dailyType_everyNDays'] = true;
			}
		}		
		# check that there is a valid option for end choice checked
		# first, check that there is an option (valid) checked.
		$validOptions = array( 'Occurrences', 'endBy' );		
		if( empty( $externals['dailyEndType'] ) or !in_array( $externals['dailyEndType'], $validOptions ) ) {
			$final[] = __( 'You must choose a Daily Recurrence <em>end choice</em> option.', 'book-a-room' );
			$errorBG['dailyEndType_Occurrences'] = $errorBG['dailyEndType_endBy'] = true;
		} else {
			if( $externals['dailyEndType'] == 'Occurrences' && ( empty( $externals['daily_Occurrence'] ) || self::NaN( $externals['daily_Occurrence'] ) ) ) {
				$final[] = __( 'You must enter a valid number in the Daily Recurrence <em>Occurrences</em> option.', 'book-a-room' );
				$errorBG['dailyEndType_Occurrences'] = true;
			}
			
			if( $externals['dailyEndType'] == 'endBy' ) {
				if( empty( $externals['daily_endBy'] ) ) {
					$final[] = __( 'You must enter a date for the <em>end by</em> setting .', 'book-a-room' );
					$errorBG['dailyEndType_endBy'] = true;
				} elseif( !is_null( $errorTemp = self::checkDate( $externals['daily_endBy'], 'Daily Recurrence <em>end by</em>' ) ) ) {
					$final[] = $errorTemp;
					$errorBG['dailyEndType_endBy'] = true;
				} else {
					if( is_null( $errorTemp = self::checkDate( $externals['eventStart'], NULL ) ) ) {
						$eventStart = strtotime( $externals['eventStart'] );
						$reocStart = strtotime( $externals['daily_endBy'] );
						if( $reocStart <= $eventStart ) {
							$final[] = __( 'Your Daily Recurrence <em>end by</em> date must come after your <em>event date</em>.', 'book-a-room' );
							$errorBG['dailyEndType_endBy'] = true;
							$errorBG['eventStart'] = true;
						}
					}
				}
			}
		}		
	}
	
	protected static
	function checkRecurrence_weekly( &$externals, &$final, &$errorBG )
	{
		# check for every n weeks value
		if( empty( $externals['everyNWeeks'] ) or self::NaN( $externals['everyNWeeks'] ) ) {
			$final[] = __( 'You must enter a valid number in the <em>Every N Weeks</em> option in Weekly Recurrence.', 'book-a-room' );
			$errorBG['everyNWeeks'] = true;
		}
		
		# check that days are selected
		if( count( $externals['weeklyDay'] ) == 0 ) {
			$final[] = __( 'You must choose some days in Weekly Recurrence.', 'book-a-room' );
			$errorBG['weeklyDay'] = true;
		}
		
		# check that there is a valid option for end choice checked
		# first, check that there is an option (valid) checked.
		$validOptions = array( 'Occurrences', 'endBy' );
		
		if( empty( $externals['weeklyEndType'] ) or !in_array( $externals['weeklyEndType'], $validOptions ) ) {
			$final[] = __( 'You must choose a Weekly Recurrence <em>end choice</em> option.', 'book-a-room' ); 
			$errorBG['weeklyEndType_Occurrences'] = $errorBG['weeklyEndType_endBy'] = true;
		} else {
			if( $externals['weeklyEndType'] == 'Occurrences' && ( empty( $externals['weekly_Occurrence'] ) || self::NaN( $externals['weekly_Occurrence'] ) ) ) {
				$final[] = __( 'You must enter a valid number in the Weekly Recurrence <em>Occurrences</em> option.', 'book-a-room' );
				$errorBG['weeklyEndType_Occurrences'] = true;
			}
			
			if( $externals['weeklyEndType'] == 'endBy' ) {
				if( empty( $externals['weekly_endBy'] ) ) {
					$final[] = __( 'You must enter a date for the <em>end by</em> setting .', 'book-a-room' );
					$errorBG['weeklyEndType_endBy'] = true;
				} elseif( !is_null( $errorTemp = self::checkDate( $externals['weekly_endBy'], 'Weekly Recurrence <em>end by</em>' ) ) ) {
					$final[] = $errorTemp;
					$errorBG['weeklyEndType_endBy'] = true;
				} else {
					if( is_null( $errorTemp = self::checkDate( $externals['eventStart'], NULL ) ) ) {
						$eventStart = strtotime( $externals['eventStart'] );
						$reocStart = strtotime( $externals['weekly_endBy'] );
						if( $reocStart <= $eventStart ) {
							$final[] = __( 'Your Weekly Recurrence <em>end by</em> date must come after your <em>event date</em>.', 'book-a-room' );
							$errorBG['weeklyEndType_endBy'] = true;
							$errorBG['eventStart'] = true;
						}
					}
				}
			}
		}
	}
	
	protected static
	function checkRecurrence( &$externals, &$final, &$errorBG )
	{
		# recurrence options
		# recurrence drop down
		$reocArray = array( 'single', 'daily', 'weekly', 'addDates' );
		
		# check that it's in the list of good options
		if( empty( $externals['recurrence'] ) or !in_array( $externals['recurrence'], $reocArray ) ) {
			$final[] = __( 'You must choose a valid recurrence option.', 'book-a-room' );
			$errorBG['recurrence'] = true;
		}
		
		switch( $externals['recurrence'] ) {
			case 'daily':
				self::checkRecurrence_daily( $externals, $final, $errorBG );
				break;
			
			case 'weekly':
				self::checkRecurrence_weekly( $externals, $final, $errorBG );
				break;

			case 'addDates':
				self::checkRecurrence_addDates( $externals, $final, $errorBG );
				break;
		}
		return true;			
	}
	
	protected static
	function checkReg( $externals )
	{
		$error = array();		
		# check for empty values
		if( empty( $externals['regName'] ) ) {
			$error[] = __( 'You must enter the full name of the person who is registering.', 'book-a-room' );
		}
		
		if( empty( $externals['regPhone'] ) and empty( $externals['regEmail'] ) ) {
			$error[] = __( 'You must enter contact information; either a phone number or email address where you can be reached.', 'book-a-room' ); 
		} else {
			if( !empty( $externals['regPhone'] ) ) {
				$cleanPhone = preg_replace( "/[^0-9]/", '', $externals['regPhone'] );
				if ( strlen( $cleanPhone ) == 11 ) {
					$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
				}
				if( !is_numeric( $cleanPhone ) || strlen( $cleanPhone ) !== 10 ) {
					$error[] = __( 'You must enter a valid phone number.', 'book-a-room' );
				}
			}	
			if( !empty( $externals['regEmail'] ) and !filter_var( $externals['regEmail'], FILTER_VALIDATE_EMAIL ) ) {
				$error[] = __( 'Please enter a valid email address.', 'book-a-room' );
			}
		}		
		if( count( $error )!== 0 ) {
			return implode( "<br />", $error );
		} else {
			return false;
		}		
	}
	
	protected static 
	function checkTime( &$timeVal, $name )
	{
		if( empty( $timeVal ) ) {
			return sprintf( __( 'You must enter a time in the %s field.', 'book-a-room' ), $name );
		}
		
		if( !preg_match( '/^(0?\d|1[0-2]):[0-5]\d\s(am|pm)$/i', $timeVal ) ) {
            return sprintf( __( 'You must enter a valid time (hh:mm am/pm) in the %s field.', 'book-a-room' ), $name );
		}
		
		$timeVal = ltrim( $timeVal, '0' );
				
		# check against increment
		$baseIncrement = get_option( 'bookaroom_baseIncrement' );
		
		$timer = strtotime( $timeVal );
		
		# hours
		$minutes = date( 'H', $timer ) * 60;
		# minutes
		$minutes += date( 'i', $timer );

		# round to increment
		$cleanTime = $baseIncrement * ( round( $minutes/$baseIncrement ) );
		$hours = floor($cleanTime/60);
    	$cleanMinutes = str_pad( $cleanTime%60, 2, '0', STR_PAD_LEFT );
		
		if( $hours == 0 ) {
			$hours = '12'; $ampm = 'am';
		} elseif( $hours == 12 ) {
			$ampm = 'pm';
		} elseif( $hours > 12 ) {
			$hours -= 12; $ampm = 'pm';
		} else {
			$ampm = 'am';
		}
		
		$newTimeVal = "{$hours}:{$cleanMinutes} {$ampm}";
		
		if( $newTimeVal !== $timeVal ) {
			return sprintf( __( 'Times are scheduled in %s minute intervals. Your %s time has been changed to reflect that. Please double check yout times and submit the form again if they are okay.', 'book-a-room' ), $baseIncrement, $name );
		}		
		return false;
	}
	
	protected static
	function deleteMulti( $eventInfo, $externals, $finalDelete, $instanceKeys )
	{
		global $wpdb;
		# first, deltete instances
		$deleteKeys = implode( ',', $finalDelete );
		$sql = "INSERT INTO `{$wpdb->prefix}bookaroom_times_deleted` ( `ti_id`, `ti_type`, `ti_extID`, `ti_created`, `ti_startTime`, `ti_endTime`, `ti_roomID`, `ti_noLocation_branch`, `ti_extraInfo` )
				SELECT `ti_id`, `ti_type`, `ti_extID`, `ti_created`, `ti_startTime`, `ti_endTime`, `ti_roomID`, `ti_noLocation_branch`, `ti_extraInfo` 
				FROM `{$wpdb->prefix}bookaroom_times` WHERE `ti_id` IN ({$deleteKeys})";				
		$wpdb->query( $sql );
		$sql = "DELETE FROM `{$wpdb->prefix}bookaroom_times` WHERE `ti_id` IN ({$deleteKeys})";		
		$wpdb->query( $sql );		
		if( count( $instanceKeys ) - count( $finalDelete ) == 0 ) {
			$sql = "INSERT INTO `{$wpdb->prefix}bookaroom_reservations_deleted` ( `res_id`, `res_created`, `ev_desc`, `ev_maxReg`, `ev_amenity`, `ev_waitingList`, `ev_presenter`, `ev_privateNotes`, `ev_publicEmail`, `ev_publicName`, `ev_publicPhone`, `ev_noPublish`, `ev_regStartDate`, `ev_regType`, `ev_submitter`, `ev_title`, `ev_website`, `ev_webText`, `me_amenity`, `me_contactAddress1`, `me_contactAddress2`, `me_contactCity`, `me_contactEmail`, `me_contactName`, `me_contactPhonePrimary`, `me_contactPhoneSecondary`, `me_contactState`, `me_contactWebsite`, `me_contactZip`, `me_desc`, `me_eventName`, `me_nonProfit`, `me_numAttend`, `me_notes`, `me_status` )
				SELECT `res_id`, `res_created`, `ev_desc`, `ev_maxReg`, `ev_amenity`, `ev_waitingList`, `ev_presenter`, `ev_privateNotes`, `ev_publicEmail`, `ev_publicName`, `ev_publicPhone`, `ev_noPublish`, `ev_regStartDate`, `ev_regType`, `ev_submitter`, `ev_title`, `ev_website`, `ev_webText`, `me_amenity`, `me_contactAddress1`, `me_contactAddress2`, `me_contactCity`, `me_contactEmail`, `me_contactName`, `me_contactPhonePrimary`, `me_contactPhoneSecondary`, `me_contactState`, `me_contactWebsite`, `me_contactZip`, `me_desc`, `me_eventName`, `me_nonProfit`, `me_numAttend`, `me_notes`, `me_status` 
				FROM `{$wpdb->prefix}bookaroom_reservations` WHERE `res_id` = '{$eventInfo['ti_extID']}'";			
			$wpdb->query( $sql );			
			$sql = "DELETE FROM `{$wpdb->prefix}bookaroom_reservations` WHERE `res_id` = '{$eventInfo['ti_extID']}'";	
			$wpdb->query( $sql );			
		}		
	}
	
	protected static
	function showDeleteRegistrations( $eventInfo, $regID, $branchList, $roomContList )
	{
		global $wpdb;
		
		$startTime = date( 'g:i a', strtotime( $eventInfo[ 'ti_startTime' ] ) );
		$endTime = date( 'g:i a', strtotime( $eventInfo[ 'ti_endTime' ] ) );
		
		$time = ( $startTime == '12:00 am' and $endTime == '11:59 pm' ) ? __( 'All Day', 'book-a-room' ) : date( 'g:i a', strtotime( $eventInfo[ 'ti_startTime' ] ) ) . ' -' . date( 'g:i a', strtotime( $eventInfo[ 'ti_endTime' ] ) );
		
		if ( !empty( $eventInfo[ 'ti_noLocation_branch' ] ) ) {
			$branch		= $branchList[ $eventInfo[ 'ti_noLocation_branch' ] ][ 'branchDesc' ];
			$room		= __( 'No location required', 'book-a-room' );
		} else {
			$room		= $roomContList[ 'id' ][ $eventInfo[ 'ti_roomID' ] ][ 'desc' ]; 
			$branch		= $branchList[ $roomContList[ 'id' ][ $eventInfo[ 'ti_roomID' ] ][ 'branchID' ] ][ 'branchDesc' ];
		}
		$registrations = self::getRegistrations( $eventInfo[ 'ti_id' ] );
		$regPhone = ( !empty( $registrations[ $regID ][ 'reg_phone' ] ) ) ? $registrations[ $regID ][ 'reg_phone' ] : null;
		
		$hashtime = time();
		$hash = md5( $hashtime . $regID . $eventInfo[ 'ti_id' ] );
		
		require( BOOKAROOM_PATH . 'templates/events/deleteRegistration.php' );
	}

	protected static function showEditRegistrations( $eventInfo, $externals, $branchList, $roomContList, $errorMSG = null )
	{
		global $wpdb;

		# branch and room
		if( !empty( $eventInfo['ti_noLocation_branch'] ) ) {
			$branch		= $branchList[$eventInfo['ti_noLocation_branch']]['branchDesc'];
			$room		= __( 'No location required', 'book-a-room' );
		} else {
			$room		= $roomContList['id'][$eventInfo['ti_roomID']]['desc'];
			$branch		= $branchList[$roomContList['id'][$eventInfo['ti_roomID']]['branchID']]['branchDesc'];
		}		
		# times
		$startTime = date( 'g:i a', strtotime( $eventInfo[ 'ti_startTime' ] ) );
		$endTime = date( 'g:i a', strtotime( $eventInfo[ 'ti_endTime' ] ) );

		$time = ( $startTime == '12:00 am'
			and $endTime == '11:59 pm' ) ? __( 'All Day', 'book-a-room' ) : date( 'g:i a', strtotime( $eventInfo[ 'ti_startTime' ] ) ) . ' -' . date( 'g:i a', strtotime( $eventInfo[ 'ti_endTime' ] ) );
		
		$registrations = self::getRegistrations( $eventInfo['ti_id'] );
		
		$hashTime	= time();
		$hash		= md5( $hashTime.$externals['regID'].$eventInfo['ti_id'] );
		
		require( BOOKAROOM_PATH . 'templates/events/editRegistration.php' );
	}

	protected static function deleteReg( $externals, $eventInfo )
	{
		global $wpdb;
		$registrations = self::getRegistrations( $externals['eventID'] );		
		# make sure reg exists
		if( !array_key_exists( $externals['regID'], $registrations ) ) {
			$final['status'] = false;
			$final['errorMSG'] = __( 'There was a problem deleting that registration. The ID doesn\'t exist. Please try again.', 'book-a-room' );
			return $final;			
		}		
		$arrKeys = array_keys( $registrations );		
		# if under max reg, find replacement
		$alertID = NULL;
		if( array_search( $externals['regID'], $arrKeys ) + 1 <= $eventInfo['ev_maxReg'] ) {
			if( !empty( $arrKeys[$eventInfo['ev_maxReg']] ) ) {
				$alertID = ( $arrKeys[$eventInfo['ev_maxReg']] );
			}
		}						 
		$sql = "DELETE FROM `{$wpdb->prefix}bookaroom_registrations` WHERE `reg_id` = '{$externals['regID']}'";		
		$wpdb->query( $sql );
		
		$final['status'] = true;
		$final['alertID'] = $alertID;		
		
		return $final;		
	}
	
	protected static
	function deleteSingle( $eventInfo, $instance )
	{		
		global $wpdb;
		$sql = "INSERT INTO `{$wpdb->prefix}bookaroom_times_deleted` ( `ti_id`, `ti_type`, `ti_extID`, `ti_created`, `ti_startTime`, `ti_endTime`, `ti_roomID`, `ti_noLocation_branch` )
				SELECT `ti_id`, `ti_type`, `ti_extID`, `ti_created`, `ti_startTime`, `ti_endTime`, `ti_roomID`, `ti_noLocation_branch` 
				FROM `{$wpdb->prefix}bookaroom_times` WHERE `ti_id` = '{$eventInfo['ti_id']}'";
		$wpdb->query( $sql );
		$sql = "DELETE FROM `{$wpdb->prefix}bookaroom_times` WHERE `ti_id` = '{$eventInfo['ti_id']}'";
		$wpdb->query( $sql );

		if ( $instance == false or $eventInfo[ 'tiCount' ] == 1 ) {
			$sql = "INSERT INTO `{$wpdb->prefix}bookaroom_reservations_deleted` ( `res_id`, `res_created`, `ev_desc`, `ev_maxReg`, `ev_amenity`, `ev_waitingList`, `ev_presenter`, `ev_privateNotes`, `ev_publicEmail`, `ev_publicName`, `ev_publicPhone`, `ev_noPublish`, `ev_regStartDate`, `ev_regType`, `ev_submitter`, `ev_title`, `ev_website`, `ev_webText`, `me_amenity`, `me_contactAddress1`, `me_contactAddress2`, `me_contactCity`, `me_contactEmail`, `me_contactName`, `me_contactPhonePrimary`, `me_contactPhoneSecondary`, `me_contactState`, `me_contactWebsite`, `me_contactZip`, `me_desc`, `me_eventName`, `me_nonProfit`, `me_numAttend`, `me_notes`, `me_status` )
				SELECT `res_id`, `res_created`, `ev_desc`, `ev_maxReg`, `ev_amenity`, `ev_waitingList`, `ev_presenter`, `ev_privateNotes`, `ev_publicEmail`, `ev_publicName`, `ev_publicPhone`, `ev_noPublish`, `ev_regStartDate`, `ev_regType`, `ev_submitter`, `ev_title`, `ev_website`, `ev_webText`, `me_amenity`, `me_contactAddress1`, `me_contactAddress2`, `me_contactCity`, `me_contactEmail`, `me_contactName`, `me_contactPhonePrimary`, `me_contactPhoneSecondary`, `me_contactState`, `me_contactWebsite`, `me_contactZip`, `me_desc`, `me_eventName`, `me_nonProfit`, `me_numAttend`, `me_notes`, `me_status` 
				FROM `{$wpdb->prefix}bookaroom_reservations` WHERE `res_id` = '{$eventInfo['ti_extID']}'";
			$wpdb->query( $sql );
			
			$sql = "DELETE FROM `{$wpdb->prefix}bookaroom_reservations` WHERE `res_id` = '{$eventInfo['ti_extID']}'";
			$wpdb->query( $sql );
		}
		return TRUE;				
	}
	
	protected static
	function deleteSuccess()
	{
		require( BOOKAROOM_PATH . 'templates/events/delete_success.php' );
	}
	public static function editRegistration( $externals )
	{
		global $wpdb;

		$regID		= $externals['regID'];
		$regName	= $externals['regName'];
	    $regPhone	= $externals['regPhone'];
    	$regNotes	= $externals['regNotes'];
    	$regEmail	= $externals['regEmail'];
		
		if( empty( $regPhone ) ) {
			$cleanPhone = NULL;
		} else {
			$cleanPhone = preg_replace( "/[^0-9]/", '', $regPhone );
			if ( strlen( $cleanPhone ) == 11 ) {
				$cleanPhone = preg_replace( "/^1/", '', $cleanPhone );
			}
			$cleanPhone = "(" . substr($cleanPhone, 0, 3) . ") " . substr($cleanPhone, 3, 3) . "-" . substr($cleanPhone, 6);	
		}		
		$sql = "UPDATE `{$wpdb->prefix}bookaroom_registrations` SET 
				`reg_fullName` = '{$externals['regName']}', 
				`reg_phone` = '{$cleanPhone}', 
				`reg_email` = '{$externals['regEmail']}', 
				`reg_notes` = '{$externals['regNotes']}' 
				WHERE `reg_id` = '{$externals['regID']}' LIMIT 1";
		$wpdb->query( $sql );
	}
	
	protected static
	function editAttendance( $externals )
	{
		global $wpdb;
		
		if( empty( $externals['attCount'] ) ) {
			$attCount = 'NULL';
		} else {
			$attCount = "'{$externals['attCount']}'";
		}
		
		$sql = "UPDATE `{$wpdb->prefix}bookaroom_times` SET `ti_attendance` = {$attCount}, `ti_attNotes` = '{$externals['attNotes']}'
				WHERE `ti_id` = '{$externals['eventID']}' LIMIT 1";
		$wpdb->query( $sql );
		
		return true;
	}
	
	protected static
	function fixSavedEventInfo( $eventInfo, $externals )
	{
		$goodArr = array( 'roomID' => 'ti_roomID', 
						 'res_id' => 'ti_extID',  
						'amenity' => 'ev_amenity',  
						'endTime' => 'ti_endTime',  
						'eventDesc' => 'ev_desc',  
						'eventTitle' => 'ev_title',  
						'maxReg' => 'ev_maxReg',  
						'presenter' => 'ev_presenter',  
						'privateNotes' => 'ev_privateNotes',  
						'publicName' => 'ev_publicName',  
						'publicPhone' => 'ev_publicPhone',  
						'publicEmail' => 'ev_publicEmail',  
						'doNotPublish' => 'ev_noPublish',  
						'yourName' => 'ev_submitter',  
						'recurrence' => '',  
						'waitingList' => 'ev_waitingList',  
						'website' => 'ev_website',  
						'websiteText' => 'ev_webText', 
						'registration' => 'ev_regType', 
						'ageGroup' => 'ageGroup', 
						'category' => 'category', 
						'noLocation_branch' => 'ti_noLocation_branch'
						);
		foreach( $goodArr as $key => $val ) {
			if( !empty( $eventInfo[$val] ) ) {
				$externals[$key] = $eventInfo[$val];
			}
		}		
		if( !empty( $eventInfo['ti_noLocation_branch'] ) ) {
			$externals['roomID'] =  'noloc-'.$eventInfo['ti_noLocation_branch'];
		}
		$startTime	= date( 'g:i a', strtotime( $eventInfo['ti_startTime'] ) );
		$endTime	= date( 'g:i a', strtotime( $eventInfo['ti_endTime'] ) );
		
		if( $startTime == '12:00 am' and $endTime == '11:59 pm' ) {
			$externals['allDay'] = true;
			$externals['startTime'] = false;
			$externals['endTime'] = false;
		} else {
			$externals['allDay'] = false;
			$externals['startTime'] = $startTime;
			$externals['endTime'] = $endTime;
		}
		
		
		$externals['eventStart'] =  date( 'm/d/Y', strtotime( $eventInfo['ti_startTime'] ) );
		switch( $eventInfo['ev_regType'] ) {
			case 'staff':
				$regType = 'staff';
				break;
			case 'yes':
				$regType = 'yes';
				break;
			default:
				$regType = 'no';
				break;
		}
	
		$externals['registration'] =  $regType;
		$externals['regDate'] =  date( 'm/d/Y', strtotime( $eventInfo['ev_regStartDate'] ) );
		
		if( !empty( $eventInfo['ev_amenity'] ) ) {
			$externals['amenity'] = unserialize( $eventInfo['ev_amenity'] );
		}
		
		return $externals;	
	}
	protected static
	function getDates_addDates()
	{
		if( $_SESSION['bookaroom_meetings_externalVals']['allDay'] == 'true' ) {
			$startTime = '12:00 am';
			$endTime = '11:59:59 pm';
		} else {
			$startTime = $_SESSION['bookaroom_meetings_externalVals']['startTime'];
			$endTime = $_SESSION['bookaroom_meetings_externalVals']['endTime'];
		}
		$startDate = $_SESSION['bookaroom_meetings_externalVals']['eventStart'];			
		$addDateVals = $_SESSION['bookaroom_meetings_externalVals']['addDateVals'];		
		array_unshift( $addDateVals, $startDate );		
		foreach( $addDateVals as $val ) {
			$mainStart = strtotime( $val.' '.$startTime );
			$mainEnd = strtotime( $val.' '.$endTime );			
			$final[] = array( 'start' => $mainStart, 'end' => $mainEnd );
		}		
		return $final;
	}
	
	protected static
	function getDates_daily()
	{
		# find Occurrence type		
		$startDate = $_SESSION['bookaroom_meetings_externalVals']['eventStart'];
		$offset = $_SESSION['bookaroom_meetings_externalVals']['dailyEveryNDaysVal'];
		$occurrence = $_SESSION['bookaroom_meetings_externalVals']['daily_Occurrence'];
		
		if( $_SESSION['bookaroom_meetings_externalVals']['allDay'] == 'true' ) {
			$startTime = '12:00 am';
			$endTime = '11:59:59 pm';
		} else {
			$startTime = $_SESSION['bookaroom_meetings_externalVals']['startTime'];
			$endTime = $_SESSION['bookaroom_meetings_externalVals']['endTime'];
		}		
		$mainStart = getdate( strtotime( $startDate.' '.$startTime ) );
		$mainEnd = getdate( strtotime( $startDate.' '.$endTime ) );		
		$final = array();
		$niceFinal = array();
		switch( $_SESSION['bookaroom_meetings_externalVals']['dailyType'] ) {
			case 'everyNDays':
				# get start date
				switch( $_SESSION['bookaroom_meetings_externalVals']['dailyEndType']) {				
					case 'Occurrences':
						for( $t=0; $t < $occurrence; $t++) {
							$start = mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + ( $t * $offset ), $mainStart['year'] );
							$end = mktime( $mainEnd['hours'], $mainEnd['minutes'], $mainEnd['seconds'], $mainEnd['mon'], $mainEnd['mday'] + ( $t * $offset ), $mainEnd['year'] );
							$final[] = array( 'start' => $start, 'end' => $end );
							
							$niceStart = date_i18n( 'l, m/d/y g:i a', mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + ( $t * $offset ), $mainStart['year'] ) );
							$niceEnd = date_i18n( 'l, m/d/y g:i a', mktime( $mainEnd['hours'], $mainEnd['minutes'], $mainEnd['seconds'], $mainEnd['mon'], $mainEnd['mday'] + ( $t * $offset ), $mainEnd['year'] ) );
							$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
						}
						break;
					case 'endBy':
						$curDate = strtotime( $startDate );						
						$endDate =  strtotime( $_SESSION['bookaroom_meetings_externalVals']['daily_endBy'] );					
						$check = 1;
						while( $curDate <= $endDate ) {
							$curOffset = $offset * $check;							
							$curDateNice = date_i18n( 'm/d/y', $curDate );							
							$start = strtotime( $curDateNice.' '.$startTime );
							$end = strtotime( $curDateNice.' '.$endTime );
							$final[] = array( 'start' => $start, 'end' => $end );
							
							$niceStart = date_i18n( 'l, m/d/y g:i a', strtotime( $curDateNice.' '.$startTime ) );
							$niceEnd = date_i18n( 'l, m/d/y g:i a', strtotime( $curDateNice.' '.$endTime ) );
							$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );													
							
							$curDate = mktime( 0, 0, 0, $mainStart['mon'], $mainStart['mday'] + $curOffset, $mainStart['year'] );		

							if( $check++ > 600) {
								die('error: daily endby loop');
							}
						}						
						break;
					default:
						die( 'error: wrong daily end by' );
						break;
				}
				break;
			case 'weekdays':
				switch( $_SESSION['bookaroom_meetings_externalVals']['dailyEndType']) {				
					case 'Occurrences':
						$count = 0;
						$check = 0;
						while( $count < $occurrence ) {			
							#$curDate
							$curDate =  mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
							$curDOW = date( 'w', $curDate );
		
							if( $curDOW > 0 and $curDOW < 6) {
								$start = mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
								$end = mktime( $mainEnd['hours'], $mainEnd['minutes'], $mainEnd['seconds'], $mainEnd['mon'], $mainEnd['mday'] + $check, $mainEnd['year'] );
								$final[] = array( 'start' => $start, 'end' => $end );
								
								$niceStart = date_i18n( 'l, m/d/y g:i a', mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] ) );
								$niceEnd = date_i18n( 'l, m/d/y g:i a', mktime( $mainEnd['hours'], $mainEnd['minutes'], $mainEnd['seconds'], $mainEnd['mon'], $mainEnd['mday'] + $check, $mainEnd['year'] ) );
								$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
								$count++;
							}
							
							if( $check++ > 100) {
								die( 'error: too many loops' );
							}
						}
						break;
					case 'endBy':
						$curDate = strtotime( $startDate );						
						$endDate =  strtotime( $_SESSION['bookaroom_meetings_externalVals']['daily_endBy'] );					
						$check = 1;						
						while( $curDate <= $endDate ) {
							$curDateNice = date_i18n( 'm/d/y', $curDate );							
							$curDOW = date( 'w', $curDate );							
							if( $curDOW > 0 and $curDOW < 6 ) {
								$start = strtotime( $curDateNice.' '.$startTime );
	
								$end = strtotime( $curDateNice.' '.$endTime );
								$final[] = array( 'start' => $start, 'end' => $end );
								
								$niceStart = date_i18n( 'l, m/d/y g:i a', strtotime( $curDateNice.' '.$startTime ) );
								$niceEnd = date_i18n( 'l, m/d/y g:i a', strtotime( $curDateNice.' '.$endTime ) );
								$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
							}
							
							$curDate = mktime( 0, 0, 0, $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
							if( $check++ > 100) {
								die('error: daily endby loop');
							}
						}
						break;
				}
				break;
			case 'weekends':
				switch( $_SESSION['bookaroom_meetings_externalVals']['dailyEndType']) {				
					case 'Occurrences':
						$count = 0;
						$check = 0;
						while( $count < $occurrence ) {			
							#$curDate
							$curDate =  mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
							$curDOW = date( 'w', $curDate );		
							if( $curDOW == 0 or $curDOW == 6) {
								$start = mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
								$end = mktime( $mainEnd['hours'], $mainEnd['minutes'], $mainEnd['seconds'], $mainEnd['mon'], $mainEnd['mday'] + $check, $mainEnd['year'] );
								$final[] = array( 'start' => $start, 'end' => $end );
								
								$niceStart = date_i18n( 'l, m/d/y g:i a', mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] ) );
								$niceEnd = date_i18n( 'l, m/d/y g:i a', mktime( $mainEnd['hours'], $mainEnd['minutes'], $mainEnd['seconds'], $mainEnd['mon'], $mainEnd['mday'] + $check, $mainEnd['year'] ) );
								$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
								$count++;
							}
							
							if( $check++ > 100) {
								die( 'error: too many loops' );
							}
						}
						break;
					case 'endBy':
						$curDate = strtotime( $startDate );						
						$endDate =  strtotime( $_SESSION['bookaroom_meetings_externalVals']['daily_endBy'] );					
						$check = 1;
						
						while( $curDate <= $endDate ) {
							$curDateNice = date( 'm/d/y', $curDate );							
							$curDOW = date( 'w', $curDate );							
							if( $curDOW == 0 or $curDOW == 6 ) {
								$start = strtotime( $curDateNice.' '.$startTime );	
								$end = strtotime( $curDateNice.' '.$endTime );
								$final[] = array( 'start' => $start, 'end' => $end );
								
								$niceStart = date_i18n( 'l, m/d/y g:i a', strtotime( $curDateNice.' '.$startTime ) );
								$niceEnd = date_i18n( 'l, m/d/y g:i a', strtotime( $curDateNice.' '.$endTime ) );
								$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
							}
							
							$curDate = mktime( 0, 0, 0, $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
							if( $check++ > 100) {
								die('error: daily endby loop');
							}
						}
						break;
				}
				break;
			default:
				die( 'error: wrong daily type' );
				break;
		}
		
		return $final;
	}
	
	protected static 
	function getDates_single()
	{
		$startDate = $_SESSION['bookaroom_meetings_externalVals']['eventStart'];
		if( $_SESSION['bookaroom_meetings_externalVals']['allDay'] == 'true' ) {
			$startTime = '12:00 am';
			$endTime = '11:59:59 pm';
		} else {
			$startTime = $_SESSION['bookaroom_meetings_externalVals']['startTime'];
			$endTime = $_SESSION['bookaroom_meetings_externalVals']['endTime'];
		}
		
		$mainStart = strtotime( $startDate.' '.$startTime );
		$mainEnd = strtotime( $startDate.' '.$endTime );
		
		$final[] = array( 'start' => $mainStart, 'end' => $mainEnd );
		
		return $final;
	}
	
	protected static 
	function getDates_weekly()
	{
		# find Occurrence type
		$startDate = $_SESSION['bookaroom_meetings_externalVals']['eventStart'];
		$offset = $_SESSION['bookaroom_meetings_externalVals']['everyNWeeks'];
		$occurrence = $_SESSION['bookaroom_meetings_externalVals']['weekly_Occurrence'];
		$weeklyDayArr = $_SESSION['bookaroom_meetings_externalVals']['weeklyDay'];
		
		if( $_SESSION['bookaroom_meetings_externalVals']['allDay'] == 'true' ) {
			$startTime = '12:00 am';
			$endTime = '11:59:59 pm';
		} else {
			$startTime = $_SESSION['bookaroom_meetings_externalVals']['startTime'];
			$endTime = $_SESSION['bookaroom_meetings_externalVals']['endTime'];
		}
		
		$mainStart = getdate( strtotime( $startDate.' '.$startTime ) );
		$mainEnd = getdate( strtotime( $startDate.' '.$endTime ) );
		
		$daysArr = array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
		
		$final = array();
		$niceFinal = array();

		switch( $_SESSION['bookaroom_meetings_externalVals']['weeklyEndType'] ) {
			case 'Occurrences':
				$check = 0;
				$count = 0;				
				# make decent date array				
				while( $count <= $occurrence ) {
					$curDate = mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );

					if( in_array( $daysArr[date( 'w', $curDate )], $_SESSION['bookaroom_meetings_externalVals']['weeklyDay'] ) ) {
						$start = strtotime( date( 'm/d/y', $curDate ).' '.$startTime );
						$end = strtotime( date( 'm/d/y', $curDate ).' '.$endTime );						
						$niceStart = date_i18n( 'm/d/h g:i a', $start );
						$niceEnd = date_i18n( 'm/d/h g:i a', $end );						
						$final[] = array( 'start' => $start, 'end' => $end );
						$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
						$count++;
					}
					
					if( !empty( $offset ) and date( 'w', $curDate ) == 6 ) {
						$check += 7 * ($offset-1);
					}
					
					if( $check++ > 500 ) {
						die( 'weekly Occurrence loop' );
					}
				}				
				break;
				
			case 'endBy':
				$curDate = strtotime( $startDate );						
				$endDate =  strtotime( $_SESSION['bookaroom_meetings_externalVals']['weekly_endBy'] );				
				$check = 0;				
				while( $curDate <= $endDate ) {
					$curDateNice = date_i18n( 'm/d/y', $curDate );
					$curDOW = date( 'w', $curDate );					
					$curDate = mktime( $mainStart['hours'], $mainStart['minutes'], $mainStart['seconds'], $mainStart['mon'], $mainStart['mday'] + $check, $mainStart['year'] );
					if( in_array( $daysArr[date( 'w', $curDate )], $_SESSION['bookaroom_meetings_externalVals']['weeklyDay'] ) ) {
						$start = strtotime( date( 'm/d/y', $curDate ).' '.$startTime );
						$end = strtotime( date( 'm/d/y', $curDate ).' '.$endTime );						
						$niceStart = date_i18n( 'm/d/h g:i a', $start );
						$niceEnd = date_i18n( 'm/d/h g:i a', $end );						
						$final[] = array( 'start' => $start, 'end' => $end );
						$niceFinal[] = array( 'start' => $niceStart, 'end' => $niceEnd );
					}
					
					if( !empty( $offset ) and date( 'w', $curDate ) == 6 ) {
						$check += 7 * ($offset-1);
					}
					if( $check++ > 500 ) {
						die( 'weekly Occurrence loop' );
					}
				}				
				break;
					
			default: 
				die( 'error: unknown weeklyEndType' );
				break;
		}		
		return $final;		
	}

	public static
	function getExternals()
	# Pull in POST and GET values
	{
		$final = array();
		
		# setup GET variables
		$getArr = array(	'action'					=> FILTER_SANITIZE_STRING, 
							'eventID'					=> FILTER_SANITIZE_STRING, 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'branchID'					=> FILTER_SANITIZE_STRING,
							'res_id'					=> FILTER_SANITIZE_STRING,
							
							'endDate'					=> FILTER_SANITIZE_STRING, 
							'published'					=> FILTER_SANITIZE_STRING, 
							'searchTerms'				=> FILTER_SANITIZE_STRING, 
							'startDate'					=> FILTER_SANITIZE_STRING,
							'time'						=> FILTER_SANITIZE_STRING,
							'hash'						=> FILTER_SANITIZE_STRING,
							'regID'						=> FILTER_SANITIZE_STRING,
							);

		# pull in and apply to final
		if( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final = array_merge( $final, $getTemp );
		}		
		# setup POST variables
		$postArr = array(	
							'action'					=> FILTER_SANITIZE_STRING,
							'allDay'					=> FILTER_SANITIZE_STRING, 
							'addDateVals'				=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'ageGroup'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'amenity'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'attCount'					=> FILTER_SANITIZE_STRING, 
							'attNotes'					=> FILTER_SANITIZE_STRING, 
							'branchID'					=> FILTER_SANITIZE_STRING, 
							'category'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'daily_endBy'				=> FILTER_SANITIZE_STRING, 
							'daily_Occurrence'			=> FILTER_SANITIZE_STRING,							
							'dailyEndType'				=> FILTER_SANITIZE_STRING, 
							'dailyEveryNDaysVal'		=> FILTER_SANITIZE_STRING, 
							'dailyType'					=> FILTER_SANITIZE_STRING, 
							'delete'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'endTime'					=> FILTER_SANITIZE_STRING, 
							'eventID'					=> FILTER_SANITIZE_STRING, 
							'eventDesc'					=> FILTER_SANITIZE_STRING, 
							'eventStart'				=> FILTER_SANITIZE_STRING, 
							'eventTitle'				=> FILTER_SANITIZE_STRING, 
							'everyNWeeks'				=> FILTER_SANITIZE_STRING, 
							'extraInfo'					=> FILTER_SANITIZE_STRING, 
							'instance'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'hash'						=> FILTER_SANITIZE_STRING, 
							'maxReg'					=> FILTER_SANITIZE_STRING, 
							'newVal'					=> array(	'filter'    => FILTER_SANITIZE_STRING,
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'presenter'					=> FILTER_SANITIZE_STRING, 
							'privateNotes'				=> FILTER_SANITIZE_STRING, 
							'publicName'				=> FILTER_SANITIZE_STRING, 
							'publicPhone'				=> FILTER_SANITIZE_STRING, 
							'publicEmail'				=> FILTER_SANITIZE_STRING, 
							'regDate'					=> FILTER_SANITIZE_STRING, 
							'regID'						=> FILTER_SANITIZE_STRING, 
							'registration'				=> FILTER_SANITIZE_STRING, 
							'doNotPublish'				=> FILTER_SANITIZE_STRING, 
							'yourName'					=> FILTER_SANITIZE_STRING, 
							'recurrence'				=> FILTER_SANITIZE_STRING, 
							'roomID'					=> FILTER_SANITIZE_STRING, 
							'startTime'					=> FILTER_SANITIZE_STRING, 
							'hashTime'					=> FILTER_SANITIZE_STRING, 
							'submit'					=> FILTER_SANITIZE_STRING, 
							'waitingList'				=> FILTER_SANITIZE_STRING, 
							'website'					=> FILTER_SANITIZE_STRING, 
							'websiteText'				=> FILTER_SANITIZE_STRING, 
							'weekly_endBy'				=> FILTER_SANITIZE_STRING, 
							'weekly_Occurrence'			=> FILTER_SANITIZE_STRING,
							'weeklyDay'					=> array(	'filter'    => FILTER_SANITIZE_STRING, 
																	'flags'     => FILTER_REQUIRE_ARRAY ), 
							'weeklyEndType'				=> FILTER_SANITIZE_STRING,
							
							'regName'					=> FILTER_SANITIZE_STRING,
							'regPhone'					=> FILTER_SANITIZE_STRING,
							'regNotes'					=> FILTER_SANITIZE_STRING,
							'regEmail'					=> FILTER_SANITIZE_STRING, 
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
		return $final;
	}
	
	protected static
	function getInstances( $res_id )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_times";				
		$sql = "SELECT `ti_id`, `ti_extID`, `ti_startTime`, `ti_endTime`, `ti_roomID`, `ti_noLocation_branch` 
				FROM `{$table_name}` WHERE `ti_extID` = '{$res_id}'
				ORDER BY `ti_startTime`";

		$cooked = ( $wpdb->get_results( $sql, ARRAY_A ) );		
		return $cooked;
	}
	
	protected static 
	function getRegistrations( $eventID )
	{
		global $wpdb;
		
		$sql = "SELECT `reg_id`, `reg_fullName`, `reg_phone`, `reg_email`, `reg_notes`, `reg_dateReg` 
				FROM `{$wpdb->prefix}bookaroom_registrations` 
				WHERE `reg_eventID` = '{$eventID}' 
				ORDER BY `reg_dateReg` ASC";
		
		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		$final = array();
		if( !is_array( $cooked ) ) {
			return $final;
		}
		
		foreach( $cooked as $key => $val ) {
			$final[$val['reg_id']] = $val;
		}		
		return $final;
	}
	
	protected static
	function getRoomContListByRoomID( $roomID, $roomContList, $allRoomList )
	{		
		if( substr( $roomID, 0, 6 ) == 'noloc-' ) {
			return array( 'finalRoomConts' => array(), 'roomContList' => array() );
		}
		
		global $wpdb;		
		# Error correcting for missing rooms in containers.
		$finalRoomConts = array();
				
		if( empty( $roomContList['id'][$roomID]['rooms'] ) ) {
			$roomArr = array();
		} else {
			$roomArr = $roomContList['id'][$roomID]['rooms'];
			$roomList = implode( ',', $roomArr );		

			$table_name = $wpdb->prefix . "bookaroom_roomConts_members";

			$sql = "SELECT `rcm_roomContID` as `roomID` FROM `{$table_name}` WHERE `rcm_roomID` in ({$roomList})";
		
			$cooked = ( $wpdb->get_results( $sql, ARRAY_A ) );

			foreach( $cooked as $val ) {
				$finalRoomConts[] = $val['roomID'];
			}
		}
		
		$finalRoomConts = array_unique( $finalRoomConts );
		$roomContList = implode( ',', $finalRoomConts );		
		return array( 'finalRoomConts' => $finalRoomConts, 'roomContList' => $roomContList );
	}
	
	protected static function makeClosingRoomList( $closedRooms, $allRooms, $branchList, $roomContList, $roomList )
	{
		if( $allRooms == true ) {
			return 'All branches, all rooms.';
		}
		
		# cycle each branch, find if an entire branch is closed.
		$display = array();		
		foreach( $branchList as $bKey => $bVal ) {
			$branchRooms = array();					
			if( empty( $roomContList['branch'][$bKey] ) or count( $roomContList['branch'][$bKey] ) == 0 ) {
				continue;
			}
			
			foreach( $roomContList['branch'][$bKey] as $rKey => $rVal ) {
				$branchRooms = array_merge( $branchRooms, $roomContList['id'][$rVal]['rooms'] );
			}
			$branchRooms = array_unique( $branchRooms );

			if( false == array_diff( $branchRooms, $closedRooms ) ) {
				$display[] = '<strong>'.$bVal['branchDesc'].'</strong><br />'.__( 'Branch closed', 'book-a-room' );
			} elseif( count( $roomsClosedList = array_intersect( $branchRooms, $closedRooms ) ) !== 0 ) {
				$header = '<strong>'.$bVal['branchDesc'].'</strong><br />';				
				$roomsFinal = array();
				foreach( $roomsClosedList as $rcKey ) {
					$roomsFinal[] = $roomList['id'][$rcKey]['desc'];
				}
				$dispTemp = implode( ', ', $roomsFinal );
				$display[] = $header.$dispTemp;
			}
			# TODO empty roomContList for that branch
		}
		
		$final = implode( "<br /><br />", $display );

		return $final;		
	}

	protected static
	function makeConflictDropDown( $conflictList, $branchList, $roomContList, $roomList )
	{
		$final = array();
		
		foreach( $branchList as $bKey => $branchVal ) {
			$temp = array();
			$temp['display'] = $branchVal['branchDesc'];
			$temp['class'] = 'dropHeader';
			$temp['disabled'] = true;
			$temp['roomID'] = NULL;
			$final[] = $temp;
			
			$temp = array();
			# if no rooms
			if( empty( $roomContList['branch'][$bKey] ) ) {
				$temp['display'] = 'No rooms in this branch.';
				$temp['class'] = 'nudgeRight';
				$temp['disabled'] = true;
				$temp['roomID'] = NULL;
				$final[] = $temp;
			} else {
				foreach( $roomContList['branch'][$bKey] as $rKey) {
					$temp = array();
					$temp['display'] = $roomContList['id'][$rKey]['desc'];
					$temp['class'] = 'nudgeRight';
					$temp['roomID'] = $rKey;
					if( array_intersect( $roomContList['id'][$rKey]['rooms'], $conflictList ) ) {
						$temp['disabled'] = true;
					} else {
						$temp['disabled'] = false;					
					}
					$final[] = $temp;
				}
			}
		}		
		return $final;
	}
	
	protected static
	function manageRegistrations( $eventInfo, $branchList, $roomContList )
	{
		global $wpdb;
		
		$startTime = date( 'g:i a', strtotime( $eventInfo['ti_startTime'] ) );
		$endTime = date( 'g:i a', strtotime( $eventInfo['ti_endTime'] ) );
		
		$time = ( $startTime == '12:00 am' and $endTime == '11:59 pm' ) ? 'All Day' : date('g:i a', strtotime( $eventInfo['ti_startTime'] ) ).' -'.date('g:i a', strtotime( $eventInfo['ti_endTime'] ) );
		
		# branch and room
		if( !empty( $eventInfo['ti_noLocation_branch'] ) ) {
			$branch		= $branchList[$eventInfo['ti_noLocation_branch']]['branchDesc'];
			$room		= 'No location required';
		} else {
			$room		= $roomContList['id'][$eventInfo['ti_roomID']]['desc'];
			$branch		= $branchList[$roomContList['id'][$eventInfo['ti_roomID']]['branchID']]['branchDesc'];
		}
		$registrations = self::getRegistrations( $eventInfo['ti_id'] );
		
		require( BOOKAROOM_PATH . 'templates/events/showRegistrations.php' );
	}
	
	protected static
	function NaN( $val )
	{
		
		if( !is_numeric( $val ) || $val < 1 || !filter_var($val , FILTER_VALIDATE_INT) ) {
			return true;
		} else {
			return false;
		}
	}
	
	protected static 
	function showAttendance( $eventInfo, $externals, $attCount = NULL, $attNotes = NULL, $errorMSG = NULL )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		
		# time and date
		$startTime = date( 'g:i a', strtotime( $eventInfo['ti_startTime'] ) );
		$endTime = date( 'g:i a', strtotime( $eventInfo['ti_endTime'] ) );		
		if( $startTime == '12:00 am' and $endTime == '11:59 pm' ) {
			$time = 'All Day';
		} else {
			$time = date('g:i a', strtotime( $eventInfo['ti_startTime'] ) ).' -'.date('g:i a', strtotime( $eventInfo['ti_endTime'] ) );
		}
		# branch and room
		if( !empty( $eventInfo['ti_noLocation_branch'] ) ) {
			$branch		= $branchList[$eventInfo['ti_noLocation_branch']]['branchDesc'];
			$room		= 'No location required';
		} else {
			$room		= $roomContList['id'][$eventInfo['ti_roomID']]['desc'];
			$branch		= $branchList[$roomContList['id'][$eventInfo['ti_roomID']]['branchID']]['branchDesc'];
		}
		$registrations = self::getRegistrations( $eventInfo['ti_id'] );
		
		require( BOOKAROOM_PATH . 'templates/events/showAttendance.php' );
	}

	protected static function showConflicts( $sessionVars, $dateList, $delete = array() )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
				
		require( BOOKAROOM_PATH . 'templates/events/showConflicts.php' );
	}
	
	protected static function showDeleteMulti( $externals, $instances, $errorMSG = NULL )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		
		
		require( BOOKAROOM_PATH . 'templates/events/delete_multi.php' );
	}
	
	protected static function showDeleteSingle( $externals, $instance = false )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		
		$action = ( $instance == true ) ? 'deleteCheckInstance' : 'deleteCheck';		
		$time = time();
		$hash = md5( $time. $externals['eventID'] );
		
		if( !empty( $externals['noLocation_branch'] ) ) {
			$roomName		= 'No location required';
			$branchName		= $branchList[$externals['noLocation_branch']]['branchDesc'];
		} else {
			$roomName		= $roomContList['id'][$externals['roomID']]['desc'];
			$branchName		= $branchList[$roomContList['id'][$externals['roomID']]['branchID']]['branchDesc'];
		}
		
		$eventTimes = ( $externals['allDay'] == true ) ? 'All Day' : $externals['startTime'].' -'.$externals['endTime'];
		
		$regDate = ( !empty( $externals['regStartDate'] ) ) ? date( 'm/d/Y', strtotime( $externals['regStartDate'] ) ) : NULL;
		
		switch( $externals['registration'] ) {
			case 'staff':
				$registration = 'Staff';
				break;
			case 'true':
				$registration = 'Yes';
				break;
			default:
				$registration = 'No';
				break;
		}
		
		require( BOOKAROOM_PATH . 'templates/events/delete_single.php' );
	}
	
	protected static function showEventForm_event( $externals, $errorArr = NULL, $eventInfo = array() )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		
		
		require( BOOKAROOM_PATH . 'templates/events/eventForm_event.php' );
	}
	
	protected static function showEventForm_instance( $externals, $errorArr = NULL, $eventInfo = array() )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();
		
		if( !empty( $externals['roomID'] ) and substr( $externals['roomID'], 0, 6 ) == 'noloc-' ) {
			$branchInfo = explode( '-', $externals['roomID'] );
			if( !array_key_exists( $branchInfo['0'], $branchList ) ) {
				$branchID = NULL;
				$roomID = NULL;
			}
		} else {
			$branchID = self::branch_and_room_id( $externals['roomID'], $branchList, $roomContList );
			$roomID = $externals['roomID'];
		}
		
		$regDate = ( !empty( $externals['regStartDate'] ) ) ? date( 'm/d/Y', strtotime( $externals['regStartDate'] ) ) : NULL;
		
		switch( $eventInfo['registration'] ) {
			case 'true':
				$regVal = 'Yes';
				break;
			case 'staff':
				$regVal = 'Staff';
				break;
			case 'false':
			default:
				$regVal = 'No';
				break;
		}
		
		require( BOOKAROOM_PATH . 'templates/events/eventForm_instance.php' );
	}
	
	protected static function showEventForm_times( $externals, $errorArr = NULL, $displayName = 'New', $action = 'checkInformation', $changeAction = 'changeRooms', $dateList = array() )
	{
		$roomContList = bookaroom_settings_roomConts::getRoomContList();
		$roomList = bookaroom_settings_rooms::getRoomList();
		$branchList = bookaroom_settings_branches::getBranchList( TRUE );
		$amenityList = bookaroom_settings_amenities::getAmenityList();

		if( !empty( $externals['roomID'] ) and substr( $externals['roomID'], 0, 6 ) == 'noloc-' ) {
			$branchInfo = explode( '-', $externals['roomID'] );
			if( !array_key_exists( $branchInfo['0'], $branchList ) ) {
				$branchID = NULL;
				$roomID = NULL;
			}
		} else {
			$branchID = self::branch_and_room_id( $externals['roomID'], $branchList, $roomContList );
			$roomID = $externals['roomID'];
		}
		
		require( BOOKAROOM_PATH . 'templates/events/eventForm_times.php' );		
	}
	

	
	protected static function showAttendanceSuccess( )
	{
		require( BOOKAROOM_PATH . 'templates/events/showAttendanceSuccess.php' );
	}
}
?>