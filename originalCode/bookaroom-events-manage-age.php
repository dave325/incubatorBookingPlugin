<?php
class bookaroom_settings_age {
	public static

	function showFormAge() {
		$externals = self::getExternals();
		#$error = NULL;

		switch ( $externals[ 'action' ] ) {
			case 'addCheck':
				# check for errors
				if ( ( $errorMSG = self::checkNewAge( $externals[ 'newName' ] ) ) == TRUE ) {
					self::showForm( $externals, $errorMSG );
					break;
				}
				self::addGroup( $externals[ 'newName' ] );
				self::showForm( NULL, sprintf( __( 'You have successfully added the group %s', 'book-a-room' ), $externals[ 'newName' ] ) );
				break;

			case 'moveTop':
			case 'moveBottom':
			case 'moveDown':
			case 'moveUp':
				# check that ID is valid and active
				$error = self::moveGroup( $externals[ 'action' ], $externals[ 'groupID' ] );
				self::showForm( $externals, $error );
				break;

			case 'edit':
				# check that ID is valid and active
				if ( ( $error = self::checkID( $externals[ 'groupID' ], 'edit' ) ) == TRUE ) {
					self::showForm( $externals, $error );
				} else {
					$nameList = self::getNameList();
					self::showEdit( $nameList[ 'active' ][ $externals[ 'groupID' ] ][ 'age_desc' ], $externals[ 'groupID' ] );
				}

				break;
			case 'editCheck':
				# check that ID is valid and active
				if ( ( $error = self::checkID( $externals[ 'groupID' ], 'edit' ) ) == TRUE ) {
					self::showForm( $externals, $error );
					break;
				} else {

					$nameList = self::getNameList();
					if ( ( $error = self::checkEdit( $externals[ 'groupID' ], $externals[ 'newName' ] ) ) == TRUE ) {
						self::showEdit( $externals[ 'newName' ], $externals[ 'groupID' ], $error );
					} else {
						self::editGroup( $externals[ 'newName' ], $externals[ 'groupID' ] );
						$nameList = self::getNameList();
						self::showForm( array(), __( 'You have successfully edited an age group.', 'book-a-room' ) );
					}
				}
				break;

			case 'deactivate':
				# check that ID is valid and active
				if ( ( $error = self::checkID( $externals[ 'groupID' ], 'deactivate' ) ) == TRUE ) {
					self::showForm( $externals, $error );
				} else {
					$nameList = self::getNameList();
					self::showActivateChangeCheck( $nameList[ 'active' ][ $externals[ 'groupID' ] ][ 'age_desc' ], $externals[ 'groupID' ], 'deactivate' );
				}
				break;

			case 'deactivateFinal':
				if ( ( $error = self::checkHash( $externals[ 'hash' ], $externals[ 'time' ], $externals[ 'groupID' ], 'active' ) ) == TRUE ) {
					$nameList = self::getNameList();
					self::showActivateChangeCheck( $nameList[ 'active' ][ $externals[ 'groupID' ] ][ 'age_desc' ], $externals[ 'groupID' ], 'deactivate', $error );
				} else {
					self::changeActivation( $externals[ 'groupID' ], 0 );
					self::showForm( $externals, __( 'You have successfully deactivated an age group.', 'book-a-room' ) );
				}
				break;

			case 'reactivate':
				# check that ID is valid and active
				if ( ( $error = self::checkID( $externals[ 'groupID' ], 'reactivate' ) ) == TRUE ) {
					self::showForm( $externals, $error );
				} else {
					$nameList = self::getNameList();
					self::showActivateChangeCheck( $nameList[ 'inactive' ][ $externals[ 'groupID' ] ][ 'age_desc' ], $externals[ 'groupID' ], 'reactivate' );
				}
				break;

			case 'reactivateFinal':
				if ( ( $error = self::checkHash( $externals[ 'hash' ], $externals[ 'time' ], $externals[ 'groupID' ], 'inactive' ) ) == TRUE ) {
					$nameList = self::getNameList();
					self::showActivateChangeCheck( $nameList[ 'active' ][ $externals[ 'groupID' ] ][ 'age_desc' ], $externals[ 'groupID' ], 'reactivate', $error );
				} else {
					self::changeActivation( $externals[ 'groupID' ], 1 );
					self::showForm( $externals, __( 'You have successfully deactivated an age group.', 'book-a-room' ) );
				}
				break;


			default:
				self::showForm( $externals );
				break;
		}
	}

