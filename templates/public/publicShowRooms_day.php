<script language="javascript">
    function checkSubmit() {
        var hourChecks = document.getElementsByName( 'hours[]' );

        var boxCount = 0;

        for ( var t = 0, checkLength = hourChecks.length; t < checkLength; t++ ) {
            if ( hourChecks[ t ].type == 'checkbox' && hourChecks[ t ].checked == true ) {
                boxCount++;
            }
        }

        if ( boxCount > 0 ) {
            document.forms[ "hoursForm" ].submit();
        } else {
            alert( "Error!\nYou haven't selected any times to reserve." );
        }
    }

    function checkHours( curChecked ) {
        /* are there only two checked boxes? */
        //alert();
        var hourChecks = document.getElementsByName( 'hours[]' );
        var boxArr = new Array();
        var boxCount = 0;
        var lastItem = false;

        // count total boxes checked
        for ( var t = 0, checkLength = hourChecks.length; t < checkLength; t++ ) {
            if ( hourChecks[ t ].type == 'checkbox' && hourChecks[ t ].checked == true ) {
                boxArr[ boxCount++ ] = t;
            }
        }

        // is this unchecking - clear under
        if ( hourChecks[ curChecked ].checked == false && curChecked < boxArr[ 0 ] ) {
            hourChecks[ curChecked ].checked = false;
        } else if ( hourChecks[ curChecked ].checked == false ) {
            for ( var t = curChecked, checkLength = hourChecks.length; t < checkLength; t++ ) {
                hourChecks[ t ].checked = false;
            }
            // is checked box higher? clear underneath (after first)
        } else if ( hourChecks[ curChecked ].checked == true && boxArr[ 1 ] > curChecked ) {
            var chkstat = true;
            for ( var t = curChecked, checkLength = hourChecks.length; t < checkLength; t++ ) {
                hourChecks[ t ].checked = chkstat;
                chkstat = false;

            }
            // are there multiple and this is the first? just uncheck it
        } else if ( boxArr.length > 1 ) {
            for ( var s = boxArr[ 0 ] + 1, e = boxArr[ boxArr.length - 1 ]; s < e; s++ ) {
                var curHour = document.getElementById( 'hours_' + s );

                if ( curHour.value == false ) {
                    hourChecks[ curChecked ].checked = false;
                    alert( "Error!\nI'm sorry, but there is already a reservation in the time you've selected. Please make sure your reservation times don't overlap someone else's reservation." );
                    break;
                } else {
                    hourChecks[ s ].checked = true;
                }
            }
        }
    }
