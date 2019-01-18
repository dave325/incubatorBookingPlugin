<script>
	jQuery( document ).ready( function () {
		// check which is checked hide extra based on this.
		if ( jQuery( 'input[name="addressType"]:checked' ).val() == 'usa' ) {
			jQuery( '#globalTable' ).hide();
			jQuery( '#usaTable' ).show();
		} else {
			jQuery( '#globalTable' ).show();
			jQuery( '#usaTable' ).hide();
		}


		// show/hide based on 

		jQuery( 'input[name="addressType"]' ).change( function () {
			if ( jQuery( 'input[name="addressType"]:checked' ).val() == 'usa' ) {
				jQuery( '#globalTable' ).slideUp( "fast" );
				jQuery( '#usaTable' ).slideDown( "fast" );
			} else {
				jQuery( '#globalTable' ).slideDown( "fast" );
				jQuery( '#usaTable' ).slideUp( "fast" );
			}
		} );
	} );
</script>
<link href="<?php echo plugins_url(); ?>/book-a-room/css/bookaroom_meetings.css" rel="stylesheet" type="text/css"/>
<div class=wrap>
	<div id="icon-options-general" class="icon32"></div>
	<h2>
		<?php _e( 'Book a Room Administration - Locale', 'book-a-room' ); ?>
	</h2>
</div>
<?php
# Display Errors if there are any
if ( !empty( $errorMSG ) ) {
	?>
	<p>
		<h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3>
	</p> <?php
}
?>
<h2>
	<?php _e( 'Locale Settings', 'book-a-room' ); ?>
</h2>

<h3>
	<?php _e( 'Address Type', 'book-a-room' ); ?>
</h3>
<p>
	<?php _e( 'If you choose United States, the system will use the US names for addresse fields. If you want to configure your own field names, choose Global.', 'book-a-room' ); ?>
</p>
<p>
	<?php _e( 'If you choose Global, you will be prompted to enter the <strong>display names</strong> for each section of the Address. These are how your users will see the address questions, and can be configured to match your country\'s&nbsp;address format. As an example, instead of Zip Code, you can enter Postal Code and that is what the form will then ask for.', 'book-a-room' ); ?>
</p>
<h3>
	<?php _e( 'Phone and Post Code options', 'book-a-room' ); ?>
</h3>
<p>
	<?php _e( 'The RegEx and Example fields allow you to use a Regular Expression to compare against when error checking the user input. You can write your own, find them online or use the values in the table below the options. Just copy and paste the value into the form.', 'book-a-room' ); ?>
</p>
<p>
	<?php _e( 'The example value is shown when there is an error&nbsp;to guide the user to the proper format. You can make up a number or user your real number here.', 'book-a-rook' ); ?>
