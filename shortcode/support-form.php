<?php
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);
error_reporting(0);

// grab recaptcha library
require_once plugin_dir_path( __FILE__ ) ."scripts/recaptchalib.php";


function ostAPI($data){
  // key = '2D055CF13150317F74C488582AA1AC17' // Live Site
  // key = 'D05D6EBE50B71F4CC2CB5D1F824D12A2' // WordPress test site
  $config = array(
      'url'=>'http://ost.neyrinck.com/api/http.php/tickets.json',
      'key'=>'2D055CF13150317F74C488582AA1AC17'
    );

    #pre-checks
  function_exists('curl_version') or die('CURL support required');
  function_exists('json_encode') or die('JSON support required');

  #set timeout
  set_time_limit(30);

  #curl post
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $config['url']);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.9');
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$config['key']));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result=curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);



  if ($code != 201)
   die('Unable to create ticket: '.$result);

  $ticket_id = (int) $result;
  return $ticket_id;

}

function sendTestEmail($body){
    $to="berniceling@yahoo.com";
    $headers = "From: Neyrinck<postmaster@neyrinck.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    // $headers .= "Bcc: bernice@rejamm.com\r\n";
    // $headers .= "Reply-To: $email\r\n";
    $subject = "TEST";


    mail($to,$subject,$body,$headers);
}

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


  $ignore[] = 'submit';
  // $ignore[] = 'code';
  $ignore[] = 'message';
  $ignore[] = 'win';
  $ignore[] = 'mac';
  $ignore[] = 'DAW';

  $mac = $_POST['mac'];
  $win = $_POST['win'];
  $DAW = $_POST['DAW'];

  $email2 = $_POST['email2'];
  $email = $_POST['email'];

  if ($email != $email2){
    $errors[]="Your email address does not match.";
    echo "<input type='hidden' id='submit_email_match_status' value='error'/>";
  }



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
          if ($index == 'os') echo "<input type='hidden' id='submit_os_status' value='error'/>";
          if ($index == 'firstname') echo "<input type='hidden' id='submit_firstname_status' value='error'/>";
          if ($index == 'lastname') echo "<input type='hidden' id='submit_lastname_status' value='error'/>";
          if ($index == 'subject') echo "<input type='hidden' id='submit_subject_status' value='error'/>";
          if ($index == 'software') echo "<input type='hidden' id='submit_product_status' value='error'/>";
        } else {
          $$index = escapeshellcmd($value);
        }
      }
    }
  }


  if (count($errors) == 0) {
    // prepare for $data
    $product = $_POST['software'];
    $subject = $_POST['subject'];
    $email  = preg_replace('/\s+/', ' ', $email);
    $msg = "DAW : $DAW \r\n";
    $msg .= "OS : $os - $mac $win \r\n";
    $msg .= "$message";

    $data = array(
      'name' => "$firstname $lastname",
      'email' => "$email",
      'subject' => "$subject",
      'message' => "$msg",
      'ip' => $_SERVER['REMOTE_ADDR'],

    );

    $ticket_id = ostAPI($data);
    $subject = "[#$ticket_id] $subject";

    // Send email with the correct header

    // $to="berniceling@yahoo.com";
    $to="support@neyrinck.com";


    $headers = "From: Neyrinck<postmaster@neyrinck.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "Bcc: berniceling@neyrinck.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    $body = "<html><body> User Name : $firstname $lastname<br />Request Date : ".date("Y-m-d h:i:s A")."<br />E-Mail : <a href='mailto:$email'>$email</a><br />Product Name : $product<br />";

    if ($product == 'V-Control Pro') $body .= 'DAW : '.$DAW.'<br />';

    $body .=" OS : $os - $mac $win<br />Message : ".nl2br($message)."</body></html>";

    //echo $body;

    mail($to,$subject,$body,$headers);
    echo "<div class='success'>Your request has been sent. Thank you.</div>";

  }
}

if (count($errors) > 0) {
  echo "<input type='hidden' id='submit_status' value='error'/>";
  // echo "<div class='error'><ul>";
  // foreach ($errors as $error) {
  //   echo "<li>".$error."</li>";
  // }
  // echo "</ul></div>";

}

