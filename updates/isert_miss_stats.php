<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('/var/www/html/ads/httpsites.php');
	//exit(0);
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
exit(0);

function getOldCountryId($NewId){
	global $db, $db2;
	
	$sql = "SELECT iso FROM country WHERE id = '$NewId' LIMIT 1";
	$ISO = $db2->getOne($sql);
	
	$sql = "SELECT id FROM countries WHERE country_code = '$ISO' LIMIT 1";
	$NewCC = intval($db->getOne($sql));
	if($NewCC == 0){
		$NewCC = 999;
	}
	return $NewCC;
}	


	$Date = '2019-11-28';
	
	//$sql = "SELECT * FROM stats WHERE Manual = 2 AND Date = '$Date'";
	$sql = "SELECT * FROM supply_monthly_resume WHERE date = '$Date'";
	
	
	$query2 = $db2->query($sql);
	while($Stats = $db->fetch_array($query2)){
		$idPub = $Stats['publisher_id'];
		$sql = "SELECT user_id FROM publisher WHERE id = '$idPub' LIMIT 1";
		$idUser = $db2->getOne($sql);
		
		$idSite = $Stats['website_id'];
		$idTag = $Stats['website_zone_id'];
				
		$Country = getOldCountryId($Stats['country_id']);
		$Domain = $Stats['domain'];
		
		$formatLoads = $Stats['formatloads'];
		$Impressions = $Stats['impressions'];
		$Opportunities = 0;
		$Revenue = $Stats['usd_revenue'];
		$Coste = $Stats['usd_cost'];
		$Clicks = $Stats['clicks'];
		$adStarts = $Stats['impressions'];
		$FirstQuartiles = $Stats['first_quartiles'];
		$MidViews = $Stats['mid_points'];
		$ThirdQuartiles = $Stats['third_quartiles'];
		$CompletedViews = $Stats['completes'];
		$timeAdded = time();
		$lastUpdate = time();
		//$Hour = $Stats['hour'];
			
		$sql = "INSERT INTO reports_resume201911 (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date,  Manual)
		VALUES ('$idUser', '$idTag', '$idSite', '$Domain', '$Country', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', 0, 0, '$Clicks', 0, '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', 2)";
		$db->query($sql);
		echo $sql;
		echo "\n";
		//exit();
	}