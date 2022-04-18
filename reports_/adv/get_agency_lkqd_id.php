<?php

@session_start();
define('CONST', 1);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require '/var/www/html/login/config.php';
require '/var/www/html/login/constantes.php';
require '/var/www/html/login/db.php';
require '/var/www/html/login/common.lib.php';
require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
require '/var/www/html/login/admin/lkqdimport/common.php';

$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie2.txt';

$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
$db2 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

$response = getAgenciesData();
if (in_array(UNAUTHORIZED_PREFIX, $response, true)) {
    logIn($name);
    $response = getAgenciesData();
}

foreach ($response['data'] as $agency) {
    foreach ($agency['orders'] as $order) {
        $cid = $order['orderId'];
        $sourceId = $agency['sourceId'];
        $agencyId = getAgencyIdByCID($cid);
        if ($agencyId) {
            setAgencyLKQDID($agencyId, $sourceId);
        }
    }
}

function setAgencyLKQDID(int $agencyId, int $LKQDID)
{
    global $db2;

    $sql = <<<SQL
UPDATE
    agency
SET
    lkqd_id = {$LKQDID}
WHERE
    id = {$agencyId}
SQL;

    $db2->query($sql);
}

function getAgencyIdByCID(int $cid)
{
    global $db2;

    $sql = <<<SQL
SELECT
    agency_id
FROM
    purchase_order
WHERE
    cid = {$cid}
SQL;

    return $db2->getOne($sql);
}
