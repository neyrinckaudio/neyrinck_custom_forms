<?php
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);
// error_reporting(0);
error_reporting(E_ALL);

// grab recaptcha library
require_once plugin_dir_path( __FILE__ ) ."scripts/recaptchalib.php";


if ($_POST['submit'] == 'Send') {
  
  // your secret key
  $secret = "6Lfs3iwUAAAAAJnS_gt1luIY-EnOTZpViD19tO3H";
  // empty response
  $response = null; 
  // check secret key
  $reCaptcha = new ReCaptcha($secret);

  // if submitted check response
  if ($_POST["g-recaptcha-response"]) {
      $response = $reCaptcha->verifyResponse(
          $_SERVER["REMOTE_ADDR"],
          $_POST["g-recaptcha-response"]
      );
  }


  if ($response != null && $response->success) {
      $valid = TRUE;
  } else {
      $valid = FALSE;
  }

  if($valid != TRUE) {
    $errors[]= "reCaptcha Error";
    echo "<input type='hidden' id='submit_security_code_status' value='error'/>";

  }
  
  $ignore = array();
  
  $email2 = $_POST['email2'];
  $email = $_POST['email'];
  $organization = $_POST['organization'];
 
  
  if (is_array($_POST)) { 

   if ($email != $email2){
      $errors[]="Your email address does not match.";
      echo "<input type='hidden' id='submit_email_match_status' value='error'/>";
    } 

    // country
    if ($_POST['country'] == '') {
      $errors[]= "Country required.";
      echo "<input type='hidden' id='submit_country_status' value='error'/>";
    }
  

    if ($_POST['departments'] == ''){
       $errors[]= "Departments required.";
        echo "<input type='hidden' id='submit_department_status' value='error'/>";
    }
    foreach ($_POST as  $index=>$value) {
      if ($index == 'email') {
        if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*" ."@"."([a-z0-9]+([\.-][a-z0-9]+)*)+"."\\.[a-z]{2,}"."$/i",$value)){
          $errors[] = "Your email address was invalid";
          echo "<input type='hidden' id='submit_email_status' value='error'/>";
        } else {
          $$index = $value;
        }
      } else if ($index == 'message') {
        if (trim($value) == '') {
          $errors[] = "Your message was blank";
          echo "<input type='hidden' id='submit_message_status' value='error'/>";
        } else {
          $$index = $value;
        }
        
      } else if (!in_array($index, $ignore)) {
        if (($value == '')) {
          $errors[]="Your $index value was invalid";
          if ($index == 'firstname') echo "<input type='hidden' id='submit_firstname_status' value='error'/>";
          if ($index == 'lastname') echo "<input type='hidden' id='submit_lastname_status' value='error'/>";
        } else { $$index = escapeshellcmd($value); }
      }   
    } 
  }
  
  if (count($errors) == 0) {
  
    if($_POST['departments'] == 'Sales') {
      $to="sales@neyrinck.com";
    } else if ($_POST['departments'] == 'Marketing') {
      $to="marketing@neyrinck.com";
    } else if ($_POST['departments'] == 'Other') {
      $to="info@neyrinck.com";
    }

//$to='berniceling@yahoo.com';
    $replyToEmail=$email;
  
    $subject = "Contact Neyrinck";
    
    $headers = "From: Neyrinck<postmaster@neyrinck.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "Reply-To: $replyToEmail\r\n";
    $headers .= "Bcc: berniceling@neyrinck.com\r\n";
    
      $body = "<html><body> 
User Name : $firstname $lastname<br />
Organization : $organization<br />
Request Date : ".date("Y-m-d h:i:s A")."<br />
E-Mail : <a href='mailto:$email'>$email</a><br />
Country : " . $country. "<br />
Message : ".nl2br($message)."</body></html>"; 
    
    
    mail($to,$subject,$body,$headers); 
    echo "<div class='success'><h3>Your request has been sent. Thank you.</h3></div>";
    
  } 
}

if (count($errors) > 0) {
  echo "<input type='hidden' id='submit_status' value='error'/>"; 
} 

if (!$_POST['submit'] || $errors > 0) {
?>

<div class='support_form form'>
<form method="post" enctype="multipart/form-data" name="contactForm" id="contactForm">
<table width="80%" border="0" cellspacing="0" cellpadding="0">

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">First Name:</td>
<td width="80%" id="firstname" ><input type="text" name="firstname" value="<?php echo $firstname; ?>" /></td>
</tr>

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Last Name:</td>
<td width="80%" id='lastname'><input type="text" name="lastname" value="<?php echo $lastname; ?>" /></td>
</tr>

<tr style='height: 3.5em;'>
<td  style="vertical-align:middle" class='w20'>Organization:</td>
<td><input type="text" name="organization" value="<?php echo $organization; ?>" /></td>
</tr>

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Email:</td>
<td width="80%" id='email'><input type="text" name="email" value="<?php echo $email; ?>"/></td>
</tr>

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Re-enter Email:</td>
<td width="80%" id='email2'><input type="text" name="email2" value="<?php echo $email2; ?>" /></td>
</tr>

<tr style='height: 3.5em;'>
<td  style="vertical-align:middle" class='w20'>Country:</td>
<td id='country'>
  <div id="prefetch"><input name='country' class="typeahead" type="text" placeholder="Type or select an option" value="<?php echo $country; ?>"></div>
</td>
</tr>






<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Contact:</td>
<td width="80%" id='departments'> 
  <div class="styled-select">
    <select name="departments">
      <option value=''>&nbsp; &#9662;  Select an option</option>
      <?php 
        $contact[] = 'Sales';
        $contact[] = 'Marketing';
        $contact[] = 'Other';
        foreach ($contact as $cont) {
          echo "    <option value='$cont'";
        if ($_POST['departments'] == $cont) {
          echo " selected";
        } 
        echo ">$cont</option>\n";
        }
      ?>
    </select>
  </div>
</td>
</tr>


<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Message:</td>
<td width="80%" id='message'><textarea name="message" rows="1"><?php echo $_POST['message']; ?></textarea></td>
</tr>


<tr style='height: 3.5em;'>
  <td colspan="2">
    <div style='padding: 0em 0em; width: 27em; margin: 0 auto;' id="code"></div>
<div style='padding: 2em 0em; width: 27em; margin: 0 auto;' class="g-recaptcha" data-sitekey="6Lfs3iwUAAAAAFD4SlDM7akXAh0MTKrEkEubo4eC"></div>
  </div>
  </td>
</tr>

<tr>
<td colspan="2">
  <div style='padding: 2em 0em; width: 27em; margin: 0 auto;'>
  <input id='download_btn' class='download_btn' type="submit" name="submit" value="Send" />
  </div>
</td>
</tr>

</table>
</form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src='/wp-content/plugins/neyrinck-custom-forms/shortcode/scripts/typeahead.js'></script>

<?php 
} ?>