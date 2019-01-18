<?php
if ( empty( $helpID ) ) {
	$helpID = 'emailHelp_' . time();
}
?>
<div class="emailForm">
	<div id="<?php echo $helpID; ?>" class="emailHelpMain" style="display: none">
		<div class="emailHelp">
			<div class="helpRow_left">
				<div class="helpRow">
					<div><?php _e( 'Item', 'book-a-room' ); ?></div>
					<div><?php _e( 'Variable', 'book-a-room' ); ?></div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Address', 'book-a-room' ); ?></div>
					<div>{contactAddress1}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Address 2', 'book-a-room' ); ?></div>
					<div>{contactAddress2}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Amenities', 'book-a-room' ); ?></div>
					<div>{amenity}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Branch Name', 'book-a-room' ); ?></div>
					<div>{branchName}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'City', 'book-a-room' ); ?></div>
					<div>{contactCity}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Contact Name', 'book-a-room' ); ?></div>
					<div>{contactName}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Date', 'book-a-room' ); ?></div>
					<div>{date}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Deposit Profit', 'book-a-room' ); ?></div>
					<div>{costDepositProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Deposit Nonprofit', 'book-a-room' ); ?></div>
					<div>{costDepositNonProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Content', 'book-a-room' ); ?></div>
					<div>{contactContent}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'End Time', 'book-a-room' ); ?></div>
					<div>{endTime}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Event Name', 'book-a-room' ); ?></div>
					<div>{eventName}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Number of Attendees', 'book-a-room' ); ?></div>
					<div>{numAttend}</div>
				</div>
			</div>
			<div class="helpRow_right">
				<div class="helpRow">
					<div><?php _e( 'Item', 'book-a-room' ); ?></div>
					<div>Variable</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Nonprofit', 'book-a-room' ); ?></div>
					<div>{nonProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Primary Phone', 'book-a-room' ); ?></div>
					<div>{contactPhonePrimary}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Purpose', 'book-a-room' ); ?></div>
					<div>{desc}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Room Cost Profit', 'book-a-room' ); ?></div>
					<div>{costRoomProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Room Cost Nonprofit', 'book-a-room' ); ?></div>
					<div>{costRoomNonProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Room Name', 'book-a-room' ); ?></div>
					<div>{roomName}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Secondary Phone', 'book-a-room' ); ?></div>
					<div>{contactPhoneSecondary}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Start Time', 'book-a-room' ); ?></div>
					<div>{startTime}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'State', 'book-a-room' ); ?></div>
					<div>{contactState}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Total Cost Profit', 'book-a-room' ); ?></div>
					<div>{costTotalProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Total Cost Nonprofit', 'book-a-room' ); ?></div>
					<div>{costTotalNonProfit}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Website', 'book-a-room' ); ?></div>
					<div>{contactWebsite}</div>
				</div>
				<div class="helpRow">
					<div><?php _e( 'Zip', 'book-a-room' ); ?></div>
					<div>{contactZip}</div>
				</div>
			</div>
		</div>
	</div>
</div>