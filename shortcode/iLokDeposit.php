
<?php

// ini_set('display_errors', 'On');

class iLokDeposit {

    public $product_id;
    public $unique_order_id;
    public $iLok_id;
    public $status;
    public $error_message;
        

	public function __construct($unique_order_id, $product_id, $iLok_id) {
        $this->unique_order_id = $unique_order_id;
        $this->product_id= $product_id;
        $this->iLok_id = $iLok_id;
        $this->check_ilok_id($iLok_id);
    }

    function check_ilok_id($iLok_id){

        $eden = new Eden_Remote();
        $user = $eden->get_ilok_info($iLok_id);
        if ($user != "Not found.")  {
            $this->activate_process();
        } else {
            $this->status = "failed";
            $this->error_message = "This iLok User ID is not valid. <br/><a href='https://neyrinck.com/activate'>Please click here to restart your activation process again. </a.";
        }
        
    }

    function activate_process(){

        //$drightGuid = $this->get_ilok_deposits_drightGuid($this->unique_order_id);
        $type = $this->get_ilok_license_type($this->product_id);

        if ($type == 'full') $this->deposit_full_license($this->product_id, $this->iLok_id, $this->unique_order_id);
        if ($type == 'upgrade') $this->deposit_license_by_SKU($this->product_id, $this->iLok_id, $this->unique_order_id);
        if ($type == 'rental') $this->deposit_license_with_terms($this->product_id, $this->iLok_id, $this->unique_order_id);

    }

    function deposit_license_with_terms($product_id, $ilok_id, $unique_order_id){

        $product_guid = $this->get_iLok_product_guid($product_id);
        $terms_guid = $this->get_iLok_terms_guid($product_id);
        $eden = new Eden_Remote();
        $drightGuidObj = $eden->deposit_license_with_terms($product_guid, $terms_guid, $ilok_id, $unique_order_id);
        if (is_object($drightGuidObj)) {
            $dright_guid = $drightGuidObj->depositedDrights[0]->drightGuid;
            $this->status = 'deposited';
            $this->update_license_ref($dright_guid, $unique_order_id);
        } else {
            $this->status = 'failed'; 
            $this->error_message = $dright_guid;
        }
    
       

    }

    function update_license_ref($license_ref, $activation_code){
        $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        // Check connection
        if (mysqli_connect_errno())
        {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } 
        $query = "UPDATE ilok_assets SET license_ref = '$license_ref' WHERE activation_code ='$activation_code'";
        $result = mysqli_query($connection, $query)or die("Couldnt execute query get_ilok_license_type");

    }

    function deposit_license_with_surrender($product_id, $ilok_id, $unique_order_id){

        $product_guid = $this->get_iLok_product_guid($product_id);
        $surrender_guid = $this->get_iLok_surrender_guid($product_id);
        $eden = new Eden_Remote();
        $drightGuidObj = $eden->deposit_license_with_surrender($product_guid, $surrender_guid, $ilok_id, $unique_order_id);

        if (is_object($drightGuidObj)) {
            $dright_guid = $drightGuidObj->depositedDrights[0]->drightGuid;
            $this->status = 'deposited';
            $this->update_license_ref($dright_guid, $unique_order_id);
        } else {
            $this->status = 'failed'; 
            $this->error_message = $dright_guid;
        }
    }

    function deposit_full_license($product_id, $ilok_id, $unique_order_id){

        $product_guid = $this->get_iLok_product_guid($product_id);
        $eden = new Eden_Remote();
        $drightGuidObj = $eden->deposit_full_license($product_guid, $ilok_id, $unique_order_id);
        if (is_object($drightGuidObj)) {
            $dright_guid = $drightGuidObj->depositedDrights[0]->drightGuid;
            $this->status = 'deposited';
            $this->update_license_ref($dright_guid, $unique_order_id);
        } else {
            $this->status = 'failed'; 
            $this->error_message = $dright_guid;
        }

    	
    }

    function deposit_license_by_SKU($product_id, $iLok_id, $unique_order_id){

        $sku_guid = $this->get_iLok_sku_guid($product_id);
        $eden = new Eden_Remote();
        $drightGuidObj = $eden->deposit_license_by_SKU($sku_guid, $iLok_id, $pace_order_id);
        if (is_object($drightGuidObj)) {
            $dright_guid = $drightGuidObj->depositedDrights[0]->drightGuid;
            $this->status = 'deposited';
            $this->update_license_ref($dright_guid, $unique_order_id);
        } else {
            $this->status = 'failed'; 
            $this->error_message = $dright_guid;
        }

    }



    function get_iLok_product_guid($product_id){
        $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        // Check connection
        if (mysqli_connect_errno())
        {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } 
        $query = "SELECT product_guid FROM ilok_products WHERE product_id = '$product_id'";
        $result = mysqli_query($connection, $query)or die("Couldnt execute query get_ilok_license_type");
        if ($row = mysqli_fetch_array($result)) {
            return $row['product_guid'];
        } else return false;


	}

    function get_iLok_surrender_guid($product_id){
        $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        // Check connection
        if (mysqli_connect_errno())
        {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } 
        $query = "SELECT surrender_guid FROM ilok_products WHERE product_id = '$product_id'";

        $result = mysqli_query($connection, $query)or die("Couldnt execute query get_iLok_surrender_guid");
        if ($row = mysqli_fetch_array($result)) {
            return $row['surrender_guid'];
        } else return false;

    }

    function get_iLok_sku_guid($product_id){
        $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        // Check connection
        if (mysqli_connect_errno())
        {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } 
        $query = "SELECT sku_guid FROM ilok_products WHERE product_id = '$product_id'";

        $result = mysqli_query($connection, $query)or die("Couldnt execute query get_iLok_sku_guid");
        if ($row = mysqli_fetch_array($result)) {
            return $row['sku_guid'];
        } else return false;
    }

	

    function get_ilok_license_type($product_id){

        $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        // Check connection
        if (mysqli_connect_errno())
        {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } 
        $query = "SELECT license_type FROM ilok_products WHERE product_id = '$product_id'";
        $result = mysqli_query($connection, $query)or die("Couldnt execute query get_ilok_license_type");
        if ($row = mysqli_fetch_array($result)) {
            return $row['license_type'];
        } else return false;
     
    }


    function get_iLok_terms_guid($product_id){

        $connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);
        // Check connection
        if (mysqli_connect_errno())
        {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        } 
        $query = "SELECT terms_guid FROM ilok_products WHERE product_id = '$product_id'";
        $result = mysqli_query($connection, $query)or die("Couldnt execute query get_ilok_license_type");
        if ($row = mysqli_fetch_array($result)) {
            return $row['terms_guid'];
        } else return false;

    }

}




?>