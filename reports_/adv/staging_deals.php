<?php

@session_start();
// Guardamos cualquier error //
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST', 1);
define('PERCENTAGE_TO_TAKE_INTO_ACCOUNT', 0.3);
define('PERCENTAGE_100', 100);
define('IMPRESSIONS_LIMIT_VTR', 6667);
define('IMPRESSIONS_LIMIT_CTR', 3334);

require('/var/www/html/login/config.php');
require('/var/www/html/login/admin/lkqdimport/common.php');
require('/var/www/html/login/db.php');

$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie5.txt';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");

if (
    !isset($_POST['uuid']) ||
    !isset($_POST['env']) ||
    !isset($_POST['deals'])
) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit(0);
}

// switch($_POST["env"]) {
//     case "dev02":
//         $db2 = new SQL($pubDev02['host'], $pubDev02['db'], $pubDev02['user'], $pubDev02['pass']);
//         break;
//     case "integration":
//         $db2 = new SQL($pubIntegration['host'], $pubIntegration['db'], $pubIntegration['user'], $pubIntegration['pass']);
//         break;
//     case "staging":
//         $db2 = new SQL($pubStaging['host'], $pubStaging['db'], $pubStaging['user'], $pubStaging['pass']);
//         break;
//     case "prod":
//         $db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
//         break;
// }

// $UUID = mysqli_real_escape_string($db2->link, $_POST['uuid']);

// $sql = "SELECT report_key.* FROM report_key WHERE report_key.unique_id = '$UUID' LIMIT 1";
// $query = $db2->query($sql);
// if ($db2->num_rows($query) > 0) {
//     $Repo = $db2->fetch_array($query);
//     $RepId = $Repo['id'];

//     $sql = "DELETE FROM report_key WHERE id = '$RepId'";
//     $db2->query($sql);
// } else {
//     header('HTTP/1.0 403 Forbidden');
//     echo 'Access denied';
//     exit(0);
// }

// $deals = [
//     [
//         "dealId" => 1051767,
//         "campaignName" => "FCH_AlmapBBDO_BR_all_CPM_USD:3.09_BRL:17.63_GeoIP:BR_vtr:60_va:60_SCJohnson_Glade_3.558.996_9Abr",
//         "startDate" => "2021-04-01",
//         "endDate" => "2021-04-07",
//     ],
// ];

$deals = json_decode($_POST["deals"], true);

$processedDeals = [];
$currentDate = new DateTime("now");

foreach ($deals as $deal) {
    $dealEndDate = new DateTime($deal["endDate"]);

    $endDate = $dealEndDate > $currentDate ? $currentDate->format("Y-m-d") : $dealEndDate->format("Y-m-d");

    $dealData = getDealData($deal["dealId"], $deal["campaignName"], $deal["startDate"], $endDate);
    $todayDealData = getDealData($deal["dealId"], $deal["campaignName"], $deal["endDate"], $endDate);
    $dealActiveSources = getActiveSources($deal["dealId"]);

    if ($dealData) {
        $data = json_decode($dealData, true);
        $todayData = json_decode($todayDealData, true);
        $sourcesData = json_decode($dealActiveSources, true);
        $activeSources = [];
        $supplySources = [];
        $todaySupplySources = [];
        $processedData = [];

        if (array_key_exists('data', $todayData) && array_key_exists('entries', $todayData['data'])) {
            $todaySupplySources = $todayData["data"]["entries"];
        }

        if (array_key_exists('data', $data) && array_key_exists('entries', $data['data'])) {
            $supplySources = $data["data"]["entries"];
        }

        if (array_key_exists("associations", $sourcesData)) {
            $activeSources = array_column($sourcesData["associations"], "siteId");
        }

        $processedData = array_map(function ($supplySource) use ($todaySupplySources) {
            $ctr = getCtr($supplySource);
            $vtr = getVtr($supplySource);
            $viewability = getViewability($supplySource);
            $sourceId = $supplySource["fieldId"];

            $currentTodaySupplySource = array_filter($todaySupplySources, function ($source) use ($sourceId) {
                return $source["fieldId"] == $sourceId;
            });

            $todayCtr = 0;
            $todayVtr = 0;
            $todayViewability = 0;

            foreach ($currentTodaySupplySource as $index => $source) {
                $todayCtr = getCtr($currentTodaySupplySource[$index]);
                $todayVtr = getVtr($currentTodaySupplySource[$index]);
                $todayViewability = getViewability($currentTodaySupplySource[$index]);
            }

            return [
                "source_id" => $sourceId,
                "demand_id" => $supplySource["dimension2Id"],
                "name" => $supplySource["fieldName"],
                "ctr" => $ctr,
                "vtr" => $vtr,
                "viewability" => $viewability,
                "impressions" => $supplySource["adImpressions"],
                "today_ctr" => $todayCtr,
                "today_vtr" => $todayVtr,
                "today_viewability" => $todayViewability,
            ];
        }, $supplySources);

        $processedDeals[$deal["dealId"]]["sources"] = $processedData;
        $processedDeals[$deal["dealId"]]["active_sources"] = $activeSources;
    }
}

function getCtr(array $supplySource): float
{

    if ($supplySource["adClicks"] == 0 || $supplySource["adImpressions"] == 0) {
        return 0;
    }

    $ctr = $supplySource["adClicks"] / $supplySource["adImpressions"] * PERCENTAGE_100;

    if ($ctr > IMPRESSIONS_LIMIT_CTR) {
        return $ctr * PERCENTAGE_TO_TAKE_INTO_ACCOUNT;
    }

    return $ctr;
}

function getVtr(array $supplySource): float
{

    if ($supplySource["adCompletedViews"] == 0 || $supplySource["adImpressions"] == 0) {
        return 0;
    }

    $vtr = $supplySource["adCompletedViews"] / $supplySource["adImpressions"] * PERCENTAGE_100;

    if ($vtr > IMPRESSIONS_LIMIT_VTR) {
        return $vtr * PERCENTAGE_TO_TAKE_INTO_ACCOUNT;
    }

    return $vtr;
}

function getViewability(array $supplySource): float
{

    if ($supplySource["adViewableImps"] == 0 || $supplySource["adImpressions"] == 0) {
        return 0;
    }

    $viewability = $supplySource["adViewableImps"] / $supplySource["adImpressions"] * PERCENTAGE_100;

    if ($viewability > IMPRESSIONS_LIMIT_VTR) {
        return $viewability * PERCENTAGE_TO_TAKE_INTO_ACCOUNT;
    }

    return $viewability;
}

function getDealData(string $dealId, string $campaignName, string $startDate, string $endDate): string
{
    $response = getCampaignDemandTagReportByDate($dealId, $campaignName, $startDate, $endDate);

    if ($response === false) {
        echo "Loggin in... \n\n";
        logIn('Deals reports update');
        $response = getCampaignDemandTagReportByDate($dealId, $campaignName, $startDate, $endDate);
    }

    return $response;
}

function getActiveSources(string $dealId): string {
    $response = getSourcesByDealId($dealId);

    if ($response === false) {
        echo "Loggin in... \n\n";
        logIn('Deals reports update');
        $response = getSourcesByDealId($dealId);
    }

    return $response;
}

?>{
    "deals": <?php echo json_encode($processedDeals); ?>
}
