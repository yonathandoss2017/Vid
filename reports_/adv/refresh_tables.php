<?php

@session_start();
define('CONST', 1);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require('/var/www/html/login/db.php');
require('/var/www/html/login/reports_/libs/common_adv.php');
require('/var/www/html/login/config.php');

$db2 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

require('/var/www/html/login/reports_/adv/config.php');
$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

mysqli_set_charset($db->link, 'utf8');
mysqli_set_charset($db2->link, 'utf8');

$sql = "SELECT id FROM user ORDER BY id DESC LIMIT 1";
$lastUser = intval($db->getOne($sql));


$sql = "SELECT * FROM user WHERE id > $lastUser";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($U = $db2->fetch_array($query2)) {
        $id = $U['id'];
        $created_by_id = $U['created_by_id'];
        $updated_by_id = $U['updated_by_id'];
        $manager_id = $U['manager_id'];
        $user_id = $U['user_id'];
        $country_id = $U['country_id'];
        $username = $U['username'];
        $roles = $U['roles'];
        $password = $U['password'];
        $name = $U['name'];
        $last_name = $U['last_name'];
        $status = $U['status'];
        $locale = $U['locale'];
        $picture = $U['picture'];
        $created_at = $U['created_at'];
        $updated_at = $U['updated_at'];
        $ip_address = $U['ip_address'];
        $nick = $U['nick'];
        $monthly_target = $U['monthly_target'];
        $show_global_stats = $U['show_global_stats'];
        $phone = $U['phone'];
        $comments = $U['comments'];
        $email = $U['email'];
        $last_login = $U['last_login'];

        $sql = "INSERT INTO user (id, created_by_id, updated_by_id, manager_id, user_id, country_id, username, roles, password, name, last_name, status, locale, picture, created_at, updated_at, ip_address, nick, monthly_target, show_global_stats, phone, comments, email, last_login) VALUES ('$id', '$created_by_id', '$updated_by_id', '$manager_id', '$user_id', '$country_id', '$username', '$roles', '$password', '$name', '$last_name', '$status', '$locale', '$picture', '$created_at', '$updated_at', '$ip_address', '$nick', '$monthly_target', '$show_global_stats', '$phone', '$comments', '$email', '$last_login')";
        $db->query($sql);
    }
}

$sql = "SELECT * FROM user WHERE id <= $lastUser";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($U = $db2->fetch_array($query2)) {
        $idU = $U['id'];
        $manager_id = $U['manager_id'];
        $name = $U['name'];
        $last_name = $U['last_name'];
        $status = $U['status'];
        $nick = $U['nick'];
        $roles = $U['roles'];

        $sql = "UPDATE user SET manager_id = '$manager_id', name = '$name', last_name = '$last_name', status = '$status', nick = '$nick', roles = '$roles' WHERE id = '$idU' LIMIT 1";
        $db->query($sql);
    }
}

$sql = "SELECT id FROM ssp ORDER BY id DESC LIMIT 1";
$lastSSP = intval($db->getOne($sql));

$sql = "SELECT * FROM ssp WHERE id >= $lastSSP";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $id = $S['id'];
        $name = $S['name'];
        $deleted = $S['deleted'];

        $sql = "INSERT INTO ssp (id, name, deleted) VALUES ('$id', '$name', '$deleted')";
        $db->query($sql);
    }
}

$sql = "SELECT id FROM dsp ORDER BY id DESC LIMIT 1";
$lastDSP = intval($db->getOne($sql));

$sql = "SELECT * FROM dsp WHERE id >= $lastDSP";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $id = $S['id'];
        $name = $S['name'];
        $created_at = $S['created_at'];
        $deleted = $S['deleted'];

        $sql = "INSERT INTO dsp (id, name, created_at, deleted) VALUES ('$id', '$name', '$created_at', '$deleted')";
        $db->query($sql);
    }
}

$sql = "SELECT id FROM advertiser ORDER BY id DESC LIMIT 1";
$lastAdv = intval($db->getOne($sql));

$sql = "SELECT * FROM advertiser WHERE id > $lastAdv";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $id = $S['id'];
        $name = $S['name'];
        $created_at = $S['created_at'];
        $deleted = $S['deleted'];

        $sql = "INSERT INTO advertiser (id, name, created_at, deleted) VALUES ('$id', '$name', '$created_at', '$deleted')";
        $db->query($sql);
    }
}


$sql = "SELECT id FROM agency ORDER BY id DESC LIMIT 1";
$lastAge = intval($db->getOne($sql));

