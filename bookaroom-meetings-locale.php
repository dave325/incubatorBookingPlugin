<?PHP

class bookaroom_settings_locale
{
	var $externals 			= array();	
	
	public static function bookaroom_admin_locale()
	{
		#
		$externals		= self::getExternalsLocale();
		$error 			= NULL;
		
		switch( $externals['action'] ) {
			case 'checkLocale':
				if( true == ( $errorMSG = self::isFormError( $externals ) ) ) {
					self::showForm( $externals, $errorMSG );
					break;
				}
				
				self::updateLocale( $externals );
				self::showSuccess();
				break;
				
			default:
				$externals = self::getValues();
				self::showForm( $externals );
		}
	}
	
	public static function getExternalsLocale()
	{
		$extArr = array(	
							'action'					=> FILTER_SANITIZE_STRING,
							'address1_name'				=> FILTER_SANITIZE_STRING,
							'address2_name'				=> FILTER_SANITIZE_STRING,
							'addressType'				=> FILTER_SANITIZE_STRING,
							'city_name'					=> FILTER_SANITIZE_STRING,
							'state_name'				=> FILTER_SANITIZE_STRING,
							'defaultStateDrop'			=> FILTER_SANITIZE_STRING,
							'defaultState_name'			=> FILTER_SANITIZE_STRING,
							'zip_name'					=> FILTER_SANITIZE_STRING,
							'phone_regex'				=> FILTER_SANITIZE_STRING,
							'phone_example'				=> FILTER_SANITIZE_STRING,
							'zip_regex'					=> FILTER_SANITIZE_STRING,
							'zip_example'				=> FILTER_SANITIZE_STRING,
							 
						);

		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$extTemp = filter_input_array( INPUT_POST, $extArr );
		} else {
			$extTemp = filter_input_array( INPUT_GET, $extArr );
		}		
		
		foreach( $extArr as $key => $val) {
			if( empty( $extTemp[$key] ) ) {
				$extTemp[$key] = NULL;
			}
		}

		return $extTemp;		
	}
	
	public static function getValues()
	{
		$final['addressType'] 			= get_option( 'bookaroom_addressType');
		$final['address1_name'] 		= get_option( 'bookaroom_address1_name' );
		$final['address2_name'] 		= get_option( 'bookaroom_address2_name' );
		$final['city_name'] 			= get_option( 'bookaroom_city_name' );
		$final['state_name'] 			= get_option( 'bookaroom_state_name' );
		$final['defaultState_name'] 	= get_option( 'bookaroom_defaultState_name' );
		$final['defaultStateDrop'] 		= get_option( 'bookaroom_defaultState_name' );
		$final['zip_name'] 				= get_option( 'bookaroom_zip_name' );
		$final['phone_regex'] 			= get_option( 'bookaroom_phone_regex' );
		$final['phone_example']			= get_option( 'bookaroom_phone_example' );
		$final['zip_regex'] 			= get_option( 'bookaroom_zip_regex' );
		$final['zip_example']			= get_option( 'bookaroom_zip_example' );
		
		return $final;
	}
	
	public static function isFormError( $externals )
	{
		if( !empty( $externals['addressType'] ) and $externals['addressType'] == 'usa' ) {
			return false;
		}
		# check for empties
		$varNames = array( 'address1_name' => 'Street Address 1 name', 'address2_name' => 'Street Address 2 name', 'city_name' => 'City name', 'state_name' => 'State/Province/Territory name', 'zip_name' => 'Zip/Postal Code name' );
		
		$error = array();
		foreach( $varNames as $key => $val ) {		
			if( empty( $externals[$key] ) ) {
				$error[] = "You are missing a value for {$val}.";
				#$errorLine[$key] = true;
			}
		}
		
		if( count( $error ) > 0 ) {
			return implode( "<br>\r\n", $error);
		}
		
		return false;
	}
	
	public static function showForm( $externals, $errorMSG = NULL )
	{
		
		require( BOOKAROOM_PATH . 'templates/locale/mainAdmin.php' );		
	}
	
	public static function showSuccess()
	{
		require( BOOKAROOM_PATH . 'templates/locale/showSuccess.php' );	
	}
	
	public static function updateLocale( $externals )
	{
		update_option( 'bookaroom_addressType', $externals['addressType'] );
		update_option( 'bookaroom_address1_name', $externals['address1_name'] );
		update_option( 'bookaroom_address2_name', $externals['address2_name'] );
		update_option( 'bookaroom_city_name', $externals['city_name'] );
		update_option( 'bookaroom_state_name', $externals['state_name'] );
		update_option( 'bookaroom_zip_name', $externals['zip_name'] );
		update_option( 'bookaroom_phone_regex', $externals['phone_regex'] );
		update_option( 'bookaroom_phone_example', $externals['phone_example'] );
		update_option( 'bookaroom_zip_regex', $externals['zip_regex'] );
		update_option( 'bookaroom_zip_example', $externals['zip_example'] );
		
		if( $externals['addressType'] == 'usa' ) {
			$defaultState_name = $externals['defaultStateDrop'];
		} else {
			$defaultState_name = $externals['defaultState_name'];
		}

		update_option( 'bookaroom_defaultState_name', $defaultState_name );
			
		return true;	
	}
}
?>