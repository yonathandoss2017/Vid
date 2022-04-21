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

function calcPercents($Perc, $Impressions, $Complete)
{
    if ($Perc == 25) {
        $VarP = rand(2100, 2400) / 1000;
    } elseif ($Perc == 50) {
        $VarP = rand(1500, 1640) / 1000;
    } else {
        $VarP = rand(1150, 1260) / 1000;
    }

    $Diff = $Impressions - $Complete;
    $Result = $Impressions - round(($Diff / $VarP));

    if ($Result < $Impressions) {
        if ($Result > $Complete) {
            return $Result;
        } else {
            return $Complete;
        }
    } else {
        return $Impressions;
    }
}


$Date1 = date('Y-m-d', time() - 3600);
//$Date1 = '2022-04-17';
/*
$date2 = new DateTime($Date1);
$date2->modify('-1 day');
$Date2 = $date2->format('Y-m-d');
*/

//$DateNoSlash2 = date('Ymd', time() - (24 * 3600));
$DateNoSlash = date('Ymd', time() - 3600);
//$DateNoSlash = '20220417';

$Json = file_get_contents('http://sfx.stickyadstv.com/api/stats/publisher?token=a40f7640279cd9ba87d47c1a74ceefa236c36f5c&group=deal&start=' . $DateNoSlash . '&end=' . $DateNoSlash . '&id=872257');

$Decoded = json_decode($Json);
//print_r($Decoded);
//exit(0);
$ActiveDeals = array();
$CampaingData = array();
$sql = "SELECT * FROM campaign WHERE ssp_id = 1 AND status = 1";
$query = $db3->query($sql);
if ($db3->num_rows($query) > 0) {
    while ($Camp = $db3->fetch_array($query)) {
        $idCamp = $Camp['id'];
        $CampaingData[$idCamp]['sales_manager_id'] = $Camp['sales_manager_id'];

        $ActiveDeals[$idCamp] = $Camp['deal_id'];

        $CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
        $CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];

        $countryId = 999;
        $sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
        if ($db3->getOne($sql) == 1) {
            $sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
            $countryId = $db3->getOne($sql);
        }

        $CampaingData[$idCamp]['Country'] = $countryId;

        //FIXED RANGES
        if ($Camp['vtr_from'] > 0 && $Camp['vtr_to'] > 0) {
            $CampaingData[$idCamp]['VTRFrom'] = $Camp['vtr_from'];
            $CampaingData[$idCamp]['VTRTo'] = $Camp['vtr_to'];
            $CampaingData[$idCamp]['CVTR'] = true;
        } else {
            $CampaingData[$idCamp]['CVTR'] = false;
        }

        if ($Camp['ctr_from'] > 0 && $Camp['ctr_to'] > 0) {
            $CampaingData[$idCamp]['CTRFrom'] = $Camp['ctr_from'];
            $CampaingData[$idCamp]['CTRTo'] = $Camp['ctr_to'];
            $CampaingData[$idCamp]['CCTR'] = true;
        } else {
            $CampaingData[$idCamp]['CCTR'] = false;
        }

        if ($Camp['viewability_from'] > 0 && $Camp['viewability_to'] > 0) {
            $CampaingData[$idCamp]['ViewFrom'] = $Camp['viewability_from'];
            $CampaingData[$idCamp]['ViewTo'] = $Camp['viewability_to'];
            $CampaingData[$idCamp]['CView'] = true;
        } else {
            $CampaingData[$idCamp]['CView'] = false;
        }
    }
}

