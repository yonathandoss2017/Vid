<?php	
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
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Year = "2019";
	$Month = "10";
	
	$N = 0;
	for($D = 9; $D <= 10; $D++){
		$N++;
		if($D >= 10){
			$Di = "$D";
		}else{
			$Di = "0$D";
		}
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
	    
	    FROM `reports" . $Year . $Month . "` WHERE Date = '" . $Year . "-" . $Month . "-" . $Di . "' AND idUser > 0
	    GROUP BY idUser, idTag, idSite, Domain, Country";
	    
	    //echo $sql;
	    //exit(0);
	    
	    $query = $db->query($sql);
		while($Da = $db->fetch_array($query)){
			
			$idUser = $Da['idUser'];
			$idTag = $Da['idTag'];
			$idSite = $Da['idSite'];
			$Domain = $Da['Domain'];
			$Country = $Da['Country'];
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
		    
	    	$sql = "INSERT INTO reports_resume" . $Year . $Month . " 
	    		(idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date)
	    		VALUES
	    		('$idUser', '$idTag', '$idSite', '$Domain', '$Country', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins', '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '" . $Year . "-" . $Month . "-" . $Di . "')";
			$db->query($sql);
			//print_r($Da);
			//exit(0);
	    }
	    //echo $sql . '<br/><br/><br/><br/><br/>';
	    echo $N . "\n";
    }
    