<?php
class bookaroom_settings_email {
	public static

	function bookaroom_admin_email() {
		# first, is there an action?
		$externals = self::getExternalsAdmin();

		switch ( $externals[ 'action' ] ) {
			case 'testEmail':
				if ( ( $errors = self::checkEmailErrors( $externals ) ) !== FALSE ) {
					self::showEmailAdmin( $externals, $errors );
					break;
				} else {
					self::updateEmail( $externals );
					self::testEmail( $externals[ 'bookaroom_alertEmail' ] );
				}
				break;
			case 'updateAlertEmail':
				# check for valid email
				if ( ( $errors = self::checkEmailErrors( $externals ) ) !== FALSE ) {
					self::showEmailAdmin( $externals, $errors );
					break;
				} else {
					self::updateEmail( $externals );
					self::showEmailAdminSuccess();
					break;
				}
			default:
				$externals = self::getDefaults();
				self::showEmailAdmin( $externals );
				break;
		}
	}

	protected static
	function updateEmail( $externals ) {
		$final = array();

		$emailAddressArr = explode( ';', $externals[ 'bookaroom_alertEmail' ] );

		$temp = array();
		foreach ( $emailAddressArr as $key => $val ) {
			$temp[] = trim( $val );
		}

		$final = implode( ';', $temp );

		update_option( 'bookaroom_alertEmail', strtolower( $externals[ 'bookaroom_alertEmail' ] ) );
		update_option( 'bookaroom_alertEmailFromName', $externals[ 'bookaroom_alertEmailFromName' ] );
		update_option( 'bookaroom_alertEmailFromEmail', $externals[ 'bookaroom_alertEmailFromEmail' ] );
		
		update_option( 'bookaroom_alertEmailReplyName', $externals[ 'bookaroom_alertEmailReplyName' ] );
		update_option( 'bookaroom_alertEmailReplyEmail', $externals[ 'bookaroom_alertEmailReplyEmail' ] );
		
		update_option( 'bookaroom_alertEmailCC', $externals[ 'bookaroom_alertEmailCC' ] );
		update_option( 'bookaroom_alertEmailBCC', $externals[ 'bookaroom_alertEmailBCC' ] );
		
		update_option( 'bookaroom_regChange_subject', $externals[ 'bookaroom_regChange_subject' ] );
		update_option( 'bookaroom_newAlert_subject', $externals[ 'bookaroom_newAlert_subject' ] );
		update_option( 'bookaroom_requestAcceptedProfit_subject', $externals[ 'bookaroom_requestAcceptedProfit_subject' ] );
		update_option( 'bookaroom_requestAcceptedNonprofit_subject', $externals[ 'bookaroom_requestAcceptedNonprofit_subject' ] );
		update_option( 'bookaroom_requestDenied_subject', $externals[ 'bookaroom_requestDenied_subject' ] );
		update_option( 'bookaroom_requestReminder_subject', $externals[ 'bookaroom_requestReminder_subject' ] );
		update_option( 'bookaroom_requestPayment_subject', $externals[ 'bookaroom_requestPayment_subject' ] );
		update_option( 'bookaroom_newInternal_subject', $externals[ 'bookaroom_newInternal_subject' ] );
		update_option( 'bookaroom_nonProfit_pending_subject', $externals[ 'bookaroom_nonProfit_pending_subject' ] );
		update_option( 'bookaroom_profit_pending_subject', $externals[ 'bookaroom_profit_pending_subject' ] );

		update_option( 'bookaroom_nonProfit_pending_body', htmlentities( $externals[ 'bookaroom_nonProfit_pending_body' ] ) );
		update_option( 'bookaroom_profit_pending_body', htmlentities( $externals[ 'bookaroom_profit_pending_body' ] ) );
		update_option( 'bookaroom_regChange_body', htmlentities( $externals[ 'bookaroom_regChange_body' ] ) );
		update_option( 'bookaroom_newAlert_body', htmlentities( $externals[ 'bookaroom_newAlert_body' ] ) );
		update_option( 'bookaroom_requestAcceptedProfit_body', $externals[ 'bookaroom_requestAcceptedProfit_body' ] );
		update_option( 'bookaroom_requestAcceptedNonprofit_body', $externals[ 'bookaroom_requestAcceptedNonprofit_body' ] );
		update_option( 'bookaroom_requestDenied_body', $externals[ 'bookaroom_requestDenied_body' ] );
		update_option( 'bookaroom_requestReminder_body', $externals[ 'bookaroom_requestReminder_body' ] );
		update_option( 'bookaroom_requestPayment_body', $externals[ 'bookaroom_requestPayment_body' ] );
		update_option( 'bookaroom_newInternal_body', $externals[ 'bookaroom_newInternal_body' ] );

		return TRUE;
	}

