<?php

@session_start();
define('CONST', 1);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require '/var/www/html/login/db.php';
require '/var/www/html/login/admin/lkqdimport/common_staging.php';
require '/var/www/html/login/config.php';
require '/var/www/html/site/constantes.php';
require '/var/www/html/site/common.lib.php';
require '/var/www/html/login/reports_/adv/config.php';

$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie2.txt';

$conexion = sprintf('mysql:host=%d;dbname=%s', $dbhost2, 'vidoomylogin');
$pdo = new PDO($conexion, $dbuser2, $dbpass2);

$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);

$date = new DateTime();

$startYear = "2018";
$finishYear = $date->format('Y');

$publisherToId = 31450;
$websiteId = 19438;

updateSitesTable();
updateSupplyTagTable();
updateWebsiteTable();

for ($i = $startYear; $i <= $finishYear; $i++) {
    for ($j = 1; $j <= 12; $j++) {
        $month = sanitizeMonth($j);
        processMonth($i, $month);
    }
}

updateServerTable('stats');
updatePanelTable('stats');

function processMonth(string $year, string $month)
{
    print_r("processing " . $year . " " . $month . "\n");
    $table1 = sprintf('reports%s%s', $year, $month);
    $table2 = sprintf('reports_resume%s%s', $year, $month);
    $table3 = sprintf('reportsresume%s%s', $year, $month);


    updateServerTable($table1);
    updateServerTable($table2);
    updatePanelTable($table3);
}

function updateWebsiteTable()
{
    global $publisherToId, $websiteId, $db2;

    $sql = <<<SQL
SELECT
    id
FROM
    publisher
WHERE
    user_id = {$publisherToId}
SQL;

    $publisherId = $db2->getOne($sql);

    if ($publisherId) {
        $sql = <<<SQL
UPDATE
    website
SET
    publisher_id = {$publisherId}
WHERE
    id = {$websiteId}
SQL;

        $db2->query($sql);
    }
}

function updateSupplyTagTable()
{
    global $publisherToId, $websiteId, $pdo;

    $sql = <<<SQL
UPDATE
    supplytag
SET
    idUser = {$publisherToId}
WHERE
    idSite = {$websiteId}
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

function updateSitesTable()
{
    global $publisherToId, $websiteId, $pdo;

    $sql = <<<SQL
UPDATE
    sites
SET
    idUser = {$publisherToId}
WHERE
    id = {$websiteId}
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

function updatePanelTable(string $table)
{
    global $publisherToId, $websiteId, $db2;

    $sql = <<<SQL
UPDATE
    {$table}
SET
    iduser = {$publisherToId}
WHERE
    idsite = {$websiteId}
SQL;

    $db2->query($sql);
}

function updateServerTable(string $table)
{
    global $publisherToId, $websiteId, $pdo;

    $sql = <<<SQL
UPDATE
    {$table}
SET
    idUser = {$publisherToId}
WHERE
    idSite = {$websiteId}
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

function sanitizeMonth(int $month): string
{
    if ($month > 9) {
        return $month;
    }

    return str_pad($month, 2, '0', STR_PAD_LEFT);
}
