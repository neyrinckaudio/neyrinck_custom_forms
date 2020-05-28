<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once( plugin_dir_path( __FILE__ ) . 'dbFunctions.php');
include_once( plugin_dir_path( __FILE__ ) . '../../includes/class-neyrinck-custom-forms-eden.php');


$vcpTrialGUID = "2A666090-6653-11E6-80BB-005056A204F3";
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
    if ($response->success==true && $response->score < 0.3) {
        
        echo "Google captcha3 believes this is spam. score = $score<br>";
        die();
    }

    try 
    {
        $ilok_user_id = $_POST['item_account_id'];
        $eden = new Neyrinck_Custom_Forms_Eden();
        
        $ilokIdTest = $eden->findUserByAccountId($ilok_user_id);
        if ($ilokIdTest != $ilok_user_id)
        {
            throw new Exception("iLok User ID is not valid");
        }
        $licenses = $eden->findUserLicenseBySKU($vcpTrialGUID, $ilok_user_id);
        if (count($licenses) > 0)
        {
            foreach ($licenses as $iter69)
            {
            //echo json_encode($iter69);
            //echo "<br><br>";
            }
            $depositedDright = $licenses[0];
            $licenseRef = $depositedDright['drightGuid'];
            echo "The iLok system indicates your iLok account already has a trial license.<br>";
            echo "License Reference: $licenseRef<br>";
        }
        else
        {
            $orderId = date('Y-m-d H:i:s') . "VCPTRIAL";
            
            $drightGuidArray = $eden->depositFullLicense($vcpTrialGUID, $ilok_user_id, $orderId);
            // $drightGuidArray is null if it failed
            if ($drightGuidArray == null) {
                throw new Exception("License depost failed.");
            }
//            echo json_encode($drightGuidArray);
            $depositedDrights = $drightGuidArray['depositedDrights'];
            $depositedDright = $depositedDrights[0];
            $licenseRef = $depositedDright['drightGuid'];
        echo "<script>ga('send', 'event', 'vcptrial', 'deposit', 'Trial License Deposit Success');</script>";
        echo "A trial license has been deposited to account $ilok_user_id. When you are ready to try V-Control Pro, launch iLok License Manager on your computer and activate the license.<br>";
        echo "License Reference: $licenseRef<br>";
        echo "You can download the V-Control Pro installer <a href='https://neyrinck.com/downloads/v-control-pro'>here</a>.";
        //include_once('vcpTrialSubmitRegistrationForm.php');
        }
    }
    catch  (Exception $e)
    {
        $errorMsg = $e->getMessage();
        echo "$errorMsg";
        echo "<script>ga('send', 'event', 'vcptrial', 'submiterror', 'Trial License Deposit Error');</script>";
        include_once('vcpTrialSubmitAccountForm.php');
        
    }
}

else
{
    include_once('vcpTrialSubmitAccountForm.php');
}

?>