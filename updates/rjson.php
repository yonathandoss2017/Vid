<?php

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$fullstring = file_get_contents('../../ads/eldigitalcastillalamancha_297.js');


$PlayerD['Schain'] = get_string_between($fullstring, "custom3: '", "'");
$PlayerD['Width'] = intval(get_string_between($fullstring, "playerWidth:", ","));
$PlayerD['Height'] = intval(get_string_between($fullstring, "playerHeight:", ","));
$PlayerD['Position'] = trim(get_string_between($fullstring, "slidePosition: '", "',"));
$PlayerD['Sid'] = trim(get_string_between($fullstring, "sid: ", ","));

$zoneCache 


$PlayerD['Sid']

print_r($PlayerD);