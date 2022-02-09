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
	
	$idSiteToClone = 2602;
	$DateToCopy = '2022-02-01';
	$DateDestiniy = '2022-02-06';
	$PercentVariation = 1.05;
	
	echo "Copy $DateToCopy to $DateDestiniy (Variation: $PercentVariation) \n";
	
	$DateToday = date('Y-m-d', time());
	
	
	$DateFrom = $DateToCopy;
	$DateTo = $DateDestiniy;
	$Date = $DateDestiniy;
	
	checkTablesByDates($DateFrom, $DateTo);
	
	$TagsArray = array();
	$DomainsArray = array();
	$CountryArray = array();
	$CountryArrayVidoomy = array();
	$Bucle1 = "No";
	$Bucle2 = "No";
	
	$N = 0;
	$Ni = 0;
	$Nno = 0;
	$Last = false;
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$TablaName = getTableName($Date);
	$TablaNameResume = getTableNameResume($Date);
	$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
		
	
	$sql = "DELETE FROM $TablaName WHERE Date = '$DateDestiniy'  AND idSite = $idSiteToClone";//AND Player = 1
	$db->query($sql);
			
	$sql = "SELECT 
		idUser, idTag, idSite, Domain, Country, Player, Date, Hour, 
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
    
    FROM $TablaName WHERE Date = '$DateFrom' AND idSite = $idSiteToClone 
    GROUP BY idUser, idTag, idSite, Domain, Country, Player, Date, Hour";

	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		$Nins++;
		$Nis++;
		$idUser = $Da['idUser'];
		$idTag = $Da['idTag'];
		$idSite = $Da['idSite'];
		$idDomain = $Da['Domain'];
		$idCountry = $Da['Country'];
		$Player = $Da['Player'];
		$Hour = $Da['Hour'];
		$Impressions = round($Da['Impressions'] * $PercentVariation);
	    $Opportunities = round($Da['Opportunities'] * $PercentVariation);
	    $formatLoads = round($Da['formatLoads'] * $PercentVariation);
	    $Revenue = $Da['Revenue'] * $PercentVariation;
	    $RevenueEur = $Da['RevenueEur'] * $PercentVariation;
	    $Coste = $Da['Coste'] * $PercentVariation;
	    $CosteEur = $Da['CosteEur'] * $PercentVariation;
	    $ExtraprimaP = $Da['ExtraprimaP'] * $PercentVariation;
	    $Extraprima = $Da['Extraprima'] * $PercentVariation;
	    $Clicks = round($Da['Clicks'] * $PercentVariation);
	    $Wins = round($Da['Wins'] * $PercentVariation);
	    $adStarts = round($Da['adStarts'] * $PercentVariation);
	    $FirstQuartiles = round($Da['FirstQuartiles'] * $PercentVariation);
	    $MidViews = round($Da['MidViews'] * $PercentVariation);
	    $ThirdQuartiles = round($Da['ThirdQuartiles'] * $PercentVariation);
	    $CompletedViews = round($Da['CompletedViews'] * $PercentVariation);
	
		$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Player', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$DateDestiniy', '$Hour')";
		$Coma = ", ";
		
		if($Nins > 5000){
			$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date, Hour) VALUES $Values ;";			
			$db->query($sql);
			//echo $sql;

			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date, Hour) VALUES $Values ;";			
		$db->query($sql);
		//echo $sql;
	}
		
	echo "Done";	
	