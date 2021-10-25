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
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$SumImp = 0;
	$SumImpPlus = 0;
	
	$sql = "SELECT * FROM reports202110 WHERE idUser = 26215 AND Date = '2021-10-24' AND Impressions > 5";
	$query = $db->query($sql);
	
	if($db->num_rows($query) > 0){
		
		while($Row = $db->fetch_array($query)){
			$idRow = $Row['id'];
			
			$SumImp += $Row['Impressions'];
			
			
			$NewImps = round($Row['Impressions'] * 1.04);
			$SumImpPlus += $NewImps;
			
			
			$NewOps = round($Row['Opportunities'] * 1.04);
			$NewFL = round($Row['formatLoads'] * 1.04);
			$NewRevenue = $Row['Revenue'] * 1.04;
			$NewRevenueEur = $Row['RevenueEur'] * 1.04;
			$NewCoste = $Row['Coste'] * 1.04;
			$NewCosteEur = $Row['CosteEur'] * 1.04;
			$NewClicks = round($Row['Clicks'] * 1.04);
			$NewFirstQuartiles = round($Row['FirstQuartiles'] * 1.04);
			$NewMidViews = round($Row['MidViews'] * 1.04);
			$NewThirdQuartiles = round($Row['ThirdQuartiles'] * 1.04);
			$NewCompletedViews = round($Row['CompletedViews'] * 1.04);
			
			$sql2 = "UPDATE reports202110 SET 
			Impressions = $NewImps,
			Opportunities = $NewOps,
			formatLoads = $NewFL,
			Revenue = '$NewRevenue',
			RevenueEur = '$NewRevenueEur',
			Coste = '$NewCoste',
			CosteEur = '$NewCosteEur',
			Clicks = $NewClicks,
			FirstQuartiles = $NewFirstQuartiles,
			MidViews = $NewMidViews,
			ThirdQuartiles = $NewThirdQuartiles,
			CompletedViews = $NewCompletedViews
			WHERE id = $idRow LIMIT 1";
			
			$db->query($sql2);
			
			echo $sql2 . "\n\n";
			
		}
	}
	
	
	
	echo "Was: " . $SumImp . " - Will be: " . $SumImpPlus;