	protected static

	function checkEmailErrors( & $externals ) {
		$count = 0;
		$errorMSG = array();
		$errorCount = 0;

		if ( empty( $externals[ 'bookaroom_alertEmail' ] ) ) {
			$temp[] = __( 'You must add an <strong>Email alerts</strong> address.', 'book-a-room' );
		}

		$emailAddressArr = explode( ',', $externals[ 'bookaroom_alertEmail' ] );

		$temp = array();

		foreach ( $emailAddressArr as $key => $valRaw ) {
			$val = trim( $valRaw );
			if ( !empty( $val ) && !filter_var( $val, FILTER_VALIDATE_EMAIL ) ) {
				$temp[] = sprintf( __( 'The email address <strong>%s</strong> is not valid.', 'book-a-room' ), $val );
				$errorCount++;
			}
		}

		if ( empty( $externals[ 'bookaroom_alertEmailFromName' ] ) ) {
			$temp[] = __( 'You must add a <strong>from name</strong>.', 'book-a-room');
		}

		if ( empty( $externals[ 'bookaroom_alertEmailFromEmail' ] ) ) {
			$temp[] = __( 'You must add a <strong>from email address</strong>.', 'book-a-room' ); 
		} elseif ( !filter_var( $externals[ 'bookaroom_alertEmailFromEmail' ], FILTER_VALIDATE_EMAIL ) ) {
			$temp[] = __( 'You must add a valid <strong>from email address</strong>.', 'book-a-room' );
		}

		# reply
		if ( !empty( $externals[ 'bookaroom_alertEmailReplyName' ] ) or !empty( $externals[ 'bookaroom_alertEmailReplyEmail' ] ) ) {
			if ( empty( $externals[ 'bookaroom_alertEmailReplyName' ] ) ) {
				$temp[] = __( 'You must add a <strong>Reply-To name</strong>.', 'book-a-room');
			}
			
			
			if ( empty( $externals[ 'bookaroom_alertEmailReplyEmail' ] ) ) {
				$temp[] = __( 'You must add a <strong>Reply-To email address</strong>.', 'book-a-room' ); 
			} elseif ( !filter_var( $externals[ 'bookaroom_alertEmailReplyEmail' ], FILTER_VALIDATE_EMAIL ) ) {
				$temp[] = __( 'You must add a valid <strong>Reply-To email address</strong>.', 'book-a-room' );
			}
		}
		if ( !empty( $externals[ 'bookaroom_alertEmailCC' ] ) and !filter_var( $externals[ 'bookaroom_alertEmailCC' ], FILTER_VALIDATE_EMAIL ) ) {
				$temp[] = __( 'Your <strong>CC email address</strong> in invalid.', 'book-a-room' );
		}
		
		if ( !empty( $externals[ 'bookaroom_alertEmailBCC' ] ) and !filter_var( $externals[ 'bookaroom_alertEmailBCC' ], FILTER_VALIDATE_EMAIL ) ) {
				$temp[] = __( 'Your <strong>BCC email address</strong> in invalid.', 'book-a-room' );
		}
		
		if ( count( $temp ) !== 0 ) {
			$errorMSG[ 'bookaroom_alertEmail' ] = implode( '<br /><br />', $temp );
		}

		$goodArr = self::arrFormList();

		foreach ( $goodArr as $key => $val ) {
			$final = array();
			if ( empty( $externals[ $key.'_subject' ] ) ) {
				$final[] = sprintf( __( 'You must enter an email subject for <em>%s</em>.', 'book-a-room' ), $val );
				$errorCount++;
			}
			if ( empty( $externals[ $key.'_body' ] ) ) {
				$final[] = sprintf( __( 'You must enter an email body for <em>%s</em>.', 'book-a-room'), $val );
				$errorCount++;
			}
			if ( count( $final ) !== 0 ) {
				$errorMSG[ $key ] = implode( '<br /><br />', $final );
			}
		}

		if ( $errorCount == 0 ) {
			return FALSE;
		} else {
			$errorMSG[ 'count' ] = $errorCount;
			return $errorMSG;
		}
	}

