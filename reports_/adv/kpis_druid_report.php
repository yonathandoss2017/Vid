<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	//require('/var/www/html/login/reports_/adv/config_pre.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	//exit(0);

	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');

	$ch = curl_init( $druidUrl );
	
	$Query = "SELECT Domain, Device, SUM(sum_Impressions) AS Impressions, SUM(sum_Complete) AS Complete, SUM(sum_Clicks) AS Clicks, SUM(sum_Vimpression) AS VImpressions
	FROM prd_rtb_event_production_1
	WHERE __time >= '2021-05-08 00:00:00' AND Deal = 'VDMY_HV_84333'
	GROUP BY Domain, Device
	ORDER BY 3 DESC";
	//VDMY_PR_20966
	//VDMY_XA_20977
	
	$context = new \stdClass();
	$context->sqlOuterLimit = 300;
	
	$payload = new \stdClass();
	$payload->query = $Query;
	$payload->resultFormat = 'array';
	$payload->header = true;
	$payload->context = $context;
	
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result) ;
	
	echo '"Dominio","Device","Impresiones","Completes","VTR","Viewability","Clicks","CTR"' . "\n";
	
	foreach($result as $key => $res){
		
		if($key > 0){
			$Domain = $res[0];
			$Device = $res[1];
			$Impressions = $res[2];
			$Completes = $res[3];
			$Clicks = $res[4];
			$VImpressions = $res[5];

			if($Impressions > 0){
				
				if($VImpressions > 0){
					$Viewability = $VImpressions / $Impressions * 100;
				}else{
					$VImpressions = 0;
				}
				$Viewability = number_format($Viewability, 2, '.', ',');
				
				if($Completes > 0){
					$VTR = $Completes / $Impressions * 100;
				}else{
					$VTR = 0;
				}
				$VTR = number_format($VTR, 2, '.', ',');
				
				if($Clicks > 0){
					$CTR = $Clicks / $Impressions * 100;
				}else{
					$CTR = 0;
				}
				$CTR = number_format($CTR, 2, '.', ',');
				
				echo '"' . $Domain . '",' . '"' . $Device . '",' . $Impressions . ',' . $Completes . ',"' . $VTR . '%","' . $Viewability . '%","' . $Clicks . '","' . $CTR . '%"' . "\n";

								
			}
			
		}
		
	}