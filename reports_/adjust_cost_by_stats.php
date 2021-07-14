<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	
	$sql = "SELECT 
			idTag, Date, 
			SUM(Coste) AS Coste,
			SUM(CosteEur) AS CosteEur
		FROM stats 
		WHERE stats.Date = '2021-06-05' AND idTag > 0 GROUP BY idTag, Date ORDER BY id ASC";// AND users.currency = 1
	 //INNER JOIN users ON users.id = stats.idUser
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			//$idS = $Row['id'];
			
			$Date = $Row['Date'];
			$idTag = $Row['idTag'];
			$Coste = $Row['Coste'];
			$CosteEur = $Row['CosteEur'];
			
			$sql = "SELECT SUM(Coste) AS Coste FROM reports202106 WHERE Date = '$Date' AND idTag = '$idTag'";
			$CosteRepo = round($db->getOne($sql), 6);
			if($CosteRepo > $Coste){
				$ReducePercent =  $Coste / $CosteRepo;
				echo "$ReducePercent = $Coste / $CosteRepo   ";
				echo $ReducePercent . "\n";
				//$ReduceTotalEur = $CosteRepoEur - $CosteEur;
				//echo $Coste . ' - ' .  $CosteRepo . ': ' . $ReduceTotal . "\n";
				
					$sql = "SELECT * FROM reports202106 WHERE Date = '$Date' AND idTag = '$idTag' AND Coste > 0 ORDER BY Coste DESC";
					$query2 = $db->query($sql);
					$TotalReg = $db->num_rows($query2);
					if($TotalReg > 0){
						//$ReduceParcial = round($ReduceTotal / $TotalReg, 6);
						while($Row2 = $db->fetch_array($query2)){
							$idRow = $Row2['id'];
							$CosteP = $ReducePercent * $Row2['Coste'];
							$CostePEur = $ReducePercent * $Row2['CosteEur'];
							
							//echo $Row2['Coste'] . " = $CosteP - ";
							//echo $Row2['CosteEur'] . " = $CostePEur - ";
							
							$sql = "UPDATE reports202106 SET Coste = $CosteP, CosteEur = $CostePEur WHERE id = $idRow LIMIT 1;";
							//echo $sql . "\n";
							$db->query($sql);
								
							
						}
					}else{
						echo "ERROR: $Date idTag: $idTag \n";
						exit();
					}
				
			}
			//$sql = "UPDATE stats SET RevenueEur = '$RevenueEur', CosteEur = '$CosteEur' WHERE id = $idS LIMIT 1";
			//$db->query($sql);
			//$db2->query($sql);
			//$db3->query($sql);
			//echo $sql . "\n";
			
		}
	}
	
	
	exit(0);
	
	//$Date = date('Y-m-d', time() - 1200);
	$Date = '2020-03-30';	
	$DateFrom = $Date;
	$DateTo = $Date;
	
	$TablaName = getTableName($Date);
	$TablaNameResume = getTableNameResume($Date);
	$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume WHERE Date = '$Date'";
	//exit(0);
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
	
	
				
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$Countries = array();
	
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
		$timeAdded = time();
		$lastUpdate = time();
		
		$idCountry = $Da['Country'];
		
		
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
				
			
	
	echo "Actualizada tabla de Resumen Server Nuevo: $Nis registros insertados.";