	protected static

	function changeActivation( $groupID, $value ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "bookaroom_event_ages";
		$wpdb->update( $table_name, array( 'age_active' => $value ), array( 'age_id' => $groupID ) );
		return TRUE;
	}

	protected static

	function checkHash( $hash, $time, $groupID, $type ) {
		$nameList = self::getNameList();
		# TODO if namelist entry empty!
		$newHash = md5( $groupID . $time . $nameList[ $type ][ $groupID ][ 'age_desc' ] );

		if ( $newHash !== $hash ) {
			return __( 'There has been a security error. Please try again.', 'book-a-room' );
		}

		return FALSE;
	}

	protected static

	function showActivateChangeCheck( $groupName, $groupID, $type, $errorMSG = NULL ) {
		$time = time();
		$hash = md5( $groupID . $time . $groupName );

		switch ( $type ) {
			case 'deactivate':
				require BOOKAROOM_PATH . 'templates/events/age_form_deactivate.php';
				break;
			case 'reactivate':
				require BOOKAROOM_PATH . 'templates/events/age_form_reactivate.php';
				break;
			default:
				die( __( 'Error: Wrong "activation change" type.', 'book-a-room' ) );
				break;
		}
	}

	protected static

	function editGroup( $newName, $groupID ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "bookaroom_event_ages";
		$wpdb->update( $table_name, array( 'age_desc' => $newName ), array( 'age_id' => $groupID ) );
		return TRUE;
	}

	protected static

	function checkEdit( $groupID, $newName ) {
		# get namelist	
		$nameList = self::getNameList();

		# check for dupe
		if ( ( $isDupe = array_search( strtolower( $newName ), $nameList[ 'all' ] ) ) == TRUE ) {
			# if dupe but same ID, error that you didn't change it
			if ( $groupID == $isDupe ) {
				return __( 'You haven\'t made any changes.', 'book-a-room' );
			} else {
				if ( true == $nameList[ 'status' ][ $isDupe ] ) {
					return __( 'The name you\'ve entered is in use in an <em><strong>active</strong></em> age group already.', 'book-a-room' );
				} else {
					return __( 'The name you\'ve entered is in use in an <em><strong>inactive</strong></em> age group already. You can reactivate it in the bottom menu.', 'book-a-room' );
				}
			}
		}
		return FALSE;
	}

	protected static

	function addGroup( $newName ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "bookaroom_event_ages";
		# get row count
		$sql = "SELECT MAX( `age_order`) AS `highCount` FROM `$table_name` ";
		$newOrderArr = $wpdb->get_results( $sql, ARRAY_A );
		$newOrder = $newOrderArr[ 0 ][ 'highCount' ];
		$wpdb->insert( $table_name,
			array(
				'age_desc' => $newName,
				'age_order' => ++$newOrder
			),
			array(
				'%s',
				'%d'
			)
		);
		return TRUE;
	}

	protected static

	function checkID( $groupID, $action ) {
		# setup vars
		$final = NULL;
		$nameList = self::getNameList();

		#check if ID exists	
		if ( !array_key_exists( $groupID, $nameList[ 'all' ] ) ) {
			return __( 'That ID doesn\'t exist.', 'book-a-room' );
		}

		switch ( $action ) {
			case 'edit':
				# check if ID active
				if ( !array_key_exists( $groupID, $nameList[ 'active' ] ) ) {
					return sprintf( __( 'If you would like to edit this age group. Please <a href=\"%s\">reactivate</a> it first.', 'book-a-room' ), "?page=bookaroom_event_settings&action=reactivate&groupID=" . $groupID );
				}
				break;

			case 'deactivate':
				if ( !array_key_exists( $groupID, $nameList[ 'active' ] ) ) {
					return __( 'This age group is already inactive. You cannot deactivate it.', 'book-a-room' );
				}
				break;

			case 'reactivate':
				if ( array_key_exists( $groupID, $nameList[ 'active' ] ) ) {
					return __( 'This age group is already active. You cannot reactivate it.', 'book-a-room' );
				}

				break;
		}

		return $final;
	}

