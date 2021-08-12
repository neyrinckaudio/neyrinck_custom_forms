<?php
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);
// error_reporting(0);
error_reporting(E_ALL);

// grab recaptcha library
require_once "recaptchalib.php";
$errors = [];
$email = "";
$email2 = "";
$name = "";

if (isset($_POST['submit']) && ($_POST['submit'] == 'Send')) {

   // your secret key
  $secret = "6Lfs3iwUAAAAAJnS_gt1luIY-EnOTZpViD19tO3H"; //registered with paul@neyrinck.com account
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
  $name = $_POST['fullname'];

  if (is_array($_POST)) {

   if ($email != $email2){
      $errors[]="Your email address does not match.";
      echo "<input type='hidden' id='submit_email_match_status' value='error'/>";
    }


    foreach ($_POST as  $index=>$value) {
      if ($index == 'email') {
        if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*" ."@"."([a-z0-9]+([\.-][a-z0-9]+)*)+"."\\.[a-z]{2,}"."$/i",$value)){
          $errors[] = "Your email address was invalid";
          echo "<input type='hidden' id='submit_email_status' value='error'/>";
        } else {
          $$index = $value;
        }
      }  else if (!in_array($index, $ignore)) {
        if (($value == '')) {
          $errors[]="Your $index value was invalid";
          if ($index == 'firstname') echo "<input type='hidden' id='submit_firstname_status' value='error'/>";
          if ($index == 'lastname') echo "<input type='hidden' id='submit_lastname_status' value='error'/>";
        } else { $$index = escapeshellcmd($value); }
      }
    }
  }

  if (count($errors) == 0) {

    //------------------- Edit here --------------------//
  $sendy_url = 'http://news.neyrinck.com';
  $list = '9zRc1Zi763mNVbooG4hmbHDw';
  //------------------ /Edit here --------------------//

  //--------------------------------------------------//
  //POST variables
  $name = $_POST['fullname'];
  $email = $_POST['email'];

  //subscribe
  $postdata = http_build_query(
      array(
      'name' => $name,
      'email' => $email,
      'list' => $list,
      'boolean' => 'true'
      )
  );
  $opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
  $context  = stream_context_create($opts);
  $result = file_get_contents($sendy_url.'/subscribe', false, $context);
  //--------------------------------------------------//

  if ($result = '1')
  {
    echo "Thank you for subscribing to our newsletter.";
    echo "<!-- Event snippet for Newsletter Sign-up conversion page --><script>gtag('event', 'conversion', {'send_to': 'AW-817619326/_pvGCP_opucCEP7C74UD'});</script>";
  } 
  else {
    echo "There is error subscribing. Please try again later.";
  }



  }
}

if (count($errors) > 0) {
  echo "<input type='hidden' id='submit_status' value='error'/>";
}

if (!isset($_POST['submit']) || count($errors) > 0) {
?>

<div class='support_form form'>
<form method="post" enctype="multipart/form-data" name="contactForm" id="contactForm">
<table width="80%" border="0" cellspacing="0" cellpadding="0">


  <tr style='height: 3.5em;'>
  <td width="20%" style="vertical-align:middle">Name:</td>
  <td width="80%" id='fullname'><input type="text" name="fullname" value="<?php echo $name;?>"/></td>
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
  <td colspan="2">
  <div style='padding: 2em 0em; width: 27em; margin: 0 auto;'>
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
<script src="contactus_form.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>

<?php
} ?>
