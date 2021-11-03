<?php
error_reporting(E_ALL);
require_once plugin_dir_path( __FILE__ ) ."recaptchalib.php";
$errors = [];
if (isset($_POST['submit']) && ($_POST['submit'] == 'Send')) {
  // recaptcha secret key
  $secret = "6Lfs3iwUAAAAAJnS_gt1luIY-EnOTZpViD19tO3H"; //registered with paul@neyrinck.com account
  // initialize response to be null
  $response = null;
  // check Captcha
  $reCaptcha = new ReCaptcha($secret);
  if (isset($_POST["g-recaptcha-response"])) {
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
  else {
    $sendy_url = 'http://news.neyrinck.com';
    $list = 'svZVgoz7YigSU0pOuuTOgw';
    $name = $_POST['dealername'];
    $email = $_POST['email'];
    $company = $_POST['company'];

    //subscribe
    $postdata = http_build_query(
        array(
        'name' => $name,
        'CompanyName' => $company,
        'email' => $email,
        'list' => $list,
        'boolean' => 'true'
        )
    );
    $opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
    $context  = stream_context_create($opts);
    $result = file_get_contents($sendy_url.'/subscribe', false, $context);
    if (($result != 'true')&&($result != '1'))
    {
      $errors[]= $result;
    }
  }

  $ignore = array();

  if (count($errors) == 0) {
    echo "<div class='success'><h3>Thank you for subscribing.</h3></div>";
  }
}

if (count($errors) > 0) {
  echo "<div>".$errors[0]."</div>";
  echo "<input type='hidden' id='submit_status' value='error'/>";
}

// show form if not a submit or if there were errors
if (!isset($_POST['submit']) || count($errors) > 0) {
?>

<form method="POST" accept-charset="utf-8">
	<label for="name">Name</label><br/>
	<input type="text" name="dealername" id="name"/>
	<br/>
  <label for="company">Company</label><br/>
	<input type="text" name="company" id="company"/>
	<br/>
	<label for="email">Email</label><br/>
	<input type="email" name="email" id="email"/>
	<br/>
	<input type="hidden" name="list" value="svZVgoz7YigSU0pOuuTOgw"/>

  <tr style='height: 3.5em;'>
  <td colspan="2">
  <div style='padding: 2em 0em; width: 27em; margin: 0 auto;'>
    <div style='padding: 0em 0em; width: 27em; margin: 0 auto;' id="code"></div>
    <div style='padding: 2em 0em; width: 27em; margin: 0 auto;' class="g-recaptcha" data-sitekey="6Lfs3iwUAAAAAFD4SlDM7akXAh0MTKrEkEubo4eC"></div>
  </td>
</tr>
<input type="submit" name="submit" id="submit" value="Send"/>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>

<?php
}
?>
