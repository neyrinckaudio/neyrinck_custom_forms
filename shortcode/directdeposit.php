<?php
 
include_once('NeyrinckActivation.php');


$step1=true;
$showILockRedeemInstruction = false;
$showRegistrationForm = false;
$showError = false;


if (isset($_POST['item_activation_code'])) $activation_code = $_POST['item_activation_code'];


if (!empty($_POST['second_submit'])) {

	$step1 = false;
	$step2 = true;
	$ilok_user_id = $_POST['item_ilok_user_id'];
	$activation_code = $_POST['item_activation_code'];
	$first_name = $_POST['item_first_name'];
	$last_name = $_POST['item_last_name'];
	$company =  $_POST['item_company'];
	$address1 =  $_POST['item_address1'];
	$address2 =  $_POST['item_address2'];
	$city =  $_POST['item_city'];
	$state =  $_POST['item_state'];
	$postal_code =  $_POST['item_postal_code'];
	$country =  $_POST['country'];
	$email2 =  $_POST['item_email2'];
	$email1 =  $_POST['EMail'];
	
	foreach ($_POST as $key => $val) {

		if ($key == 'item_first_name' ){
			if (empty($val)) {
				echo "<input type='hidden' id='submit_firstname_status' value='error'/>";
				$errors[] = "This is a required field.";
			}
		}
		if ($key == 'item_last_name' ){
			if (empty($val)) {
				echo "<input type='hidden' id='submit_lastname_status' value='error'/>";
				$errors[] = "This is a required field.";
			}
		}

		if ($key == 'item_ilok_user_id' ){
			if (empty($val)) {
				echo "<input type='hidden' id='submit_ilok_id_status' value='error'/>";
				$errors[] = "This is a required field.";
			}
		}
	    
	    if ($key == 'EMail'){
	    	if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*" ."@"."([a-z0-9]+([\.-][a-z0-9]+)*)+"."\\.[a-z]{2,}"."$/i",$val)){
	    		$errors[] = "Your email address was invalid.";
				echo "<input type='hidden' id='submit_email_status' value='error'/>";
			} 
	    }

	    if ($key == 'item_email2'){ 	
	    	if ($email1 != $val){
	    		$errors[]="Your email address does not match.";
      			echo "<input type='hidden' id='submit_email_match_status' value='error'/>";
	    	} 
	    }
	}

	if (count($errors) > 0) {
		echo "<input type='hidden' id='submit_status' value='error'/>";
		$showRegistrationForm = true;

	} else {
	
		$step2_result = $NeyrinckActivation->SecondStep($ilok_user_id, $activation_code, $first_name, $last_name, $company, $address1, $address2, $city, $state, $postal_code, $country, $email1);
	

	   	if (!$step2_result['success']) $showError = true;
	   	else $showILockRedeemInstruction = true;
	}



}

if (!empty($_POST['first_submit']) && !empty($activation_code)) {
   $step1 = true;
   $step2 = false;


   $step1_result = $NeyrinckActivation->FirstStep($activation_code);

   // retrieve results
   $ilok_user_id = $step1_result['ilok_user_id'];
   // $ilok_url = "http://www.ilok.com/lc/r?c=?" . $step1_result["ilok_license_code"] . "&u=" . $ilok_user_id;
   // $ilok_license_code = $step1_result["ilok_license_code"];

   if (!$step1_result['success']) $showError = true;

   // if ($step1_result['activated'] == 'activated' ) $showILockRedeemInstruction = true;

   if ($step1_result['success']) $showRegistrationForm = true;  
}





?>



<?php 
// *******************************************
//                 HTML CODE 
// *******************************************
?>

