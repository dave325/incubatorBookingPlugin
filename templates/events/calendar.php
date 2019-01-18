<?php
# check for a valid timestamp, if not use current time.
if (	empty( $timestampRaw ) 
		or FALSE == ( ( string )( int )$timestampRaw === $timestampRaw ) 
		and ( $timestampRaw <= PHP_INT_MAX ) 
		and ( $timestampRaw >= ~PHP_INT_MAX ) ) {
	$timestampRaw = time();
}
# get the event list
$monthEvents = self::getMonthEvents( $timestampRaw, $filter, $age, $category );
#timestamp
$timestampArr = getdate( $timestampRaw );
$timestamp = mktime( 0, 0, 0, $timestampArr[ 'mon' ], 1, $timestampArr[ 'year' ] );
# previous year
$prevYearRaw = mktime( 0, 0, 0, 1, 1, $timestampArr[ 'year' ] - 1 );
$prevYearDisp = date( 'Y', $prevYearRaw );
# next year
$nextYearRaw = mktime( 0, 0, 0, 1, 1, $timestampArr[ 'year' ] + 1 );
$nextYearDisp = date( 'Y', $nextYearRaw );
# current year
$curYearDisp = date( 'Y', $timestamp );
# current month
$curMonthDisp = date( 'F', $timestamp );
# the real current month, regardless of timestamp
$realCurMonth = $timestampArr[ 'mon' ];

# find how many weeks in the month.
# days in month
$daysInMonth = date( 't', $timestamp );
# add offset
$dayOffset = date( 'w', $timestamp );
$weeks = ceil( ( $daysInMonth + $dayOffset ) / 7 );

# get basic infos for branch name
$roomContList = bookaroom_settings_roomConts::getRoomContList();
$roomList = bookaroom_settings_rooms::getRoomList();
$branchList = bookaroom_settings_branches::getBranchList( TRUE );

?>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/calendar.css" rel="stylesheet" type="text/css"/>
<div class=wrap id="top">
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room - View Staff Events', 'book-a-room' ); ?>
	</h2>
</div>
<script language="javascript">
	$( function () {
		// Setup date drops
		$( '#startDate, #endDate' ).datepicker( {
			dateFormat: 'mm/dd/yy'
		} );
	} );
</script>
<div id="monthWrapper">
	<div class="monthTitle">
		<?php 
	  printf( __( 'Choose a month  [%s %s]', 'book-a-room' ), date( 'F', $timestamp ), date( 'Y', $timestamp ) ); ?>
	</div>
	<div class="monthDisp"><a href="?page=bookaroom_event_management_staff&timestamp=<?php echo $prevYearRaw; ?>">&lt;&lt;<?php echo $prevYearDisp; ?></a>
	</div>


	<?php
	# display top bar of months in the current year
	for ( $m = 1; $m <= 12; $m++ ) {
		$curMonthTimestamp = mktime( 0, 0, 0, $m, 1, $timestampArr[ 'year' ] );
		$curMonthDisp = date( 'M', $curMonthTimestamp );
		# check if this is the right month
		if ( $timestampArr[ 'mon' ] == $m ) {
		?>
	<div class="monthDisp selected">
		<?php echo $curMonthDisp; ?>
	</div>
	<?php
		} else {
		# for link, add link value
		?>
	<div class="monthDisp">
		<a href="?page=bookaroom_event_management_staff&timestamp=<?php echo $curMonthTimestamp; ?>">
			<?php echo $curMonthDisp; ?>
		</a>
	</div>
	<?php
		}
	}
	?>
	<div class="monthDisp">
		<a href="?page=bookaroom_event_management_staff&timestamp=<?php echo $nextYearRaw; ?>">
			<?php echo $nextYearDisp; ?> &gt;&gt;</a>
	</div>