$sql = "SELECT * FROM agency WHERE id >= $lastAge";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $id = $S['id'];
        $sales_manager_id = $S['sales_manager_id'];
        $name = $S['name'];
        $type = $S['type'];
        $rebate = $S['rebate'];
        $details = $S['details'];
        $account_manager = $S['account_manager'];
        $deleted = $S['deleted'];

        $sql = "INSERT INTO agency (id, sales_manager_id, name, type, rebate, details, account_manager, deleted) VALUES ('$id', '$sales_manager_id', '$name', '$type', '$rebate', '$details', '$account_manager', '$deleted')";
        $db->query($sql);
    }
}

$sql = "SELECT * FROM agency WHERE id < $lastAge";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $id = $S['id'];
        $sales_manager_id = $S['sales_manager_id'];
        $name = $S['name'];
        $type = $S['type'];
        $rebate = $S['rebate'];
        $account_manager = $S['account_manager'];
        $deleted = $S['deleted'];

        $sql = "UPDATE agency SET
        sales_manager_id = $sales_manager_id,
        name = '$name',
        type = $type,
        rebate = '$rebate',
        account_manager = '$account_manager',
        deleted = $deleted
        WHERE id = $id LIMIT 1";
        $db->query($sql);
    }
}

$sql = "SELECT id FROM campaign ORDER BY id DESC LIMIT 1";
$lastCamp = intval($db->getOne($sql));

$sql = "SELECT c.*, po.sales_manager_id purchase_order_sales_manager_id FROM campaign c LEFT JOIN purchase_order po ON c.purchase_order_id = po.id WHERE c.id > $lastCamp";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $id = $S['id'];
        $agency_id = $S['agency_id'];
        $advertiser_id = $S['advertiser_id'];
        $ssp_id = $S['ssp_id'];
        $dsp_id = $S['dsp_id'];
        $name = $S['name'];

        $name = mysqli_real_escape_string($db->link, $name);

        $type = $S['type'];
        $deal_id = $S['deal_id'];
        $vtr = $S['vtr'];
        $viewability = $S['viewability'];
        $ctr = $S['ctr'];
        $volume = $S['volume'];
        $list_type = $S['list_type'];
        $details = $S['details'];
        $cpm = $S['cpm'];
        $start_at = $S['start_at'];
        $end_at = $S['end_at'];
        $rebate = $S['rebate'];
        $status = $S['status'];
        $created_at = $S['created_at'];
        $created_by = $S['created_by'];
        $purchaseOrderId = $S['purchase_order_id'] ?: "NULL";
        $salesManagerId = $S['purchase_order_sales_manager_id'] ?: $S['sales_manager_id'] ?: "NULL";
        $budget = $S['budget'] ?? 0;

        if (intval($S['dsp_id']) == 0 && intval($S['spotx_dsp_id']) == 9) {
            $dsp_id = 11;
        }

        $deleted = $S['deleted'];

        $sql = "INSERT INTO campaign (id, agency_id, advertiser_id, ssp_id, dsp_id, name, type, deal_id, vtr, viewability, ctr, volume, list_type, details, cpm, start_at, end_at, rebate, status, created_at, deleted, created_by, purchase_order_id, sales_manager_id, budget)
        VALUES ('$id', '$agency_id', '$advertiser_id', '$ssp_id', '$dsp_id', '$name', '$type', '$deal_id', '$vtr', '$viewability', '$ctr', '$volume', '$list_type', '$details', '$cpm', '$start_at', '$end_at', '$rebate', '$status', '$created_at', '$deleted', '$created_by', {$purchaseOrderId}, {$salesManagerId}, {$budget})";
        $db->query($sql);
    }
}