<?php
// *******  First Submit  *********//
if ((empty($_POST['first_submit']) && $step1) || ((!empty($_POST['first_submit']) && empty($activation_code) && $step1)) ){
	?>
	<div style='margin-top: 20px'>
	<h2>Online iLok Activation</h2>
	<p>If you have a Neyrinck product iLok activation card, you must activate it here by following the steps below so that an iLok license can be delivered to your iLok.com account. An iLok license will be placed in your iLok.com account and you will receive an e-mail confirmation.</p>
	<p>In order to activate your plug-in you will need the following information:</p>
	<p class='strong'>Your activation code from the activation card delivered by e-mail.</p>
		<form name="activate_first" id="activate_first" method="POST" action="">
		    
		    <p>Please fill in the activation code below.</p>
		    <p><label>Activation Code:  &nbsp; &nbsp;</label><input name="item_activation_code" id="item_activation_code" value="" type="text" size='25'></p>
		    <p><input class='download_btn' type="submit" name="first_submit" id="first_submit" value="Submit Activation"></p>
		</form>
	</div>
	<?php 
} ?>

<?php
// *******  Message Div   *********//
if ($showError) {
	if ($step1) echo "<p style='padding-top: 50px;'>".$step1_result['msg']."</p>";
	if ($step2) echo "<p style='padding-top: 50px;'>". $step2_result['msg']."</p>";
	// reset
	$showError = false;
}
?>

<?php
// ***** Activation Form ********//
if ($showRegistrationForm) {
	?>
	
	<p style="padding-top: 50px;">Please fill in the registration information below. This will complete both your product registration and activation. It is very important that you enter a valid e-mail address so you can be notified when product updates are available.</p>
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
					<input onclick="disableBtn();" class='download_btn' type="submit" name="second_submit" id='second_submit' value="Submit Activation"/>
					</div>
				</td>
			</tr>

		</table>	

	</form>
	</div>
	<script src="/scripts/activation/directdeposit.js"></script>
	<?php 
		// reset
		$showRegistrationForm = false;
} ?>

<?php 
// ************* Instruction to Redeem iLok License *************//
if ($showILockRedeemInstruction){ 	?>
	<p style="padding-top: 50px;">The license has been deposited to you iLok.com account. Please use the iLok License Manager to transfer the license to your iLok USB key/machine.</p>
	<p>Please contact <a href='mailto:support@neyrinck.com'>support@neyrinck.com</a> if you need any help.</p>
	<?php 
		// reset
		$showILockRedeemInstruction = false;

		// generate coupon if product is SPILL
		if (strpos($activation_code, 'SPILL') === false) {
		    return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( !is_plugin_active( 'neyrinck_coupon/neyrinck_coupon.php' ) ) {
			return false;
		}

		$ilok_user_id = strtolower($ilok_user_id);
		$coupon_code = bl_generate_coupon($ilok_user_id, 'DEALER');
		$customer['firstname'] = $first_name;
    	$customer['lastname'] = $last_name;
    	$customer['email'] = $email2;

		bl_send_coupon($ilok_user_id, $coupon_code, $customer);
	?>

	<p>Thank you for purchasing Spill. Your purchase includes a coupon redeemable for <a href="https://neyrinck.com/products/v-control-pro-bundle/" target="_blank" style="color:#0599d8; font-weight: 500; text-decoration: none;">V-Control Pro Bundle</a> at no cost for the first 12 months. V-Control Pro Bundle is the premiere, professional system for controlling your DAW, surround panning, and the Spill PlugIn using the controller apps V-Console, V-Panner, and V-PlugIn. V-PlugIn's ability to select Spill tracks and edit them without opening the plug-in window is a huge time saver.</p>

	<p>To redeem your coupon please click the button below and add V-Control Pro Bundle to the cart and when you checkout, enter the coupon code shown here:</p>

	<p>V-Control Pro Bundle Coupon Code : <?php echo $coupon_code; ?></p>

	<p>This coupon is valid for iLok user ID: <?php echo $ilok_user_id; ?></p>

	<div style="width: 200px; margin: 50px auto;">
	    <a href="https://neyrinck.com/product/v-control-pro-bundle/" target="_blank" style="background-color:transparent; font-weight:500; color:black; text-decoration: none; border: 2px solid black; padding: 10px 20px;"> Redeem Now</a>
	    
	</div>


<?php
} ?>


