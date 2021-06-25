<?php

@session_start();

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST',1);

require('/var/www/html/login/config.php');
require('/var/www/html/login/constantes.php');
require('/var/www/html/login/db.php');
require('/var/www/html/login/common.lib.php');
require('/var/www/html/login/admin/lkqdimport/common.php');

$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);

$ipAddress = $_SERVER['REMOTE_ADDR'];
$dateTime = date('Y-m-d H:i:s');
    
$params = serialize($_POST);
$params = mysqli_real_escape_string($db->link, $params);

if (isset($_POST['name'])) {
    $name = $_POST['name'];

    $sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime) VALUES ('$name', 1, '$ipAddress', 1, '$params', '$dateTime')";
    $db->query($sql);

    $response = newDomain($name);
    if ($response == 'unauthorized') {
        logIn();
        $response = newDomain($name);
    }

    echo $response;
} else {
    $name = '';
    
    $sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime) VALUES ('$name', 0, '$ipAddress', 1, '$params', '$dateTime')";
    $db->query($sql);
    
    echo 'no name';
}
