<?php

include('softwareInformation.php');

// Get software list
$softwarePacks = $NeyrinckSoftware->packages["V-Control Pro"];

// V-Control Pro – MAC OS X
$link = $NeyrinckSoftware->downloads[$softwarePacks[0]];
$version = get_string_between($link, '_', '.dmg');
$macName = "Download V-Control Pro ".$version." - MAC OS X";
echo "<a style='font-weight:bold' href='".$link."'>$macName</a> <br/>";


// V-Control Pro – 32-bit Windows
$link2 = $NeyrinckSoftware->downloads[$softwarePacks[1]];
$version2 = get_string_between($link2, '_32_', '.zip');
$Win32 = "Download V-Control Pro ".$version2." - 32-bit Windows";
echo "<a style='font-weight:bold' href='".$link2."'>$Win32</a> <br/>";

// V-Control Pro – 64-bit Windows
$link3 = $NeyrinckSoftware->downloads[$softwarePacks[2]];
$version3 = get_string_between($link3, '_64_', '.zip');
$Win64 = "Download V-Control Pro ".$version3." - 64-bit Windows";
echo "<a style='font-weight:bold' href='".$link3."'>$Win64</a> <br/>";


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}




?>