	protected static
	function getDefaults() {
		$option = array();
		$option[ 'bookaroom_alertEmail' ] = get_option( 'bookaroom_alertEmail' );
		$option[ 'bookaroom_alertEmailFromEmail' ]					= get_option( 'bookaroom_alertEmailFromEmail' );
		$option[ 'bookaroom_alertEmailFromName' ]					= get_option( 'bookaroom_alertEmailFromName' );
		$option[ 'bookaroom_alertEmailReplyEmail' ]					= get_option( 'bookaroom_alertEmailReplyEmail' );
		$option[ 'bookaroom_alertEmailReplyName' ]					= get_option( 'bookaroom_alertEmailReplyName' );
		$option[ 'bookaroom_alertEmailCC' ]							= get_option( 'bookaroom_alertEmailCC' );
		$option[ 'bookaroom_alertEmailBCC' ]						= get_option( 'bookaroom_alertEmailBCC' );
		$option[ 'bookaroom_newAlert_body' ]						= html_entity_decode( get_option( 'bookaroom_newAlert_body' ) );
		$option[ 'bookaroom_newAlert_subject' ]						= get_option( 'bookaroom_newAlert_subject' );
		$option[ 'bookaroom_newInternal_body' ]						= html_entity_decode( get_option( 'bookaroom_newInternal_body' ) );
		$option[ 'bookaroom_newInternal_subject' ] 					= get_option( 'bookaroom_newInternal_subject' );
		$option[ 'bookaroom_nonProfitDeposit' ] 					= get_option( 'bookaroom_nonProfitDeposit' );
		$option[ 'bookaroom_nonProfitIncrementPrice' ] 				= get_option( 'bookaroom_nonProfitIncrementPrice' );
		$option[ 'bookaroom_nonProfit_pending_body' ] 				= html_entity_decode( get_option( 'bookaroom_nonProfit_pending_body' ) );
		$option[ 'bookaroom_nonProfit_pending_subject' ] 			= get_option( 'bookaroom_nonProfit_pending_subject' );
		$option[ 'bookaroom_profitDeposit' ] 						= get_option( 'bookaroom_profitDeposit' );
		$option[ 'bookaroom_profitIncrementPrice' ] 				= get_option( 'bookaroom_profitIncrementPrice' );
		$option[ 'bookaroom_profit_pending_body' ] 					= html_entity_decode( get_option( 'bookaroom_profit_pending_body' ) );
		$option[ 'bookaroom_profit_pending_subject' ] 				= get_option( 'bookaroom_profit_pending_subject' );
		$option[ 'bookaroom_regChange_body' ] 						= html_entity_decode( get_option( 'bookaroom_regChange_body' ) );
		$option[ 'bookaroom_regChange_subject' ] 					= get_option( 'bookaroom_regChange_subject' );
		$option[ 'bookaroom_requestAcceptedNonprofit_body' ]		= html_entity_decode( get_option( 'bookaroom_requestAcceptedNonprofit_body' ) );
		$option[ 'bookaroom_requestAcceptedNonprofit_subject' ]		= get_option( 'bookaroom_requestAcceptedNonprofit_subject' );
		$option[ 'bookaroom_requestAcceptedProfit_body' ]			= html_entity_decode( get_option( 'bookaroom_requestAcceptedProfit_body' ) );
		$option[ 'bookaroom_requestAcceptedProfit_subject' ]		= get_option( 'bookaroom_requestAcceptedProfit_subject' );
		$option[ 'bookaroom_requestDenied_body' ]					= html_entity_decode( get_option( 'bookaroom_requestDenied_body' ) );
		$option[ 'bookaroom_requestDenied_subject' ]				= get_option( 'bookaroom_requestDenied_subject' );
		$option[ 'bookaroom_requestPayment_body' ]					= html_entity_decode( get_option( 'bookaroom_requestPayment_body' ) );
		$option[ 'bookaroom_requestPayment_subject' ]				= get_option( 'bookaroom_requestPayment_subject' );
		$option[ 'bookaroom_requestReminder_body' ]					= html_entity_decode( get_option( 'bookaroom_requestReminder_body' ) );
		$option[ 'bookaroom_requestReminder_subject' ]				= get_option( 'bookaroom_requestReminder_subject' );
		return $option;
	}

	public static

