<?php
$dbconnection = NULL;
function getConnection()
{
    if ($dbconnection == NULL)
    {
        $dbconnection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        if (mysqli_connect_errno())
        {
            $dbconnection = NULL;
            return $dbconnection;
        }
    }
    return $dbconnection;
}

function getActivationInfo($activation_code)
{
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    $query = "SELECT ilok_asset_id, registration_id, ilok_product_id, ilok_user_id, ilok_license_code, date_manufactured FROM main.ilok_assets WHERE activation_code = '$activation_code'";
    $count_query = mysqli_query($connection, $query) or die("Couldnt execute query");
    $check = mysqli_fetch_array($count_query);
    if ($check)
    {
        $result["ilok_asset_id"] = $check['ilok_asset_id'];
        $result["activation_code"] = $activation_code;
    $result["ilok_user_id"] = $check['ilok_user_id'];
    $result["ilok_product_id"] = $check['ilok_product_id'];
    $result["registration_id"] = $check['registration_id'];
    $result["frozen"] = $check['frozen'];
    $result["date_manufactured"] = $check['date_manufactured'];
    $result["success"] = true;
    }
    return $result;
}

function getProductInfo($ilok_product_id)
{
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    $query = "SELECT license_type, product_id, name, product_guid, terms_guid, surrender_guid, sku_guid FROM main.ilok_products WHERE ilok_product_id = '$ilok_product_id'";
    $product_query = mysqli_query($connection, $query ) or die("Couldnt execute query 3");
    $ilok_product = mysqli_fetch_array($product_query);
    if ($ilok_product)
    {
        $result["success"] = true;
        $result["license_type"] = $ilok_product['license_type'];
        $result["product_id"] = $ilok_product['product_id'];
        $result["product_name"] = $ilok_product["name"];
        $result["product_guid"] = $ilok_product["product_guid"];
        $result["terms_guid"] = $ilok_product["terms_guid"];
        $result["sku_guid"] = $ilok_product["sku_guid"];
        $result["surrender_guid"] = $ilok_product["surrender_guid"];
    }
    return $result;
}

function updateLicenseRef($license_ref, $activation_code){
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    $query = "UPDATE ilok_assets SET license_ref = '$license_ref' WHERE activation_code ='$activation_code'";
    $qresult = mysqli_query($connection, $query)or die("Couldnt execute query updateLicenseRef");
}

function updateActivationInfo($ilok_asset_id, $registration_id, $ilok_id)
{
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    // modify iLok asset ID to have registration id
    $sql = "UPDATE main.ilok_assets SET registration_id = '$registration_id', ilok_user_id = '$ilok_id' where ilok_asset_id = '$ilok_asset_id'";
    //echo "$sql\n";
    $sql_result = mysqli_query($connection, $sql) or die("Error:  could not modify ilok asset");
}

function addProductRegistration($product_id, $customers_id)
{
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    // add registration record
    $datetime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO main.products_registrations (product_id, customers_id, registration_type, registration_datetime) VALUES ('$product_id', '$customers_id', 'netauth', '$datetime')";
    // echo "$sql\n";
    $sql_result = mysqli_query($connection, $sql ) or die("Error:  could not add product registration");
    $registration_id = mysqli_insert_id($connection);
    return $registration_id;
}

function getCustomer($email1)
{
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    $query = "SELECT customers_id, customers_lastname, customers_firstname FROM main.customers WHERE customers_email_address = '$email1'";
    $cust_query = mysqli_query($connection, $query) or die("Couldnt execute query 2");
    $customer = mysqli_fetch_array($cust_query);
    if ($customer)
    {
    $result["success"] = true;
    $result["customers_id"] = $customer["customers_id"];
    $result["customers_lastname"] = $customer["customers_lastname"];
    $result["customers_firstname"] = $customer["customers_firstname"];
    }
    return $result;
}

