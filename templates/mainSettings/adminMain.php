<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Main Settings', 'book-a-room' ); ?>
	</h2>
</div>
<h2>
	<?php _e( 'Main Settings', 'book-a-room' ); ?>
</h2>
<p>
	<?php _e( 'Please see the bottom of the page for simple instructions and a key to each setting.', 'book-a-room' ); ?>
</p>
<?php
# Display Errors if there are any
if ( !empty( $errorMSG ) ) {
	?>
	<p>
		<h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3>
	</p>
	<?php
}
?>
<form id="form1" name="form1" method="post" action="?page=bookaroom_Settings">
	<table class="tableMain">
		<tr>
			<td nowrap="nowrap">
				<?php _e( 'Description', 'book-a-room' ); ?>
			</td>
			<td>
				<?php _e( 'Setting', 'book-a-room' ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_baseIncrement">
					<?php _e( 'Base increment in minutes', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_baseIncrement" type="text" id="bookaroom_baseIncrement" value="<?php echo $options['bookaroom_baseIncrement']; ?>" size="4" maxlength="4" title="test"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_setupIncrement">
					<?php _e( 'Setup increments', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_setupIncrement" type="text" id="bookaroom_setupIncrement" value="<?php echo $options['bookaroom_setupIncrement']; ?>" size="4" maxlength="4"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_cleanupIncrement">
					<?php _e( 'Cleanup increments', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_cleanupIncrement" type="text" id="bookaroom_cleanupIncrement" value="<?php echo $options['bookaroom_cleanupIncrement']; ?>" size="4" maxlength="4"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="bookaroom_reservation_URL">
					<?php _e( 'URL for reservations page', 'book-a-room' ); ?><br/>
					<em>
						<?php _e( '(Please see instructions for more information)', 'book-a-room' ); ?>
					</em>
				</label>
			</td>
			<td><input name="bookaroom_reservation_URL" type="text" id="bookaroom_reservation_URL" value="<?php echo $options['bookaroom_reservation_URL']; ?>" size="48" maxlength="255"/>
			</td>
		</tr>
		<!--<tr>
			<td>
				<label for="bookaroom_paymentLink">
					< ?php _e( 'URL for payment link', 'book-a-room' ); ? ><br/>
					<em>
						< ?php _e( 'Enter the FULL URL including http: or https:', 'book-a-room' ); ? >
					</em>
				</label>
			</td>
			<td><input name="bookaroom_paymentLink" type="text" id="bookaroom_paymentLink" value="< ?php echo $options['bookaroom_paymentLink']; ? >" size="48" maxlength="255"/>
			</td>
		</tr>-->
		<tr>
			<td nowrap="nowrap">
				<?php _e( 'URL for event calendar', 'book-a-room' ); ?><br/>
				<em>
					<?php _e( 'Leave empty if on same WP site', 'book-a-room' ); ?>
				</em>
			</td>
			<td><input name="bookaroom_eventLink" type="text" id="bookaroom_eventLink" value="<?php echo $options['bookaroom_eventLink']; ?>" size="48" maxlength="255"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_setupColor">
					<?php _e( 'Background color for Setup', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_setupColor" type="text" id="bookaroom_setupColor" value="<?php echo $options['bookaroom_setupColor']; ?>" size="7" maxlength="7"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_setupFont">
					<?php _e( 'Font color for Setup', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_setupFont" type="text" id="bookaroom_setupFont" value="<?php echo $options['bookaroom_setupFont']; ?>" size="7" maxlength="7"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_reservedColor">
					<?php _e( 'Background color for Reserved', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_reservedColor" type="text" id="bookaroom_reservedColor" value="<?php echo $options['bookaroom_reservedColor']; ?>" size="7" maxlength="7"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_reservedFont">
					<?php _e( 'Font color for Reserved', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_reservedFont" type="text" id="bookaroom_reservedFont" value="<?php echo $options['bookaroom_reservedFont']; ?>" size="7" maxlength="7"/>
			</td>
		</tr>
		<!--<tr>
			<td nowrap="nowrap">
				< ?php _e( 'Days before meeting to send reminders', 'book-a-room' ); ? >
			</td>
			<td><input name="bookaroom_daysBeforeRemind" type="text" id="bookaroom_daysBeforeRemind" value="< ?php echo $options['bookaroom_daysBeforeRemind']; ? >" size="7" maxlength="7"/>
			</td>
		</tr>-->
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_reserveBuffer">
					<?php _e( 'Days Buffer for Reserve', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_reserveBuffer" type="text" id="bookaroom_reserveBuffer" value="<?php echo $options['bookaroom_reserveBuffer']; ?>" size="7" maxlength="7"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_reserveAllow">
					<?php _e( 'Days Allowed to Reserve', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_reserveAllowed" type="text" id="bookaroom_reserveAllowed" value="<?php echo $options['bookaroom_reserveAllowed']; ?>" size="7" maxlength="7"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<label for="bookaroom_defaultEmailDaily">
					<?php _e( 'Default Email for Daily Reservations', 'book-a-room' ); ?>
				</label>
			</td>
			<td><input name="bookaroom_defaultEmailDaily" type="text" id="bookaroom_defaultEmailDaily" value="<?php echo $options['bookaroom_defaultEmailDaily']; ?>" size="32" maxlength="254"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<?php _e( 'For Profit Room Deposit', 'book-a-room' ); ?>
			</td>
			<td>$
				<input name="bookaroom_profitDeposit" type="text" id="bookaroom_profitDeposit" value="<?php echo $options['bookaroom_profitDeposit']; ?>" size="5" maxlength="5"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<?php _e( 'Non-profit Room Deposit', 'book-a-room' ); ?>
			</td>
			<td>$
				<input name="bookaroom_nonProfitDeposit" type="text" id="bookaroom_nonProfitDeposit" value="<?php echo $options['bookaroom_nonProfitDeposit']; ?>" size="5" maxlength="5"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<?php _e( 'For Profit price per increment', 'book-a-room' ); ?>
			</td>
			<td>$
				<input name="bookaroom_profitIncrementPrice" type="text" id="bookaroom_profitIncrementPrice" value="<?php echo $options['bookaroom_profitIncrementPrice']; ?>" size="5" maxlength="5"/>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
				<?php _e( 'Non-profit price per increment', 'book-a-room' ); ?>
			</td>
			<td>$
				<input name="bookaroom_nonProfitIncrementPrice" type="text" id="bookaroom_nonProfitIncrementPrice" value="<?php echo $options['bookaroom_nonProfitIncrementPrice']; ?>" size="5" maxlength="5"/>
			</td>
			<tr>
				<td nowrap="nowrap">
					<?php _e( 'Waiting list default', 'book-a-room' ); ?>
				</td>
				<td><input name="bookaroom_waitingListDefault" type="text" id="bookaroom_waitingListDefault" value="<?php echo $options['bookaroom_waitingListDefault']; ?>" size="5" maxlength="5"/>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap">
					<?php _e( 'Hide Meeting Room Contract', 'book-a-room' ); ?>
				</td>
				<td><input name="bookaroom_hide_contract" type="checkbox" id="bookaroom_hide_contract" value="TRUE" <?php echo ( empty( $options[ 'bookaroom_hide_contract'] ) ) ? NULL : ' checked="checked"'; ?>>
				<?php _e( 'Check to hide the contract.', 'book-a-room' ); ?></td>
			</tr>
			<tr>
				<td nowrap="nowrap">
					<?php _e( 'Hide Public Event Names', 'book-a-room' ); ?>
				</td>
				<td><input name="bookaroom_obfuscatePublicNames" type="checkbox" id="bookaroom_obfuscatePublicNames" value="TRUE" <?php echo ( empty( $options[ 'bookaroom_obfuscatePublicNames'] ) ) ? NULL : ' checked="checked"'; ?>>
					<?php _e( 'Public Events Show as "In Use"', 'book-a-room' ); ?>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap">
					<?php _e( 'Screen Width', 'book-a-room' ); ?>
				</td>
				<td>
					<select name="bookaroom_screenWidth" id="bookaroom_screenWidth">
						<?php
						$screenWidthOptions = array( 0 => 'Responsive/Wide', 1 => 'Narrow' );
						foreach ( $screenWidthOptions as $key => $val ) {
							$selected = NULL;
							if ( $options[ 'bookaroom_screenWidth' ] == $key ) {
								$selected = ' selected="selected"';
							}
							?>
						<option value="<?php echo $key;  ?>" <?php echo $selected; ?>>
							<?php echo $val;  ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap">
					<?php _e( 'Library Card Regex', 'book-a-room' ); ?>
				</td>
				<td><input name="bookaroom_libcardRegex" type="text" id="bookaroom_libcardRegex" value="<?php echo $options['bookaroom_libcardRegex']; ?>" size="48"/>
				</td>
			</tr>
			<tr>
				<td colspan="4" nowrap="nowrap"><input name="action" type="hidden" value="updateSettings"/>
					<input type="submit" name="button" id="button" <?php _e( 'Submit', 'book-a-room' ); ?>/>
				</td>
			</tr>
	</table>
</form>
<h3>
	<?PHP _e( 'Instructions & Key', 'book-a-room' ); ?>
</h3>
<table width="0" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Base increment', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'Specify how many minutes are used per scheduling increment.' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Setup/Cleanup increments', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'Add a buffer, in increments, to the beginning and/or end of each meeting request. If you have both a setup and cleanup specified, you may have a large amount of time between meetings, so you may want to choose one or the other.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Font/Background colors', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'Set the colors for reserved and setup/cleaning slots on the signup calendar.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Days Buffer for Reserve', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'The number of days, from today, that people can reserve. A zero would mean customers could reserve a room on today\'s date. A two would mean customers couldn\'t make a reservation sooner than two days from today\'s date.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Days Allowed to Reserve', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'The number of days, after today, that people can reserve. A ninety would mean customers couldn\'t make a reservation later than ninety days from today\'s date.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Default Email for Daily Reservations', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'Enter the email that the reservations will come from. This should be the person or email group that will get responses via replies to the email alerts.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'For/Non-Profit Room Deposits', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'Enter the amount that needs to be deposited for the reservation.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'For/Non-Profit price per increment', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'Enter the amount that each increment will cost.', 'book-a-room' ); ?>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<strong>
				<?php _e( 'Screen Width', 'book-a-room' ); ?>
			</strong>
		</td>
		<td valign="top">
			<?php _e( 'If your theme\'s content area is too thin you can force the display to be thinner so that everything renders correctly.', 'book-a-room' ); ?>
		</td>
	</tr>
</table>