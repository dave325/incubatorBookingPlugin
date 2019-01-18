<script type="text/javascript" src="<?php echo plugins_url( ); ?>/book-a-room/scripts/zebra-dialog/javascript/zebra_dialog.js"></script>
<link rel="stylesheet" href="<?php echo plugins_url( ); ?>/book-a-room/scripts/zebra-dialog/css/default/zebra_dialog.css" type="text/css">
<script>
<?php 
$showContract = ( get_option( "bookaroom_hide_contract" ) == true ) ? '1' : '0';
?>
	if ( true != <?php echo $showContract; ?> ) {
		jQuery( document ).ready( function () {
			jQuery( 'a.reservelink' ).click( function ( ev ) {
				var realHeight = parseInt( jQuery( window ).height() * .6 );
				var realWidth = parseInt( jQuery( window ).width() * .7 );
				var realTop = parseInt( jQuery( window ).height() * .09 );
				ev.preventDefault();
				var addressValue = jQuery( this ).attr( "href" );
				jQuery.Zebra_Dialog( <?php echo json_encode( get_option( 'bookaroom_content_contract' ) ); ?>, {
					'type': 'question',
					'width': realWidth,
					'max_height': realHeight,
					'position': [ 'center', 'top + ' + realTop ],
					'buttons': [ {
						caption: 'Accept',
						callback: function () {
							window.location.href = addressValue
						}
					}, {
						caption: 'No'
					}, ],
				} );
			} );
		} );
	}
</script>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div id="bookaroom_main_container">
    <h2>
        <?php _e( 'Meeting Rooms by Branch', 'book-a-room' ); ?>
    </h2>
    <?php
    if ( empty( $branchList ) || count( $branchList ) < 1 ) {
        # No branches
        _e( 'There are currently no branches available to reserve a room.', 'book-a-room' ); 
    } else {
        $linkList = array();
        foreach( $branchList as $key => $val ) {
            $linkList[$val['branchDesc']] = '<a href="#branch-'.$val['branchID'].'">'.$val['branchDesc'].'</a>';
        }
        echo '<p>'.implode( ' | ', $linkList ).'</p>';
        
        foreach( $branchList as $key => $val ) {
    ?>
    <a name="<?php echo 'branch-'.$val['branchID']; ?>" id="<?php echo 'branch-'.$val['branchID']; ?>"></a>
    <?php
            if( empty( $val['branchImageURL'] ) ) {
                $style = NULL;
            } else {
                $style = ' style="background-image: url('.$val['branchImageURL'].'); background-position: right; background-repeat: no-repeat;"';
            }
    ?>
    <div class="branchContainer"<?php echo $style; ?>>
        <div class="branchTitle"><?php echo $val['branchDesc']; ?></div>
        <div class="branchAddress"><?php echo nl2br( $val['branchAddress'] ); ?></div>
        <div class="branchLinks">
            <div class="map"><a href="<?php echo $val['branchMapLink']; ?>" target="_blank"><?php _e( 'View on Map', 'book-a-room' ); ?></a>
            </div>
        </div>
    </div>
    <div class="branchRoomsContainer">
        <?php
            if( empty( $roomContList ) || empty( $roomContList['branch'][$key] ) ) {
        ?>
        <div class="branchRoom">
            <div class="roomColWide"><?php _e( 'There are no rooms available at this branch.', 'book-a-room' ); ?></div>
        </div>
        <?php
            } else {
                # check each container for rooms
                foreach( $roomContList['branch'][$key] as $tinyKey ) {
                    $roomsInCont = $roomContList['id'][$tinyKey]['rooms'];
                    
                    if( count( $roomsInCont ) < 1 ) {
                        continue;
                    }
                    
                    foreach( $roomContList['id'][$tinyKey]['rooms'] as $aKey ) {
                        $roomAmenities = array();
                        foreach( $roomContList['id'][$tinyKey]['rooms'] as $aKey ) {
                            if( !empty( $roomList['id'][$aKey]['amenity'] ) ) {
                                $roomAmenities +=  $roomList['id'][$aKey]['amenity'] ;
                            }
                        }
                        if( count( $roomAmenities ) == 0 ) {
                            $amenities = __( 'None', 'book-a-rooom' ); 
                        } else {
                            $roomAmenities = array_unique( $roomAmenities );
                            foreach( $roomAmenities as $afKey => &$afVal ) {
                                if( !empty( $amenityList[$afVal] ) ) {
                                    $afVal = $amenityList[$afVal];
                                } else {
                                    unset( $roomAmenities[$afKey] );
                                }
                            }
                            array_filter( $roomAmenities );
                            asort( $roomAmenities );
                            $amenities = implode( '; ', $roomAmenities );
                        }
                    }
        ?>
        <div class="branchRoom">
            <div class="roomColWide">
                <h4><?php echo $roomContList['id'][$tinyKey]['desc']; ?></h4>
            </div>
            <div class="roomCol1"><strong><?php _e( 'Capacity', 'book-a-room' ); ?></strong><br/><?php printf( __( '%s people', 'book-a-room' ), $roomContList['id'][$tinyKey]['occupancy'] ); ?></div>
            <div class="roomCol2"><strong><?php _e( 'Amenities', 'book-a-room' ); ?></strong><br/><?php echo $amenities; ?></div>
            <div class="roomCol3"><strong><a class="reservelink" href="<?php echo makeLink_correctPermaLink( get_option( 'bookaroom_reservation_URL' ) ); ?>action=reserve&amp;roomID=<?php echo $tinyKey; ?>"><?php _e( 'Reserve', 'book-a-room' ); ?></a></strong>
            </div>
                <?php
                    if( false == $branchList[$roomContList['id'][$tinyKey]['branchID']]['branch_isSocial'] and true == $branchList[$roomContList['id'][$tinyKey]['branchID']]['branch_showSocial']) {
            ?><div class="branchSocial"><?php _e( 'No social gatherings are permitted at this branch.', 'book-a-room' ); ?></div><?php
                    }
            ?>
        </div>
        <?php
                }
            }
        ?>
    </div>
    <?php
        }
    }
    ?>
</div>