	function getExternalsAdmin()
	# Pull in POST and GET values
	{
		$final = array();

		# setup GET variables
		$getArr = array( 'action' => FILTER_SANITIZE_STRING, );
		# pull in and apply to final
		if ( $getTemp = filter_input_array( INPUT_GET, $getArr ) ) {
			$final = array_merge( $final, $getTemp );
		}

		# setup POST variables
		$postArr = array( 
			'action'											=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmail'								=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmailFromEmail'						=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmailFromName'						=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmailReplyEmail'					=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmailReplyName'						=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmailCC'							=> FILTER_SANITIZE_STRING,
			'bookaroom_alertEmailBCC'							=> FILTER_SANITIZE_STRING,
			'bookaroom_newAlert_body'							=> FILTER_UNSAFE_RAW,
			'bookaroom_newAlert_subject'						=> FILTER_SANITIZE_STRING,
			'bookaroom_newInternal_body'						=> FILTER_UNSAFE_RAW,
			'bookaroom_newInternal_subject'						=> FILTER_SANITIZE_STRING,
			'bookaroom_nonProfit_pending_body'					=> FILTER_UNSAFE_RAW,
			'bookaroom_nonProfit_pending_subject'				=> FILTER_SANITIZE_STRING,
			'bookaroom_profit_pending_body'						=> FILTER_UNSAFE_RAW,
			'bookaroom_profit_pending_subject'					=> FILTER_SANITIZE_STRING,
			'bookaroom_regChange_body'							=> FILTER_UNSAFE_RAW,
			'bookaroom_regChange_subject'						=> FILTER_SANITIZE_STRING,
			'bookaroom_requestAcceptedNonprofit_body'			=> FILTER_UNSAFE_RAW,
			'bookaroom_requestAcceptedNonprofit_subject'		=> FILTER_SANITIZE_STRING,
			'bookaroom_requestAcceptedProfit_body'				=> FILTER_UNSAFE_RAW,
			'bookaroom_requestAcceptedProfit_subject'			=> FILTER_SANITIZE_STRING,
			'bookaroom_requestDenied_body'						=> FILTER_UNSAFE_RAW,
			'bookaroom_requestDenied_subject'					=> FILTER_SANITIZE_STRING,
			'bookaroom_requestPayment_body'						=> FILTER_UNSAFE_RAW,
			'bookaroom_requestPayment_subject'					=> FILTER_SANITIZE_STRING,
			'bookaroom_requestReminder_body'					=> FILTER_UNSAFE_RAW,
			'bookaroom_requestReminder_subject'					=> FILTER_SANITIZE_STRING,
			'submit'											=> FILTER_SANITIZE_STRING,
			'testEmail'											=> FILTER_SANITIZE_STRING );


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

		# fix action
		if ( !empty( $final[ 'testEmail' ] ) ) {
			$final[ 'action' ] = 'testEmail';
		}
		return $final;
	}

	protected static
	function showEmailAdmin( $externals, $errorMSG = NULL ) {
		require( BOOKAROOM_PATH . 'templates/email/mainAdmin.php' );
	}

	protected static
	function showEmailAdminSuccess() {
		require( BOOKAROOM_PATH . 'templates/email/addSuccess.php' ); 
	}

	protected static
	function testEmail( $bookaroom_alertEmail ) {
		ob_start();
		require( BOOKAROOM_PATH . 'templates/email/test.php' );
		$mailContents = ob_get_contents();
   		ob_end_clean();
		$mailContents = str_replace( '#bookaroom_alertEmail#', $bookaroom_alertEmail, $mailContents );
		
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
		'From: Test_email' . "\r\n" .
		'Reply-To: <>' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		mail( $bookaroom_alertEmail, 'Book a Room Email Test', $mailContents, $headers );
		
		require( BOOKAROOM_PATH . 'templates/email/testSuccess.php' );
	}

	protected static
	function arrFormList() {
		$goodArr = array();
		$goodArr[ 'bookaroom_newAlert']						= __( 'New Request Alert Settings', 'book-a-room' );
		$goodArr[ 'bookaroom_newInternal']					= __( 'New Staff Request Receipt', 'book-a-room' );
		$goodArr[ 'bookaroom_regChange' ]					= __( 'Registration Change (From Waiting List)', 'book-a-room' );
		$goodArr[ 'bookaroom_requestAcceptedNonprofit' ]	= __( 'Request Accepted (Nonprofit) Settings', 'book-a-room' );
		$goodArr[ 'bookaroom_requestAcceptedProfit' ]		= __( 'Request Accepted (Profit) Settings', 'book-a-room' );
		$goodArr[ 'bookaroom_requestDenied' ]				= __( 'Request Denied Settings', 'book-a-room' );
		$goodArr[ 'bookaroom_nonProfit_pending' ]			= __( 'Waiting on Nonprofit Paperwork', 'book-a-room' );
		$goodArr[ 'bookaroom_profit_pending' ]				= __( 'Waiting on Payment', 'book-a-room' );
		return $goodArr;
	}
}
?>