</p>
<form name="form1" method="post" action="?page=bookaroom_Settings_Locale">
	<?php
	$addressType_other_checked = $addressType_usa_checked = false;
	switch ( $externals[ 'addressType' ] ) {
		case 'usa':
			$addressType_usa_checked = ' checked="checked"';
			break;
		default:
			$addressType_other_checked = ' checked="checked"';
			break;
	}

	if ( !empty( $errorMSG ) ) {
		?>
	<p>
		<h3 style="color: red;"><strong><?php echo $errorMSG; ?></strong></h3>
	</p>
	<?php
	}
	?>
	<table class="tableMain">
		<tbody>
			<tr>
				<td colspan="2">
					<?php _e( 'Address type', 'book-a-room' ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'United States', 'book-a-room' ); ?>
				</td>
				<td><input name="addressType" type="radio" id="addressType_usa" value="usa"<?php echo $addressType_usa_checked; ?>></td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Global', 'book-a-room' ); ?>
				</td>
				<td><input name="addressType" type="radio" id="addressType_other" value="other"<?php echo $addressType_other_checked; ?>></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<input name="action" type="hidden" id="action" value="checkLocale">
					<input type="submit" name="submit" id="submit" value="<?php _e( 'Submit', 'book-a-room' ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<div id="usaTable" style="display:nones">
		<table class="tableMain">
			<tbody>
				<tr>
					<td colspan="2">
						<?php _e( 'Address options', 'book-a-room' ); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php _e( 'Default State/Province/Territory Value', 'book-a-room' ); ?>
					</td>
					<td>
						<select name="defaultStateDrop" id="defaultStateDrop">
							<?php
							$stateList = bookaroom_public::getStates();
							foreach ( $stateList as $key => $val ) {
								$selected = ( !empty( $externals['defaultStateDrop'] ) and $externals['defaultStateDrop'] == $val ) ? ' selected="selected"' : NULL;								
								?>
								<option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right"><input type="submit" name="submit" id="submit" <?php _e( 'Submit', 'book-a-room' ); ?>>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="globalTable" style="display:nones">
		<table class="tableMain">
			<tbody>
				<tr>
					<td colspan="2"><?php _e( 'Address options', 'book-a-room' ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Street Address 1 Name', 'book-a-room' ); ?></td>
					<td><input name="address1_name" type="text" id="address1_name" value="<?php echo $externals['address1_name']; ?>">
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Street Address 2 Name', 'book-a-room' ); ?></td>
					<td><input name="address2_name" type="text" id="address2_name" value="<?php echo $externals['address2_name']; ?>">
					</td>
				</tr>
				<tr>
					<td><?php _e( 'City Name', 'book-a-room' ); ?></td>
					<td><input name="city_name" type="text" id="city_name" value="<?php echo $externals['city_name']; ?>">
					</td>
				</tr>
				<tr>
					<td><?php _e( 'State/Province/Territory Name', 'book-a-room' ); ?></td>
					<td><input name="state_name" type="text" id="state_name" value="<?php echo $externals['state_name']; ?>">
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Default State/Province/Territory Value', 'book-a-room' ); ?></td>
					<td><input name="defaultState_name" type="text" id="defaultState_name" value="<?php echo $externals['defaultState_name']; ?>">
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Zip/Post Code Name', 'book-a-room' ); ?></td>
					<td><input name="zip_name" type="text" id="zip_name" value="<?php echo $externals['zip_name']; ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right"><input type="submit" name="submit" id="submit" value="<?php _e( 'Submit', 'book-a-room' ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<br>
	<table class="tableMain">
		<tbody>
			<tr>
				<td colspan="2"><?php _e( 'Phone options', 'book-a-room' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Phone Regex', 'book-a-room' ); ?></td>
				<td><input name="phone_regex" type="text" id="phone_regex" value="<?php echo $externals['phone_regex']; ?>">
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Phone Example', 'book-a-room' ); ?></td>
				<td><input name="phone_example" type="text" id="phone_example" value="<?php echo $externals['phone_example']; ?>">
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'Canada/USA', 'book-a-room' ); ?></td>
				<td align="right"><pre>/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/</pre>
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'UK', 'book-a-room' ); ?></td>
				<td align="right">/^((1[1-9]|2[03489]|3[0347]|5[56]|7[04-9]|8[047]|9[018])\d{8}|(1[2-9]\d|[58]00)\d{6}|8(001111|45464\d))$/
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'Australia', 'book-a-room' ); ?></td>
				<td align="right"><pre>/^(?:\+?61|0)[2-478](?:[ -]?[0-9]){8}$/</pre>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right"><input type="submit" name="submit" id="submit" value="<?php _e( 'Submit', 'book-a-room' ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="tableMain">
		<tbody>
			<tr>
				<td colspan="2"><?php _e( 'Zip/Postal Code options', 'book-a-room' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Zip Regex', 'book-a-room' ); ?></td>
				<td><input name="zip_regex" type="text" id="zip_regex" value="<?php echo $externals['zip_regex']; ?>">
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Zip Example', 'book-a-room' ); ?></td>
				<td><input name="zip_example" type="text" id="zip_example" value="<?php echo $externals['zip_example']; ?>">
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'USA', 'book-a-room' ); ?></td>
				<td align="right"><pre>/(^\d{5}$)|(^\d{5}-\d{4}$)/</pre>
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'Canada', 'book-a-room' ); ?></td>
				<td align="right"><pre>/^[a-zA-Z]\d{1}[a-zA-Z](\-| |)\d{1}[a-zA-Z]\d{1}$/</pre>
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'UK', 'book-a-room' ); ?></td>
				<td align="right"><pre>/^[A-Z][A-Z]?[0-9][A-Z0-9]? ?[0-9][ABDEFGHJLNPQRSTUWXYZ]{2}$/i</pre>
				</td>
			</tr>
			<tr>
				<td align="right"><?php _e( 'Australia', 'book-a-room' ); ?></td>
				<td align="right"><pre>/^[0-9]{4}/</pre>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right"><input type="submit" name="submit" id="submit" value="<?php _e( 'Submit', 'book-a-room' ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<p>&nbsp;</p>
</form>