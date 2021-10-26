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
	echo $Date = '2021-10-25';
		
	$DateFrom = $Date;
	$DateTo = $Date;
	
	$TablaName = getTableName($Date);
	$TablaNameResume = getTableNameResume($Date);
	$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume WHERE Date = '$DateFrom' AND Player = 1";
	$db->query($sql);
	
	$sql = "SELECT 
		idUser, idTag, idSite, Domain, Country, Player, Date, 
		SUM(Impressions) AS Impressions, 
	    SUM(Opportunities) AS Opportunities, 
	    SUM(formatLoads) AS formatLoads, 
	    SUM(Revenue) AS Revenue,
	    SUM(RevenueEur) AS RevenueEur,
	    SUM(Coste) AS Coste,
	    SUM(CosteEur) AS CosteEur,
	    ExtraprimaP,
	    SUM(Extraprima) AS Extraprima,
	    SUM(Clicks) AS Clicks,
	    SUM(Wins) AS Wins,
	    SUM(adStarts) AS adStarts,
	    SUM(FirstQuartiles) AS FirstQuartiles,
	    SUM(MidViews) AS MidViews,
	    SUM(ThirdQuartiles) AS ThirdQuartiles,
	    SUM(CompletedViews) AS CompletedViews
    
    FROM $TablaName WHERE Date = '$DateFrom' AND idUser > 0 AND Player = 1
    GROUP BY idUser, idTag, idSite, Domain, Country, Player";
	
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
	    $RevenueEur = $Da['RevenueEur'];
	    $Coste = $Da['Coste'];
	    $CosteEur = $Da['CosteEur'];
	    $ExtraprimaP = $Da['ExtraprimaP'];
	    $Extraprima = $Da['Extraprima'];
	    $Clicks = $Da['Clicks'];
	    $Wins = $Da['Wins'];
	    $adStarts = $Da['adStarts'];
	    $FirstQuartiles = $Da['FirstQuartiles'];
	    $MidViews = $Da['MidViews'];
	    $ThirdQuartiles = $Da['ThirdQuartiles'];
	    $CompletedViews = $Da['CompletedViews'];
	
		$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '1', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$DateFrom')";
		$Coma = ", ";
		
		if($Nins > 5000){
			$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";	
			//exit(0);
			$db->query($sql);
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
		$db->query($sql);
	}
			
			
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$timeAdded = time();
	$lastUpdate = time();
	
	$Countries = array();
	
	$sql = "DELETE FROM $TablaNameResume2 WHERE date = '$DateFrom' AND player = 1";
	$db2->query($sql);
	
	
	$sql = "SELECT * FROM $TablaNameResume WHERE Date = '$DateFrom' AND idUser > 0 AND idSite > 0 AND Player = 1";
	
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		$Nins++;
		$Nis++;
		$ID = $Da['id'];
		$idUser = $Da['idUser'];
		$idTag = $Da['idTag'];
		$idSite = $Da['idSite'];
		$idDomain = $Da['Domain'];
		$idC = $Da['Country'];
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
		
		/*
		if(array_key_exists($idC, $Countries){
			$idCountry = $Countries[$idC];
		}else{
			$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$idC' LIMIT 1";
			$idCountry = $db->getOne($sql);
			
			$Countries[$idC] = $idCountry;
		}
		*/
		
		$idCountry = $Da['Country'];
		
		
		$Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$DateFrom', '$idSite', '$formatLoads', '0', '1')";
		$Coma = ", ";
		
		if($Nins > 2000){
			$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads, product, player) VALUES $Values ;";			
			$db2->query($sql);
			
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads, product, player) VALUES $Values ;";			
		$db2->query($sql);
	}
