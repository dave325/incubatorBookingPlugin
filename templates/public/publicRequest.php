<div id="bookaroom_main_container">
    <form name="form1" method="post">
        <div id="topRow">
            <div class="col">
                <div class="instructionsSmooth">
                    <span class="header">
                        <?php _e( 'Step 5.', 'book-a-room' ); ?>
                    </span>
                    <p>
                        <em>
                            <?php _e( 'Complete the registration form.', 'book-a-room' ); ?>
                        </em>
                    </p>
                    <p><em><strong><?php _e( 'Items marked with an asterisk* are required fields.', 'book-a-room' ); ?></strong></em>
                    </p>
                    <?php
                    # Display Errors if there are any
                    if ( !empty( $errorArr[ 'errorMSG' ] ) ) {
                        ?>
                    <p>
                        <h3 style="color: red;"><strong><?php echo implode( "<br>", $errorArr['errorMSG'] ); ?></strong></h3>
                    </p>
                    <?php
                    }
                    ?>
                </div>
                <div class="options">
                    <div id="formRow">
                        <div class="wideCol">
                            <div class="question">
                                <?php _e( 'Branch and Room', 'book-a-room' ); ?>
                            </div>
                            <div class="formInput">
                                <strong>
                                    <?php echo $branchList[$roomContList['id'][$roomContID]['branchID']]['branchDesc']; ?>
                                </strong><br/>
                                <em>
                                    <?php echo $roomContList['id'][$roomContID]['desc']; ?>
                                </em>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <?php _e( 'Date', 'book-a-room' ); ?>
                            </div>
                            <div class="formInput">
                                <?php echo date_i18n( 'l, F jS, Y', $externals['startTime'] ); ?>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <?php _e( 'Requested times', 'book-a-room' ); ?>
                            </div>
                            <div class="formInput">
                                <strong>
                                    <?php echo date_i18n( 'g:i a', $externals['startTime'] ); ?>
                                </strong> -
                                <strong>
                                    <?php echo date_i18n( 'g:i a', $externals['endTime'] ); ?>
                                </strong>
                            </div>
                        </div>
                       <!--  <div class="wideCol">
                            <div class="question"><?php _e( 'Non Profit 501(c)(3)', 'book-a-room' ); ?> *</div>
                            <div class="formInput">
                                <div style="float: left; margin-right: 30px;">
									<input name="nonProfit" type="radio" id="nonProfitYes" value="TRUE" <?php echo $NPyesChecked; ?>/>
									<?php _e( 'Yes', 'book-a-room' ); ?>
								</div>
								<div style="float: left"><input name="nonProfit" type="radio" id="nonProfitNo" value="" <?php echo $NPnoChecked; ?>/>
									<?php _e( 'No', 'book-a-room' ); ?>
								</div>
                            </div>
                        </div> -->
                        <!--  
					<div class="wideCol" id="fileUpload">
						<div class="question">< ?php #_e( '501(c)(3) paperwork', 'book-a-room' ); ? > *</div>
						<div class="formInput"><input name="bookaroomUpload" type="file" id="bookaroomUpload"><br>
							<span style="font-size:.75em; font-weight: normal">< ?php #_e( 'Please attach 501(c)(3) paperwork in JPG or PDF format.', 'book-a-room' ); ? ></span>
						</div>
					</div>
					Start file upload -->
                        <!--  Start social code -->
                        <!-- <?php
                        if ( $branchList[ $roomContList[ 'id' ][ $roomContID ][ 'branchID' ] ][ 'branch_isSocial' ] ) {
                            ?>
                        <div class="wideCol" id="socialEvent">
                            <div class="question">
                                <?php _e( 'Social Event?', 'book-a-room' ); ?>
                            </div>
                            <div class="formInput">
                                <div style="float: left; margin-right: 30px">
									<input name="isSocial" type="radio" id="socialYes" value="TRUE" <?php echo $SOyesChecked; ?>/>
									<?php _e( 'Yes', 'book-a-room' ); ?>
								</div>
								<div style="float: left">
									<input name="isSocial" type="radio" id="socialNo" value="" <?php echo $SOnoChecked; ?>/>
									<?php _e( 'No', 'book-a-room' ); ?>
								</div>
								<div style="clear: both">
									<span style="font-size:.75em; font-weight: normal">
										<?php _e( '(A baby shower or a birthday party, for example.)', 'book-a-room' ); ?>
									</span>
								</div>
                            </div>
                        </div>
                        <!--   Library Card --
                        <div class="wideCol" id="libraryCard">
                            <div class="question">
                                <?php _e( 'Library Card Number?', 'book-a-room' ); ?> *</div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'libcardNum'] ) ) echo ' class="error"'; ?> name="libcardNum" type="text" id="libcardNum" value="<?php echo $externals['libcardNum']; ?>"/>
                            </div>
                        </div>
                        <?php
                        }
                        ?> -->
                        <div class="wideCol">
                            <div class="question">
                                <label for="eventName">
                                    <?php _e( 'Event / Organization name', 'book-a-room' ); ?> *</label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'eventName'] ) ) echo ' class="error"'; ?> name="eventName" type="text" id="eventName" value="<?php echo $externals['eventName']; ?>" size="64" maxlength="255"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="numAtend">
                                    <?php _e( 'Number of attendees', 'book-a-room' ); ?> *</label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'numAttend'] ) ) echo ' class="error"'; ?> name="numAttend" type="number" id="numAttend" value="<?php echo $externals['numAttend']; ?>" size="3" maxlength="3"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="desc">
                                    <?php _e( 'Purpose of meeting', 'book-a-room' ); ?> *</label>
                            </div>
                            <div class="formInput">
                                <textarea<?php if( !empty( $errorArr[ 'classes'][ 'desc'] ) ) echo ' class="error"'; ?> name="desc" rows="3" id="desc"><?php echo htmlspecialchars_decode( $externals['desc'] ); ?></textarea>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="contactName">
                                    <?php _e( 'Contact name', 'book-a-room' ); ?> *</label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactName'] ) ) echo ' class="error"'; ?> name="contactName" type="text" id="contactName" value="<?php echo $externals['contactName']; ?>" size="32" maxlength="64"/>
                            </div>
                        </div>
                       <!--  <div class="wideCol">
                            <div class="question">
                                <label for="contactPhonePrimary">
                                    <?php _e( 'Primary phone', 'book-a-room' ); ?> *</label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactPhonePrimary'] ) ) echo ' class="error"'; ?> name="contactPhonePrimary" type="tel" id="contactPhonePrimary" value="<?php echo $externals['contactPhonePrimary']; ?>"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="contactPhoneSecondary">
                                    <?php _e( 'Alternative phone', 'book-a-room' ); ?>
                                </label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactPhoneSecondary'] ) ) echo ' class="error"'; ?> name="contactPhoneSecondary" type="tel" id="contactPhoneSecondary" value="<?php echo $externals['contactPhoneSecondary']; ?>"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="contactAddress1">
                                    <?php echo $address1_name; ?> *</label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactAddress1'] ) ) echo ' class="error"'; ?> name="contactAddress1" type="text" id="contactAddress1" value="<?php echo $externals['contactAddress1']; ?>" size="64" maxlength="255"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="contactAddress2">
                                    <?php echo $address2_name; ?>
                                </label>
                            </div>
                            <div class="formInput">
                                <input name="contactAddress2" type="text" id="contactAddress2" value="<?php echo $externals['contactAddress2']; ?>" size="64" maxlength="255"/>
                            </div>
                        </div>
                        <!-- city information including drop down for social events --
                        <div class="wideCol" id="cityFill">
                            <div class="question">
                                <label for="contactCity">
                                    <?php echo $city_name; ?>
                                </label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactCity'] ) ) echo ' class="error"'; ?> name="contactCity" type="text" id="contactCity" value="<?php echo $externals['contactCity']; ?>" maxlength="255"/>
                            </div>
                        </div>

                        <div class="wideCol" id="cityDrop">
                            <div class="question">
                                <label for="contactCityDrop">
                                    <?php echo $city_name; ?>
                                </label>
                            </div>
                            <div class="formInput">
                                <div class="formDiv">
                                    <select<?php if( !empty( $errorArr[ 'classes'][ 'contactCity'] ) ) echo ' class="error"'; ?> name="contactCityDrop" id="contactCityDrop">
                                        <?php
                                        $selected = ( empty( $externals[ 'contactCityDrop' ] )or!array_key_exists( $key, $cityList ) ) ? ' selected="selected"' : null;
                                        ?>
                                        <option value="" <?php echo $selected; ?>>
                                            <?php _e( 'Choose a city', 'book-a-room' ); ?>
                                        </option>
                                        <?php
                                        foreach ( $cityList as $key => $val ) {
                                            # Contact City Drop
                                            $selected = ( $externals[ 'contactCityDrop' ] == $key ) ? ' selected="selected"' : null;
                                            ?>
                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>>
                                            <?php echo $val; ?>
                                        </option>
                                        <?php
                                        }
                                        ?>
                                        </select>
                                </div>
                            </div>
                        </div>

                        <div class="wideCol">
                            <div class="question">
                                <label for="contactState">
                                    <?php echo $state_name; ?>
                                </label>
                            </div>
                            <div class="formInput">
                                <div class="formDiv">
                                    <?php
                                    if ( get_option( 'bookaroom_addressType' ) == 'usa' ) {
                                        # State dropdown
                                        ?>
                                    <select<?php if( !empty( $errorArr[ 'classes'][ 'contactState'] ) ) echo ' class="error"'; ?> name="contactState" id="contactState">
                                        <?php
                                        $stateList = self::getStates();

                                        $selected = ( empty( $externals[ 'contactState' ] )or!array_key_exists( $externals[ 'contactState' ], $stateList ) ) ? ' selected="selected"' : null;

                                        ?>
                                        <option value="" <?php echo $selected; ?>>
                                            <?php _e( 'Choose a state', 'book-a-room' ); ?>
                                        </option>
                                        <?php

                                        foreach ( $stateList as $key => $val ) {
                                            $selected = ( !empty( $externals[ 'contactState' ] )and $externals[ 'contactState' ] == $key ) ? ' selected="selected"' : null;
                                            ?>
                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>>
                                            <?php echo $val; ?>
                                        </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <?php
                                    } else {
                                        # State text entry
                                    ?>
                                    <input<?php if( !empty( $errorArr[ 'classes'][ 'contactState'] ) ) echo ' class="error"'; ?> name="contactState" type="text" id="contactState" value="<?php echo $externals['contactState']; ?>" name="contactState" maxlength="255"/>
                                    <?php
                                    }                                    
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="contactZip">
                                    <?php echo $zip_name; ?>
                                </label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactZip'] ) ) echo ' class="error"'; ?> name="contactZip" type="text" id="contactZip" value="<?php echo $externals['contactZip']; ?>"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <label for="contactEmail">
                                    <?php _e( 'Email Address', 'book-a-room' ); ?> *</label>
                            </div>
                            <div class="formInput">
                                <input<?php if( !empty( $errorArr[ 'classes'][ 'contactEmail'] ) ) echo ' class="error"'; ?> name="contactEmail" type="email" id="contactEmail" value="<?php echo $externals['contactEmail']; ?>" size="64" maxlength="255"/>
                            </div>
                        </div>
                        <div class="wideCol">
                            <div class="question">
                                <?php _e( 'Amenities', 'book-a-room' ); ?>
                            </div>
                            <div class="formInput">
                                <?php
                                $amenityList = bookaroom_settings_amenities::getAmenityList();
                                $amenitiesCur = array();
                                foreach ( $roomContList[ 'id' ][ $roomContID ][ 'rooms' ] as $key => $val ) {
                                    if ( !empty( $roomList[ 'id' ][ $val ][ 'amenity' ] ) ) {
                                        $amenitiesCur += $roomList[ 'id' ][ $val ][ 'amenity' ];
                                    }
                                }
                                $amenitiesCur = array_unique( $amenitiesCur );

                                if ( empty( $amenitiesCur ) ) {
                                    __( 'No amenities are available for this room.', 'book-a-room' );
                                } else {
                                    $final = array();
                                    $temp = NULL;
                                    if ( !array( $externals[ 'amenity' ] ) ) {
                                        $externals[ 'amenity' ] = array();
                                    }



                                    foreach ( $amenitiesCur as $key => $val ) {
                                        if ( !array_key_exists( $val, $amenityList ) ) {
                                            continue;
                                        }

                                        $checked = ( !is_array( $externals[ 'amenity' ] )or!in_array( $val, $externals[ 'amenity' ] ) ) ? NULL : ' checked="checked"';
                                        # amenities
                                        ?>
                                <div class="formAmenities">
									<label for="amenity_<?php echo $val; ?>"><input style="float:left" type="checkbox" value="<?php echo $val; ?>" name="amenity[]" id="amenity_<?php echo $val; ?>" <?php echo $checked; ?> />
									<?php echo trim( $amenityList[$val] ); ?></label>
                                </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div> -->
                        <div class="wideCol">
                            <div class="question">&nbsp;&nbsp;</div>
                            <div class="formInput">
                                <input name="startTime" type="hidden" id="startTime" value="<?php echo  $externals['startTime']; ?>"/>
                                <input name="endTime" type="hidden" id="endTime" value="<?php echo  $externals['endTime']; ?>"/>
                                <input name="roomID" type="hidden" id="roomID" value="<?php echo $roomContID; ?>"/>
                                <input name="action" type="hidden" id="action" value="<?php echo $externals['action']; ?>"/>
                                <input type="submit" name="button" id="button" value="<?php _e( 'Submit', 'book-a-room' ); ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    /* check if nonprofit is selected */
    /* jQuery( "#nonProfitYes" ).click( function () {
        jQuery( "#socialEvent" ).hide();
        jQuery( "#libraryCard" ).hide();
        jQuery( "#cityDrop" ).hide();
        jQuery( "#cityFill" ).show();
        jQuery( "#fileUpload" ).show();
        refreshColors();
    } );
    jQuery( "#nonProfitNo" ).click( function () {
        jQuery( "#socialEvent" ).show();
        jQuery( "#fileUpload" ).hide();
        if ( jQuery( "input[name=social]:checked" ).val() == "TRUE" ) {
            jQuery( "#libraryCard" ).show();
        }
        refreshColors();
    } );

    jQuery( "#socialYes" ).click( function () {
        jQuery( "#cityDrop" ).show();
        jQuery( "#cityFill" ).hide();
        jQuery( "#libraryCard" ).show();
        refreshColors();
    } );
    jQuery( "#socialNo" ).click( function () {
        jQuery( "#cityDrop" ).hide();
        jQuery( "#cityFill" ).show();
        jQuery( "#libraryCard" ).hide();
        refreshColors();
    } ); */
    jQuery( document ).ready( function () {
       /*  if ( jQuery( "input[name=nonProfit]:checked" ).val() == "" ) {
            jQuery( "#socialEvent" ).show();
            jQuery( "#fileUpload" ).hide();
        } else {
            jQuery( "#socialEvent" ).hide();
            jQuery( "#libraryCard" ).hide();
            jQuery( "#fileUpload" ).show();
        }
        <?php 
		if( false == $branchList[$roomContList['id'][$roomContID]['branchID']]['branch_isSocial'] ) {
			
			?>
        jQuery( "#cityDrop" ).hide();
        jQuery( "#cityFill" ).show();
        jQuery( "#libraryCard" ).hide();
        <?php
		} else {
		?>
        console.log( jQuery( "input[name=isSocial]:checked" ).val() );
        if ( jQuery( "input[name=isSocial]:checked" ).val() == "" ) {
            jQuery( "#cityDrop" ).hide();
            jQuery( "#cityFill" ).show();
            jQuery( "#libraryCard" ).hide();

        } else {
            jQuery( "#cityDrop" ).show();
            jQuery( "#cityFill" ).hide();
            jQuery( "#libraryCard" ).show();
        }
        <?php
		}
		?> */
        refreshColors();

    } );

    function refreshColors() {
        jQuery( '#formRow div.question:visible:even, #formRow div.formInput:visible:even' ).css( 'background', '#FFF' );
        jQuery( '#formRow div.question:visible:odd, #formRow div.formInput:visible:odd' ).css( 'background', '#EEE' );
    }
</script> 