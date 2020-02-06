<?php


// grab  libraries
require_once "scripts/recaptchalib.php";
require_once "scripts/vendor/autoload.php";
require_once "scripts/twilio-php-master/Twilio/autoload.php";



use Twilio\Rest\Client;
use Twilio\Rest\Lookups;

if ($_POST['submit'] == 'Send') {

   $phone = $_POST['phone'];
  if (!$phone){
    echo "<p style='font-weight:bold; color : red'>Please enter a phone number.</p>";
    $errors[]= "phone number not entered Error";
  }

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

  $errors = [];
  if($valid != TRUE) {
    echo "<p style='font-weight:bold; color : red'>Please prove that you are human.</p>";
    $errors[]= "reCaptcha Error";

  }
  
  
  $sid = 'ACa252a42d49d8ee2dff1cdc9e179e6992';
  $token = 'd45cb74706133951ae76f73f11451ab8';
  
    if ($phone) {

    $CountryCode = $_POST['countryCode'];
    // varify number
    $lookups_client = new Lookups_Services_Twilio($sid, $token);
   
   try {
      // Make a call to the Lookup API
      $number = $lookups_client->phone_numbers->get($phone, array("CountryCode" => $CountryCode));
      // Print the E.164 format
      $E164 = $number->phone_number;
      $client2 = new Client($sid, $token);
    
      $client2->messages->create(
          // the number you'd like to send the message to
          $E164,
          array(
              // A Twilio phone number you purchased at twilio.com/console
              'from' => '+14152126219',
              // the body of the text message you'd like to send
              'body' => "https://itunes.apple.com/us/app/v-console/id1068680239"
          )
      );

      $url = 'https://neyrinck.com/products/v-control-pro-bundle/a-text-message-is-on-the-way/';
      ob_start();
      header('Location: '.$url);
      ob_end_flush();
      die();
    
    } catch (Exception $e) {

        email ($e);
        echo "<p style='font-weight:bold; color : red'>The phone number you entered is either invalid or not a mobile number.<br/>Please enter a valid mobile phone number and check that the Country Code is correct.</p>";

        $errors[]= "phone number Error";
    } 
    }

}

if (count($errors) > 0) {
  echo "<input type='hidden' id='submit_status' value='error'/>"; 
} 