</div>
<div id="calEventWrapper">
	<?php
	for ( $day = 1; $day <= $daysInMonth; $day++ ) {
		?>
	<div class="calSingleDateWrapper" name="date_<?php echo $day; ?>" id="date_<?php echo $day; ?>">
		<div class="calDateBorder">
			<div class="calDateWrapper">
				<div class="calDateDayName">
					<?php
					$dateTimestamp = mktime( 0, 0, 0, $timestampArr[ 'mon' ], $day, $timestampArr[ 'year' ] );
					echo date( 'l', $dateTimestamp )
					?>
				</div>
				<div class="calDateDayDate">
					<?php echo date( 'F jS', $dateTimestamp );?>
				</div>
				<div class="miniCalContainer">
					<div class="calWeek">
						<div class="calDay calHeader"><?php _e( 'Su', 'book-a-room' ); ?></div>
						<div class="calDay calHeader"><?php _e( 'Mo', 'book-a-room' ); ?></div>
						<div class="calDay calHeader"><?php _e( 'Tu', 'book-a-room' ); ?></div>
						<div class="calDay calHeader"><?php _e( 'We', 'book-a-room' ); ?></div>
						<div class="calDay calHeader"><?php _e( 'Th', 'book-a-room' ); ?></div>
						<div class="calDay calHeader"><?php _e( 'Fr', 'book-a-room' ); ?></div>
						<div class="calDay calHeader"><?php _e( 'Sa', 'book-a-room' ); ?></div>
					</div>
					<?php
					$count = 1;
					for ( $w = 1; $w <= $weeks; $w++ ) {
					?><div class="calWeek"><?php
						switch ( $w ) {
							case 1:
								for ( $d = 0; $d <= 6; $d++ ) {
									if ( $d < $dayOffset ) {
					?><div class="calDay noDay"></div><?php
									} else  {
										$disp = $count++;
										if ( $timestampArr[ 'mday' ] == $count ) {
											$class = ' selDay';
										} else  {
											$class = NULL;
										}
					?><div class="calDay<?php echo $class; ?>"><a href="#date_<?php echo $disp; ?>"><?php echo $disp; ?></a></div><?php
									}
								}
								$endDay = 7 - $dayOffset;
								$nextStart = $endDay + 1;
								break;
							default:
								$startDay = $nextStart;
								$endDay = $startDay + 6;
								$nextStart = $endDay + 1;
								for ( $d = $startDay; $d <= $startDay + 6; $d++ ) {
									if ( $timestampArr[ 'mday' ] == $count ) {
										$class = ' selDay';
									} else  {
										$class = null;
									}
					?><div class="calDay<?php echo $class; ?>"><a href="#date_<?php echo $count; ?>"><?php echo $count; ?></a></div><?php
							$count++;
								}
								break;
							case $weeks:
								$weekLeft = 7;
								for ( $d = $count; $d <= $daysInMonth; $d++ ) {
									if ( $timestampArr[ 'mday' ] == $count ) {
										$class = ' selDay';
									} else  {
										$class = null;
									}
									$count++;
									$weekLeft--;
					?><div class="calDay<?php echo $class; ?>"><a href="#date_<?php echo $count; ?>"><?php echo $count; ?></a></div><?php
								}
								for ( $d = 1; $d <= $weekLeft; $d++ ) {
					?><div class="calDay noDay"></div><?php
								}
							$startDay = $nextStart;
							$endDay = $daysInMonth;
							break;
						}
						?>
					</div>
					<?php
					}
					?>
					</div>
					<div class="calDateTop"><a href="#top"><?php _e( 'Back to the top', 'book-a-room' ); ?></a></div>
				</div>
				<div class="calEventListWrapper">
					<div class="calEventListContents">
					<?php

					if ( empty( $monthEvents[ $day ] ) ) {
						?>
						<div class="calEventItemTime"><?php _e( 'There are no events today.' , 'book-a-room' ); ?></div>
					<?php
					} else {
						foreach( $monthEvents[$day] as $eventNow ) {
							$startTime = date( 'g:i a', strtotime( $eventNow['ti_startTime'] ) );
							$endTime = date( 'g:i a', strtotime( $eventNow['ti_endTime'] ) );
							if( $startTime == '12:00 am' ) {
								$times = __( 'All Day', 'book-a-room' );
							} else {
								$times = $startTime.' - '.$endTime;
							}
							if( empty( $eventNow['ti_extraInfo'] ) ) {
								$extraInfo = NULL;
							} else {
								$extraInfo = '<br />'.$eventNow['ti_extraInfo'];
							}
							# branch and room
							if( !empty( $eventNow['ti_noLocation_branch'] ) ) {
								$branch = $branchList[$eventNow['ti_noLocation_branch']]['branchDesc'];
							} else {
								$branch = $branchList[$roomContList['id'][$eventNow['ti_roomID']]['branchID']]['branchDesc'];
							}
						?><div class="calEventItemName"><a href="?page=bookaroom_event_management_staff&action=viewEvent&amp;eventID=<?php echo $eventNow['ti_id'];  ?>" target="_blank"><?php echo $eventNow['ev_title']; ?></a></div>
						<div class="calEventItemTime"><?php echo $times; ?></div>
						<div class="calEventItemBranch"><?php echo $branch; ?></div>
						<div class="calEventItemDesc"><?php echo $eventNow['ev_desc'] . $extraInfo; ?></div>
						<div class="calEventItemLink"><a href="?page=bookaroom_event_management_staff&action=viewEvent&amp;eventID=<?php echo $eventNow['ti_id']; ?>" target="_blank"><?php _e( 'View the even\'s details.', 'book-a-room' ); ?></a></div><br><?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
	}
	?>
</div>