<?php

@session_start();
define('CONST', 1);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require('/var/www/html/login/db.php');
require('/var/www/html/login/reports_/libs/common_adv.php');
require('/var/www/html/login/config.php');
require('/var/www/html/login/reports_/adv/config.php');

$conexion = sprintf('mysql:host=%d;dbname=%s', $dbhost2, $dbname2);
$pdo = new PDO($conexion, $dbuser2, $dbpass2);

$userId = 513;
$sql = <<<SQL
SELECT
    c.id campaign_id,
    c.rebate,
    c.sales_manager_id campaign_sales_manager_id,
    a.sales_manager_id agency_sales_manager_id
FROM
    campaign_test c
LEFT JOIN agency a ON c.agency_id = a.id
WHERE
    c.created_by != {$userId}
SQL;

$stmt = $pdo->query($sql);
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCampaigns = count($campaigns);
$progress = 1;
foreach ($campaigns as $campaign) {
    $campaignId = $campaign['campaign_id'];
    $creativityId = $campaign['campaign_id'];
    $purchaseOrderId = $campaign['campaign_id'];
    $rebate = $campaign['rebate'];
    $salesManagerId = $campaign['campaign_sales_manager_id']
        ?: $campaign['agency_sales_manager_id'];

    $subSql = <<<SQL
UPDATE
    reports_deals_test
SET
    idCreativity = {$creativityId},
    idPurchaseOrder = {$purchaseOrderId},
    budgetConsumed = revenue,
    rebatePercentage = {$rebate},
    idSalesManager = {$salesManagerId}
WHERE
    idCampaing = {$campaignId}
SQL;
    // TODO update idCampaign with the id of the new created campaign

    echo "Processing campaign {$progress} of {$totalCampaigns}\n";
    $stmt = $pdo->prepare($subSql);
    $stmt->execute();
    $progress++;
}
