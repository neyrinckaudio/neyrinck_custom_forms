<?php
?>
<p>If you have a Neyrinck product iLok activation card, you must activate it here by following the steps below so that an iLok license can be delivered to your iLok.com account. An iLok license will be placed in your iLok.com account and you will receive an e-mail confirmation.</p>
<br>
	<p>Please fill in the registration information below. This will complete both your product registration and activation. It is very important that you enter a valid e-mail address so you can be notified when product updates are available.</p>
	<div class='form'>
	<form name="activate_second" id="activate_second" method="post" action="">
	    <table border="0" cellspacing="0" cellpadding="0">
			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Activation Code:</td>
			<td><input type="text" name="item_activation_code" value="<?php echo $activation_code; ?>" readonly /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>iLok User ID:</td>
			<input id="fake_user_name" name="fake_username" style="position:absolute; top:10px; display:none;" type="text" value="Safari Autofill Me">
			<?php if (empty($ilok_user_id)) {?>
			
			<td id='ilokId'><input autocomplete='off' type="text" name="item_ilok_user_id" id="item_ilok_user_id" onblur="validate_iLok_account()" value="<?php echo $ilok_user_id; ?>"/></td>
			<?php } else { ?>
			<td id='ilokId'><input type="text" name="item_ilok_user_id" id="item_ilok_user_id" onblur="validate_iLok_account()" value="<?php echo $ilok_user_id; ?>"/></td>
			<?php } ?>
			</tr>
			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Company:</td>
			<td><input type="text" name="item_company" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>First Name:</td>
			<td id='firstname' ><input type="text" name="item_first_name" value="<?php echo $first_name; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Last Name:</td>
			<td id='lastname'><input type="text" name="item_last_name" value="<?php echo $last_name; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Address 1:</td>
			<td><input type="text" name="item_address1"  value="<?php echo $address1; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Address 2:</td>
			<td><input type="text" name="item_address2" value="<?php echo $address2; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>City:</td>
			<td><input type="text" name="item_city"  value="<?php echo $city; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>State:</td>
			<td><input type="text" name="item_state" value="<?php echo $state; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Zip Code/Postal Code:</td>
			<td><input type="text" name="item_postal_code" value="<?php echo $postal_code; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Country:</td>
			<td><input type="text" name="item_country" value="<?php echo $country; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>E-mail:</td>
			<td id='email'><input type="text" name="EMail" value="<?php echo $email1; ?>" /></td>
			</tr>

			<tr style='height: 3.5em;'>
			<td  style="vertical-align:middle" class='w20'>Confirm E-mail:</td>
			<td id='email2' ><input type="text" name="item_email2"  value="<?php echo $email2; ?>"/></td>
			</tr>

			<tr>
				<td colspan='2'>
					<div style='padding: 2em 0em; width: 27em; margin: 0 auto;'>
					<input onclick="disableBtn();" class='download_btn' type="submit" name="submitActivationRegistration" id='second_submit' value="Submit Activation"/>
					</div>
				</td>
			</tr>

		</table>	

	</form>
	</div>
<?php 
?>