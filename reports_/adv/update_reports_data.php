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

$userId = 555;
$sql = <<<SQL
SELECT
    cOld.id old_campaign_id,
    cNew.id new_campaign_id,
    cNew.purchase_order_id,
    dt.id creativity_id,
    cNew.rebate new_campaign_rebate,
    cOld.rebate old_campaign_rebate,
    po.sales_manager_id purchase_order_sales_manager_id,
    cOld.sales_manager_id old_campaign_sales_manager_id,
    a.sales_manager_id agency_sales_manager_id
FROM
    campaign_test cOld
LEFT JOIN demand_tag dt ON cOld.deal_id = dt.demand_tag_id
LEFT JOIN campaign_test cNew ON cNew.id = dt.campaign_id
LEFT JOIN purchase_order po ON po.id = cNew.purchase_order_id
LEFT JOIN agency a ON cOld.agency_id = a.id
WHERE
    cOld.created_by != {$userId}
SQL;

$stmt = $pdo->query($sql);
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCampaigns = count($campaigns);

$progress = 1;
foreach ($campaigns as $campaign) {
    $oldCampaignId = $campaign['old_campaign_id'];
    $newCampaignId = $campaign['new_campaign_id'] ?: $campaign['old_campaign_id'];
    $purchaseOrderId = $campaign['purchase_order_id'] ?: $campaign['old_campaign_id'];
    $creativityId = $campaign['creativity_id'] ?: $campaign['old_campaign_id'];
    $rebate = $campaign['new_campaign_rebate'] ?: $campaign['old_campaign_rebate'];
    $oldCampaignRebate = $campaign['old_campaign_rebate'];
    $salesManagerId = $campaign['purchase_order_sales_manager_id']
        ?: $campaign['old_campaign_sales_manager_id']
        ?: $campaign['agency_sales_manager_id'];

    $subSql = <<<SQL
UPDATE
    reports_test
SET
    idCreativity = {$creativityId},
    idPurchaseOrder = {$purchaseOrderId},
    budgetConsumed = revenue,
    rebatePercentage = {$rebate},
    idSalesManager = {$salesManagerId},
    idCampaing = {$newCampaignId}
WHERE
    idCampaing = {$oldCampaignId}
SQL;

    echo "Processing campaign {$progress} of {$totalCampaigns}\n";
    $stmt = $pdo->prepare($subSql);
    $stmt->execute();
    $progress++;
}
