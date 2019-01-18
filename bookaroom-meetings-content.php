<?php
class bookaroom_settings_content {
	public static
	function bookaroom_admin_content() {
		# first, is there an action?
		$externals = self::getExternalsAdmin();

		switch ( $externals[ 'action' ] ) {
			case 'updateContent':
				# check for valid content
				if ( ( $errors = self::checkContentContents( $externals ) ) !== FALSE ) {
					self::showContentAdmin( $externals, $errors );
					break;
				} else {
					self::updateContent( $externals );
					self::showContentAdminSuccess();
					break;
				}
			default:
				$externals = self::getDefaults();
				self::showContentAdmin( $externals );
				break;

		}
	}


	protected static
	function checkContentContents( & $externals ) {
		$errorMSG = array( 'contract' => null );
		$errorCount = 0;
		
		if ( empty( $externals[ 'bookaroom_content_contract' ] ) ) {
			$errorMSG[ 'contract' ] = __( 'You must enter a value for the <em>Meeting Room Contract</em>.', 'book-a-room' );
			$errorCount++;
		}

		if ( $errorCount == 0 ) {
			return FALSE;
		} else {
			return $errorMSG;
		}
	}

	protected static
	function getDefaults() {
		$option = array();
		$option[ 'bookaroom_content_contract' ] = get_option( 'bookaroom_content_contract' );

		return $option;
	}

	public static
	function getExternalsAdmin()
	# Pull in POST and GET values
	{
		$final = array();

		# setup GET variables
		$getArr = array( 'action' => FILTER_SANITIZE_STRING );

		# pull in and apply to final
		if ( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final = array_merge( $final, $getTemp );
		}

		# setup POST variables
		$postArr = array( 'action' => FILTER_SANITIZE_STRING,
			'bookaroom_content_contract' => FILTER_UNSAFE_RAW,
			'submit' => FILTER_SANITIZE_STRING );


		# pull in and apply to final
		if ( $postTemp = filter_input_array( INPUT_POST, $postArr ) ) {
			$final = array_merge( $final, $postTemp );
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

	protected static
	function showContentAdmin( $externals, $errorMSG = NULL ) {
		require( BOOKAROOM_PATH . 'templates/content/mainAdmin.php' );
	}

	protected static
	function showContentAdminSuccess() {
		require( BOOKAROOM_PATH . 'templates/content/addSuccess.php' );
	}

	protected static
	function makeFormList() {
		$goodArr = array();

		$goodArr[ 'Meeting Room Contract' ] = array(
			'wordpressSettingName' => 'bookaroom_content_contract' );

		return $goodArr;
	}

	protected static
	function updateContent( $externals ) {
		$final = array();

		update_option( 'bookaroom_content_contract', $externals[ 'bookaroom_content_contract' ] );

		return TRUE;
	}
}
?>