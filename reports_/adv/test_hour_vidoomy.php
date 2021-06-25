<?php

use Carbon\Carbon;
use \MongoDB\BSON\UTCDateTime;
use \MongoDB\Model\BSONDocument;

@session_start();
// Guardamos cualquier error //
ini_set('display_errors', 0);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require('/var/www/html/login/config.php');

define('CONST',1);
define("CPM_DIVIDER", 1000);
define("SSP_VIDOOMY_ID", 7);

try {
    $db = $advProd["db"];
    $host = $advProd["host"];
    $dsn = "mysql:host={$host};dbname={$db}";
    $pdo = new PDO($dsn, $advProd["user"], $advProd["pass"]);

    $dsnLocal = "mysql:host={$dbhost};dbname={$dbAdvName}";
    $pdoLocal = new PDO($dsnLocal, $dbuser, $dbpass);
} catch (PDOException $e){
    echo $e->getMessage();
}

$client = new MongoDB\Client($mongoUrl);
$collection = $client->bidoomy->StatsAggregate;

$deals = getDeals();
$countries = getCountries();

for($D = 26; $D <= 28; $D++){
	for($H = 0; $H <= 23; $H++){
		$date = getLastHourDate(2, $D, $H);
		
		foreach ($deals as $deal) {
			
			if(strpos($deal["deal_id"], '(') !== false){
				$exD = explode('(', $deal["deal_id"]);
				$dealID = $exD[0];
			}else{
				$dealID = $deal["deal_id"];
			}
			
		    $cursor = $collection->aggregate([
		        [
		            '$match' => [
		                "deal" => $dealID,
		                "date" => $date,
		            ],
		        ],
		        [
		            '$group' => [
		                "_id" => '$country',
		                "requests" => ['$sum' => '$bidRequest'],
		                "bids" => ['$sum' => '$bidResponse'],
		                "impressions" => ['$sum' => '$impression'],
		                "revenue" => ['$sum' => '$impressionMoney'],
		                "clicks" => ['$sum' => '$click']
		            ],
		        ],
		    ]);
		    
		    foreach ($cursor as $document) {
		        $countryId = getCountryId($countries, $document["_id"]);
		        //print_r($document);
		        insertRecord($countryId, $document, $deal);
		    }
		    
		}
	}
}
/**
 * Returns the current date and previous hour
 *
 * @return Carbon $date
 */
function getLastHourDate ($M, $D, $H): UTCDateTime {
    //$date = Carbon::now();
    $date = Carbon::create(2021, $M, $D, $H, 0, 0, 'UTC');
    $date = Carbon::parse($date->format("Y-m-d H:i:s"));
    //$date->subHour()->minute = 0;
    $date->minute = 0;
    $date->second = 0;
    
    echo $date->format("Y-m-d H:i:s");

    return new UTCDateTime($date);
}

/**
 * Insert a record to the report table
 *
 * @param string $countryId
 * @param array $document
 */
function insertRecord (string $countryId, BSONDocument $document, array $deal) {
    global $pdoLocal;
    global $date;

    $sql = <<<SQL
INSERT INTO
    reports (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour)
VALUES
    (:ssp, :camp_id, :country_id, :requests, :bids, :impressions, :revenue, :v_impressions, :clicks, :complete_v, :complete_25, :complete_50, :complete_75, :complete_v_per, :rebate, :date, :hour);
SQL;

    $parsedDate = Carbon::parse($date->toDateTime()->format("Y-m-d h:m:s"));
    $revenue = $document["revenue"] / CPM_DIVIDER;

    $stmt = $pdoLocal->prepare($sql);

    $stmt->bindValue(":ssp", $deal["ssp_id"]);
    $stmt->bindValue(":camp_id", $deal["campaign_id"]);
    $stmt->bindValue(":country_id", $countryId);
    $stmt->bindValue(":requests", $document["requests"]);
    $stmt->bindValue(":bids", $document["bids"]);
    $stmt->bindValue(":impressions", $document["impressions"]);
    $stmt->bindValue(":revenue", $revenue);
    $stmt->bindValue(":v_impressions", $document["impressions"]);
    $stmt->bindValue(":clicks", $document["clicks"]);
    $stmt->bindValue(":complete_v", $document["impressions"]);
    $stmt->bindValue(":complete_25", $document["impressions"]);
    $stmt->bindValue(":complete_50", $document["impressions"]);
    $stmt->bindValue(":complete_75", $document["impressions"]);
    $stmt->bindValue(":complete_v_per", 0);
    $stmt->bindValue(":rebate", getRebate($revenue, $deal["rebate"]));
    $stmt->bindValue(":date", $parsedDate->format("Y-m-d"));
    $stmt->bindValue(":hour", $parsedDate->hour);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        error_log($e->getMessage(), 0);
    }
}

/**
 * Calculate rebate
 *
 * @param float $revenue
 * @param float $rebate campaign rebate
 *
 * @return float $rebate
 */
function getRebate(float $revenue, float $rebate): float {
    if ($rebate === 0) {
        return 0;
    }

    return $revenue * ($rebate / 100);
}

/**
 * Gets all countries
 *
 * @return array countries
 */
function getCountries (): array {
    global $pdo;

    $sql = <<<SQL
SELECT
    iso, id
FROM
    country
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Gets the id of the country given an iso
 * @param array $countries
 * @param string $iso
 *
 * @return string id of the country
 */
function getCountryId (array $countries, string $iso): string {
    $key = array_search($iso, array_column($countries, "iso"));

    if ($key) {
        return $countries[$key]["id"];
    }

    return 0;
}

/**
 * Gets the deals from advertisers panel
 */
function getDeals (): array {
    global $pdo;
    $sspVidoomyId = SSP_VIDOOMY_ID;

    $sql = <<<SQL
SELECT
    ssp_id,
    deal_id,
    id as campaign_id,
    rebate
FROM
    campaign
WHERE
    ssp_id = {$sspVidoomyId};
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
