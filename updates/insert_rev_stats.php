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
	require('/var/www/html/ads/httpsites.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);	
	exit(0);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);

	//$Date = date('Y-m-d', time() - 3600);
	//$Date = '2019-11-28';
	
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
	$TablaName = 'reports201912';
	$TablaNameResume = 'reports_resume201912';
	$TablaNameResume2 = 'reportsresume201912'; 
	$DomainsArray = array();
	$Date = '2019-12-12';
	
	/*
	$sql = "SELECT supply_monthly_report.*, publisher.user_id FROM supply_monthly_report 
	INNER JOIN publisher ON publisher.id = supply_monthly_report.publisher_id 
	WHERE date = '2019-12-12' AND hour > 5 AND hour < 13";		
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Stats = $db2->fetch_array($query)){
			$idUser = $Stats['user_id'];
			$idTag = $Stats['website_zone_id'];
			$idSite = $Stats['website_id'];
			$Domain = $Stats['domain'];
			$Country = getOldCountryId($Stats['country_id']);
							
			$Impressions = intval($Stats['impressions']) * 2;
			$formatLoads = intval($Stats['formatloads']);
			$Revenue = $Stats['usd_revenue'] * 2;
			$Coste = $Stats['usd_cost'] * 2;
			$Clicks = intval($Stats['clicks']) * 2;
			
			$adStarts = $Stats['starts'] * 2;
			$FirstQuartiles = $Stats['first_quartiles'] * 2;
			$MidViews = $Stats['mid_points'] * 2;
			$ThirdQuartiles = $Stats['third_quartiles'] * 2;
			$CompletedViews = $Stats['completes'] * 2;
			
			$timeAdded = time();
			$lastUpdate = time();
			
			$Date = $Stats['date'];
			$Hour = $Stats['hour'];
			
			if(array_key_exists($Domain, $DomainsArray)){
				$idDomain = $DomainsArray[$Domain];
			}else{
				$DomainS = mysqli_real_escape_string($db->link, $Domain);
				$sql = "SELECT id FROM reports_domain_names WHERE Name LIKE '$DomainS' LIMIT 1";
				$idDomain = intval($db->getOne($sql));
				if($idDomain == 0){
					$sql = "INSERT INTO reports_domain_names (Name) VALUES ('$DomainS')";
					
					$db->query($sql);
					$idDomain = mysqli_insert_id($db->link);
				}
				$DomainsArray[$Domain] = $idDomain;
			}
			
			
			$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste,  Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour, Manual) VALUES 
			('$idUser', '$idTag', '$idSite', '$idDomain', '$Country', '$Impressions', 0, '$formatLoads', '$Revenue', '$Coste', '$Clicks', 0, '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour', 4)";
			
			$db->query($sql);
			//echo $sql;
			echo "\n";
			//exit(0);

		}
	}

	//exit(0);
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume WHERE Date = '$Date'";
	$db->query($sql);
	
	$sql = "SELECT 
		idUser, idTag, idSite, Domain, Country, Date, Manual, 
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
	    
	    $Date = $Da['Date'];
	
		$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$Date', 4)";
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
	
	
	*/
	//STATS	
	
	$SumRevenue = 0;
	
	$sql = "SELECT 
		idUser, idTag, idSite, Date, 
		SUM(Impressions) AS Impressions, 
	    SUM(Opportunities) AS Opportunities, 
	    SUM(formatLoads) AS formatLoads, 
	    SUM(Revenue) AS Revenue, 
	    SUM(Coste) AS Coste,
	    SUM(Clicks) AS Clicks
    
    FROM $TablaName WHERE Date = '$Date' AND idUser > 0 AND Manual = 4 
    GROUP BY idUser, idTag, idSite";
	
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
	    
	    $Date = $Da['Date'];
	    
	    $timeAdded = time();
		$lastUpdate = time();
	
		$sql = "SELECT id FROM stats WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 4";
		$idStat = $db->getOne($sql);
		if($idStat > 0){
			$sql = "UPDATE stats SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
			$db->query($sql);
		}else{
			$sql = "INSERT INTO stats (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 4)";
			$db->query($sql);
		}
		echo $sql;
		$SumRevenue += $Coste;
	}
	
	
	echo $SumRevenue;
	
	exit(0);
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume2 WHERE Date = '$Date'";
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
			$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
			$db2->query($sql);
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
		$db2->query($sql);
	}
