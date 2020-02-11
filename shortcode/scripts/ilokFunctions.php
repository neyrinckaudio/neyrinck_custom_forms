<?php

function doEden($functionName, $data)
{
    $data_string = json_encode($data);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://edenproxy.neyrinck.com/$functionName");
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
    $result = curl_exec ($curl);
    curl_close ($curl);
    return $result;
}

function findUserByAccountId($accountId)
{
    $data = array("accountId" => $accountId, "accessKeyStr" => "FQKoGGpkMU9QTHpTcWRlODVsZXNOdUp1ZXc9PRwVAqUAFQIVApnzEETBWA55jdnuD6KGr2qW7IoAAA==");                                                                    
    $result = doEden('findUserByAccountId', $data);
    $jsonResult = json_decode($result, TRUE);
    return $jsonResult['accountId'];
}

function depositFullLicense($product_guid, $accountId, $unique_order_id)
{
    $accountingInfo = array("orderNumber" => $unique_order_id);
    $item = array("id"  => null, "guid" => $product_guid);
    $itemsToDeposit[] = $item;
    $data = array("itemsToDeposit" => $itemsToDeposit, "accountingInfo" => $accountingInfo, "accountId" => $accountId, "accessKeyStr" => "FQKoGGpkMU9QTHpTcWRlODVsZXNOdUp1ZXc9PRwVAqUAFQIVApnzEETBWA55jdnuD6KGr2qW7IoAAA==");                                                                    
    $result = doEden('depositFullLicenses', $data);
    $jsonResult = json_decode($result, TRUE);
    return $jsonResult;
}

?>