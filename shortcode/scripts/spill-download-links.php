<?php

include('softwareInformation.php');

// Get software list
$softwarePacks = $NeyrinckSoftware->packages["Spill"];

// Spill – MAC OS X
$link = $NeyrinckSoftware->downloads[$softwarePacks[0]];
$version = get_string_between($link, '_', '.dmg');
$macName = "Download Spill ".$version." - MAC OS X";
echo "<a style='font-weight:bold' href='".$link."'>$macName</a> <br/>";



$link2 = $NeyrinckSoftware->downloads[$softwarePacks[1]];
$version2 = get_string_between($link2, '_32_', '.zip');
$Win32 = "Download Spill".$version2." - Windows";
echo "<a style='font-weight:bold' href='".$link2."'>$Win32</a> <br/>";


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}




?>