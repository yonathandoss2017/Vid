<?php

@session_start();
define('CONST', 1);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require '/var/www/html/login/db.php';
require '/var/www/html/login/admin/lkqdimport/common_staging.php';
require '/var/www/html/login/config.php';
require '/var/www/html/login/reports_/adv/config.php';

$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie2.txt';

// $conexion = sprintf('mysql:host=%d;dbname=%s', $dbhost2, $dbname2);
// $pdo = new PDO($conexion, $dbuser2, $dbpass2);
$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

$sql = <<<SQL
SELECT
    id,
    name,
    lkqd_id,
    rebate,
    payment_terms
FROM
    agency
WHERE
    name in (SELECT name FROM agency group by name having count(name) > 1)
ORDER BY name, id
SQL;

$agencies = $db3->getAll($sql);

$agencyId = 0;
$agencyName = '';
$agenciesToDelete = [];
$totalAgencies = count($agencies);
foreach ($agencies as $index => $agency) {
    if ($agency['name'] !== $agencyName) {
        if ($index > 0) {
            processAgency($agencyId, $agenciesToDelete);
        }


        $agencyId = $agency['id'];
        $agencyName = $agency['name'];
        $agenciesToDelete = [];
        continue;
    }

    $agenciesToDelete[] = $agency['id'];
}

processAgency($agencyId, $agenciesToDelete);

function processAgency(int $agencyId, array $agenciesToDelete)
{
    transferCampaignsToAgency($agencyId, $agenciesToDelete);
    transferPurchaseOrdersToAgency($agencyId, $agenciesToDelete);
    deleteAgency($agenciesToDelete);
}

function deleteAgency(array $agenciesToDelete)
{
    global $db3;

    $agencies = implode(",", $agenciesToDelete);
    $sql = <<<SQL
UPDATE
    agency
SET
    deleted = 1
WHERE
    id in ({$agencies})
SQL;

    $db3->query($sql);
}

function transferPurchaseOrdersToAgency(int $agencyId, array $agenciesToDelete)
{
    global $db3;

    $agencies = implode(",", $agenciesToDelete);
    $sql = <<<SQL
UPDATE
    purchase_order
SET
    agency_id = {$agencyId}
WHERE
    agency_id in ({$agencies})
SQL;

    $db3->query($sql);
}

function transferCampaignsToAgency(int $agencyId, array $agenciesToDelete)
{
    global $db3;

    $agencies = implode(",", $agenciesToDelete);
    $sql = <<<SQL
UPDATE
    campaign
SET
    agency_id = {$agencyId}
WHERE
    agency_id in ({$agencies})
SQL;

    $db3->query($sql);
}


// $fromDate = new DateTime(date('Y-m-d H:00', time() - (3600 * 1)));
// $toDate   = new DateTime(date('Y-m-d 23:00'));
// $ImportData = getAdvertiserDemandReportCSVByDateRange($fromDate, $toDate, []);

// foreach ($ImportData as $index => $tag) {
//     if ($index == 0) {
//         continue;
//     }

//     if ($tag[1] == 1071924) {
//         var_dump($tag);
//         die();
//     }
// }
// $agencies = getAgenciesData();
// if (in_array(UNAUTHORIZED_PREFIX, $agencies, true)) {
//     logIn($name);
//     $agencies = getAgenciesData();
// }
// $total = count($agencies['data']);
// $current = 1;
// foreach ($agencies['data'] as $agency) {
//     print_r(sprintf('Processing agency %d of $d', $current, $total));
//     $agencyName = $agency['sourceName'];
//     $sourceId = $agency['sourceId'];

//     $sql = <<<SQL
// UPDATE
//     agency
// SET
//     name = '{$agencyName}'
// WHERE
//     lkqd_id = {$sourceId}
// SQL;
//     $db3->query($sql);
//     $current++;
// }

// $csvData = array_map('str_getcsv', file('repeatedAgencies.csv'));

// foreach ($csvData as $index => $agency) {
//     if (in_array($index, [0, 1, 2, 3, 4, 5, 6])) {
//         continue;
//     }
//     $agencyId = $agency[0];
//     $agencyName = $agency[1];

//     $sql = <<<SQL
// SELECT
//     id
// FROM
//     agency
// WHERE
//     name = '{$agencyName}'
//     AND id <> {$agencyId}
// SQL;

//     $agencies = $db3->getAll($sql, 'id');
//     var_dump($agencies);
//     die();
//     $agencyName = $agency[0];
//     $sourceId = $agency[1];

//     $sql = <<<SQL
//     UPDATE
//         agency
//     SET
//         lkqd_id = {$sourceId}
//     WHERE
//         name = '{$agencyName}'
// SQL;

//             $db3->query($sql);
// }

// $sql = <<<SQL
// SELECT
//     id,
//     name,
//     lkqd_id
// FROM
//     agency
// WHERE
//     lkqd_id is not null
// SQL;

// $agencies = $db3->getAll($sql);
// var_dump($agencies);
