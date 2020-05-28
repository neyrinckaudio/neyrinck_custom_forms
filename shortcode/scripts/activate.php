<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once( plugin_dir_path( __FILE__ ) . 'dbFunctions.php');
include_once( plugin_dir_path( __FILE__ ) . '../../includes/class-neyrinck-custom-forms-eden.php');

$debug = true;
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

if (isset($_POST['submitActivationCode']) && !empty($_POST['item_activation_code']))
{
    try 
    {
        // retrieve activation info
        $result = getActivationInfo($_POST['item_activation_code']);
        if ($result['date_manufactured'] == '0000-00-00') {
            throw new Exception("Activation Error 1:  Please contact Neyrinck at <a href='mailto:support@neyrinck.com'>support@neyrinck.com</a. and report this error.");
        }
        if ($result['frozen'] == '1') {
            throw new Exception("Activation Error 2:  Please contact Neyrinck at <a href='mailto:support@neyrinck.com'>support@neyrinck.com</a> and report this error.");
        }
        if ($result['success'] == false) {
            throw new Exception("Activation Code Not Found");
        }
        if ($result["registration_id"] != '0') {
            throw new Exception("License already registered.");
        }
        $ilok_user_id = $result["ilok_user_id"];
        $activation_code = $_POST['item_activation_code'];
        include_once('activationPageInfo.php');
        include_once('activationSubmitRegistrationForm.php');
    }
    catch  (Exception $e)
    {
        include_once('activationPageInfo.php');
        include_once('activationSubmitCodeForm.php');
        $errorMsg = $e->getMessage();
        include_once('activationError.php');
    }
}
else if (isset($_POST['submitActivationRegistration']))
{
    try 
    {
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
        //preflightCheck();
        if (empty($_POST['item_ilok_user_id']) || empty($_POST['item_email2']) || empty($_POST['EMail']))
        {
            include_once('activationPageInfo.php');
            if (empty($_POST['item_ilok_user_id']))
            {
            echo "<p>Please enter iLok User ID.</p>";
            }
            if (empty($_POST['EMail']))
            {
            echo "<p>Please enter email address.</p>";
            }
            if (empty($_POST['item_email2']))
            {
            echo "<p>Please enter email confirmation.</p>";
            }
            throw new Exception("Incomplete form entry.");
        }
        else
        {
            $eden = new Neyrinck_Custom_Forms_Eden();

            $ilokIdTest = $eden->findUserByAccountId($ilok_user_id);
            if ($ilokIdTest != $ilok_user_id)
            {
                throw new Exception("iLok User ID is not valid");
            }
            $result = getActivationInfo($_POST['item_activation_code']);
            if ($result['success'] != true)
            {
                throw new Exception("Database check failed. Please try again or contact support@neyrinck.com.");
            }
            $ilok_product_id = $result["ilok_product_id"];
            $ilok_asset_id = $result["ilok_asset_id"];
            $result = getProductInfo($ilok_product_id);
            if ($result['success'] != true)
            {
                throw new Exception("Database info failed:getProductInfo failed. Please contact support@neyrinck.com.");
            }
            $product_name = $result['product_name'];
            $product_guid = $result['product_guid'];
            $license_type = $result['license_type'];
            $terms_guid = $result["terms_guid"];
            $sku_guid = $result["sku_guid"];
            $licenseRef = "error";
            
            if ($license_type == 'full')
            { 
                if (!$product_guid)
                {
                    throw new Exception("Product GUID not found. License deposit failed. Please contact support@neyrinck.com.");
                }
                $drightGuidArray = $eden->depositFullLicense($product_guid, $ilok_user_id, $activation_code);
            }
            else if ($license_type == 'upgrade')
            {
                if (!$sku_guid)
                {
                    throw new Exception("SKU GUID not found. License deposit failed. Please contact support@neyrinck.com.");
                }
                $drightGuidArray = $eden->depositFullLicense($sku_guid, $ilok_user_id, $activation_code);
               // $this->deposit_license_by_SKU($this->product_id, $this->iLok_id, $this->unique_order_id);
            }
            else if ($license_type == 'rental')
            {
                if (!$terms_guid)
                {
                    throw new Exception("Terms GUID not found. License deposit failed. Please contact support@neyrinck.com.");
                }
                $drightGuidArray = $eden->depositLicenseWithTerms($product_guid, $ilok_user_id, $activation_code, $terms_guid);
            }
            else
            {
                throw new Exception("error: License type not handled. Please contact support@neyrinck.com.");
            }
            // $drightGuidArray is null if it failed
            if (!$drightGuidArray)
            {
                throw new Exception("License deposit failed. Please try again or contact support@neyrinck.com.");
            }
            
            $depositedDrights = $drightGuidArray['depositedDrights'];
            $depositedDright = $depositedDrights[0];
            $licenseRef = $depositedDright['drightGuid'];
            updateLicenseRef($licenseRef, $activation_code);
            $result = getCustomer($email1);
            if ($result['success'] == false)
            {
                addCustomer($first_name, $last_name, $email1, $company, $ilok_user_id);
                $result = getCustomer($email1);
            }
            $customers_id = $result['customers_id'];
            $registration_id = addProductRegistration($product_id, $customers_id);
            updateActivationInfo($ilok_asset_id, $registration_id, $ilok_user_id);
            sendEmail($product_name, $first_name, $last_name, $email1, $activation_code, $ilok_user_id, $company, $licenseRef);
            include_once('activationSuccess.php');
            echo "<p>License Reference: $licenseRef</p>";
        }
    }
    catch (Exception $e) {
        $msg = $e->getMessage();
        echo "<p>$msg</p>";
        include_once('activationSubmitRegistrationForm.php');
    }
}
else
{
    include_once('activationPageInfo.php');
    include_once('activationSubmitCodeForm.php');
}
?>