if (!$_POST['submit'] || $errors > 0) {
?>


<div class='support_form form'>
<form method="post" enctype="multipart/form-data" name="supportForm" id="supportForm">
<table width="80%" border="0" cellspacing="0" cellpadding="0">

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Subject:</td>
<td width="80%" id="subject" ><input type="text" name="subject" value="<?php echo $subject; ?>" />
</tr>

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">First Name:</td>
<td width="80%" id="firstname" ><input type="text" name="firstname" value="<?php echo $firstname; ?>" /></td>
</tr>

<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">Last Name:</td>
<td width="80%" id='lastname'><input type="text" name="lastname" value="<?php echo $lastname; ?>" /></td>
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
<td width="20%" style="vertical-align:middle">Product:</td>
<td width="80%" id="product">
  <div class="styled-select">
    <select name="software"  class="product" value="<?php echo $_POST['software']; ?>">
      <option value=''>&nbsp; &#9662;  Select an option</option>
      <?php
        $products[] = 'Spill';
        $products[] = 'SoundCode For Dolby E Bundle';
        $products[] = 'SoundCode For Dolby E Encoder';
        $products[] = 'SoundCode For Dolby E Decoder';
        $products[] = 'SoundCode Stereo LtRt';
        $products[] = 'SoundCode LtRt Tools';
        $products[] = 'SoundCode Exchange MXF Import';
        $products[] = 'SoundCode Exchange MXF';
        $products[] = 'SoundCode For Dolby Digital';
        $products[] = 'SoundCode For DTS';
        $products[] = 'V-Control Pro';
        $products[] = 'V-Mon';
        $products[] = 'Other';
        foreach ($products as $prod) {
          echo "    <option value='$prod'";
        if (html_entity_decode($_POST['software']) == $prod) {
          echo " selected='selected'";
        }
        echo ">$prod</option>\n";
        }
      ?>
    </select>
  </div>
</td>
</tr>

<tr style='height: 3.5em;' id="DAW">
<td width="20%" style="vertical-align:middle">DAW: </td>
<td width="80%">
  <div class="styled-select">
    <select name="DAW" >
      <option value="Pro Tools" select="selected">&nbsp; &#9662;  Pro Tools</option>
      <?php
        $DAWS[] = "Audition";
        $DAWS[] = "Cubase";
        $DAWS[] = "Digital Performer";
        $DAWS[] = "Final Cut Pro 7";
        $DAWS[] = "Live";
        $DAWS[] = "Logic Pro";
        $DAWS[] = "MIO Console";
        $DAWS[] = "Media Composer";
        $DAWS[] = "Reaper";
        $DAWS[] = "Reason";
        $DAWS[] = "Studio One";
        $DAWS[] = "Sonar";
        $DAWS[] = "Tracktion";
        foreach ($DAWS as $D) {
          echo "    <option value='$D'";
          if (html_entity_decode($_POST['DAW']) == $D) {
          echo " selected='selected'";
          }
          echo ">$D</option>\n";
        }
      ?>
    </select>
  </div>
</td>
</tr>


<tr style='height: 3.5em;'>
<td width="20%" style="vertical-align:middle">System:</td>
<td width="80%" id='os'>
   <div class="styled-select">
    <select name="os" id="os">
       <option value="" select="selected">&nbsp; &#9662;  Select an option</option>
       <?php
        $OS[] = "Mac";
        $OS[] = "Win";
        foreach ($OS as $D) {
          echo "    <option value='$D'";
          if (html_entity_decode($_POST['os']) == $D) {
          echo " selected='selected'";
          }
          echo ">$D</option>\n";
        }

       ?>

    </select>
   </div>
</td>
</tr>

<tr style='height: 3.5em;' id="SystemMac">
<td width="20%" style="vertical-align:middle">Mac:</td>
<td width="80%">
  <div class="styled-select">
    <select name="mac" id="mac">
      <option value=''>&nbsp; &#9662;  Select an option</option>
      <?php
        $macOS[] = "10.4 Tiger";
        $macOS[] = "10.5 Leopard";
        $macOS[] = "10.6 Snow Leopard";
        $macOS[] = "10.7 Lion";
        $macOS[] = "10.8 Mountain Lion";
        $macOS[] = "10.9 Mavericks";
        $macOS[] = "10.10 Yosemite";
        $macOS[] = "10.11 El Capitan";
        $macOS[] = "10.12 Sierra";

        foreach ($macOS as $M) {
          echo "    <option value='$M'";
          if (html_entity_decode($_POST['mac']) == $M) {
            echo " selected='selected'";
          }
          echo ">$M</option>\n";
        }
      ?>


    </select>
  </div>
</td>
</tr>


<tr style='height: 3.5em;' id="SystemWin">
<td width="20%" style="vertical-align:middle">Win:</td>
<td width="80%">
  <div class="styled-select">
    <select name="win" id="win">
      <option value=''>&nbsp; &#9662;  Select an option</option>
      <?php
        $winOS[] = "XP";
        $winOS[] = "Vista";
        $winOS[] = "Windows 7";
        $winOS[] = "Windows 8";
        $winOS[] = "Windows 10";

        foreach ($winOS as $W) {
          echo "    <option value='$W'";
          if (html_entity_decode($_POST['win']) == $W) {
            echo " selected='selected'";
          }
          echo ">$W</option>\n";
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

<!--table>
  <tr>E-mail support request undergoing maintenance. Please send an e-mail to support at neyrinck dot com</tr>
</table-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src='/wp-content/plugins/neyrinck-custom-forms/shortcode/scripts/typeahead.js'></script>

<?php
} ?>
