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
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);	
	
	$Yesterday = '2020-06-13';
	$DateOwn = '2020-06-14';
	$Date = $DateOwn;
	
	$TablaName = getTableName($DateOwn);
	$TablaNameResume = getTableNameResume($DateOwn);
	$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
	
	$Ni = 0;
	$CountryArray2 = array();
	$PublisherArray2 = array();
	$DomainsArray = array();
	
	$sql = "SELECT * FROM supply_monthly_report WHERE date = '$DateOwn' AND hour = 12";
	//$sql = "SELECT * FROM $TablaName WHERE Date = '$Yesterday' AND Hour <= 9";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Sta = $db2->fetch_array($query)){
			$idSite = $Sta['website_id'];
			$idTag = $Sta['website_zone_id'];
			$Domain = $Sta['domain'];
			$id2Country = $Sta['country_id'];
			$idPublisher = $Sta['publisher_id'];
			$Hour = $Sta['hour'];
			
			
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
			
			if(array_key_exists($id2Country, $CountryArray2)){
				$idCountry = $CountryArray2[$id2Country];
			}else{
				//$sql = "SELECT iso FROM country WHERE id = '$id2Country' LIMIT 1";
				//$ISO = $db2->getOne($sql);
				
				$sql = "SELECT id FROM reports_country_names WHERE idVidoomy = '$id2Country' LIMIT 1";
				$idCountry = intval($db->getOne($sql));
				
				if($idCountry == 0){
					$idCountry = 999;
				}
				$CountryArray2[$id2Country] = $idCountry;
			}
			
			if(array_key_exists($idPublisher, $PublisherArray2)){
				$idUser = $PublisherArray2[$idPublisher];
			}else{
				$sql = "SELECT user_id FROM publisher WHERE id = '$idPublisher' LIMIT 1";
				$idUser = $db2->getOne($sql);

				$PublisherArray2[$idPublisher] = $idUser;
			}
			
			$Impressions = $Sta['impressions'];
			$Opportunities = 0;
			$formatLoads = $Sta['formatloads'];
			$Revenue = $Sta['usd_revenue'];
			$Coste = $Sta['usd_cost'];
			$ExtraprimaP = 0;
			$Extraprima = 0;
			$Clicks = $Sta['clicks'];
			$Wins = 0;
			$adStarts = $Sta['starts'];
			$FirstQuartiles = $Sta['first_quartiles'];
			$Midpoints = $Sta['mid_points'];
			$ThirdQuartiles = $Sta['third_quartiles'];
			$CompletedViews = $Sta['completes'];

			$timeAdded = time();
			$lastUpdate = time();
			
			$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour, Manual)
			 VALUES 
			('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$DateOwn', '$Hour', '3')";
			$db->query($sql);
			//echo $sql;
			
			$Ni++;
		}
	}
	
	
	echo "Hours Imported - Own\n";
	
	exit(0);
	
	
	
	if($Ni > 0){
		$Subject = 'Hourly Update OK ' . $LastU;
		$message = "Actualizacion realizada. $Ni registros insertados. Hour: $HFrom - $HTo Date: $Date";
		
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
		
			$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$Date')";
			$Coma = ", ";
			
			if($Nins > 5000){
				$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
				$db->query($sql);
				$Nins = 0;
				$Values = "";
				$Coma = "";
			}
		}
		
		if($Nins > 1){
			$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
			$db->query($sql);
		}
		
		
		
		$mem_var = new Memcached('reps');
		$mem_var->addServer("localhost", 11211);
		$mem_var->flush(1);
		

		
		$Nins = 0;
		$Nis = 0;
		$Coma = "";
		$Values = "";
		
		$Countries = array();
		
		$sql = "DELETE FROM $TablaNameResume2 WHERE Date = '$Date'";
		$db2->query($sql);
		//$db3->query($sql);
		
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
			
			
			$Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$idSite', '$formatLoads')";
			$Coma = ", ";
			
			if($Nins > 3000){
				$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
				$db2->query($sql);
				//$db3->query($sql);
				$Nins = 0;
				$Values = "";
				$Coma = "";
			}
		}
		
		if($Nins > 1){
			$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
			$db2->query($sql);
			//$db3->query($sql);
		}
					
				
		
		$message .= "\nActualizada tabla de Resumen Server Nuevo: $Nis registros insertados.";
		
		
		
		echo "Resume Ready \n";
	}else{
		$Subject = 'Hourly Update KO - 0 Registros ' . $LastU;
		$message = "$Ni registros insertados. Hour: $HFrom - $HTo Date: $Date - Bucle 1: $Bucle1 Bucle 2: $Bucle2";
	}