	protected static

	function showEdit( $newName, $groupID, $errorMSG = NULL ) {
		require BOOKAROOM_PATH . 'templates/events/age_form_edit.php';
	}

	protected static

	function moveGroup( $moveType, $moveID ) {
		# valid move type?
		$goodArr = array( 'moveUp', 'moveDown', 'moveTop', 'moveBottom' );
		if ( !in_array( $moveType, $goodArr ) ) {
			return __( 'You have chosen an invalid move type. Please try again.', 'book-a-room' );
		}

		# get name list
		$nameList = self::getNameList();

		# check for valid id
		if ( !array_key_exists( $moveID, $nameList[ 'status' ] ) ) {
			return __( 'The ID you\'ve tried to move doesn\'t exist.', 'book-a-room' );
		}

		# check if inactive
		if ( $nameList[ 'status' ][ $moveID ] == FALSE ) {
			return __( 'The group you\'re trying to move is inactive and can\'t be reordered.', 'book-a-room' );
		}


		# get entire list and find current position.
		$curList = array_values( $nameList[ 'order' ] );

		# if move up or top, error if first line
		if ( in_array( $moveType, array( 'moveUp', 'moveTop' ) ) ) {
			if ( ( $curPos = array_search( $moveID, $curList ) ) == 0 ) {
				return __( 'The group you\'re trying to move is the first in the list and can go no higher.', 'book-a-room' );
			}
		}

		# if move down or bottom, error
		if ( in_array( $moveType, array( 'moveDown', 'moveBottom' ) ) ) {
			if ( ( $curPos = array_search( $moveID, $curList ) ) == ( count( $curList ) - 1 ) ) {
				return __( 'The group you\'re trying to move is the last in the list and can go no lower.', 'book-a-room' );
			}
		}

		switch ( $moveType ) {
			case 'moveUp':
				$holder = $curList[ $curPos ];
				$curList[ $curPos ] = $curList[ $curPos - 1 ];
				$curList[ $curPos - 1 ] = $holder;
				break;

			case 'moveTop':
				$holder = $curList[ $curPos ];
				unset( $curList[ $curPos ] );
				$curList = array_merge( array( $holder ), $curList );
				break;

			case 'moveDown':
				$holder = $curList[ $curPos ];
				$curList[ $curPos ] = $curList[ $curPos + 1 ];
				$curList[ $curPos + 1 ] = $holder;
				break;

			case 'moveBottom':
				$listCount = count( $curList ) - 1;
				$holder = $curList[ $curPos ];
				unset( $curList[ $curPos ] );
				$curList = array_merge( $curList, array( $holder ) );
				break;

		}
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_event_ages";

		$count = 1;
		foreach ( $curList as $val ) {
			$wpdb->update( $table_name, array( 'age_order' => $count++ ), array( 'age_id' => $val ) );
		}

		return NULL;
	}

	protected static

	function checkNewAge( $newAgeName, $curID = NULL ) {
		$errors = array();
		$errorMSG = FALSE;

		# check for empty
		if ( empty( $newAgeName ) ) {
			$errors[] = __( 'You haven\'t entered a new age category. Please try again.', 'book-a-room' );
		} else {
			$nameList = self::getNameList();
			# check if live or inactive group shared the same name
			if ( $dupeID = array_search( strtolower( trim( $newAgeName ) ), $nameList[ 'all' ] ) ) {
				if ( true == $nameList[ 'status' ][ $dupeID ] ) {
					$errors[] = __( 'The name you\'ve entered is in use in an <em><strong>active</strong></em> age group already.', 'book-a-room' );
				} else {
					$errors[] = __( 'The name you\'ve entered is in use in an <em><strong>inactive</strong></em> age group already. You can reactivate it in the bottom menu.', 'book-a-room' );
				}
			}
		}

		# check for errors
		if ( count( $errors ) !== 0 ) {
			array_walk( $errors, function ( & $value ) {
				$value = "<p>{$value}</p>";
			} );
			$errorMSG = implode( "\r\n", $errors );
		}
		return $errorMSG;
	}

