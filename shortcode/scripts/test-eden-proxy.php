<?php
/*
curl -XPOST -H "Content-type: application/json" -d '{"accountId":"neyrincktest","accessKeyStr":"FQKoGGpkMU9QTHpTcWRlODVsZXNOdUp1ZXc9PRwVAqUAFQIVApnzEETBWA55jdnuD6KGr2qW7IoAAA=="}' 'https://test.neyrinck.com/findUserByAccountId/'
*/


// From URL to get webpage contents. 
$url = "https://test.neyrinck.com/findUserByAccountId"; 
$curl = curl_init();
curl_setopt ($curl, CURLOPT_URL, "https://test.neyrinck.com/findUserByAccountId");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_VERBOSE, true);

curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_POSTREDIR, 3);

$result = curl_exec ($curl);

echo "$result<br>";
curl_close ($curl);

?>