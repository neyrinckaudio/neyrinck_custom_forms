<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once( plugin_dir_path( __FILE__ ) . 'dbFunctions.php');
include_once( plugin_dir_path( __FILE__ ) . '../../includes/class-neyrinck-custom-forms-eden.php');


$vcpTrialGUID = "CC54A710-4E89-11EE-94E1-00505692C25A";
$vcpStandardProductGUID = "238293B0-3B95-11EE-B381-00505692C25A";
$vcpPlusTrialGUID = "FAD35810-E5C7-11EC-BF81-00505692C25A";
$vcpPlusProductGUID = "ADFFC4D0-DD2D-11EC-8FBF-005056920FF7";
//Use this site key in the HTML code your site serves to users
//6LfduNcUAAAAAMCW0-tF_IDCz2gew_xTS1wYW2Mh

//Use this secret key for communication between your site and reCAPTCHA
//6LfduNcUAAAAAMcvZOqItm0PLqH-x3yjMwEtdqXy server ca[tcha 3 key]
function sendEmail($product_name, $first_name, $last_name, $email1, $activation_code, $ilok_id, $company, $licenseRef)
{
    $subject = "Neyrinck $product_name Activation";
    $to = "store@neyrinck.com";
   
    
    $headers = "From: Neyrinck <store@neyrinck.com>" . "\r\n";
   // $headers .= 'Bcc: berniceling@neyrinck.com' . "\r\n";
    $headers .= "Reply-To: $first_name $last_name<$email1>" . "\r\n";

    $mail_cont = "Name: $first_name $last_name\r\nActivation Code: $activation_code\r\nLicense Reference: $licenseRef\r\niLok ID: $ilok_id\r\nCompany: $company\r\nEmail: $email1\r\n";

    $mail_cont .= "\n\nDear $first_name,\n\nWe have processed activation code $activation_code and delivered a $product_name iLok license to User ID $ilok_id.";
    
    $mail_cont .= " The latest version of $product_name can be downloaded here:\n\n";
    $mail_cont .= "http://www.neyrinck.com/downloads\n\n";
    $mail_cont .= "Thank you for purchasing $product_name.\n\nSincerely,\nPaul Neyrinck";

    // mail to store@neyrinck.com
    mail($to, $subject, $mail_cont, $headers);

    // mail to customer
    $to = "$first_name $last_name<$email1>";
    $headers = "From: Neyrinck <store@neyrinck.com>" . "\r\n";
    //$headers .= 'Bcc: bernice@rejamm.com' . "\r\n";
    $headers .= "Reply-To: Neyrinck<store@neyrinck.com>" . "\r\n";
    mail($to, $subject, $mail_cont, $headers);
}

if (isset($_POST['submitAccountId']) && !empty($_POST['item_account_id']))
{
    $orderId = date('Y-m-d H:i:s') . "VCPTRIAL";
    vcp_processTrialLicenseRequest($vcpStandardProductGUID, $vcpTrialGUID, "V-Control Pro Standard", "standard32", $orderId);
}
else if (isset($_POST['submitPlusAccountId']) && !empty($_POST['item_account_id']))
{
    /*
    if (isset($_POST['g-recaptcha-response'])) {
        $captcha = $_POST['g-recaptcha-response'];
    } else {
        $captcha = false;
    }
    
    if (!$captcha) {
        echo "Google captcha3 not detected.<br>";
        die();
    } else {
        $secret   = '6LfduNcUAAAAAMcvZOqItm0PLqH-x3yjMwEtdqXy';
        $response = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
        );
        // use json_decode to extract json response
        $response = json_decode($response);
    
        if ($response->success === false) {
            echo "<p>The Google captcha3 system has warned that your request might be from a malicious system. Please try again after a few minutes or contact us to help you, weekdays 9 AM to 5 PM PST. Email: support at neyrinck dot com. Facebook @neyrinckaudio.</p>";
            die();
        }
    }
    $score = $response->score;
    //... The Captcha is valid you can continue with the rest of your code
    //... Add code to filter access using $response . score
    if ($response->success==true && $response->score < 0.1) {
        
        echo "Google captcha3 believes this is spam. score = $score<br>";
        die();
    }
*/
    $orderId = date('Y-m-d H:i:s') . "VCPPLUSTRIAL";
    vcp_processTrialLicenseRequest($vcpPlusProductGUID, $vcpPlusTrialGUID, "V-Control Pro Plus", "plus2", $orderId);
}
else
{
    include_once('vcpTrialSubmitAccountForm.php');
}

