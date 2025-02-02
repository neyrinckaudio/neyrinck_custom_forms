<?php
class Neyrinck_Custom_Forms_Eden
{  
private function doEden($functionName, $data)
{
    $data_string = json_encode($data);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://edenproxy.neyrinck.com/$functionName/");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTREDIR, 3);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);                                                                  
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($data_string))                                                                       
    );
    //echo "<p>curl:".$data_string."</p>";
    $result = curl_exec ($curl);
    curl_close ($curl);
    return $result;
}

public function findUserByAccountId($accountId)
{
    $info = [];
    if (method_exists('WPEdenRemote', 'findUserByAccountId')) {
        $result = WPEdenRemote::findUserByAccountId($accountId);
	    if (isset($result['httpcode']))
        {
            $info['httpcode'] = $result['httpcode'];
            if ($result['httpcode'] === 200){
                $jsonResult = json_decode($result['response'], TRUE);
                $info['accountId'] = $jsonResult['accountId'];
                return $info;
            }
            else{
                $info['error'] = $result['response'];
                return $info;
            }
        }
        else{
            $info['error'] = "findUserByAccountId did not execute";
            return $info;
		}
   }
   $info['error'] = "findUserByAccountId did not execute";
   return $info;
}

public function depositFullLicense($product_guid, $accountId, $unique_order_id)
{
    $accountingInfo = array("orderNumber" => $unique_order_id);
    $item = array("id"  => null, "guid" => $product_guid);
    $itemsToDeposit[] = $item;
    $data = array("itemsToDeposit" => $itemsToDeposit, "accountingInfo" => $accountingInfo, "accountId" => $accountId, "accessKeyStr" => "FQKoGGpkMU9QTHpTcWRlODVsZXNOdUp1ZXc9PRwVAqUAFQIVApnzEETBWA55jdnuD6KGr2qW7IoAAA==");                                                                    
    $result = $this->doEden('depositFullLicenses', $data);
    $jsonResult = json_decode($result, TRUE);
    return $jsonResult;
}

public function depositLicenseWithTerms($product_guid, $accountId, $unique_order_id, $termsGuid)
{
    $accountingInfo = array("orderNumber" => $unique_order_id);
    $item = array("id"  => null, "guid" => $product_guid);
    $itemsToDeposit[] = $item;

    $predefinedArray = array("licenseTermsGuid"=>$termsGuid);
    $terms = array("predefined"=>$predefinedArray);

    $data = array("itemsToDeposit" => $itemsToDeposit, "terms" => $terms, "accountingInfo" => $accountingInfo, "accountId" => $accountId, "accessKeyStr" => "FQKoGGpkMU9QTHpTcWRlODVsZXNOdUp1ZXc9PRwVAqUAFQIVApnzEETBWA55jdnuD6KGr2qW7IoAAA==");                                                                    
    $result = $this->doEden('depositLicensesWithTerms', $data);
    // !!! Returns an object
    $jsonResult = json_decode($result, TRUE);
    return $jsonResult;
}

public function findUserLicenseBySKU($sku, $accountId)
{
    $info = [];
    if (method_exists('WPEdenRemote', 'findUserLicenseBySKU'))
    {       
        $result = WPEdenRemote::findUserLicenseBySKU($accountId, $sku);
        if (isset($result['httpcode']))
        {
            $info['httpcode'] = $result['httpcode'];
            if ($result['httpcode'] === 200){
                $jsonResult = json_decode($result['response'], TRUE);
                $info['licenses'] = $jsonResult['licenses'];
                return $info;
            }
            else {
                $info['error'] = $result['response'];
                return $info;
            }
        }
        else{
            $info['error'] = "findUserLicenseBySKU did not execute";
            return $info;
		}
   }
   $info['error'] = "findUserLicenseBySKU did not execute";
   return $info;
}

public function depositSkus($sku_guids, $account_id, $order_id)
{
    $info = [];
    if (method_exists('WPEdenRemote', 'depositSkus'))
    {  
        $result = WPEdenRemote::depositSkus( $sku_guids, $account_id, $order_id );
        if (isset($result['httpcode']))
        {
            if ($result['httpcode'] === 200){
                $response = json_decode($result['response'], true);
                $info['depositReference'] = $response['depositReference'];
                return $info;
            }
            else{
                $info['error'] = $result['response'];
                return $info;
            }
        }
        else
        {
            $info['error'] = "depositSkus did not execute";
            return $info;
        }
    }
    $info['error'] = "function depositSkus not found";
    return $info;
}

} // class 
?>