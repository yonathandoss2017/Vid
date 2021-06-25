<?php

//exit();
	
@session_start();
// Guardamos cualquier error //
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST',1);
require('/var/www/html/login/config.php');
require('/var/www/html/login/constantes.php');
require('/var/www/html/login/db.php');
require('/var/www/html/login/common.lib.php');
require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
require('/var/www/html/login/admin/lkqdimport/common.php');
require('/var/www/html/login/supply/authorized.php');

$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie4.txt';

$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

// $ipAddress = $_SERVER['REMOTE_ADDR'];
$DateTime = date('Y-m-d H:i:s');

echo $Res = newSupplyPartner("gadiel_test3");
if($Res == 'unauthorized'){
    logIn('Partner test');
    $Res = newSupplyPartner("gadiel_test3");
}
echo $Res;
