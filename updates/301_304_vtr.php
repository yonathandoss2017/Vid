<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

function calcPercents($Perc , $Impressions, $Complete){
	if($Perc == 25){
		$VarP = rand(2100, 2400) / 1000;
	}elseif($Perc == 50){
		$VarP = rand(1500, 1640) / 1000;
	}else{
		$VarP = rand(1150, 1260) / 1000;
	}
	
	$Diff = $Impressions - $Complete;
	$Result = $Impressions - round(($Diff / $VarP));
	
	if($Result < $Impressions){
		if($Result > $Complete){
			return $Result;
		}else{
			return $Complete;
		}
	}else{
		return $Impressions;
	}
}

	$sql = "SELECT * FROM reports WHERE idCampaing BETWEEN 301 AND 304 AND Date BETWEEN '2020-05-15' AND '2020-05-17'";
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$idRow = $S['id'];
		$Impressions = $S['Impressions'];
		
		$RandVI = rand(7100,7200)/10000;
		$CompleteV = round($Impressions * $RandVI);
		
		$Complete25 = calcPercents(25, $Impressions, $CompleteV);
		$Complete50 = calcPercents(50, $Impressions, $CompleteV);
		$Complete75 = calcPercents(75, $Impressions, $CompleteV);
		
		$sql = "UPDATE reports SET CompleteV = $CompleteV, Complete25 = $Complete25, Complete50 = $Complete50, Complete75 = $Complete75 WHERE id = '$idRow' LIMIT 1";
		$db->query($sql);
		//echo $sql . "\n";
	}