$sql = "SELECT c.*, po.sales_manager_id po_sales_manager_id, po.advertiser_id po_advertiser_id, po.agency_id po_agency_id  FROM campaign c LEFT JOIN purchase_order po ON c.purchase_order_id = po.id WHERE c.id <= $lastCamp";
$query2 = $db2->query($sql);
if ($db2->num_rows($query2) > 0) {
    while ($S = $db2->fetch_array($query2)) {
        $name = $S['name'];
        $idC = $S['id'];
        $advertiser_id = $S['advertiser_id'] ?: $S['po_advertiser_id'] ?: "NULL";
        $agency_id = $S['agency_id'] ?: $S['po_agency_id'] ?: "NULL";
        $deal_id = $S['deal_id'] ?: "NULL";
        $sspId = $S['ssp_id'] ?: "NULL";
        $dspId = $S['dsp_id'] ?: 0;
        $type = $S['type'] ?: "NULL";
        $createdBy = $S['created_by'] ?: "NULL";
        $purchaseOrderId = $S['purchase_order_id'] ?? "NULL";
        $salesManagerId = $S['po_sales_manager_id'] ?: $S['sales_manager_id'] ?: "NULL";
        $rebate = $S['rebate'] ?? 0;
        $budget = $S['budget'] ?? 0;
        $vtr = $S['vtr'] ?: 0;
        $viewability = $S['viewability'] ?: 0;
        $ctr = $S['ctr'] ?: 0;
        $volume = $S['volume'] ?: 0;
        $details = $S['details'] ?: "NULL";
        $cpm = $S['cpm'] ?: 0;
        $createdAt = $S['created_at'] ?: "NULL";
        $startAt = $S['start_at'] ?: "NULL";
        $endAt = $S['end_at'] ?: "NULL";
        $deleted = $S['deleted'] ?: 0;
        $fromVmp = $S['from_vmp'] ?: 0;


        $sql = <<<SQL
UPDATE
    campaign
SET
    agency_id = {$agency_id},
    advertiser_id = {$advertiser_id},
    ssp_id = {$sspId},
    dsp_id = {$dspId},
    name = "{$name}",
    type = {$type},
    deal_id = "{$deal_id}",
    vtr = {$vtr},
    ctr = {$ctr},
    volume = {$volume},
    viewability = {$viewability},
    details = "{$details}",
    cpm = {$cpm},
    start_at = "{$startAt}",
    end_at = "{$endAt}",
    rebate = {$rebate},
    created_at = "{$createdAt}",
    deleted = {$deleted},
    from_vmp = {$fromVmp},
    created_by = {$createdBy},
    purchase_order_id = {$purchaseOrderId},
    sales_manager_id = {$salesManagerId},
    budget = {$budget}
WHERE
    id = {$idC} LIMIT 1
SQL;

        $db->query($sql);
    }
}


$sql = <<<SQL
SELECT
    id
FROM
    purchase_order
ORDER BY id
DESC LIMIT 1
SQL;
$lastPurchaseOrder = intval($db->getOne($sql));

$sql = <<<SQL
SELECT
    *
FROM
    purchase_order
WHERE
    id > {$lastPurchaseOrder}
SQL;
$purchaseOrderQuery = $db2->query($sql);
if ($db2->num_rows($purchaseOrderQuery) > 0) {
    while ($purchaseOrder = $db2->fetch_array($purchaseOrderQuery)) {
        $purchaseOrderId = $purchaseOrder['id'];
        $purchaseOrderName = $purchaseOrder['name'];
        $purchaseOrderDocument = $purchaseOrder['document'];
        $purchaseOrderBudget = $purchaseOrder['budget'];
        $purchaseOrderCostType = $purchaseOrder['cost_type'] ?: "NULL";
        $purchaseOrderCPM = $purchaseOrder['cpm'] ?: "NULL";
        $purchaseOrderCPV = $purchaseOrder['cpv'] ?: "NULL";
        $purchaseOrderCPC = $purchaseOrder['cpc'] ?: "NULL";
        $purchaseOrderVCPM = $purchaseOrder['vcpm'] ?: "NULL";
        $purchaseOrderStartAt = $purchaseOrder['start_at'];
        $purchaseOrderEndAt = $purchaseOrder['end_at'];
        $purchaseOrderCreatedAt = $purchaseOrder['created_at'];
        $purchaseOrderCreatedBy = $purchaseOrder['created_by'] ?: "NULL";
        $purchaseOrderModifiedAt = $purchaseOrder['modified_at'];
        $purchaseOrderModifiedBy = $purchaseOrder['modified_by'] ?: "NULL";
        $purchaseOrderFileName = $purchaseOrder['file_name'];
        $purchaseOrderAgencyId = $purchaseOrder['agency_id'] ?: "NULL";
        $purchaseOrderAdvertiserId = $purchaseOrder['advertiser_id'] ?: "NULL";
        $purchaseOrderSalesManagerId = $purchaseOrder['sales_manager_id'] ?: "NULL";
        $purchaseOrderCID = $purchaseOrder['cid'] ?: "NULL";
        $purchaseOrderRebate = $purchaseOrder['rebate'] ?: "NULL";
        $purchaseOrderLKQDID = $purchaseOrder['lkqd_id'] ?: "NULL";

        $insertSql = <<<SQL
INSERT INTO
    purchase_order
(
    id,
    name,
    document,
    budget,
    cost_type,
    cpm,
    cpv,
    cpc,
    vcpm,
    start_at,
    end_at,
    created_at,
    created_by,
    modified_at,
    modified_by,
    `file_name`,
    agency_id,
    advertiser_id,
    sales_manager_id,
    cid,
    rebate,
    lkqd_id
)
VALUES (
    {$purchaseOrderId},
    "{$purchaseOrderName}",
    "{$purchaseOrderDocument}",
    "{$purchaseOrderBudget}",
    {$purchaseOrderCostType},
    {$purchaseOrderCPM},
    {$purchaseOrderCPV},
    {$purchaseOrderCPC},
    {$purchaseOrderVCPM},
    "{$purchaseOrderStartAt}",
    "{$purchaseOrderEndAt}",
    "{$purchaseOrderCreatedAt}",
    {$purchaseOrderCreatedBy},
    "{$purchaseOrderModifiedAt}",
    {$purchaseOrderModifiedBy},
    "{$purchaseOrderFileName}",
    {$purchaseOrderAgencyId},
    {$purchaseOrderAdvertiserId},
    {$purchaseOrderSalesManagerId},
    {$purchaseOrderCID},
    {$purchaseOrderRebate},
    {$purchaseOrderLKQDID}
)
SQL;

        $db->query($insertSql);
    }
}

