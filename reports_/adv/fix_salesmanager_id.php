<?php

@session_start();
// Guardamos cualquier error //
ini_set('display_errors', 0);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST', 1);
require('/var/www/html/login/config.php');
require('/var/www/html/login/reports_/adv/config.php');
require('/var/www/html/login/db.php');
$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

require('/var/www/html/login/reports_/adv/common.php');

$sql = "SELECT * FROM campaign WHERE ssp_id = 1 AND status = 1";
$query = $db3->query($sql);
if ($db3->num_rows($query) > 0) {
    while ($Camp = $db3->fetch_array($query)) {
        $idCamp = $Camp['id'];
        $salesManagerId = $Camp['sales_manager_id'];

        $sql = "UPDATE reports SET idSalesManager = $salesManagerId WHERE idCampaing = $idCamp AND Date >= '2022-04-17'";
        echo $sql . "\n";
        $db->query($sql);
    }
}
