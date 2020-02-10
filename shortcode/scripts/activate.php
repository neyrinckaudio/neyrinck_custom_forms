<?php
include_once('dbFunctions.php');

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
    include_once('activationPageInfo.php');
    //preflightCheck();
    if (empty($_POST['item_ilok_user_id']) || empty($_POST['item_email2']) || empty($_POST['EMail']))
    {
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
        include_once('activationSubmitRegistrationForm.php');
    }
    else
    {
        //$result = depositLicense($result.info);
        //check for success and act accordingly
        include_once('activationSuccess.php');
    }
    
}
else
{
    include_once('activationPageInfo.php');
    include_once('activationSubmitCodeForm.php');
}
?>