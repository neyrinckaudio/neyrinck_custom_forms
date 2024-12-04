<?php
//ini_set('display_startup_errors',1);
//ini_set('display_errors',1);
//error_reporting(E_ALL);

include( plugin_dir_path( __FILE__ ) . 'scripts/database.php');
require_once plugin_dir_path( __FILE__ ) ."scripts/recaptchalib.php";
include( plugin_dir_path( __FILE__ ) . 'scripts/softwareInformation.php');

// Get software list
$softwarePacks = $NeyrinckSoftware->packages[$downloadProduct];
$errors = array();
$firstname = '';
$lastname = '';
$organization = '';
$email = '';
$country = '';
$softwarename = '';
if (isset($_POST['submit']) && ($_POST['submit'] != ''))
{
  // your secret key
  $secret = "6Lfs3iwUAAAAAJnS_gt1luIY-EnOTZpViD19tO3H";

  // empty response
  $response = null;
  // check secret key
  $reCaptcha = new ReCaptcha($secret);

  // if submitted check response
  if ($_POST["g-recaptcha-response"])
  {
      $response = $reCaptcha->verifyResponse(
          $_SERVER["REMOTE_ADDR"],
          $_POST["g-recaptcha-response"]
      );
  }

  if ($response != null && $response->success)
      $valid = TRUE;
  else 
      $valid = FALSE;

  if($valid != TRUE)
  {
    $errors[]= "reCaptcha Error";
    echo "<input type='hidden' id='submit_security_code_status' value='error'/>";
  }

  // software
  if ($_POST['software'] == '') {
    $errors[]= "Please specify an Software download.";
    echo "<input type='hidden' id='submit_software_status' value='error'/>";
  } else {
    $softwarename = $_POST['software'];
    }

  // first name
  if ($_POST['firstname'] == '') {
    $errors[]= "First name required.";
    echo "<input type='hidden' id='submit_firstname_status' value='error'/>";
  }
  // last name
  if ($_POST['lastname'] == '') {
    $errors[]= "Last name required.";
    echo "<input type='hidden' id='submit_lastname_status' value='error'/>";
  }

  // email
  if ($_POST['email'] == '') {
    $errors[]= "Email required.";
    echo "<input type='hidden' id='submit_email_status' value='error'/>";
  }

  if ($_POST['email']== 'email') {
      if(!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*" ."@"."([a-z0-9]+([\.-][a-z0-9]+)*)+"."\\.[a-z]{2,}"."$/i",$value)){
        $errors[] = "Your email address was invalid.";
        echo "<input type='hidden' id='submit_email_status' value='error'/>";
      }
  }

  //make sure that any data coming from user input is escaped
  if(is_array($_POST))
  {
    foreach ($_POST as $index=>$value){
      if (!is_array($value)) $$index = escapeshellcmd($value);
    }
  }

  // Process results
  if (count($errors) == 0) 
  {
    $firstname = trim($_POST['firstname']);
    $lastname =  trim($_POST['lastname']);
    $email =  trim($_POST['email']);
    $newsletter = $_POST['newsletter'];
    if (!$newsletter){
      $newsletter = 0;
    }
    
    // Save to database
    $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
    // Check connection
    if (mysqli_connect_errno())
    {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      echo "<BR>";
      die();
    }

    $query="INSERT INTO ekl_software_downloads
     (firstname, lastname, organization, email, software, country, downdate, newsletter) VALUES
      ('$firstname', '$lastname', '$organization', '".$_POST['email']."', '$softwarename', '$country', '".date("Y-m-d h:i:s")."', '$newsletter')";

    $result = mysqli_query($connection, $query) or die ("Error in query: $query. ".mysqli_error());

    if ( false===$result ) {
      printf("error: %s\n", mysqli_error($connection));
    }

    // Update customer table if email is new
    $query="SELECT * FROM customers WHERE customers_email_address ='".$email."'";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) == 0) {
      $query="INSERT INTO customers (customers_firstname, customers_lastname, customers_email_address, organization, customers_newsletter) VALUES ('$firstname', '$lastname', '$email', '$organization', '$newsletter')";
      $result = mysqli_query($connection, $query) or die ("Error in query: $query. ".mysqli_error());
      mysqli_close($connection);
    }
    // subscribe to newsletter
    if ($newsletter)
    {
      $sendy_url = 'http://news.neyrinck.com';
      $list = 'BfkRKWbN9a82ETTCoriB1g';
      $name = $firstname." ".$lastname;
      $company = $organization;

      //subscribe
      $postdata = http_build_query(
          array(
          'name' => $name,
          'CompanyName' => $company,
          'Country' => $country,
          'email' => $email,
          'list' => $list,
          'boolean' => 'true'
          )
      );
      $opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
      $context  = stream_context_create($opts);
      $result = file_get_contents($sendy_url.'/subscribe', false, $context);
    }
    else 
    {
    // for testing  echo "not subscribing<br>";
    }
    // Launch Download
    // if contains "V-90" add gtag
    if (str_starts_with($softwarename, 'V-90 Symphonic')) {
      echo "<!-- Event snippet for download V-90 symphonic conversion page -->
      <script>
        gtag('event', 'conversion', {'send_to': 'AW-817619326/ISVRCNGTi44ZEP7C74UD'});
      </script>";

  }

    $file = $NeyrinckSoftware->downloads[$softwarename];
    /*
    if ($downloadProduct == "Spill")
    {
      $thankyou_url = site_url()."/downloads/spill/thank-you-for-dowloading-spill/?software=$file";
      // do a redirect to Thank you page and download
      // Thank you Page include a php script : thankyou_download.php
      ob_start();
      header("Location: ".$thankyou_url);
      ob_end_flush();
      die();
    }
    if ($downloadProduct == "V-Control Pro")
    {
      $thankyou_url = site_url()."/downloads/v-control-pro/thank-you-for-dowloading-vcp/?software=$file";
      // do a redirect to Thank you page and download
      // Thank you Page include a php script : thankyou_download.php
      ob_start();
      header("Location: ".$thankyou_url);
      ob_end_flush();
      die();
    }
    */

    if (strpos($file,'https') !== false) {
      echo "<div class='success'><br /><a style='text-size:1.3em; font-weight:bold' href=$file>Click here if your download does not start.</a></div>";
      echo "<iframe style='border:0' width=0 height=0 src=$file></iframe>";
    } else {
      echo "<div>Download Error - Please contact neyrinck.com/support</div>";
    }
  }
}