$sql = <<<SQL
SELECT
    *
FROM
    purchase_order
WHERE
    id <= {$lastPurchaseOrder}
SQL;

$purchaseOrderQuery = $db2->query($sql);
if ($db2->num_rows($purchaseOrderQuery) > 0) {
    while ($purchaseOrder = $db2->fetch_array($purchaseOrderQuery)) {
        $purchaseOrderId = $purchaseOrder['id'];
        $purchaseOrderName = $purchaseOrder['name'];
        $purchaseOrderDocument = $purchaseOrder['document'];
        $purchaseOrderBudget = $purchaseOrder['budget'];
        $purchaseOrderCostType = $purchaseOrder['cost_type'] ?: "NULL";
        $purchaseOrderCPM = $purchaseOrder['cpm'] ?: "NULL";
        $purchaseOrderCPV = $purchaseOrder['cpv'] ?: "NULL";
        $purchaseOrderCPC = $purchaseOrder['cpc'] ?: "NULL";
        $purchaseOrderVCPM = $purchaseOrder['vcpm'] ?: "NULL";
        $purchaseOrderStartAt = $purchaseOrder['start_at'];
        $purchaseOrderEndAt = $purchaseOrder['end_at'];
        $purchaseOrderCreatedAt = $purchaseOrder['created_at'];
        $purchaseOrderCreatedBy = $purchaseOrder['created_by'] ?: "NULL";
        $purchaseOrderModifiedAt = $purchaseOrder['modified_at'];
        $purchaseOrderModifiedBy = $purchaseOrder['modified_by'] ?: "NULL";
        $purchaseOrderFileName = $purchaseOrder['file_name'];
        $purchaseOrderAgencyId = $purchaseOrder['agency_id'] ?: "NULL";
        $purchaseOrderAdvertiserId = $purchaseOrder['advertiser_id'] ?: "NULL";
        $purchaseOrderSalesManagerId = $purchaseOrder['sales_manager_id'] ?: "NULL";
        $purchaseOrderCID = $purchaseOrder['cid'] ?: "NULL";
        $purchaseOrderRebate = $purchaseOrder['rebate'] ?: "NULL";
        $purchaseOrderLKQDID = $purchaseOrder['lkqd_id'] ?: "NULL";

        $insertSql = <<<SQL
UPDATE
    purchase_order
SET
    name = "{$purchaseOrderName}",
    document = "{$purchaseOrderDocument}",
    budget = "{$purchaseOrderBudget}",
    cost_type = {$purchaseOrderCostType},
    cpm = {$purchaseOrderCPM},
    cpv = {$purchaseOrderCPV},
    cpc = {$purchaseOrderCPC},
    vcpm = {$purchaseOrderVCPM},
    start_at = "{$purchaseOrderStartAt}",
    end_at = "{$purchaseOrderEndAt}",
    created_at = "{$purchaseOrderCreatedAt}",
    created_by = {$purchaseOrderCreatedBy},
    modified_at = "{$purchaseOrderModifiedAt}",
    modified_by = {$purchaseOrderModifiedBy},
    `file_name` = "{$purchaseOrderFileName}",
    agency_id = {$purchaseOrderAgencyId},
    advertiser_id = {$purchaseOrderAdvertiserId},
    sales_manager_id = {$purchaseOrderSalesManagerId},
    cid = {$purchaseOrderCID},
    rebate = {$purchaseOrderRebate},
    lkqd_id = {$purchaseOrderLKQDID}
WHERE
    id = {$purchaseOrderId}
SQL;

        $db->query($insertSql);
    }
}


