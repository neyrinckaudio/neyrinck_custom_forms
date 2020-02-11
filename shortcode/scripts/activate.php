<?php
include_once('dbFunctions.php');
include_once('ilokFunctions.php');

if (isset($_POST['submitActivationCode']) && !empty($_POST['item_activation_code']))
{
    // retrieve activation info
    $result = getActivationInfo($_POST['item_activation_code']);
    $ilok_user_id = $result["ilok_user_id"];
    if ($result["success"] == true)
    {
        $activation_code = $_POST['item_activation_code'];
        include_once('activationPageInfo.php');
        include_once('activationSubmitRegistrationForm.php');
    }
    else
    {
        include_once('activationPageInfo.php');
        include_once('activationSubmitCodeForm.php');
        $errorMsg = $result["msg"];
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
            $ilokIdTest = findUserByAccountId($ilok_user_id);
            if ($ilokIdTest != $ilok_user_id)
            {
                throw new Exception("iLok User ID is not valid");
            }
            $result = getActivationInfo($_POST['item_activation_code']);
            if ($result['success'] != true)
            {
                throw new Exception("getActivationInfo failed.");
            }
            $ilok_user_id = $result["ilok_user_id"];
            $ilok_product_id = $result["ilok_product_id"];
            $result = getProductInfo($ilok_product_id);
            if ($result['success'] != true)
            {
                throw new Exception("getProductInfo failed.");
            }
            $product_guid = $result['product_guid'];
            $license_type = $result['license_type'];
            $dright_guid = "error";
            if ($license_type == 'full')
            { 
                $drightGuidArray = depositFullLicense($product_guid, $ilok_user_id, $activation_code);
                // $drightGuidArray is null if it failed
                if (drightGuidArray) {
                    $depositedDrights = $drightGuidArray['depositedDrights'];
                    $depositedDright = $depositedDrights[0];
                    $dright_guid = $depositedDright['drightGuid'];
                    updateLicenseRef($dright_guid, $activation_code);
                } else {
                    throw new Exception("depositFullLicense failed.");
                }

            }
            if ($license_type == 'upgrade')
            {
               // $this->deposit_license_by_SKU($this->product_id, $this->iLok_id, $this->unique_order_id);
            }
            if ($license_type == 'rental')
            {
               // $this->deposit_license_with_terms($this->product_id, $this->iLok_id, $this->unique_order_id);
            }
    
            include_once('activationSuccess.php');
            echo "<p>License Reference: $dright_guid</p>";
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