foreach ($Decoded->results as $Deal) {
    //if($Deal->deal_id == 'VID-CAD-00003'){
    if (in_array($Deal->deal_id, $ActiveDeals)) {
        $idCampaing = array_search($Deal->deal_id, $ActiveDeals);
        $RebatePercent = $CampaingData[$idCampaing]['Rebate'];
        $DealID = $CampaingData[$idCampaing]['DealId'];
        $idCountry = $CampaingData[$idCampaing]['Country'];
        $salesManagerId = $CampaingData[$idCamp]['sales_manager_id'];

        $CVTR = $CampaingData[$idCampaing]['CVTR'];
        $CCTR = $CampaingData[$idCampaing]['CCTR'];
        $CView = $CampaingData[$idCampaing]['CView'];

        //$Date = date('Y-m-d', time() - 3600);
        $Hour = date('H', time() - 3600);
        //$Hour = 23;

        $Requests = $Deal->offered;
        $Bids = $Deal->bids_done;
        $Revenue = $Deal->revenue;
        $Impressions = $Deal->impressions;

        $Clicks = $Deal->clicks;
        $CompleteV = $Deal->vtr100;

        $Complete25 = $Deal->vtr25;
        $Complete50 = $Deal->vtr50;
        $Complete75 = $Deal->vtr75;

        $sql = "SELECT
            SUM(Requests) AS Requests,
            SUM(Bids) AS Bids,
            SUM(Revenue) AS Revenue,
            SUM(Impressions) AS Impressions,
            SUM(VImpressions) AS VImpressions,
            SUM(Clicks) AS Clicks,
            SUM(CompleteV) AS CompleteV,
            SUM(Complete25) AS Complete25,
            SUM(Complete50) AS Complete50,
            SUM(Complete75) AS Complete75
            FROM reports WHERE SSP = 1 AND idCampaing = $idCampaing AND Date = '$Date1'";
        $query = $db->query($sql);
        $W = $db->fetch_array($query);

        $Requests = $Requests - $W['Requests'];
        $Bids = $Bids - $W['Bids'];
        $Revenue = $Revenue - $W['Revenue'];
        $Impressions = $Impressions - $W['Impressions'];

        $RandVI = rand(8000, 8600) / 10000;
        $VImpressions = ceil($Impressions * $RandVI);

        //$VImpressions = $VImpressions - $W['VImpressions'];
        if ($VImpressions < 0) {
            $VImpressions = 0;
        }
        $Clicks = $Clicks - $W['Clicks'];

        $CompleteV = $CompleteV - $W['CompleteV'];
        $Complete25 = $Complete25 - $W['Complete25'];
        $Complete50 = $Complete50 - $W['Complete50'];
        $Complete75 = $Complete75 - $W['Complete75'];

        if ($RebatePercent > 0 && $Revenue > 0) {
            $Rebate = $Revenue * $RebatePercent / 100;
        } else {
            $Rebate = 0;
        }

        //APPLY FIXED RANGES
        if ($CCTR === true) {
            $CTRFrom = $CampaingData[$idCampaing]['CTRFrom'] * 100;
            $CTRTo = $CampaingData[$idCampaing]['CTRTo'] * 100;

            $RandCTR = rand($CTRFrom, $CTRTo) / 10000;
            $Clicks = intval($Impressions * $RandCTR);
        }

        if ($CVTR === true) {
            $VTRFrom = $CampaingData[$idCampaing]['VTRFrom'] * 100;
            $VTRTo = $CampaingData[$idCampaing]['VTRTo'] * 100;

            $RandVTR = rand($VTRFrom, $VTRTo) / 10000;
            $CompleteV = intval($Impressions * $RandVTR);
            $CompleteVPerc = $RandVTR;

            $Complete25 = calcPercents(25, $Impressions, $CompleteV);
            $Complete50 = calcPercents(50, $Impressions, $CompleteV);
            $Complete75 = calcPercents(75, $Impressions, $CompleteV);
        }

        if ($CView === true) {
            $ViewFrom = $CampaingData[$idCampaing]['ViewFrom'] * 100;
            $ViewTo = $CampaingData[$idCampaing]['ViewTo'] * 100;

            $RandView = rand($ViewFrom, $ViewTo) / 10000;
            $VImpressions = ceil($Impressions * $RandView);
        }

        $sql = "INSERT INTO reports
        (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, Rebate, Date, Hour, idCreativity, idPurchaseOrder, budgetConsumed, rebatePercentage, idSalesManager) 
        VALUES (1, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', $Complete75, '$Rebate', '$Date1', '$Hour', {$idCampaing}, {$idCampaing}, {$Revenue}, {$RebatePercent}, {$salesManagerId})";

        //echo $sql . "\n\n";
        /*
        $sql = "INSERT INTO reports
        (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Rebate, Date, Hour)
        VALUES (1, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Rebate', '$Date1', '$Hour')";
        */
        $db->query($sql);
    }
}

$Date = date('Y-m-d', time() - 3600);
updateReportCards($db3, $Date);
//updateReportCards($db2, $Date);