if (count($errors) > 0) {
  echo "<input type='hidden' id='submit_status' value='error'/>";
}

if (!isset($_POST['submit']) || count($errors) > 0)
{
  ?>
  <div class='mobileDevice'>Downloads are not available from a mobile device.</div>
  <div class='download_form form'>
  <form method="post" enctype="multipart/form-data" name="downloadForm" id="downloadForm">
  <table border="0" cellspacing="0" cellpadding="0">
  <tr style='height: 3.5em;'>
  <td  style="vertical-align:middle" class='w20'>First Name:</td>
  <td id='firstname'><input type="text" name="firstname" value="<?php echo $firstname; ?>" /></td>
  </tr>

  <tr style='height: 3.5em;'>
  <td  style="vertical-align:middle" class='w20'>Last Name:</td>
  <td id='lastname'><input type="text" name="lastname" value="<?php echo $lastname; ?>"/></td>
  </tr>

  <tr style='height: 3.5em;'>
  <td  style="vertical-align:middle" class='w20'>Email:</td>
  <td id='email'><input type="text" name="email" value="<?php echo $email; ?>"/></td>
  </tr>

  <tr style='height: 3.5em;'>
  <td  style="vertical-align:middle" class='w20'>Software:</td>
  <td id='software'>
  <div class="styled-select">
  <select name="software">
  <option value=''>&nbsp; &#9662;  Select an option</option>
    <?php
    foreach ($softwarePacks as $prod) {
      echo "
      <option value='$prod'";
    if ($softwarename == $prod) {
      echo " selected";
    }
    echo ">$prod</option>\n";
    }
    ?>
    </select>
    </div>
  </td>
  </tr>

  <tr>
  <td colspan='2'>
  <div style='padding: 0em 0em; width: 27em; margin: 0 auto;' id="code"></div>
  <div style='padding: 2em 0em; width: 27em; margin: 0 auto;' class="g-recaptcha" data-sitekey="6Lfs3iwUAAAAAFD4SlDM7akXAh0MTKrEkEubo4eC"></div></td>
  </tr>

  <tr>
  <td colspan='2'>
    <div style='padding: 2em 0em; width: 27em; margin: 0 auto;'>
    <input id='download_btn' class='download_btn' type="submit" name="submit" value="Download" />
    </div>
  </td>
  </tr>
  <tr>
    <td colspan='2'>
    <div style='padding: 0em; width: 27em; margin: 0 auto;'>
    <input type="checkbox" name="newsletter" value="1" checked> <span class='notify'>Notify me on new updates and other important Neyrinck news.</span>
    </div>
  </td>
  </tr>
  </table>
  </form>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <?php 
}
?>
