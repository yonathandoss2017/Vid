<?php 
// API RESPONSE:
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require __DIR__  . '/../config_local.php';
}

require __DIR__  . '/../constantes.php';
require __DIR__  . '/../db.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
header('Content-Type: application/json');
