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
	
	$Date = date('Y-m-d', time() - (4800 * 1));
	//echo $Date = '2022-02-06';
	$MonthT = date('Ym', time() - (4800 * 1));
	//$MonthT = '202006';
	
	$DateSRate = array();
	$ArrayCurrency = array();

	//exit(0);
	// $Date = '2020-07-08';

	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	
	$DateSRate = array();
	
	$sql = "SELECT 
	idUser, idSite, idTag,
	SUM(Coste) AS Coste,
	SUM(CosteEur) AS CosteEur,
	SUM(Revenue) AS Revenue,
	SUM(RevenueEur) AS RevenueEur,
	SUM(formatLoads) AS formatLoads,
	SUM(Impressions) AS Impressions,
	SUM(Opportunities) AS Opportunities,
	SUM(Clicks) AS Clicks
	
	FROM reports_resume$MonthT WHERE Date = '$Date' AND idUser > 0 GROUP BY idTag";
	//SUM(CosteEur) AS CosteEur,
	//SUM(RevenueEur) AS RevenueEur,
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($S = $db->fetch_array($query)){
	
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$Revenue = $S['Revenue'];
			$RevenueEur = $S['RevenueEur'];
			$Coste = $S['Coste'];
			$CosteEur = $S['CosteEur'];
			$Clicks = $S['Clicks'];
			$formatLoads = $S['formatLoads'];
			
			/*
			//CONVERSION A EUROS
			if(array_key_exists($Date, $DateSRate)){
				$Rate = $DateSRate[$Date];
		    }else{
			    $DateForRate = new DateTime($Date);
			    $DateForRate->modify('-1 day');
			    
			    $JsonRates = file_get_contents('https://api.exchangeratesapi.io/' . $DateForRate->format('Y-m-d') . '?access_key=7df9c3b2eb8318c2294112c50f5209c8');
			    $Rates = json_decode($JsonRates);
			    $Rate = $Rates->rates->USD;
			    
			    $DateSRate[$Date] = $Rate;
		    }
		    
		    if($Revenue > 0){
				$RevenueEur = $Revenue / $Rate;   
		    }else{
			    $RevenueEur = 0;
		    }
		    if($Coste > 0){
				$CosteEur = $Coste / $Rate;
		    }else{
			    $CosteEur = 0;
		    }
			*/
			$idSite = $S['idSite'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			
			$timeAdded = time();
			$lastUpdate = time();
			
			/*
			if($idSite == 11513 && date('d', time() - 3600) == 27){
				$Impressions = $formatLoads / 10.8;
				$Coste = $Impressions * 0.0015;
				$Revenue = $Coste * 1.4286;
			}
			*/
			/*
			if($idSite == 11630){
				$Impressions = $formatLoads / 4;
				$Coste = $Impressions * 0.0015;
				$Revenue = $Coste * 1.4286;
			}
			
			if(array_key_exists($Date, $DateSRate)){
				$Rate = $DateSRate[$Date];
		    }else{
			    $DateForRate = new DateTime($Date);
			    $DateForRate->modify('-1 day');
			    
			    $JsonRates = file_get_contents('https://api.exchangeratesapi.io/' . $DateForRate->format('Y-m-d'));
			    $Rates = json_decode($JsonRates);
			    $Rate = $Rates->rates->USD;
			    
			    $DateSRate[$Date] = $Rate;
		    }
		    */
		    
			//$CosteEur = $Coste / $Rate;
			//$RevenueEur = $Revenue / $Rate;
			
			$sql = "SELECT id FROM " . STATS . " WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 0";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				$sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', RevenueEur = '$RevenueEur', Coste = '$Coste', CosteEur = '$CosteEur', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO " . STATS . " (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, RevenueEur, CosteEur, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$RevenueEur', '$CosteEur', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
				$db->query($sql);
			}
		}
	}
	
	echo "OK 1 \n";
	
	//exit(0);	
	$sql = "SELECT 
		idUser, Date, Country,
	    SUM(Coste) AS Coste,
	    SUM(CosteEur) AS CosteEur,
	    Date 
    FROM reports_resume$MonthT WHERE idUser > 0 AND Coste > 0 AND Date = '$Date'
    GROUP BY idUser, Country, Date";

	$N = 0;
	$query = $db->query($sql);
	$TotalRows = $db->num_rows($query);
	if($TotalRows > 0){
		while($Da = $db->fetch_array($query)){
			$N++;
			$idUser = $Da['idUser'];
			$idCountry = $Da['Country'];
		    $Coste = $Da['Coste'];
		    $CosteEur = $Da['CosteEur'];
		    //$CosteEur = $Coste / $Rate;
		    $Date = $Da['Date'];
		    //echo "\n";		    
		    
		    $timeAdded = time();
			$lastUpdate = time();
			
			$sql = "SELECT id FROM stats_country_r2020 WHERE idUser = '$idUser' AND idCountry = '$idCountry' AND Date = '$Date'";
			$idStat = $db->getOne($sql);
			
			if($idStat > 0){
				$sql = "UPDATE stats_country_r2020 SET Coste = '$Coste', CosteEur = '$CosteEur' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO stats_country_r2020 (idUser, idCountry, Coste, CosteEur, Date) VALUES ('$idUser', '$idCountry',  '$Coste', '$CosteEur', '$Date')";
				$db->query($sql);				
			}
		}
	}
	
	echo "OK 1B \n";

	$Day = $Date;
	$Nv = 0;
	
	$sql = "SELECT * FROM stats WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "DELETE FROM stats WHERE date = '$Day'";
		$db2->query($sql);
		
		$Values = "";
		$Coma = "";
		
		while($S = $db->fetch_array($query)){
			$Nv++;	
			
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			$idSite = $S['idSite'];
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$formatLoads = $S['formatLoads'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			$CosteEur = $S['CosteEur'];
		    $RevenueEur = $S['RevenueEur'];
			
			
			$Clicks = $S['Clicks'];
			$timeAdded = $S['timeAdded'];
			$lastUpdate = $S['lastUpdate'];
			$Date = $S['Date'];
			
			$Values .= "$Coma ('$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$RevenueEur','$Coste','$CosteEur','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$Coma = ",";
			
			if($Nv >= 3000){
				$sql = "INSERT INTO stats (iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
				VALUES $Values";
				$db2->query($sql);
				
				$Nv = 0;
				$Values = "";
				$Coma = "";
			}
		}
		if($Nv > 0){
			$sql = "INSERT INTO stats (iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
			VALUES $Values";
			$db2->query($sql);			
		}
	}
	
	echo "OK 2 \n";

	$CountryOld = array();
	$sql = "SELECT * FROM stats_country_r2020 WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$Nv = 0;
		
		$Datem10 = date('Y-m-d', time() - 10 * 24 * 3600);
		$sql = "DELETE FROM stats_country_last WHERE date < '$Datem10' OR date = '$Day'";
		$db2->query($sql);
		
		$sql = "DELETE FROM stats_country2020 WHERE date = '$Day'";
		$db2->query($sql);
		
		$Values = "";
		$Coma = "";
				
		while($S = $db->fetch_array($query)){	
			$Nv++;
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idCountryOld = $S['idCountry'];
			
			if(array_key_exists($idCountryOld, $CountryOld)){
				$idCountry = $CountryOld[$idCountryOld];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = $idCountryOld LIMIT 1";
				$idCountry = $db->getOne($sql);
				
				$CountryOld[$idCountryOld] = $idCountry;
			}
			
			$Coste = $S['Coste'];
			$CosteEur = $S['CosteEur'];
			
			$lastUpdate = time();
			
			
			//$Values .= "$Coma ('$idS', '$idUser', '$idCountry', '$Coste', '$CosteEur', '$lastUpdate', '$Date')";
			$Values .= "$Coma ('$idUser', '$idCountry', '$Coste', '$CosteEur', '$lastUpdate', '$Date')";
			$Coma = ",";
			
			if($Nv >= 3000){
				$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
				VALUES $Values";
				$db2->query($sql);
				
				$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
				VALUES $Values";
				$db2->query($sql);
				
				$Nv = 0;
				$Values = "";
				$Coma = "";
			}
			
		}
		
		if($Nv > 0){
			$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
			VALUES $Values";
			$db2->query($sql);
				
			$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
			VALUES $Values";
			$db2->query($sql);			
		}
	}
	
	echo " OK 2B \n";

	$db3 = new SQL($pubStaging['host'], $pubStaging['db'], $pubStaging['user'], $pubStaging['pass']);
	
	//Pre-Prod
	
	$Nv = 0;
	
	$sql = "SELECT * FROM stats WHERE Date = '$Day'";
	//echo $sql . "\n";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "DELETE FROM stats WHERE date = '$Day'";
		//echo $sql . "\n";
		$db3->query($sql);
		
		$Values = "";
		$Coma = "";
		
		while($S = $db->fetch_array($query)){
			$Nv++;	
			
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			$idSite = $S['idSite'];
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$formatLoads = $S['formatLoads'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			$CosteEur = $S['CosteEur'];
		    $RevenueEur = $S['RevenueEur'];
			
			
			$Clicks = $S['Clicks'];
			$timeAdded = $S['timeAdded'];
			$lastUpdate = $S['lastUpdate'];
			$Date = $S['Date'];
			
			$Values .= "$Coma ('$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$RevenueEur','$Coste','$CosteEur','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$Coma = ",";
			
			if($Nv >= 3){
				$sql = "INSERT INTO stats (iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
				VALUES $Values";
				//echo $sql . "\n";
				//exit(0);
				$db3->query($sql);
				
				$Nv = 0;
				$Values = "";
				$Coma = "";
			}
		}
		if($Nv > 0){
			$sql = "INSERT INTO stats (iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
			VALUES $Values";
			$db3->query($sql);			
		}
	}
	
	echo "OK 3 \n";
	
	$CountryOld = array();
	$sql = "SELECT * FROM stats_country_r2020 WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$Nv = 0;
		
		$Datem10 = date('Y-m-d', time() - 10 * 24 * 3600);
		$sql = "DELETE FROM stats_country_last WHERE date < '$Datem10' OR date = '$Day'";
		$db3->query($sql);
		
		$sql = "DELETE FROM stats_country2020 WHERE date = '$Day'";
		$db3->query($sql);
		
		$Values = "";
		$Coma = "";
				
		while($S = $db->fetch_array($query)){	
			$Nv++;
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idCountryOld = $S['idCountry'];
			
			if(array_key_exists($idCountryOld, $CountryOld)){
				$idCountry = $CountryOld[$idCountryOld];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = $idCountryOld LIMIT 1";
				$idCountry = $db->getOne($sql);
				
				$CountryOld[$idCountryOld] = $idCountry;
			}
			
			$Coste = $S['Coste'];
			$CosteEur = $S['CosteEur'];
			
			$lastUpdate = time();
			
			
			$Values .= "$Coma ('$idUser', '$idCountry', '$Coste', '$CosteEur', '$lastUpdate', '$Date')";
			$Coma = ",";
			
			if($Nv >= 3000){
				$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
				VALUES $Values";
				$db3->query($sql);
				
				$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
				VALUES $Values";
				$db3->query($sql);
				
				$Nv = 0;
				$Values = "";
				$Coma = "";
			}
			
		}
		
		if($Nv > 0){
			$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
			VALUES $Values";
			$db3->query($sql);
				
			$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
			VALUES $Values";
			$db3->query($sql);			
		}
	}
	
	echo " OK 3B \n";