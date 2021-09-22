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

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	$db3 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
	
	$Countries = array();
	$CountriesV = array();
	$Tags = array();
	
	$Date = '2020-02-03';
	
	$decoded_result = getStatsPlusCountry($Date);
	
	$sql = "DELETE FROM stats_temporal WHERE Date != '$Date'";
	$db->query($sql);
	
	$sql = "DELETE FROM stats_country_last WHERE Date < DATE_SUB(NOW(), INTERVAL 11 DAY)";
	$db2->query($sql);

	$N = 0;
		
	foreach($decoded_result->data->entries as $entry){
		$N++;
		
		$Impressions = $entry->adImpressions;
		$Opportunities = $entry->adOpportunities;
		$Revenue = $entry->revenue;
		$Coste = $entry->siteCost;
		$Clicks = $entry->adClicks;
		$LKQDid = $entry->fieldId;
		$LKQDuser = $entry->fieldName;
		$TagId = $entry->dimension2Id;
		$Tag = $entry->dimension2Name;
		$formatLoads = $entry->formatLoads;		
		$CountryCode = $entry->dimension3Id;
		
		$timeAdded = time();
		$lastUpdate = time();
		
		$inserta = 0;
		
		if(array_key_exists($TagId, $Tags)){
			$inserta = $Tags[$TagId]['ins'];
			
			if($inserta > 0){
				$idTag = $Tags[$TagId]['idTag'];
				$idUser = $Tags[$TagId]['idUser'];
				$idSite = $Tags[$TagId]['idSite'];
			}
		}else{
			$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
			$idTag = $db->getOne($sql);
			if($idTag > 0){
				$inserta = 1;	
				$sql = "SELECT idSite FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
				$idSite = intval($db->getOne($sql));
				$sql = "SELECT idUser FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
				$idUser = intval($db->getOne($sql));
				
				$Tags[$TagId]['idSite'] = $idSite;
				$Tags[$TagId]['idUser'] = $idUser;
				$Tags[$TagId]['idTag'] = $idTag;
			}else{
				$inserta = 0;
			}
			
			$Tags[$TagId]['ins'] = $inserta;
		}
		
		if(array_key_exists($CountryCode, $Countries)){
			$idCountry = $Countries[$CountryCode];
		}else{
			if($CountryCode != ''){
				$sql = "SELECT id FROM reports_country_names WHERE Code = '$CountryCode' LIMIT 1";
				$idCountry = $db->getOne($sql);
				$Countries[$CountryCode] = $idCountry;
			}else{
				$idCountry = 999;
			}
		}
				
		if($inserta == 1){
			$sql = "SELECT id FROM stats_temporal WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND idCountry = '$idCountry' LIMIT 1";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				
				$sql = "UPDATE stats_temporal SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
				
			}else{
				
				$sql = "INSERT INTO stats_temporal (idUser, idTag, idSite, idCountry, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date) VALUES ('$idUser', '$idTag', '$idSite', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date')";
				$db->query($sql);
				
			}
			//echo $sql . "\n";
		}else{
			
			$sql = "SELECT id FROM stats_missing_country WHERE idTag = '$TagId' AND Date = '$Date' AND idCountry = '$idCountry' LIMIT 1";
			$idStat = $db->getOne($sql);
			if($idStat > 0){			
				$sql = "UPDATE stats_missing_country SET Impressions = '$Impressions', formatLoads = '$formatLoads', Revenue = '$Revenue' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO stats_missing_country (idTag, idCountry, formatLoads, Impressions, Revenue, TagName, Date, Time) 
				VALUES ('$TagId', '$idCountry', '$formatLoads', '$Impressions', '$Revenue', '$Tag', '$Date', '$timeAdded')";
				$db->query($sql);
			}
		}
	}
	
	
	$sql = "SELECT DISTINCT idUser FROM stats_temporal ORDER BY idUser ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($U = $db->fetch_array($query)){
			$idUser = $U['idUser'];
			echo $idUser . date(' i:s ') . "\n";
			
			$timeAdded = time();
			$lastUpdate = time();
			$sql = "SELECT 
				idTag, idSite, 
			
				SUM(Impressions) AS Impressions, 
				SUM(Opportunities) AS Opportunities, 
				SUM(formatLoads) AS formatLoads, 
				SUM(Revenue) AS Revenue, 
				SUM(Coste) AS Coste, 
				SUM(Clicks) AS Clicks
				
				FROM stats_temporal WHERE idUser = '$idUser' AND Coste > 0 GROUP BY idTag, idSite";
				
			$query2 = $db->query($sql);
			
			if($db->num_rows($query2) > 0){
				while($S = $db->fetch_array($query2)){
					$idTag = $S['idTag'];
					$idSite = $S['idSite'];
					
					$Impressions = $S['Impressions'];
					$Opportunities = $S['Opportunities'];
					$formatLoads = $S['formatLoads'];
					$Revenue = $S['Revenue'];
					$Coste = $S['Coste'];
					$Clicks = $S['Clicks'];
					
					$CosteEur = correctCurrency($Coste, 2);
					$RevenueEur = correctCurrency($Revenue, 2);
					
					$sql = "SELECT id FROM stats_to_test WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 0";
					$idStat = $db->getOne($sql);
					
					if($idStat > 0){
						$sql = "UPDATE stats_to_test SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db->query($sql);
					}else{
						$sql = "INSERT INTO stats_to_test (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
						$db->query($sql);
					}
					
					//PRODUCCION
					$sql = "SELECT id FROM stats WHERE iduser = '$idUser' AND idtag = '$idTag' AND date = '$Date'";
					$idStat = $db2->getOne($sql);
					
					if($idStat > 0){
						$sql = "UPDATE stats SET impressions = '$Impressions', opportunities = '$Opportunities', format_loads = '$formatLoads', usd_revenue = '$Revenue', eur_revenue = '$RevenueEur', usd_cost = '$Coste', eur_cost = '$CosteEur', clicks = '$Clicks', last_update = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db2->query($sql);
					}else{
						$sql = "INSERT INTO stats (iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
						$db2->query($sql);
					}
					
					//PREPRODUCCION
					$sql = "SELECT id FROM stats WHERE iduser = '$idUser' AND idtag = '$idTag' AND date = '$Date'";
					$idStat = $db3->getOne($sql);
					
					if($idStat > 0){
						$sql = "UPDATE stats SET impressions = '$Impressions', opportunities = '$Opportunities', format_loads = '$formatLoads', usd_revenue = '$Revenue', eur_revenue = '$RevenueEur', usd_cost = '$Coste', eur_cost = '$CosteEur', clicks = '$Clicks', last_update = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db3->query($sql);
					}else{
						$sql = "INSERT INTO stats (iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
						$db3->query($sql);
					}
				}
			}

			$sql = "SELECT 
				idCountry, 
				SUM(Coste) AS Coste  
				FROM stats_temporal WHERE idUser = '$idUser' AND Coste > 0 GROUP BY idCountry";
			
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				while($S = $db->fetch_array($query2)){
					$idCountry = $S['idCountry'];

					$Coste = $S['Coste'];
					$CosteEur = correctCurrency($Coste, 2);
					
					if(array_key_exists($idCountry, $CountriesV)){
						$idCountryV = $CountriesV[$idCountry];
					}else{
						$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$idCountry' LIMIT 1";
						$idCountryV = $db->getOne($sql);
						$CountriesV[$idCountry] = $idCountryV;
					}
					
					//PRODUCCION
					$sql = "SELECT id FROM stats_country2020 WHERE iduser = '$idUser' AND idcountry = '$idCountryV' AND date = '$Date'";
					$idStat = $db2->getOne($sql);
					if($idStat > 0){
						$sql = "UPDATE stats_country2020 SET usd_cost = '$Coste', eur_cost = '$CosteEur', last_update = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db2->query($sql);						
					}else{
						$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, date, last_update) 
						VALUES ('$idUser', '$idCountryV', '$Coste', '$CosteEur', '$Date', '$lastUpdate')";
						$db2->query($sql);
					}
					
					$sql = "SELECT id FROM stats_country_last WHERE iduser = '$idUser' AND idcountry = '$idCountryV' AND date = '$Date'";
					$idStat = $db2->getOne($sql);
					if($idStat > 0){
						$sql = "UPDATE stats_country_last SET usd_cost = '$Coste', eur_cost = '$CosteEur', last_update = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db2->query($sql);
					}else{						
						$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, date, last_update) 
						VALUES ('$idUser', '$idCountryV', '$Coste', '$CosteEur', '$Date', '$lastUpdate')";
						$db2->query($sql);
					}
					
					//PREPRODUCCION
					$sql = "SELECT id FROM stats_country2020 WHERE iduser = '$idUser' AND idcountry = '$idCountryV' AND date = '$Date'";
					$idStat = $db3->getOne($sql);
					if($idStat > 0){
						$sql = "UPDATE stats_country2020 SET usd_cost = '$Coste', eur_cost = '$CosteEur', last_update = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db3->query($sql);						
					}else{
						$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, date, last_update) 
						VALUES ('$idUser', '$idCountryV', '$Coste', '$CosteEur', '$Date', '$lastUpdate')";
						$db3->query($sql);
					}
					
					$sql = "SELECT id FROM stats_country_last WHERE iduser = '$idUser' AND idcountry = '$idCountryV' AND date = '$Date'";
					$idStat = $db2->getOne($sql);
					if($idStat > 0){
						$sql = "UPDATE stats_country_last SET usd_cost = '$Coste', eur_cost = '$CosteEur', last_update = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
						$db3->query($sql);
					}else{						
						$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, date, last_update) 
						VALUES ('$idUser', '$idCountryV', '$Coste', '$CosteEur', '$Date', '$lastUpdate')";
						$db3->query($sql);
					}
				}
			}
				
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	