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

$userId = 506;
$sql = <<<SQL
SELECT
    cOld.id campaign_id,
    po.id purchase_order_id,
    dt.id creativity_id,
    cNew.rebate,
    po.sales_manager_id
FROM
    campaign_test cOld
INNER JOIN demand_tag dt ON cOld.deal_id = dt.demand_tag_id
INNER JOIN campaign_test cNew ON cNew.id = dt.campaign_id
INNER JOIN purchase_order po ON po.id = cNew.purchase_order_id
WHERE
    cOld.created_by != {$userId}
SQL;

$stmt = $pdo->query($sql);
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCampaigns = count($campaigns);
$progress = 1;
foreach ($campaigns as $campaign) {
    $campaignId = $campaign['campaign_id'];
    $purchaseOrderId = $campaign['purchase_order_id'];
    $creativityId = $campaign['creativity_id'];
    $rebate = $campaign['rebate'];
    $salesManagerId = $campaign['sales_manager_id'];

    $subSql = <<<SQL
UPDATE
    reports_test
SET
    idCreativity = {$creativityId},
    idPurchaseOrder = {$purchaseOrderId},
    budgetConsumed = revenue,
    rebatePercentage = {$rebate},
    idSalesManager = {$salesManagerId}
WHERE
    idCampaign = {$campaignId}
SQL;

    echo "Processing campaign {$progress} of {$totalCampaigns}\n";
    $stmt = $pdo->prepare($subSql);
    $stmt->execute();
    $progress++;
}