function addCustomer($first_name, $last_name, $email1, $company, $ilok_id)
{
    $result = array(
        "success" => false
    );
    $connection = getConnection();
    if (!$connection)
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    }
    $sql = "INSERT INTO main.customers (customers_firstname, customers_lastname, customers_email_address, organization, ilok_id) VALUES ('$first_name', '$last_name', '$email1', '$company', '$ilok_id')";
    $sql_result = mysqli_query($connection, $sql);
    
    if(!$sql_result){
        $result["success"] = false;
        $result["msg"] = "Error:  The e-mail address $email1 is already in use by a user with a different last name.  Please use a different e-mail address.";
        return $result;
    }
    $result["success"] = true;
    return $result;
}

function doActivation($ilok_id, $activation_code, $first_name, $last_name, $company, $address1, $address2, $city, $state, $postal_code, $country, $email1, $email2) {

    $result = array(
        "success" => true
    );

    $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
    // Check connection
    if (mysqli_connect_errno())
    {
        $result["success"] = false;
        $result["msg"] = "Database Error: " . mysqli_connect_error();
        return $result;
    } 
    $query = "SELECT ilok_asset_id, registration_id, ilok_product_id, ilok_user_id, ilok_license_code, date_manufactured FROM main.ilok_assets WHERE activation_code = '$activation_code'";
    $asset_query = mysqli_query($connection, $query) or die("Couldnt execute query 1");
    $asset = mysqli_fetch_array($asset_query);

    if (!$asset["ilok_asset_id"]) {
        $result["success"] = false;
        $result["msg"] = "The activation code is not valid.  Please check that you have entered the activation code correctly.";
        return $result;
    }
    if ($asset["registration_id"] != '0') {
        $result["success"] = false;
        $result["msg"] = "The activation code has already been used to activate an iLok license.  Please contact support@neyrinck.com if you believe this is an error.";
        return $result;
    }
    if ($asset["ilok_user_id"] != '') {
        if ($asset["ilok_user_id"] != $ilok_id){
            $result["success"] = false;
            $result["msg"] = "The iLok user ID you entered does not match the iLok user ID for this license.  Please contact support@neyrinck.com if you believe this is an error.";
            return $result;
        }
    }
    if ($asset['date_manufactured'] == '0000-00-00') {
        $result["success"] = false;
        $result["msg"] = "Activation Error:  Please contact Neyrinck at <a href='mailto:support@neyrinck.com'>support@neyrinck.com</a> and report this error.";
        return $result;
    }

    $ilok_asset_id = $asset["ilok_asset_id"];
    $ilok_product_id = $asset["ilok_product_id"];
    $ilok_license_code = $asset["ilok_license_code"];

    // get product id
    $query = "SELECT product_id, name FROM main.ilok_products WHERE ilok_product_id = '$ilok_product_id'";
    $product_query = mysqli_query($connection, $query ) or die("Couldnt execute query 3");
    $ilok_product = mysqli_fetch_array($product_query);
    $product_id = $ilok_product["product_id"];
    $product_name = $ilok_product["name"];


    // CHANGES BEGIN!!!!
    // Make direct deposit here!!
    include_once('iLokDeposit.php');
    $license = new iLokDeposit($activation_code, $product_id, $ilok_id);


    if ( $license->status == 'failed'){

        $result["success"] = false;

        if ($license->error_message) {
            $result["msg"] = $license->error_message;
        } else {
            $result["msg"] = "License Deposit Error :  Please contact Neyrinck at <a href='mailto:support@neyrinck.com'>support@neyrinck.com</a> and report this error.";
        }
        
        return $result;
    }

  

    // look for customer in database
    // check if e-mail is in database
    $query = "SELECT customers_id, customers_lastname FROM main.customers WHERE customers_email_address = '$email1'";
    $cust_query = mysqli_query($connection, $query) or die("Couldnt execute query 2");
    $customer = mysqli_fetch_array($cust_query);

    $existing_user = '1'; $update_names = '0';
    $customers_id = $customer["customers_id"];
    if ($customer["customers_id"]) {
        // if ($customer["customers_lastname"] != $last_name)
        if (strcasecmp($customer["customers_lastname"], $last_name)!=0) {
            $update_names = '1';
        }
    }
    else {
        $existing_user = '0';
    }

    if ($update_names = '1'){
        $sql = "UPDATE main.customers SET customers_firstname = '$first_name', customers_lastname = '$last_name', ilok_id = '$ilok_id' WHERE customers_email_address = '$email1'";
        $sql_result = mysqli_query($connection, $sql);
        if(!$sql_result){
            $result["success"] = false;
            $result["msg"] = "Error:  Unable to update information.";
            return $result;
        }
    }

    if ($existing_user == '0') {
        $sql = "INSERT INTO main.customers (customers_firstname, customers_lastname, customers_email_address, organization, ilok_id) VALUES ('$first_name', '$last_name', '$email1', '$company', '$ilok_id')";
        $sql_result = mysqli_query($connection, $sql);
       
        if(!$sql_result){
            $result["success"] = false;
            $result["msg"] = "Error:  The e-mail address $email1 is already in use by a user with a different last name.  Please use a different e-mail address.";
            return $result;
        }

        // verify name was added
        $sql = "SELECT customers_id FROM main.customers WHERE customers_email_address = '$email1'";
        $sql_result = mysqli_query($connection, $sql) or die("Couldnt execute query 2");
        $row = mysqli_fetch_array($sql_result);

        // test if a valid recipient
        $validuserid = '1';
        if (!$row["customers_id"]) {
            $validuserid = '0';
            $result["success"] = false;
            $result["msg"] = "Error:  could not retrieve name added to user list";
            return $result;
        }
        $customers_id = $row["customers_id"];
        //  echo "New customer entry completed\n";
    } else {
        //  echo "Customer e-mail already in database\n";
        // verify iLok ID and write it in if blank
    }

    $result["success"] = true;
    $result["msg"] = "The license has been deposited to you iLok.com account. Please use the iLok License Manager to transfer the license to your iLok USB key.";
   

    // look for default address book entry
    // add registration record
    $datetime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO main.products_registrations (product_id, customers_id, registration_type, registration_datetime) VALUES ('$product_id', '$customers_id', 'netauth', '$datetime')";
    // echo "$sql\n";
    $sql_result = mysqli_query($connection, $sql ) or die("Error:  could not add product registration");
    $registration_id = mysqli_insert_id($connection);

    // modify iLok asset ID to have registration id
    $sql = "UPDATE main.ilok_assets SET registration_id = '$registration_id', ilok_user_id = '$ilok_id' where ilok_asset_id = '$ilok_asset_id'";
    //echo "$sql\n";
    $sql_result = mysqli_query($connection, $sql) or die("Error:  could not modify ilok asset");


    $subject = "Neyrinck $product_name Activation";
    $to = "store@neyrinck.com";
   
    
    $headers = "From: Neyrinck <store@neyrinck.com>" . "\r\n";
   // $headers .= 'Bcc: berniceling@neyrinck.com' . "\r\n";
    $headers .= "Reply-To: $first_name $last_name<$email1>" . "\r\n";

    $mail_cont = "Name: $first_name $last_name\r\nActivation Code: $activation_code\r\niLok ID: $ilok_id\r\nCompany: $company\r\nEmail: $email1\r\nAddress1: $address1\r\nAddress2: $address2\r\nCity: $city\r\nState: $state\r\nPostal Code: $postal_code\r\nCountry: $country\r\n";

    $mail_cont .= "\n\nDear $first_name,\n\nWe have processed activation code $activation_code and delivered a $product_name iLok license to User ID $ilok_id.";
    
    $mail_cont .= " The latest version of $product_name can be downloaded here:\n\n";
    $mail_cont .= "http://www.neyrinck.com/downloads\n\n";
    $mail_cont .= "A user's guide and other information will be available on your computer after running the installer.  Please see the README file for more information.\n\n";
    $mail_cont .= "Thank you for purchasing $product_name.\n\nSincerely,\nPaul Neyrinck";

    // mail to store@neyrinck.com
    mail($to, $subject, $mail_cont, $headers);

    // mail to customer
    $to = "$first_name $last_name<$email1>";
    $headers = "From: Neyrinck <store@neyrinck.com>" . "\r\n";
    //$headers .= 'Bcc: bernice@rejamm.com' . "\r\n";
    $headers .= "Reply-To: Neyrinck<store@neyrinck.com>" . "\r\n";
    mail($to, $subject, $mail_cont, $headers);
    
    return $result;
}
?>
