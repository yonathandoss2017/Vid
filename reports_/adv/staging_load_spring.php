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
$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

// TODO change campaign_test and reports_deals_test when going to pro

function csvToJson($fname)
{
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    $key = fgetcsv($fp, "1024", ",");
    $json = array();
    while ($row = fgetcsv($fp, "1024", ",")) {
        $json[] = array_combine($key, $row);
    }
    fclose($fp);

    return json_encode($json);
}

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


$JsonRel = json_decode(csvToJson('/var/www/html/login/reports_/adv/LKQD_Spring_REL.csv'));

//print_r($JsonRel);
$IDs = '';
$Coma = '';
foreach ($JsonRel as $Rels) {
    $sql = "SELECT * FROM campaign_test WHERE ssp_id = 4 AND status = 1 AND deal_id = '" . $Rels->LKQD . "' LIMIT 1";
    $query = $db3->query($sql);
    if ($db3->num_rows($query) > 0) {
        $Camp = $db3->fetch_array($query);
        $idCamp = $Camp['id'];

        $CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
        $CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];
        $CampaingData[$idCamp]['Type'] = $Camp['type'];
        if ($Camp['cpm'] > 0) {
            $CampaingData[$idCamp]['CPM'] = $Camp['cpm'];
        } else {
            $CampaingData[$idCamp]['CPM'] = 0;
        }
        if ($Camp['cpv'] > 0) {
            $CampaingData[$idCamp]['CPV'] = $Camp['cpv'];
        } else {
            $CampaingData[$idCamp]['CPV'] = 0;
        }
        if ($Camp['cpc'] > 0) {
            $CampaingData[$idCamp]['CPC'] = $Camp['cpc'];
        } else {
            $CampaingData[$idCamp]['CPC'] = 0;
        }
        if ($Camp['vcpm'] > 0) {
            $CampaingData[$idCamp]['vCPM'] = $Camp['vcpm'];
        } else {
            $CampaingData[$idCamp]['vCPM'] = 0;
        }
        $CampaingData[$idCamp]['Type'] = $Camp['type'];
        $CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];
        $CampaingData[$idCamp]['sales_manager_id'] = $Camp['sales_manager_id'];


        $countryId = 999;
        $sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
        if ($db3->getOne($sql) == 1) {
            $sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
            $countryId = $db3->getOne($sql);
        }

        $CampaingData[$idCamp]['Country'] = $countryId;

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


        $Relation[$Rels->Spring]['idCamp'] = $idCamp;
        $IDs .= $Coma . $Rels->Spring;
        $Coma = ',';
    }
}

$End = date('Y-m-d H:00:00', time() - 6 * 3600);
$Start = date('Y-m-d H:00:00', time() - 8 * 3600);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://console.springserve.com/api/v0/auth",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => file_get_contents('/var/www/html/login/reports_/adv/auth_spring'),
    CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
    ),
));

$Res = curl_exec($curl);
curl_close($curl);

$ResO = json_decode($Res);
//echo $ResO->token . "\n\n";

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://console.springserve.com/api/v0/report",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => str_replace('{End}', $End, str_replace('{Start}', $Start, str_replace('{IDS}', $IDs, file_get_contents('/var/www/html/login/reports_/adv/sping_report')))),
    CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    'Authorization: ' . $ResO->token
    ),
));

$Res = curl_exec($curl);
curl_close($curl);

$Report = json_decode($Res);
//print_r($Report);
//exit(0);

foreach ($Report as $Record) {
    $DateH = new DateTime($Record->date);
    $Hour = $DateH->format('H');
    $Date = $DateH->format('Y-m-d');

    $idCampaing = $Relation[$Record->demand_tag_id]['idCamp'];

    $sql = "SELECT id FROM reports_test WHERE SSP = 4 AND idCampaing = $idCampaing AND Date = '$Date' AND Hour = '$Hour'";
    $idRepRow = $db->getOne($sql);

    $Requests = $Record->demand_requests;
    $Bids = 0;
    $Impressions = $Record->impressions;
    $VImpressions = ceil($Record->moat_viewability_rate * $Impressions);
    $Clicks = $Record->clicks;
    $CompleteV = $Record->fourth_quartile;
    $Complete25 = $Record->first_quartile;
    $Complete50 = $Record->second_quartile;
    $Complete75 = $Record->third_quartile;


    $RebatePercent = $CampaingData[$idCampaing]['Rebate'];
    $DealID = $CampaingData[$idCampaing]['DealId'];
    $idCountry = $CampaingData[$idCampaing]['Country'];
    $Type = $CampaingData[$idCampaing]['Type'];
    $CPM = $CampaingData[$idCampaing]['CPM'];
    $CPV = $CampaingData[$idCampaing]['CPV'];
    $CPC = $CampaingData[$idCampaing]['CPC'];
    $vCPM = $CampaingData[$idCampaing]['vCPM'];
    $AgencyId = $CampaingData[$idCampaing]['AgencyId'];
    $salesManagerId = $CampaingData[$idCampaing]['sales_manager_id'];

    $CVTR = $CampaingData[$idCampaing]['CVTR'];
    $CCTR = $CampaingData[$idCampaing]['CCTR'];
    $CView = $CampaingData[$idCampaing]['CView'];

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
        $VImpressions = intval($Impressions * $RandView);
    }

    if ($Impressions > 0 && $CPM > 0) {
        $Revenue = $Impressions * $CPM / 1000;
    } elseif ($CompleteV > 0 && $CPV > 0) {
        $Revenue = $CompleteV * $CPV;
    } elseif ($Clicks > 0 && $CPC > 0) {
        $Revenue = $Clicks * $CPC;
    } elseif ($VImpressions > 0 && $vCPM > 0) {
        $Revenue = $VImpressions * $vCPM / 1000;
    } else {
        $Revenue = 0;
    }

    if ($RebatePercent > 0 && $Revenue > 0) {
        $Rebate = $Revenue * $RebatePercent / 100;
    } else {
        $Rebate = 0;
    }

    if ($idRepRow > 0) {
        $sql = "UPDATE reports_test SET
            Requests = $Requests, 
            Bids = $Bids, 
            Impressions = $Impressions, 
            Revenue = $Revenue, 
            VImpressions = $VImpressions, 
            Clicks = $Clicks, 
            CompleteV = $CompleteV, 
            Complete25 = $Complete25, 
            Complete50 = $Complete50, 
            Complete75 = $Complete75, 
            Rebate = $Rebate
            WHERE id = $idRepRow LIMIT 1";
    } else {
        $sql = "INSERT INTO reports_test
        (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, Rebate, Date, Hour, idCreativity, idPurchaseOrder, budgetConsumed, rebatePercentage, idSalesManager) 
        VALUES (4, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', $Complete75, '$Rebate', '$Date', '$Hour', {$idCampaing}, {$idCampaing}, {$Revenue}, {$RebatePercent}, {$salesManagerId})";
    }

    echo $sql . "\n";
    //$db->query($sql);
}