$sql = <<<SQL
SELECT
    id
FROM
    demand_tag
ORDER BY id
DESC LIMIT 1
SQL;
$lastDemandTagId = intval($db->getOne($sql));

$sql = <<<SQL
SELECT
    *
FROM
    creativity
WHERE
    id > {$lastDemandTagId}
SQL;
$creativityQuery = $db2->query($sql);
if ($db2->num_rows($creativityQuery) > 0) {
    while ($creativity = $db2->fetch_array($creativityQuery)) {
        $creativityId = $creativity['id'];
        $creativityCampaignId = $creativity['campaign_id'] ?: "NULL";
        $creativitySize = $creativity['size'];
        $creativityImage = $creativity['image'];
        $creativityClickUrl = $creativity['click_url'];
        $creativityTrackingPixel = $creativity['tracking_pixel'] ?: "NULL";
        $creativityImpressionPixel = $creativity['impression_pixel'];
        $creativityClickPixel = $creativity['click_pixel'];
        $creativityVideo = $creativity['video'];
        $creativityStatus = $creativity['status'] ?: "NULL";
        $creativityName = $creativity['name'];
        $creativityDevice = $creativity['device'] ?: "NULL";
        $creativityLKQDId = $creativity['lkqd_id'] ?: "NULL";
        $creativityDemandTagId = $creativity['demand_tag_id'] ?: "NULL";
        $creativityDemandTagUrl = $creativity['demand_tag_url'];
        $creativityType = $creativity['type'] ?: "NULL";

        $insertSql = <<<SQL
INSERT INTO
    demand_tag
(
    id,
    campaign_id,
    size,
    image,
    click_url,
    tracking_pixel,
    impression_pixel,
    click_pixel,
    video,
    status,
    name,
    device,
    lkqd_id,
    demand_tag_id,
    demand_tag_url,
    type
)
VALUES (
    {$creativityId},
    {$creativityCampaignId},
    "{$creativitySize}",
    "{$creativityImage}",
    "{$creativityClickUrl}",
    {$creativityTrackingPixel},
    "{$creativityImpressionPixel}",
    "{$creativityClickPixel}",
    "{$creativityVideo}",
    {$creativityStatus},
    "{$creativityName}",
    {$creativityDevice},
    {$creativityLKQDId},
    {$creativityDemandTagId},
    "{$creativityDemandTagUrl}",
    {$creativityType}
)
SQL;

        $db->query($insertSql);
    }
}

$sql = <<<SQL
SELECT
    *
FROM
    creativity
WHERE
    id <= {$lastDemandTagId}
SQL;
$creativityQuery = $db2->query($sql);
if ($db2->num_rows($creativityQuery) > 0) {
    while ($creativity = $db2->fetch_array($creativityQuery)) {
        $creativityId = $creativity['id'];
        $creativityCampaignId = $creativity['campaign_id'];
        $creativitySize = $creativity['size'];
        $creativityImage = $creativity['image'];
        $creativityClickUrl = $creativity['click_url'];
        $creativityVideo = $creativity['video'];
        $creativityStatus = $creativity['status'] ?: "NULL";
        $creativityName = $creativity['name'];
        $creativityDevice = $creativity['device'] ?: "NULL";
        $creativityLKQDId = $creativity['lkqd_id'] ?: "NULL";
        $creativityDemandTagId = $creativity['demand_tag_id'] ?: "NULL";
        $creativityDemandTagUrl = $creativity['demand_tag_url'];
        $creativityType = $creativity['type'] ?: "NULL";

        $insertSql = <<<SQL
UPDATE
    demand_tag
SET
    campaign_id = {$creativityCampaignId},
    size = "{$creativitySize}",
    image = "{$creativityImage}",
    click_url = "{$creativityClickUrl}",
    video = "{$creativityVideo}",
    status = {$creativityStatus},
    name = "{$creativityName}",
    device = {$creativityDevice},
    lkqd_id = {$creativityLKQDId},
    demand_tag_id = {$creativityDemandTagId},
    demand_tag_url = "{$creativityDemandTagUrl}",
    type = {$creativityType}
WHERE
    id = {$creativityId}
LIMIT 1
SQL;

        $db->query($insertSql);
    }
}

$sql = "TRUNCATE campaign_country_new";
$db->query($sql);

$result = mysqli_query($db2->link, "SELECT * FROM campaign_country");
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $sql = "INSERT INTO campaign_country_new (" . implode(", ", array_keys($row)) . ") VALUES ('" . implode("', '", array_values($row)) . "')";
    mysqli_query($db->link, $sql);
}