</script>
<div id="bookaroom_main_container">
    <div id="topRow">
        <div class="col">
            <div class="instructions">
                <span class="header">
                    <?php _e( 'Step 1.', 'book-a-room' ); ?>
                </span>
                <p>
                    <em>
                        <?php _e( 'Choose a branch.', 'book-a-room' ); ?>
                    </em><br/>
                    <?php printf( __( 'For a detailed view of branches and rooms, <a href="%saction=reserve">click here</a>.', 'book-a-room' ), CHUH_BaR_Main_permalinkFix() ); ?>
                </p>
            </div>
            <div class="options">
                <span class="header">
                    <?php _e( 'Branch List', 'book-a-room' ); ?>
                </span>
                <?php
                if ( count( $branchList ) == 0 ) {
                    ?>
                <div class="normalItem">
                    <?php _e( 'There are no branches available.', 'book-a-room' ); ?>
                </div>
                <?php
                } else {
                    if ( empty( $branchID ) ) {
                        $branchID = key( $branchList );
                    }
                    foreach ( $branchList as $key => $val ) {
                        # is this the current branch?
                        if ( !empty( $branchID )and $branchID == $key ) {
                ?>
                <div class="selectedItem">
                    <?php echo $val['branchDesc']; ?>
                </div>
                <?php
                        } else {
                ?>
                <div class="normalItem">
                    <a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;branchID=<?php echo $val['branchID']; ?>&amp;timestamp=<?php echo $timestamp; ?>">
                        <?php echo $val['branchDesc']; ?>
                    </a>
                </div>
                <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
        <div class="col">
            <div class="instructions">
                <span class="header">
                    <?php _e( 'Step 2.', 'book-a-room' ); ?>
                </span>
                <p>
                    <em>
                        <?php _e( 'Choose a Room.', 'book-a-room' ); ?>
                    </em><br/>
                    <?php _e( 'Some reservable spaces may combine two or more physical rooms and aren\'t available if any of the single rooms that make them up are already reserved.', 'book-a-room' ); ?>
                </p>
            </div>
            <div class="options">
                <span class="header">
                    <?php _e( 'Room List', 'book-a-room' ); ?>
                </span>
                <?php
                if ( empty( $roomContList[ 'branch' ][ $branchID ] ) ) {
                    ?>
                <div class="normalItem">
                    <?php _e( 'There are no rooms available in this branch.', 'book-a-room' ); ?>
                </div>
                <?php
                } else {
                    foreach ( $roomContList[ 'branch' ][ $branchID ] as $key => $val ) {
                        if ( count( $branchList ) == 0 ) {
                ?>
                <div class="normalItem">
                    <?php _e( 'There are no branches or rooms available.', 'book-a-room' ); ?>
                </div>
                <?php
                        } else {
                            # is this the current room?
                            if ( $roomID == $val ) {
                ?>
                <div class="itemCont">
                    <div class="selectedItem">
                        <?php echo $roomContList['id'][$val]['desc']; ?>
                    </div>
                    <div class="itemDesc">
                        <?php printf( __( 'Occupancy: %s', 'book-a-room' ), $roomContList['id'][$val]['occupancy'] ); ?>
                    </div>
                </div>
                <?php
                            } else {
                ?>
                <div class="itemCont">
                    <div class="normalItem">
                        <a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;roomID=<?php echo $val; ?>&amp;timestamp=<?php echo $timestamp; ?>">
                            <?php echo $roomContList['id'][$val]['desc']; ?>
                        </a>
                    </div>
                    <div class="itemDesc">
                        <?php printf( __( 'Occupancy: %s', 'book-a-room' ), $roomContList['id'][$val]['occupancy'] ); ?>
                    </div>
                </div>
                <?php
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
        <div class="col">
            <div class="instructions">
                <span class="header">
                    <?php _e( 'Step 3.', 'book-a-room' ); ?>
                </span>
                <p>
                    <em>
                        <?php _e( 'Choose a date.', 'book-a-room' ); ?>
                    </em><br/>
                    <?php _e( 'Once you have chosen a Branch, room and date, the hours that are available will be shown below.', 'book-a-room' ); ?><br/>
                </p>
            </div>
            <?php
            # small calendar
            if ( empty( $timestamp )or( !is_numeric( $timestamp )and( $timestamp <= PHP_INT_MAX )and( $timestamp >= ~PHP_INT_MAX ) ) ) {
                $timestamp = current_time( 'timestamp' );
            }

            $urlInfoRaw = parse_url( get_permalink() );
            $urlInfo = ( !empty( $urlInfoRaw[ 'query' ] ) ) ? $urlInfoRaw[ 'query' ] : NULL;

            if ( empty( $urlInfo ) ) {
                $permalinkCal = '?';
            } else {
                $permalinkCal = '?' . $urlInfo . '&';
            }

            # full month timestamp
            $timestampInfo = getdate( $timestamp );
            $thisMonth = mktime( 0, 0, 0, $timestampInfo[ 'mon' ], 1, $timestampInfo[ 'year' ] );
            $nextMonth = mktime( 0, 0, 0, $timestampInfo[ 'mon' ] + 1, 1, $timestampInfo[ 'year' ] );
            $prevMonth = mktime( 0, 0, 0, $timestampInfo[ 'mon' ] - 1, 1, $timestampInfo[ 'year' ] );
            $dayOfWeek = date_i18n( 'w', $thisMonth );
            # How many weeks are there in this month?
            $daysInMonth = date_i18n( 't', $thisMonth );
            $weeksInMonth = ceil( ( $daysInMonth + $dayOfWeek ) / 7 );
            ?>
            <div class="options">
                <span class="header">
                    <?php _e( 'Calendar', 'book-a-room' ); ?>
                </span>
                <div class="calNav">
                    <div class="prevMonth"><a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;roomID=<?php echo $roomID; ?>&amp;timestamp=<?php echo $prevMonth; ?>">&lt;&nbsp;<?php _e( 'Prev', 'book-a-room' ); ?></a>
                    </div>
                    <div class="curMonth">
                        <?php echo date_i18n( 'F', $thisMonth ) . ' ' . date_i18n( 'Y', $thisMonth ); ?>
                    </div>
                    <div class="nextMonth">
                        <a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;roomID=<?php echo $roomID; ?>&amp;timestamp=<?php echo $nextMonth; ?>">
                            <?php _e( 'Next', 'book-a-room' ); ?> &gt;</a>
                    </div>
                </div>
                <div id="calDisplay">
                    <div class="calWeek">
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'Su', 'book-a-room' ); ?>
                            </div>
                        </div>
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'Mo', 'book-a-room' ); ?>
                            </div>
                        </div>
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'Tu', 'book-a-room' ); ?>
                            </div>
                        </div>
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'We', 'book-a-room' ); ?>
                            </div>
                        </div>
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'Th', 'book-a-room' ); ?>
                            </div>
                        </div>
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'Fr', 'book-a-room' ); ?>
                            </div>
                        </div>
                        <div class="calCell">
                            <div class="dayHeader">
                                <?php _e( 'Sa', 'book-a-room' ); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $curDay = 1;
                    for ( $week = 0; $week < $weeksInMonth; $week++ ) {
                        ?>
                    <div class="calWeek">
                        <?php
                        for ( $day = 1; $day <= 7; $day++ ) {

                            $noday = false;
                            # days before the first of the month on the calendar
                            if ( !empty( $dayOfWeek ) ) {
                                $dayOfWeek--;
                                $noday = true;
                            }
                            # days after the last of the month on the calendar
                            if ( $curDay > $daysInMonth ) {
                                $noday = true;
                            }
                            # show blank days and continue
                            if ( $noday ) {
                                # No day
                                ?>
                        <div class="calCell">
                            <div class="calNoDay">&nbsp;</div>
                        </div>
                        <?php
                                continue;
                            }
                            # Selected day
                            $thisTimeStamp = mktime( 0, 0, 0, $timestampInfo[ 'mon' ], $curDay, $timestampInfo[ 'year' ] );
                            # is date selected?
                            if ( date_i18n( 'm-d-y', $timestamp ) == date_i18n( 'm-d-y', $thisTimeStamp ) ) {
                        ?>
                        <div class="calCell">
                            <div class="calSelectedDay">
                                <a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;roomID=<?php echo $roomID; ?>&amp;timestamp=<?php echo $thisTimeStamp; ?>">
                                    <?php echo $curDay++; ?>
                                </a>
                            </div>
                        </div>
                        <?php
                                continue;
                            }
                            $curTimeInfo = getdate( current_time( 'timestamp' ) );
                            if ( $thisTimeStamp == mktime( 0, 0, 0, $curTimeInfo[ 'mon' ], $curTimeInfo[ 'mday' ], $curTimeInfo[ 'year' ] ) ) {
                        ?>
                        <div class="calCell">
                            <div class="calToday">
                                <a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;roomID=<?php echo $roomID; ?>&amp;timestamp=<?php echo $thisTimeStamp; ?>">
                                    <?php echo $curDay++; ?>
                                </a>
                            </div>
                        </div>
                        <?php
                                continue;
                            }
                        ?>
                        <div class="calCell">
                            <div class="calContent">
                                <a href="<?php echo CHUH_BaR_Main_permalinkFix(); ?>action=reserve&amp;roomID=<?php echo $roomID; ?>&amp;timestamp=<?php echo $thisTimeStamp; ?>">
                                    <?php echo $curDay++; ?>
                                </a>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="calNav">
                <div id="calFormCont">
                    <form method="get" id="calForm">
                        <div class="calContain">
                            <select name="calMonth" id="calMonth">
                                <?php
                                $curInfo = getdate( current_time( 'timestamp' ) );
                                for ( $month = 1; $month <= 12; $month++ ) {
                                    $selected = ( $timestampInfo[ 'mon' ] == $month ) ? ' selected="selected"' : null;
                                    ?>
                                <option value="<?php echo $month; ?>" <?php echo $selected; ?>>
                                    <?php echo date_i18n( 'F', mktime( 0, 0, 0, $month, 1, $timestampInfo['year'] ) ); ?>
                                </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="calContain">
                            <select name="calYear" id="calYear">
                                <?php
                                for ( $year = $curInfo[ 'year' ] - 1; $year < $curInfo[ 'year' ] + 3; $year++ ) {
                                    $selected = ( $timestampInfo[ 'year' ] == $year ) ? ' selected="selected"' : null;
                                    ?>
                                <option value="<?php echo $year; ?>" <?php echo $selected; ?>>
                                    <?php echo $year; ?>
                                </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="calContain">
                            <input name="page_id" type="hidden" value="#page_id#"/>
                            <input name="roomID" type="hidden" value="<?php echo $roomID; ?>"/>
                            <input name="action" type="hidden" value="calDate"/>
                            <input type="submit" name="submitCal" id="submitCal" value="Go"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    $reserveBuffer = get_option( 'bookaroom_reserveBuffer' );
    $allowedBuffer = get_option( 'bookaroom_reserveAllowed' );
    ?>

    <form action="<?php echo makeLink_correctPermaLink( get_option( 'bookaroom_reservation_URL' ) ); ?>action=reserve" method="post" id="hoursForm">
        <div id="botRow">
            <div class="col1">
                <div class="instructions">
                    <span class="header">
                        <?php _e( 'Step 4.', 'book-a-room' ); ?>
                    </span>
                    <p>
                        <em>
                            <?php _e( 'Choose the hours you would like to reserve.', 'book-a-room' ); ?>
                        </em>
                    </p>
                </div>
                <div class="options">
                    <p class="header">
                        <?php _e( 'Please check the start and the end time that you would like to reserve.', 'book-a-room' ); ?>
                    </p>
                    <p>
                        <?php _e( 'Please include any set up and clean up time you need in your reservation. Any times marked Unavailable on the schedule are for staff only.', 'book-a-room' ); ?>
                    </p>
                    <?php
                    # If buffers, show the buffer message
                    if ( $allowedBuffer ) {
                        ?>
                    <div style="color: red;" stlye="clear:both;">
                        <?php printf( __( '*Rooms are available to reserve no more than %s %s from today\'s date.', 'book-a-room' ), $allowedBuffer, _n( 'day', 'days', $allowedBuffer, 'book-a-room' ) ); ?>
                    </div>
                    <?php
                    }
                    if ( $reserveBuffer ) {
                        ?>
                    <div style="color: red;" stlye="clear:both;">
                        <?php printf( __( '*Rooms are available to reserve %s %s after today\'s date.', 'book-a-room' ), $reserveBuffer, _n( 'day', 'days', $reserveBuffer, 'book-a-room' ) ); ?>
                    </div>
                    <?php
                    }
                    ?>
                    <p id="topSubmit">
                        <input type="submit" name="submitHours" id="submitHours" value="<?php _e( 'Click here when you are finished', 'book-a-room' ); ?>" onclick="checkSubmit(); return false;"/>
                        <br/>
                        <input type="reset" name="Reset" id="resetHours" value="<?php _e( 'Clear the form', 'book-a-room' ); ?>"/>
                    </p>
                </div>
            </div>
            <?php
            # get reservations
            $dayOfWeek = date_i18n( 'w', $timestamp );
            $baseIncrement = get_option( 'bookaroom_baseIncrement' );
            $cleanupIncrements = get_option( 'bookaroom_cleanupIncrement' );
            $closeTime = strtotime( date_i18n( 'Y-m-d ' . $branchList[ $branchID ][ "branchClose_{$dayOfWeek}" ], $timestamp ) );
            $closings = self::getClosings( $roomID, $timestamp, $roomContList );
            $openTime = strtotime( date_i18n( 'Y-m-d ' . $branchList[ $branchID ][ "branchOpen_{$dayOfWeek}" ], $timestamp ) );
            $reservations = self::getReservations( $roomID, $timestamp );
            $setupIncrements = get_option( 'bookaroom_setupIncrement' );
            $timeInfo = getdate( $timestamp );
            $incrementList = array();
            $increments = ( ( $closeTime - $openTime ) / 60 ) / $baseIncrement;


            if ( empty( $roomContList[ 'branch' ][ $branchID ] ) ) {
                # No rooms 
                ?>
            <div class="col2">
                <div class="options">
                    <table id="hoursTable">
                        <tr class="calHours">
                            <td class="calCheckBox">
                                <?php _e( 'Select', 'book-a-room' ); ?>
                            </td>
                            <td class="calTime">
                                <?php _e( 'Time', 'book-a-room' ); ?>
                            </td>
                            <td class="calStatus">
                                <?php _e( 'Status', 'book-a-room' ); ?>
                            </td>
                        </tr>
                        <tr class="calHours">
                            <td class="calCheckBox">&nbsp;</td>
                            <td class="calTime">
                                <?php _e( 'There are no rooms available to request at this branch.', 'book-a-room' ); ?>
                            </td>
                            <td class="calStatus">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php
            } elseif ( empty( $branchList[ $branchID ][ "branchOpen_{$dayOfWeek}" ] )or empty( $branchList[ $branchID ][ "branchClose_{$dayOfWeek}" ] ) ) {
                    # Room closed
                    ?>
            <div class="col2">
                <div class="options">
                    <table id="hoursTable">
                        <tr class="calHours">
                            <td class="calCheckBox">Select </td>
                            <td class="calTime">Time</td>
                            <td class="calStatus">Status</td>
                        </tr>
                        <tr class="calHours">
                            <td class="calCheckBox">&nbsp;</td>
                            <td class="calTime">This branch isn't open today.</td>
                            <td class="calStatus">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php
            } else {
                # regular day
                ?>
            <div class="col2">
                <div class="options">
                    <table id="hoursTable">
                        <tr class="calHours">
                            <td class="calCheckBox">
                                <?php _e( 'Select', 'book-a-room' ); ?>
                            </td>
                            <td class="calTime">
                                <?php _e( 'Time', 'book-a-room' ); ?>
                            </td>
                            <td class="calStatus">
                                <?php _e( 'Status', 'book-a-room' ); ?>
                            </td>
                        </tr>
                        <?php
                $count = 1;
                for ( $i = 0; $i < $increments; $i++ ) {
                    #if( $count++ > 50) wp_die( 'loop' );
                    # find increment offset  from start
                    
                    $curStart = $openTime + ( $baseIncrement * 60 * $i );
                    echo 'This is curStart: ' .  $curStart; 
                    $curEnd = $openTime + ( $baseIncrement * 60 * ( $i + 1 ) );
                    #find the time difference 
                    $diff = abs(strtotime($curStart) - strtotime($timeInfo));
                    $tmins = $diff/60;
                    $mins = $tmins%60; 
                    if( $mins < 30 ){
                        echo 'Error - cannot book room 30 minutes beforehand';
                    }
                    if ( $curEnd > $closeTime ) {
                        $curEnd = $closeTime;
                    }
                    # last line?
                    if ( $i + $cleanupIncrements >= $increments ) {
                        $incrementList[ $i ][ 'type' ] = 'last';
                    } else {
                        if ( empty( $reservations ) ) {
                            if ( $curStart < current_time( 'timestamp' ) ) {
                                $incrementList[ $i ][ 'type' ] = 'unavailable';
                            } else {
                                $incrementList[ $i ][ 'type' ] = 'regular';
                            }
                        } else {
                            foreach ( $reservations as $resKey => $resVal ) {
                                $resVal[ 'timestampStart' ] = strtotime( $resVal[ 'ti_startTime' ] );
                                $resVal[ 'timestampEnd' ] = strtotime( $resVal[ 'ti_endTime' ] );
                                # check if increment time is equal to or after start and before end
                                if ( $curStart >= $resVal[ 'timestampStart' ]and $curEnd <= $resVal[ 'timestampEnd' ] ) {
                                    $incrementList[ $i ][ 'type' ] = 'reserved';
                                    # show by type
                                    if ( $resVal[ 'ti_type' ] == 'event' ) {
                                        $incrementList[ $i ][ 'desc' ] = $resVal[ 'ev_title' ];
                                    } else {
                                        $incrementList[ $i ][ 'desc' ] = $resVal[ 'me_eventName' ];
                                    }
                                    if ( $curStart == $resVal[ 'timestampStart' ] ) {
                                        # This adds unavailable slots before each reservation if there are setup increments
                                        if ( ( int )$cleanupIncrements !== 0 ) {
                                            $incrementList[ $i - 1 ][ 'type' ] = 'unavailable';
                                        }
                                        # setup time
                                        for ( $s = $i - 1; $s > ( $i - 1 - $setupIncrements ); $s-- ) {
                                            if ( !empty( $incrementList[ $s ][ 'type' ] )and $incrementList[ $s ][ 'type' ] !== 'reserved' ) {
                                                $incrementList[ $s ][ 'type' ] = 'setup';
                                            }
                                        }
                                    }
                                    #cleanup time
                                    if ( $curEnd == $resVal[ 'timestampEnd' ] ) {
                                        for ( $s = $i + 1; $s < ( $i + 1 + $cleanupIncrements ); $s++ ) {
                                            $incrementList[ $s ][ 'type' ] = 'setup';
                                        }
                                    }
                                } else {
                                    $validStart = strtotime( date_i18n( 'Y-m-d' ) ) + ( get_option( 'bookaroom_reserveBuffer' ) * 24 * 60 * 60 );
                                    $validEnd = $validStart + ( get_option( 'bookaroom_reserveAllowed' ) * 24 * 60 * 60 ); #reme();
                                    $startTime = strtotime( $resVal[ 'ti_startTime' ] );
                                    $endTime = strtotime( $resVal[ 'ti_endTime' ] );
                                    
                                    $admin = current_user_can( 'activate_plugins' );
                                    if ( ( ( $startTime <= $validStart || $endTime <= $validStart ) && empty( $res_id ) && $admin == false ) || ( ( $startTime >= $validEnd || $endTime >= $validEnd ) && empty( $res_id ) && $admin == false ) ) {
                                        $incrementList[ $i ][ 'type' ] = 'unavailable';
                                    }
                                    if ( empty( $incrementList[ $i ][ 'type' ] ) ) {
                                        $incrementList[ $i ][ 'type' ] = 'regular';
                                    }
                                }
                            }
                        }
                    }
                }
                for ( $i = 0; $i < $increments; $i++ ) {
                    $curStart = $openTime + ( $baseIncrement * 60 * $i );
                    $curEnd = $openTime + ( $baseIncrement * 60 * ( $i + 1 ) );
                    
                    if ( $curEnd > $closeTime ) {
                        $curEnd = $closeTime;
                    }
                    if ( $closings !== false ) {
                        #Closed
                        ?>
                        <tr class="calHoursReserved">
                            <td class="calCheckBox"><input id="hours_<?php echo $i; ?>" name="hours[]" type="hidden" value="" onchange="checkHours()" disabled="disabled"/>
                            </td>
                            <td class="calTime">
                                <?php echo date_i18n( 'g:i a', $curStart ) .' - '. date_i18n( 'g:i a', $curEnd ); ?>
                            </td>
                            <td class="calStatus">
                                <?php _e( 'Closed', 'book-a-room' ); ?>
                            </td>
                        </tr>
                        <?php
                        continue;
                    } else {
                        switch ( $incrementList[ $i ][ 'type' ] ) {
                            case 'setup':
                                # Setup 
                        ?>
                        <tr class="calHoursSetup" style="background: <?php echo get_option( 'bookaroom_setupColor' ); ?>; color: <?php echo get_option( 'bookaroom_setupFont' ); ?>">
                            <td class="calCheckBox"><input id="hours_<?php echo $i; ?>" name="hours[]" type="hidden" value="" onchange="checkHours()"/>
                            </td>
                            <td class="calTime">
                                <?php echo date_i18n( 'g:i a', $curStart ) .' - '. date_i18n( 'g:i a', $curEnd ); ?>
                            </td>
                            <td class="calStatus">&nbsp;</td>
                        </tr>
                        <?php
                                break;
                                
                            case 'reserved':
                                # Reserved
                        ?>
                        <tr class="calHoursReserved" style="background: <?php echo get_option( 'bookaroom_reservedColor' ); ?>; color: <?php echo get_option( 'bookaroom_reservedFont' ); ?>">
                            <td class="calCheckBox"><input id="hours_<?php echo $i; ?>" name="hours[]" type="hidden" value="" onchange="checkHours()"/>
                            </td>
                            <td class="calTime">
                                <?php echo date_i18n( 'g:i a', $curStart ) .' - '. date_i18n( 'g:i a', $curEnd ); ?>
                            </td>
                            <td class="calStatus">
                                <?php echo htmlspecialchars_decode( $incrementList[$i]['desc'] ); ?>
                            </td>
                        </tr>
                        <?php
                                break;
                            
                            case 'last':
                                # Last line
                        ?>
                        <tr class="calHoursReserved">
                            <td class="calCheckBox"><input id="hours_<?php echo $i; ?>" name="hours[]" type="hidden" value="" onchange="checkHours()"/>
                            </td>
                            <td class="calTime">
                                <?php echo date_i18n( 'g:i a', $curStart ) .' - '. date_i18n( 'g:i a', $curEnd ); ?>
                            </td>
                            <td class="calStatus">&nbsp;</td>
                        </tr>
                        <?php
                                break;
                            
                            case 'unavailable':
                                # Unavailable
                        ?>
                        <tr class="calHoursReserved">
                            <td class="calCheckBox"><input id="hours_<?php echo $i; ?>" name="hours[]" type="hidden" value="" onchange="checkHours()" disabled="disabled"/>
                            </td>
                            <td class="calTime">
                                <?php echo date_i18n( 'g:i a', $curStart ) .' - '. date_i18n( 'g:i a', $curEnd ); ?>
                            </td>
                            <td class="calStatus">
                                <?php _e( 'Unavailable*', 'book-a-room' ); ?>
                            </td>
                        </tr>
                        <?php
                                break;
                            
                            case 'regular':
                            default:
                                # Regular
                        ?>
                        <tr class="calHours">
                            <td class="calCheckBox"><label for="hours_<?php echo $i; ?>">
					  				<input id="hours_<?php echo $i; ?>" name="hours[]" type="checkbox" value="<?php echo $curStart; ?>" onchange="checkHours('<?php echo $i; ?>')" /></label>
                            
                            </td>
                            <td class="calTime">
                                <label for="hours_<?php echo $i; ?>">
                                    <?php echo date_i18n( 'g:i a', $curStart ) .' - '. date_i18n( 'g:i a', $curEnd ); ?>
                                </label>
                            </td>
                            <td class="calStatus">
                                <label for="hours_<?php echo $i; ?>">
                                    <?php _e( 'Open', 'book-a-room' ); ?>
                                </label>
                            </td>
                        </tr>
                        <?php
                                break;
                        }
                    }
                }
                        ?>
                    </table>
                </div>
            </div>
            <div id="botSubmit">
                <div class="col1">
                    <div class="instructions">
                        <span class="header">
                            <?php _e( 'Step 4. continued', 'book-a-room' ); ?>
                        </span>
                        <p>
                            <em>
                                <?php _e( 'Choose the hours you would like to reserve.', 'book-a-room' ); ?>
                            </em>
                        </p>
                    </div>
                    <div class="options">
                        <p>
                            <input name="action" type="hidden" id="action" value="fillForm"/>
                            <input name="roomID" type="hidden" id="roomID" value="<?php echo $roomID; ?>"/>
                            <input type="submit" name="submitHours" id="submitHours" value="<?php _e( 'Click here when you are finished', 'book-a-room' ); ?>" onclick="checkSubmit(); return false;"/>
                            <br/>
                            <input type="reset" name="Reset" id="resetHours" value="<?php _e( 'Clear the form', 'book-a-room' ); ?>"/>
                        </p>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </form>
</div>