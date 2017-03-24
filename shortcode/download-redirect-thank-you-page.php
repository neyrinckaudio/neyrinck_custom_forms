<?php
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);

// error_reporting(1);

// grab recaptcha library
require_once "scripts/recaptchalib.php";
include('scripts/softwareInformation.php');



// Get software list
$softwarePacks = $NeyrinckSoftware->packages[$downloadProduct];


if ($_POST['submit'] != '') {

  // your secret key
  $secret = "6LfiXuISAAAAAKQqhDHJXfU80Lsa45RPs86uYhym";
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

  // software 
  if ($_POST['software'] == '') {
    $errors[]= "Please specify an Software download.";
    echo "<input type='hidden' id='submit_software_status' value='error'/>";
  } else {
    $software = $_POST['software'];
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


  // country
    if ($_POST['country'] == '') {
      $errors[]= "Country required.";
      echo "<input type='hidden' id='submit_country_status' value='error'/>";
    }

  //make sure that any data coming from user input is escaped 
  if(is_array($_POST)){
    foreach ($_POST as $index=>$value){
      if (!is_array($value)) $$index = escapeshellcmd($value);
    }
  }


  // Process results
  if (count($errors) == 0) {
    $firstname = trim($_POST['firstname']);
    $lastname =  trim($_POST['lastname']);
    $organization =  trim($_POST['organization']);
    $email =  trim($_POST['email']);
    $newsletter = $_POST['newsletter'];
    $country = trim($_POST['country']);

    // Save to database
    $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database']);
    // Check connection
    if (mysqli_connect_errno())
    {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else echo "C O N N E C T E D ";

    $query="INSERT INTO main.ekl_software_downloads
      (firstname, lastname, organization, email, software, country, downdate, newsletter) VALUES
      ('$firstname', '$lastname', '$organization', '".$_POST['email']."', '$software', '$country', '".date("Y-m-d h:i:s")."', '$newsletter')";
    
     $result = mysqli_query($connection, $query) or die ("Error in query: $query. ".mysqli_error());

     if ( false===$result ) {
        printf("error: %s\n", mysqli_error($connection));
      }
      

    // Update customer table if email is new
    $query="SELECT * FROM main.customers WHERE customers_email_address ='".$email."'";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) == 0) {
    $query="INSERT INTO `main.customers` (customers_firstname, customers_lastname, customers_email_address, organization, customers_newsletter) VALUES ('$firstname', '$lastname', '$email', '$organization', '$newsletter')";
     $result = mysqli_query($connection, $query) or die ("Error in query: $query. ".mysqli_error());

      mysqli_close($connection);

  }



      //******* INTEGRATE SENDY ***********//
  
    if ($newsletter == '1'){
    
      $sendy_url = 'http://news.neyrinck.com';
    
      $lists = [
          "V-Control Pro" => "nHfnq4FWodRmpDyGQj763j4Q",
          "Dolby E" => "KUWUxDMfPkustsYJuaB88Q",
          "LtRt" => "D1ccJ8920jIBueq91MZ64faQ ",
          "Dolby Digital 2" => "BbeVLmHmcwI15pZU3mLllA",
          "MXF" => "KcvgXw56GHOVYa76I5KZdA",
          "V-Mon" => "T6ZzYzcL763J892dip4NDHwZjA",
          "Spill" => "8QLAD6Sq892ivbOKLxl763oJ8g"
      ];

      $list;
      // determine which list to use
      if (strpos($software, 'V-Control Pro') !== false 
        || strpos($software, 'Ney-Fi') !== false) 
      {
          $list = $lists["V-Control Pro"];
      } else if (strpos($software, 'Dolby E') !== false)
      {
          $list = $lists["Dolby E"];
      } else if (strpos($software, 'LtRt') !== false)
      {
          $list = $lists["LtRt"];
      } else if (strpos($software, 'Dolby Digital 2') !== false)
      {
          $list = $lists["Dolby Digital 2"];
      } else if (strpos($software, 'MXF') !== false)
      {
          $list = $lists["MXF"];
      } else if (strpos($software, 'V-Mon') !== false)
      {
          $list = $lists["V-Mon"];
      }  else if (strpos($software, 'Spill') !== false)
      {
          $list = $lists["Spill"];
      }

      
      //variables
      $name = $firstname ." ". $lastname;
     
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
      
    }


    //***********************************//
    // Launch Download
    $file = $NeyrinckSoftware->downloads[$software];

   
    $thankyou_url;
    if ($list == $lists["Spill"]){
      $thankyou_url = site_url()."/products/spill/download/thank-you-for-dowloading-spill/?software=$file";
    }
    
    // do a redirect to Thank you page and download
    // Thank you Page include a php script : thankyou_download.php
    header("Location: $thankyou_url");
    die();  
  
 /*****
    if (strpos($file,'https') !== false) {
        echo "<div class='success'><br /><a style='text-size:1.3em; font-weight:bold' href=$file>Click here if your download does not start.</a></div>";
      echo "<iframe style='border:0' width=0 height=0 src=$file></iframe>";
    } else {
      echo "<div class='success'><br /><a style='text-size:1.3em; font-weight:bold' href='https://neyrinck.com/download/".$file."'>Click here if your download does not start.</a></div>";
      echo "<iframe style='border:0' width=0 height=0 src=https://neyrinck.com/download/download.php?file=".urlencode($file)."></iframe>";
    }

  *****/
    
  }
}

if (count($errors) > 0) {
  echo "<input type='hidden' id='submit_status' value='error'/>";
}




if (!$_POST['submit'] || $errors > 0) {

?>
<!-- <div class='mobileDevice'>Downloads are not available from a mobile device.</div> -->
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
<td  style="vertical-align:middle" class='w20'>Organization:</td>
<td><input type="text" name="organization" value="<?php echo $organization; ?>" /></td>
</tr>

<tr style='height: 3.5em;'>
<td  style="vertical-align:middle" class='w20'>Email:</td>
<td id='email'><input type="text" name="email" value="<?php echo $email; ?>"/></td>
</tr>

<tr style='height: 3.5em;'>
<td  style="vertical-align:middle" class='w20'>Country:</td>
<td id='country'>
  <div id="prefetch"><input name='country' class="typeahead" type="text" placeholder="Type or select an option" value="<?php echo $country; ?>"></div>
</td>
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
  if ($software == $prod) {
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
<div style='padding: 2em 0em; width: 27em; margin: 0 auto;' class="g-recaptcha" data-sitekey="6LfiXuISAAAAALP4gpHG-9yuN_xc1X0IN3AIuxpI"></div></td>
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

<div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src='/wp-content/plugins/neyrinck-custom-forms/shortcode/scripts/typeahead.js'></script>
<?php } 
?>