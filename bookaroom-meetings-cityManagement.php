<?php
class bookaroom_settings_cityManagement {
	public static
	function bookaroom_admin_mainCityManagement() {
		$cityList = self::getCityList();
		$externals = self::getExternals();

		switch ( $externals[ 'action' ] ) {
			case 'deleteCheck':
				# check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'cityID' ], $cityList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/cities/IDerror.php' );
				} else {
					# show delete screen
					$roomList = bookaroom_settings_rooms::getRoomList();
					self::deleteCity( $externals[ 'cityID' ] );
					require( BOOKAROOM_PATH . 'templates/cities/delete_success.php' );
				}

				break;

			case 'delete':
				# check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'cityID' ], $cityList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/cities/IDerror.php' );
				} else {
					# show delete screen
					$cityInfo = self::getCityInfo( $externals[ 'cityID' ] );
					self::showCityDelete( $cityInfo );
				}

				break;

			case 'editCheck':
				# check entries
				if ( ( $errors = self::checkEditCity( $externals, $cityList ) ) == NULL ) {
					self::editCity( $externals );
					require( BOOKAROOM_PATH . 'templates/cities/editSuccess.php' );

					break;
				}

				$externals[ 'errors' ] = $errors;

				# check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'cityID' ], $cityList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/cities/IDerror.php' );
				} else {
					# show edit screen
					self::showCityFormEdit( $externals, 'editCheck', 'Edit' );
				}

				break;

			case 'edit':
				# check that there is an ID and it is valid
				if ( bookaroom_settings::checkID( $externals[ 'cityID' ], $cityList ) == FALSE ) {
					# show error page
					require( BOOKAROOM_PATH . 'templates/cities/IDerror.php' );
				} else {
					# show edit screen
					$cityInfo = self::getCityInfo( $externals[ 'cityID' ] );
					self::showCityFormEdit( $cityInfo, 'editCheck', 'Edit' );
				}

				break;

			case 'addCheck':
				# check entries
				if ( ( $errors = self::checkEditCity( $externals, $cityList ) ) == NULL ) {
					self::addCity( $externals );
					require( BOOKAROOM_PATH . 'templates/cities/add_success.php' );

					break;
				}

				$externals[ 'errors' ] = $errors;
				# show edit screen
				self::showCityFormEdit( $externals, 'addCheck', 'Add' );
				break;

			case 'add':
				self::showCityFormEdit( $externals, 'addCheck', 'Add' );
				break;

			default:
				self::showCityFormList( $cityList );
				break;

		}
	}

	public static
	function addCity( $externals )
	# add a new branch
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_cityList";

		$final = $wpdb->insert( $table_name, array( 'cityDesc' => $externals[ 'cityDesc' ] ) );

		return true;
	}

	public static
	function checkEditCity( & $externals, $cityList )
	# check the name for duplicates or empty
	{
		$final = NULL;
		$error = array();

		# check for empty city name
		if ( empty( $externals[ 'cityDesc' ] ) ) {
			$error[] = 'You must enter an city name.';
		}

		# check dupe name
		if ( bookaroom_settings::dupeCheck( $cityList, $externals[ 'cityDesc' ], $externals[ 'cityID' ] ) == 1 ) {
			$error[] = 'That city name is already in use. Please choose another.';
		}

		# if errors, implode and return error messages

		if ( count( $error ) !== 0 ) {
			$final = implode( "<br />", $error );
		}

		return $final;

	}

	public static
	function deleteCity( $cityID )
	# add a new branch
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_cityList";

		$sql = "DELETE FROM `{$table_name}` WHERE `cityID` = '{$cityID}' LIMIT 1";

		$wpdb->query( $sql );

		return true;

	}

	public static
	function editCity( $externals )
	# change the branch settings
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_cityList";


		$final = $wpdb->update( $table_name,
			array( 'cityDesc' => $externals[ 'cityDesc' ], ),
			array( 'cityID' => $externals[ 'cityID' ] ) );

		return true;
	}

	public static
	function getCityInfo( $cityID )
	# get information about branch from daabase based on the ID
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "bookaroom_cityList";

		$final = $wpdb->get_row( $wpdb->prepare( "SELECT cityDesc FROM `$table_name` WHERE `cityID` = %d", $cityID ) );

		$cityInfo = array( 'cityDesc' => $final->cityDesc, 'cityID' => $cityID );

		return $cityInfo;
	}

	public static
	function getCityList()
	# get a list of all of the available cities. 
	# Return NULL on no cities
	# otherwise, return an array with the unique ID of each city
	# as the key and the description as the val
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "bookaroom_cityList";

		$sql = "SELECT `cityID`, `cityDesc` FROM `$table_name` ORDER BY `cityDesc`";
		$count = 0;

		$cooked = $wpdb->get_results( $sql, ARRAY_A );
		if ( count( $cooked ) == 0 ) {
			return NULL;
		}

		foreach ( $cooked as $key => $val ) {
			$final[ $val[ 'cityID' ] ] = $val[ 'cityDesc' ];
		}

		return $final;
	}

	public static
	function getExternals()
	# Pull in POST and GET values
	{
		$final = array();

		# setup GET variables
		$getArr = array( 'action' => FILTER_SANITIZE_STRING,
			'cityID' => FILTER_SANITIZE_STRING, );

		# pull in and apply to final
		if ( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final += $getTemp;
		}

		# setup POST variables
		$postArr = array( 'action' => FILTER_SANITIZE_STRING,
			'cityID' => FILTER_SANITIZE_STRING,
			'cityDesc' => FILTER_SANITIZE_STRING, );



		# pull in and apply to final
		if ( $postTemp = filter_input_array( INPUT_POST, $postArr ) ) {
			$final += $postTemp;
		}

		$arrayCheck = array_unique( array_merge( array_keys( $getArr ), array_keys( $postArr ) ) );

		foreach ( $arrayCheck as $key ) {
			if ( empty( $final[ $key ] ) ) {
				$final[ $key ] = NULL;
			} else {
				$final[ $key ] = trim( $final[ $key ] );
			}
		}

		return $final;
	}

	public static
	function showCityFormEdit( $cityInfo, $action, $actionName )
	# show edit page and fill with values
	{
		require( BOOKAROOM_PATH . 'templates/cities/edit.php' );
	}

	public static
	function showCityDelete( $cityInfo )
	# show delete page and fill with values
	{
		require( BOOKAROOM_PATH . 'templates/cities/delete.php' );
	}

	public static
	function showCityFormList( $cityList )
	# show edit page and fill with values
	{
		require( BOOKAROOM_PATH . 'templates/cities/mainAdmin.php' );
	}
}
?>