	public static

	function getExternals()
	# Pull in POST and GET values
	{
		$final = array();

		# setup GET variables
		$getArr = array( 'action' => FILTER_SANITIZE_STRING,
			'groupID' => FILTER_SANITIZE_STRING,
			'hash' => FILTER_SANITIZE_STRING,
			'newName' => FILTER_SANITIZE_STRING,
			'time' => FILTER_SANITIZE_STRING );

		# pull in and apply to final
		if ( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final = array_merge( $final, $getTemp );
		}

		# setup POST variables
		$postArr = array( 'action' => FILTER_SANITIZE_STRING,
			'newName' => FILTER_SANITIZE_STRING,
			'groupID' => FILTER_SANITIZE_STRING );



		# pull in and apply to final
		if ( $postTemp = filter_input_array( INPUT_POST, $postArr ) ) {
			$final = array_merge( $final, $postTemp );
		}

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
		return $final;
	}

	public static

	function getNameList() {
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_event_ages";
		$sql = "SELECT `age_id`, `age_desc`, `age_order`, `age_active` FROM `$table_name` ";

		$cooked = $wpdb->get_results( $sql, ARRAY_A );

		$final = array( 'active' => array(), 'inactive' => array(), 'all' => array(), 'status' => array(), 'order' => array() );

		foreach ( $cooked as $key => $val ) {
			$active = ( empty( $val[ 'age_active' ] ) ) ? 'inactive' : 'active';
			$final[ $active ][ $val[ 'age_id' ] ] = $val;
			$final[ 'all' ][ $val[ 'age_id' ] ] = strtolower( $val[ 'age_desc' ] );
			$final[ 'status' ][ $val[ 'age_id' ] ] = $val[ 'age_active' ];
			$final[ 'order' ][ $val[ 'age_order' ] ] = $val[ 'age_id' ];
		}

		ksort( $final[ 'order' ] );

		return $final;
	}

	protected static

	function showForm( $externals, $errorMSG = NULL ) {
		require BOOKAROOM_PATH . 'templates/events/age_form.php';
	}

	protected static

	function makeLinks( $groupID, $curPos, $topPos ) {

		$link = array();
		# if there top?
		switch ( $curPos ) {
			case 0:
				# top line
				$link[] = __( 'Top', 'book-a-room' );
				$link[] = __( 'Move Up', 'book-a-room' );
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveDown&groupID=' . $groupID . '">' . __( 'Move Down', 'book-a-room' ) . '</a>';
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveBottom&groupID=' . $groupID . '">' . __( 'Bottom', 'book-a-room' ) . '</a>';
				break;

			case ( $curPos == ( $topPos - 1 ) ):
				# bottom line
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveTop&groupID=' . $groupID . '">' . __( 'Top', 'book-a-room' ) . '</a>';
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveUp&groupID=' . $groupID . '">' . __( 'Move Up', 'book-a-room' ) . '</a>';
				$link[] = __( 'Move Down', 'book-a-room' );
				$link[] = __( 'Bottom', 'book-a-room' );
				break;

			default:
				# any other lines
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveTop&groupID=' . $groupID . '">' . __( 'Top', 'book-a-room' ) . '</a>';
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveUp&groupID=' . $groupID . '">' . __( 'Move Up', 'book-a-room' ) . '</a>';
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveDown&groupID=' . $groupID . '">' . __( 'Move Down', 'book-a-room' ) . '</a>';
				$link[] = '<a href="?page=bookaroom_event_settings&action=moveBottom&groupID=' . $groupID . '">' . __( 'Bottom', 'book-a-room' ) . '</a>';
				break;
		}

		$final = implode( ' | ', $link );

		return $final;
	}
}
?>