if (!$_POST['submit'] || $errors > 0) {
?>




<div class='support_form form' style='width: 100%'>
<form method="post" enctype="multipart/form-data" name="phoneForm" id="phoneForm">




<div class='floatLeft50'>
<p style='font-weight: bold; font-size:1.2em;  padding:20px; margin-left: 20px; margin-right: 20px; '>Take control with the V-Console app for iPhone and iPad.</p>
<p>Enter your phone number below and we will message an App Store link to you for V-Console on your iPhone and iPad. V-Console is a <b>free</b> download</p>
 <div style='padding: 2em; margin-left: 10px'>
 
    <label style='display: inline-block; vertical-align: middle;'>Country Code: </label><select name="countryCode" id="" style='display: inline-block; vertical-align: middle; margin-left: 10px;'>
          <option value="US" Selected>United States</option>
          <optgroup label="Other countries">
            <option value="AF">Afghanistan</option>
            <option value="AX">Åland Islands</option>
            <option value="AL">Albania</option>
            <option value="DZ">Algeria</option>
            <option value="AS">American Samoa</option>
            <option value="AD">Andorra</option>
            <option value="AO">Angola</option>
            <option value="AI">Anguilla</option>
            <option value="AQ">Antarctica</option>
            <option value="AG">Antigua and Barbuda</option>
            <option value="AR">Argentina</option>
            <option value="AM">Armenia</option>
            <option value="AW">Aruba</option>
            <option value="AU">Australia</option>
            <option value="AT">Austria</option>
            <option value="AZ">Azerbaijan</option>
            <option value="BS">Bahamas</option>
            <option value="BH">Bahrain</option>
            <option value="BD">Bangladesh</option>
            <option value="BB">Barbados</option>
            <option value="BY">Belarus</option>
            <option value="BE">Belgium</option>
            <option value="BZ">Belize</option>
            <option value="BJ">Benin</option>
            <option value="BM">Bermuda</option>
            <option value="BT">Bhutan</option>
            <option value="BO">Bolivia, Plurinational State of</option>
            <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
            <option value="BA">Bosnia and Herzegovina</option>
            <option value="BW">Botswana</option>
            <option value="BV">Bouvet Island</option>
            <option value="BR">Brazil</option>
            <option value="IO">British Indian Ocean Territory</option>
            <option value="BN">Brunei Darussalam</option>
            <option value="BG">Bulgaria</option>
            <option value="BF">Burkina Faso</option>
            <option value="BI">Burundi</option>
            <option value="KH">Cambodia</option>
            <option value="CM">Cameroon</option>
            <option value="CA">Canada</option>
            <option value="CV">Cape Verde</option>
            <option value="KY">Cayman Islands</option>
            <option value="CF">Central African Republic</option>
            <option value="TD">Chad</option>
            <option value="CL">Chile</option>
            <option value="CN">China</option>
            <option value="CX">Christmas Island</option>
            <option value="CC">Cocos (Keeling) Islands</option>
            <option value="CO">Colombia</option>
            <option value="KM">Comoros</option>
            <option value="CG">Congo</option>
            <option value="CD">Congo, the Democratic Republic of the</option>
            <option value="CK">Cook Islands</option>
            <option value="CR">Costa Rica</option>
            <option value="CI">Côte d'Ivoire</option>
            <option value="HR">Croatia</option>
            <option value="CU">Cuba</option>
            <option value="CW">Curaçao</option>
            <option value="CY">Cyprus</option>
            <option value="CZ">Czech Republic</option>
            <option value="DK">Denmark</option>
            <option value="DJ">Djibouti</option>
            <option value="DM">Dominica</option>
            <option value="DO">Dominican Republic</option>
            <option value="EC">Ecuador</option>
            <option value="EG">Egypt</option>
            <option value="SV">El Salvador</option>
            <option value="GQ">Equatorial Guinea</option>
            <option value="ER">Eritrea</option>
            <option value="EE">Estonia</option>
            <option value="ET">Ethiopia</option>
            <option value="FK">Falkland Islands (Malvinas)</option>
            <option value="FO">Faroe Islands</option>
            <option value="FJ">Fiji</option>
            <option value="FI">Finland</option>
            <option value="FR">France</option>
            <option value="GF">French Guiana</option>
            <option value="PF">French Polynesia</option>
            <option value="TF">French Southern Territories</option>
            <option value="GA">Gabon</option>
            <option value="GM">Gambia</option>
            <option value="GE">Georgia</option>
            <option value="DE">Germany</option>
            <option value="GH">Ghana</option>
            <option value="GI">Gibraltar</option>
            <option value="GR">Greece</option>
            <option value="GL">Greenland</option>
            <option value="GD">Grenada</option>
            <option value="GP">Guadeloupe</option>
            <option value="GU">Guam</option>
            <option value="GT">Guatemala</option>
            <option value="GG">Guernsey</option>
            <option value="GN">Guinea</option>
            <option value="GW">Guinea-Bissau</option>
            <option value="GY">Guyana</option>
            <option value="HT">Haiti</option>
            <option value="HM">Heard Island and McDonald Islands</option>
            <option value="VA">Holy See (Vatican City State)</option>
            <option value="HN">Honduras</option>
            <option value="HK">Hong Kong</option>
            <option value="HU">Hungary</option>
            <option value="IS">Iceland</option>
            <option value="IN">India</option>
            <option value="ID">Indonesia</option>
            <option value="IR">Iran, Islamic Republic of</option>
            <option value="IQ">Iraq</option>
            <option value="IE">Ireland</option>
            <option value="IM">Isle of Man</option>
            <option value="IL">Israel</option>
            <option value="IT">Italy</option>
            <option value="JM">Jamaica</option>
            <option value="JP">Japan</option>
            <option value="JE">Jersey</option>
            <option value="JO">Jordan</option>
            <option value="KZ">Kazakhstan</option>
            <option value="KE">Kenya</option>
            <option value="KI">Kiribati</option>
            <option value="KP">Korea, Democratic People's Republic of</option>
            <option value="KR">Korea, Republic of</option>
            <option value="KW">Kuwait</option>
            <option value="KG">Kyrgyzstan</option>
            <option value="LA">Lao People's Democratic Republic</option>
            <option value="LV">Latvia</option>
            <option value="LB">Lebanon</option>
            <option value="LS">Lesotho</option>
            <option value="LR">Liberia</option>
            <option value="LY">Libya</option>
            <option value="LI">Liechtenstein</option>
            <option value="LT">Lithuania</option>
            <option value="LU">Luxembourg</option>
            <option value="MO">Macao</option>
            <option value="MK">Macedonia, the former Yugoslav Republic of</option>
            <option value="MG">Madagascar</option>
            <option value="MW">Malawi</option>
            <option value="MY">Malaysia</option>
            <option value="MV">Maldives</option>
            <option value="ML">Mali</option>
            <option value="MT">Malta</option>
            <option value="MH">Marshall Islands</option>
            <option value="MQ">Martinique</option>
            <option value="MR">Mauritania</option>
            <option value="MU">Mauritius</option>
            <option value="YT">Mayotte</option>
            <option value="MX">Mexico</option>
            <option value="FM">Micronesia, Federated States of</option>
            <option value="MD">Moldova, Republic of</option>
            <option value="MC">Monaco</option>
            <option value="MN">Mongolia</option>
            <option value="ME">Montenegro</option>
            <option value="MS">Montserrat</option>
            <option value="MA">Morocco</option>
            <option value="MZ">Mozambique</option>
            <option value="MM">Myanmar</option>
            <option value="NA">Namibia</option>
            <option value="NR">Nauru</option>
            <option value="NP">Nepal</option>
            <option value="NL">Netherlands</option>
            <option value="NC">New Caledonia</option>
            <option value="NZ">New Zealand</option>
            <option value="NI">Nicaragua</option>
            <option value="NE">Niger</option>
            <option value="NG">Nigeria</option>
            <option value="NU">Niue</option>
            <option value="NF">Norfolk Island</option>
            <option value="MP">Northern Mariana Islands</option>
            <option value="NO">Norway</option>
            <option value="OM">Oman</option>
            <option value="PK">Pakistan</option>
            <option value="PW">Palau</option>
            <option value="PS">Palestinian Territory, Occupied</option>
            <option value="PA">Panama</option>
            <option value="PG">Papua New Guinea</option>
            <option value="PY">Paraguay</option>
            <option value="PE">Peru</option>
            <option value="PH">Philippines</option>
            <option value="PN">Pitcairn</option>
            <option value="PL">Poland</option>
            <option value="PT">Portugal</option>
            <option value="PR">Puerto Rico</option>
            <option value="QA">Qatar</option>
            <option value="RE">Réunion</option>
            <option value="RO">Romania</option>
            <option value="RU">Russian Federation</option>
            <option value="RW">Rwanda</option>
            <option value="BL">Saint Barthélemy</option>
            <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
            <option value="KN">Saint Kitts and Nevis</option>
            <option value="LC">Saint Lucia</option>
            <option value="MF">Saint Martin (French part)</option>
            <option value="PM">Saint Pierre and Miquelon</option>
            <option value="VC">Saint Vincent and the Grenadines</option>
            <option value="WS">Samoa</option>
            <option value="SM">San Marino</option>
            <option value="ST">Sao Tome and Principe</option>
            <option value="SA">Saudi Arabia</option>
            <option value="SN">Senegal</option>
            <option value="RS">Serbia</option>
            <option value="SC">Seychelles</option>
            <option value="SL">Sierra Leone</option>
            <option value="SG">Singapore</option>
            <option value="SX">Sint Maarten (Dutch part)</option>
            <option value="SK">Slovakia</option>
            <option value="SI">Slovenia</option>
            <option value="SB">Solomon Islands</option>
            <option value="SO">Somalia</option>
            <option value="ZA">South Africa</option>
            <option value="GS">South Georgia and the South Sandwich Islands</option>
            <option value="SS">South Sudan</option>
            <option value="ES">Spain</option>
            <option value="LK">Sri Lanka</option>
            <option value="SD">Sudan</option>
            <option value="SR">Suriname</option>
            <option value="SJ">Svalbard and Jan Mayen</option>
            <option value="SZ">Swaziland</option>
            <option value="SE">Sweden</option>
            <option value="CH">Switzerland</option>
            <option value="SY">Syrian Arab Republic</option>
            <option value="TW">Taiwan, Province of China</option>
            <option value="TJ">Tajikistan</option>
            <option value="TZ">Tanzania, United Republic of</option>
            <option value="TH">Thailand</option>
            <option value="TL">Timor-Leste</option>
            <option value="TG">Togo</option>
            <option value="TK">Tokelau</option>
            <option value="TO">Tonga</option>
            <option value="TT">Trinidad and Tobago</option>
            <option value="TN">Tunisia</option>
            <option value="TR">Turkey</option>
            <option value="TM">Turkmenistan</option>
            <option value="TC">Turks and Caicos Islands</option>
            <option value="TV">Tuvalu</option>
            <option value="UG">Uganda</option>
            <option value="UA">Ukraine</option>
            <option value="AE">United Arab Emirates</option>
            <option value="GB">United Kingdom</option>
            <option value="US">United States</option>
            <option value="UM">United States Minor Outlying Islands</option>
            <option value="UY">Uruguay</option>
            <option value="UZ">Uzbekistan</option>
            <option value="VU">Vanuatu</option>
            <option value="VE">Venezuela, Bolivarian Republic of</option>
            <option value="VN">Viet Nam</option>
            <option value="VG">Virgin Islands, British</option>
            <option value="VI">Virgin Islands, U.S.</option>
            <option value="WF">Wallis and Futuna</option>
            <option value="EH">Western Sahara</option>
            <option value="YE">Yemen</option>
            <option value="ZM">Zambia</option>
            <option value="ZW">Zimbabwe</option>
          </optgroup>
    </select>
  
      <p>Enter your phone number: <input type="tel" name="phone" value="<?php echo $phone; ?>" /></p>
      <p style='font-size: 0.9em'>We will not save your phone number and send any other messages to you.</p>
  </div>
</div>
<!-- <div class='floatLeft50'><img src="https://neyrinck.com/wp-content/uploads/2016/09/Neyrinck_V-PlugIn_iPad_iPhone_Spill.jpg" alt="V-PlugIn iPad iPhone Controlling Spill" width="600" height="321">
</div> -->

<div class='floatLeft50 PaddingTop6EmForDesktop'><img src="https://s3-us-west-2.amazonaws.com/neyrinckdownloads/newsletter/images/Optimized-iPhone-iPad-Macbook-V-Console.jpg" alt="V-Console DAW Controller Runs On Phones, Tablets, and Browsers" >
</div>


<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr style='height: 3.5em;'>
    <td colspan="2">
      <div style='padding: 2em 0em; width: 302px; margin: 0 auto;' class="g-recaptcha" data-sitekey="6LfiXuISAAAAALP4gpHG-9yuN_xc1X0IN3AIuxpI">
    </div>
    </td>
  </tr>

  <tr>
    <td colspan="2">
      <div style='padding: 2em 0em; width: 277px; margin: 0 auto;'>
        <input id='download_btn' class='download_btn' type="submit" name="submit" value="Send"/>
        <p style='text-align: center;'>
          <a style='font-weight: bold' href="https://neyrinck.com/products/v-control-pro-bundle/features/#pm">Not Now</a>
        </p>
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