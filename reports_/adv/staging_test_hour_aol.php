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
require('/var/www/html/login/reports_/adv/common.php');

$Date = date('Y-m-d', time() - (12 * 3600));
//echo time();
$date2 = new DateTime();
$date2->modify('+1 day');
$Date2 = $date2->format('Y-m-d\TH');

$curl = curl_init();

// TODO change campaign_test and reports_test when going to pro

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://id.b2b.verizonmedia.com/identity/oauth2/access_token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "grant_type=client_credentials&realm=aolcorporate/aolexternals&scope=one&client_secret=pWhNvVEJLZJM5o53Sj9jdrcULhfqepo1TcGQHN2toQzUthK0Ag&client_id=a5129a6d-de34-49d5-aa22-e004448132fd",
    CURLOPT_HTTPHEADER => array(
    "Content-Type: application/x-www-form-urlencoded",
    "Accept: application/json"
    ),
));

$Response = curl_exec($curl);
$DResponse = json_decode($Response);
curl_close($curl);


$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.onereporting.aol.com/v4/report-management/reporttask",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{ \"reportTemplateId\": \"5e3407531b3b480001abba85\",\n\"reportParameters\": {\n\"sd\": [\"" . $Date . "T0\"],\n\"ed\": [\"" . $Date2 . "\"],\n\"cid\": [\"7864450951\"],\n\"tz\": [\"UTC\"],\n\"af\": [\"false\"]\n}\n}",
    CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Authorization: Bearer " . $DResponse->access_token
    ),
));

$Response = curl_exec($curl);
curl_close($curl);
$ReportID = str_replace('"', '', $Response);

echo "Reporte ID: $ReportID \n";
echo "Token: " . $DResponse->access_token . "\n";

$Decoded->status = 'RUNNING';

$N = 0;

while ($Decoded->status == 'RUNNING') {
    $N++;
    echo "Try $N \n";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.onereporting.aol.com/v4/report-management/reporttask/$ReportID",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Authorization: Bearer " . $DResponse->access_token
        ),
    ));

    $Response = curl_exec($curl);
    curl_close($curl);

    $Decoded = json_decode($Response);

    //print_r($Decoded);

    sleep(4);
}


$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);


$ActiveDeals = array();
$CampaingData = array();
$sql = "SELECT * FROM campaign_test WHERE ssp_id = 3 AND status = 1";
$query = $db3->query($sql);

if ($db3->num_rows($query) > 0) {
    while ($Camp = $db3->fetch_array($query)) {
        $idCamp = $Camp['id'];
        $salesManagerId = $Camp['sales_manager_id'];

        //echo $idCamp . "\n";

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
    }
}

//print_r($Decoded->data->blocks);
//exit(0);

foreach ($Decoded->data->blocks as $RepData) {
    foreach ($RepData->rows as $Deal) {
        //print_r($ActiveDeals);
        //if($Deal->fields->sellerDealId1V == 15471){
        if (in_array($Deal->fields->sellerDealId1V, $ActiveDeals)) {
            print_r($Deal);

            $idCampaing = array_search($Deal->fields->sellerDealId1V, $ActiveDeals);
            $RebatePercent = $CampaingData[$idCampaing]['Rebate'];
            $DealID = $CampaingData[$idCampaing]['DealId'];
            $idCountry = $CampaingData[$idCampaing]['Country'];

            //echo 'Token: ' . $DResponse->access_token . "\n";
            //echo 'REP ID: ' . $ReportID . "\n";

            //$Date = date('Y-m-d', time() - 3600);
            //$Hour = date('H', time() - 3600);

            $Requests = $Deal->fields->bidRequests1V;
            $Bids = $Deal->fields->bidResponses1V;
            $Revenue = $Deal->fields->adRevenueOneVideo; //
            $Impressions = $Deal->fields->adImpressions1V;
            $VImpressions = $Deal->fields->iabViewableAdImps1V;
            $Clicks = $Deal->fields->clicks1V;
            $CompleteV = $Deal->fields->completions1V;
            $Complete25 = $Deal->fields->twentyFiveCompletions1V;
            $Complete50 = $Deal->fields->fiftyCompletions1V;
            $Complete75 = $Deal->fields->seventyFiveCompletions1V;

            $Hour = $Deal->fields->hour1V;
            $Date = date('Y-m-d', $Deal->fields->scaledDay1V / 1000);
            echo "Reg Date: $Date\n";

            if ($RebatePercent > 0 && $Revenue > 0) {
                $Rebate = $Revenue * $RebatePercent / 100;
            } else {
                $Rebate = 0;
            }


            $sql = "SELECT id FROM reports WHERE 
                SSP = 3 AND 
                idCampaing = $idCampaing AND 
                Date = '$Date' AND 
                Hour = '$Hour'";
            $idRep = intval($db->getOne($sql));

            if ($idRep > 0) {
                    $sql = "UPDATE reports_test SET Requests = '$Requests', Bids = '$Bids', Impressions = '$Impressions', Revenue = '$Revenue', VImpressions = '$VImpressions', Clicks = '$Clicks', CompleteV = '$CompleteV', Complete25 = '$Complete25', Complete50 = '$Complete50', Complete75 = '$Complete75', Rebate = '$Rebate',  WHERE id = '$idRep' LIMIT 1";
                /*
                $sql = "UPDATE reports SET Requests = '$Requests', Bids = '$Bids', Impressions = '$Impressions', Revenue = '$Revenue', VImpressions = '$VImpressions', Clicks = '$Clicks', CompleteV = '$CompleteV', Rebate = '$Rebate' WHERE id = '$idRep' LIMIT 1";
                */
            } else {
                $sql = "INSERT INTO reports_test
                (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, Rebate, Date, Hour, idCreativity, idPurchaseOrder, budgetConsumed, rebatePercentage, idSalesManager) 
                VALUES (3, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$Rebate', '$Date', '$Hour', {$idCampaing}, {$idCampaing}, {$Revenue}, {$RebatePercent}, {$salesManagerId})";
                /*
                $sql = "INSERT INTO reports
                (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Rebate, Date, Hour)
                VALUES (3, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Rebate', '$Date', '$Hour')";
                */
            }
            $db->query($sql);
                    echo $sql;
        }
    }
}

echo $Date = date('Y-m-d', time() - 3600);
updateReportCards($db3, $Date);
//updateReportCards($db2, $Date);
