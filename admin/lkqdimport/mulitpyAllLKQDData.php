<?php	
	//exit();
	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
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
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
	
	
	//echo $Date = date('Y-m-d', time() - 1200);
	echo $Date = '2022-04-03';
	$Multi = 1.5;
		
	$DateFrom = $Date;
	$DateTo = $Date;
	
	$TablaName = getTableName($Date);
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	
	$sql = "SELECT * FROM $TablaName WHERE Date = '$DateFrom' AND idUser > 0 AND Player = 1";
	
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		$Nins++;
		$Nis++;
		$idRow = $Da['id'];
		$Impressions = round($Da['Impressions'] * $Multi);
	    $Opportunities = round($Da['Opportunities'] * $Multi);
	    $formatLoads = round($Da['formatLoads'] * $Multi);
	    $Revenue = $Da['Revenue'] * $Multi;
	    $RevenueEur = $Da['RevenueEur'] * $Multi;
	    $Coste = $Da['Coste'] * $Multi;
	    $CosteEur = $Da['CosteEur'] * $Multi;
	    $Extraprima = $Da['Extraprima'] * $Multi;
	    $Clicks = round($Da['Clicks'] * $Multi);
	    $Wins = round($Da['Wins'] * $Multi);
	    $adStarts = round($Da['adStarts'] * $Multi);
	    $FirstQuartiles = round($Da['FirstQuartiles'] * $Multi);
	    $MidViews = round($Da['MidViews'] * $Multi);
	    $ThirdQuartiles = round($Da['ThirdQuartiles'] * $Multi);
	    $CompletedViews = round($Da['CompletedViews'] * $Multi);
	
		$sql = "UPDATE $TablaName SET Impressions = $Impressions, Opportunities = $Opportunities, formatLoads = $formatLoads, Revenue = '$Revenue', RevenueEur = '$RevenueEur', Coste = '$Coste', CosteEur = '$CosteEur', ExtraprimaP = '$Extraprima', Clicks = $Clicks, Wins = $Wins, adStarts = $adStarts, FirstQuartiles = $FirstQuartiles, MidViews = $MidViews, ThirdQuartiles = $ThirdQuartiles, CompletedViews = $CompletedViews WHERE id = $idRow";
		$db->query($sql);
		//echo $sql . "\n";

	}
	
