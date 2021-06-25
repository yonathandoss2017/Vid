<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('../admin/libs/display.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	require('/var/www/html/ads/httpsites.php');

function vary20($Val, $noInt = false){
	/*$Por = rand(50,60);
	$ValPor = $Val * $Por / 100;
	
	$Val = $Val - $ValPor;
	
	if($noInt){
		return $Val;
	}else{
		return intval($Val);
	}*/
	return $Val;
}
	/*
	$SumRevenue = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array());
	$TotalRev = array(0, 1, 2, 3, 4, 5);
	
		$sql = "SELECT * FROM reports201912 WHERE Date = '2019-12-11' AND Hour <= 5 AND Hour > 0";
		$query = $db->query($sql);
		while($Stats = $db->fetch_array($query)){
			if($Stats['formatLoads'] > 10){
				$idUser = $Stats['idUser'];
				$idTag = $Stats['idTag'];
				$idSite = $Stats['idSite'];
				$Domain = $Stats['Domain'];
				$Country = $Stats['Country'];
				
				$Hour = $Stats['Hour'];
				
				$Impressions = vary20($Stats['Impressions']);
				$Opportunities = vary20($Stats['Opportunities']);
				$formatLoads = vary20($Stats['formatLoads']);
				$Revenue = vary20($Stats['Revenue'], true);
				$Coste = vary20($Stats['Coste'], true);
				$ExtraprimaP = 0;
				$Extraprima = 0;
				$Clicks = vary20($Stats['Clicks']);
				$adStarts = vary20($Stats['adStarts']);
			    $FirstQuartiles = vary20($Stats['FirstQuartiles']);
			    $MidViews = vary20($Stats['MidViews']);
			    $ThirdQuartiles = vary20($Stats['ThirdQuartiles']);
			    $CompletedViews = vary20($Stats['CompletedViews']);
				
				$timeAdded = time();
				$lastUpdate = time();
				$Date = '2019-12-12';


				
				$sql = "INSERT INTO reports201912 (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour, Manual) 
				VALUES ('$idUser', '$idTag', '$idSite', '$Domain', '$Country', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', 0, 0, '$Clicks', 0, '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour', 3)";
				$db->query($sql);
				
				$sql = "SELECT TagName FROM supplytag WHERE id = $idTag LIMIT 1";
				$TagName = $db->getOne($sql);
				
				if(array_key_exists($TagName, $SumRevenue[$Hour])){
					$SumRevenue[$Hour][$TagName] += $Revenue;
				}else{
					$SumRevenue[$Hour][$TagName] = $Revenue;
				}
				
				$TotalRev[$Hour] += $Coste;
			}
		}
	
	//print_r($SumRevenue);
	print_r($TotalRev);
	
	exit(0);
	
	*/
	
	
	//exit(0);
	
	$TablaName = "reports201912";
	$TablaNameResume = "reports_resume201912";
	$Date = '2019-12-12';
	
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume WHERE Date = '$Date'";
	$db->query($sql);
	
	$sql = "SELECT 
		idUser, idTag, idSite, Domain, Country, Date, 
		SUM(Impressions) AS Impressions, 
	    SUM(Opportunities) AS Opportunities, 
	    SUM(formatLoads) AS formatLoads, 
	    SUM(Revenue) AS Revenue, 
	    SUM(Coste) AS Coste,
	    ExtraprimaP,
	    SUM(Extraprima) AS Extraprima,
	    SUM(Clicks) AS Clicks,
	    SUM(Wins) AS Wins,
	    SUM(adStarts) AS adStarts,
	    SUM(FirstQuartiles) AS FirstQuartiles,
	    SUM(MidViews) AS MidViews,
	    SUM(ThirdQuartiles) AS ThirdQuartiles,
	    SUM(CompletedViews) AS CompletedViews
    
    FROM $TablaName WHERE Date = '$Date' AND idUser > 0
    GROUP BY idUser, idTag, idSite, Domain, Country";
	
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		$Nins++;
		$Nis++;
		$idUser = $Da['idUser'];
		$idTag = $Da['idTag'];
		$idSite = $Da['idSite'];
		$idDomain = $Da['Domain'];
		$idCountry = $Da['Country'];
		$Impressions = $Da['Impressions'];
	    $Opportunities = $Da['Opportunities'];
	    $formatLoads = $Da['formatLoads'];
	    $Revenue = $Da['Revenue'];
	    $Coste = $Da['Coste'];
	    $ExtraprimaP = $Da['ExtraprimaP'];
	    $Extraprima = $Da['Extraprima'];
	    $Clicks = $Da['Clicks'];
	    $Wins = $Da['Wins'];
	    $adStarts = $Da['adStarts'];
	    $FirstQuartiles = $Da['FirstQuartiles'];
	    $MidViews = $Da['MidViews'];
	    $ThirdQuartiles = $Da['ThirdQuartiles'];
	    $CompletedViews = $Da['CompletedViews'];
	
		$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$Date', 3)";
		$Coma = ", ";
		
		if($Nins > 5000){
			$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date, Manual) VALUES $Values ;";			
			$db->query($sql);
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date, Manual) VALUES $Values ;";			
		$db->query($sql);
	}
	
	
	
	
	
	
	$sql = "SELECT 
		idUser, idTag, idSite, Date, 
		SUM(Impressions) AS Impressions, 
	    SUM(Opportunities) AS Opportunities, 
	    SUM(formatLoads) AS formatLoads, 
	    SUM(Revenue) AS Revenue, 
	    SUM(Coste) AS Coste,
	    SUM(Clicks) AS Clicks
    
    FROM $TablaName WHERE Date = '$Date' AND idUser > 0 AND Manual = 3
    GROUP BY idUser, idTag, idSite, Domain, Country";
	
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		$idUser = $Da['idUser'];
		$idTag = $Da['idTag'];
		$idSite = $Da['idSite'];
		$Impressions = $Da['Impressions'];
	    $Opportunities = $Da['Opportunities'];
	    $formatLoads = $Da['formatLoads'];
	    $Revenue = $Da['Revenue'];
	    $Coste = $Da['Coste'];
	    $Clicks = $Da['Clicks'];
	    
	    $timeAdded = time();
		$lastUpdate = time();
		
		$sql = "INSERT INTO " . STATS . " (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES 
		('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 3)";
		$db->query($sql);
		
	}
	
	//exit(0);
	
	
	
	
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM reportsresume201912 WHERE Date = '$Date'";
	$db2->query($sql);
	
	$sql = "SELECT * FROM $TablaNameResume WHERE Date = '$Date' AND idUser > 0 AND idSite > 0";
	
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		$Nins++;
		$Nis++;
		$ID = $Da['id'];
		$idUser = $Da['idUser'];
		$idTag = $Da['idTag'];
		$idSite = $Da['idSite'];
		$idDomain = $Da['Domain'];
		$idCountry = $Da['Country'];
		$Impressions = $Da['Impressions'];
	    $Opportunities = $Da['Opportunities'];
	    $formatLoads = $Da['formatLoads'];
	    $Revenue = $Da['Revenue'];
	    $Coste = $Da['Coste'];
	    $ExtraprimaP = $Da['ExtraprimaP'];
	    $Extraprima = $Da['Extraprima'];
	    $Clicks = $Da['Clicks'];
	    $Wins = $Da['Wins'];
	    $adStarts = $Da['adStarts'];
	    $FirstQuartiles = $Da['FirstQuartiles'];
	    $MidViews = $Da['MidViews'];
	    $ThirdQuartiles = $Da['ThirdQuartiles'];
	    $CompletedViews = $Da['CompletedViews'];
	
		$Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$idSite', '$formatLoads')";
		$Coma = ", ";
		
		if($Nins > 3000){
			$sql = "INSERT INTO reportsresume201912 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
			$db2->query($sql);
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO reportsresume201912 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
		$db2->query($sql);
	}
				
				
	
	$message .= "\nActualizada tabla de Resumen Server Nuevo: $Nis registros insertados.";
	
	echo "Resume Ready \n";