function vcp_processTrialLicenseRequest($productGuid, $trialSkuGuid, $productName, $gtagProductId, $orderId)
{
    try 
    {
        $ilok_user_id = $_POST['item_account_id'];
        $eden = new Neyrinck_Custom_Forms_Eden();
        
        $info = $eden->findUserByAccountId($ilok_user_id);
        if (isset($info['error']))
        {
            throw new Exception($info['error']);
        }
        $ilokIdTest = $info['accountId'];
        
        if ($ilokIdTest != $ilok_user_id)
        {
            throw new Exception("iLok User ID is not valid");
        }
        $info = $eden->findUserLicenseBySKU($productGuid, $ilok_user_id);
        if (isset($info['error']))
        {
            throw new Exception($info['error']);
        }
        $licenses =  $info['licenses'];
        if (is_array($licenses))
        {
            $existingTrialLicenseRef = '';
            foreach ($licenses as $license)
            {
                //echo json_encode($license);
                //echo "<br><br>";
                if (isset($license['licenseType']))
                {
                    if ($license['licenseType'] === 'TRIAL')
                    {
                        $existingTrialLicenseRef = $license['licenseGuid'];
                        //echo $existingTrialLicenseRef;
                        //echo "<br><br>";
                        break;
                    }
                }
            }
            if (strlen($existingTrialLicenseRef) > 0)
            {
                /* removed, no need to track fails
                echo "<script>gtag('event', 'vcp_trial_fail', {'product':'standard3','ilok_id':'".$ilok_user_id."'});</script>";

                echo "<script>fbq('track', 'Lead', {'content_category':'vcptrial','content_name':'standard3fail', 'value':'".$ilok_user_id."'});</script>";
                */
                echo "The iLok system indicates your iLok account already has a ".$productName." trial license.<br>";
                echo "License Reference: ".$existingTrialLicenseRef."<br>";
            }
            else 
            {
                
                $guids = [$trialSkuGuid];
                $info = $eden->depositSkus($guids, $ilok_user_id, $orderId);
                if (isset($info['error']))
                {
                    throw new Exception($info['error']);
                }
                $licenseRef = $info['depositReference'];
                
                echo "<script>gtag('event', 'conversion', {'send_to': 'AW-817619326/-FpvCIrA_OYCEP7C74UD'});</script>";
                echo "<script>gtag('event', 'vcp_trial', {'product': '".$gtagProductId."','ilok_id':'".$ilok_user_id."'});</script>";
                //facebook tracking, not useful at this time. echo "<script>fbq('track', 'Lead', {'content_category':'vcptrial','content_name':'standard3', 'value':'".$ilok_user_id."'});</script>";
                
                echo "A ".$productName." trial license has been deposited to account $ilok_user_id. When you are ready to try V-Control Pro, launch iLok License Manager on your computer and activate the license.<br>";
                echo "License Reference: $licenseRef<br>";
                echo "You must activate the license to your computer or an iLok USB key using iLok License Manager. Please go to <a href='https://www.ilok.com' target='_blank'>iLok.com</a> for more information.<br>";
                
                echo "You can download the V-Control Pro installer <a href='https://neyrinck.com/downloads/v-control-pro'>here</a>.";
                //include_once('vcpTrialSubmitRegistrationForm.php');
                
            }
            
        }
        else {
            echo "A system error has occurred. Please contact support at neyrinck.com/help.<br>";
            if (is_string($licenses)){
                echo $licenses."<br>";
            }
        }
    }
    catch  (Exception $e)
    {
        $errorMsg = $e->getMessage();
        echo "$errorMsg";
        echo "<script>gtag('event', 'submiterror', {'event_category': 'vcptrial','event_label': 'Trial License Deposit Error'});</script>";
        include_once('vcpTrialSubmitAccountForm.php');
    }
}
?>