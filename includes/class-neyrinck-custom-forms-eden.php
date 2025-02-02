<?php
class Neyrinck_Custom_Forms_Eden
{
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