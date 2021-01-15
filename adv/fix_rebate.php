<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	require('../../config.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	
	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	
	//exit(0);
	//$Agencys = array(60, 80, 91, 15, 93, 108, 20, 37, 97, 21, 101, 18, 14, 71, 48, 104, 38, 23, 109, 118, 100, 94, 123, 22, 31, 40, 12);
	//$Agencys = array(16);
	//$Agencys = array(41, 106, 75, 44, 72);
	
	//foreach($Agencys as $AgencyId){
		
		$sql = "SELECT * FROM campaign WHERE type = 2 AND rebate > 0";
		$query = $db3->query($sql);
		if($db3->num_rows($query) > 0){
			while($Camp = $db3->fetch_array($query)){
				$idCamp = $Camp['id'];
				$RebatePercent = $Camp['rebate'];
				//$RebatePercent = 40;
				
				$sql = "SELECT * FROM reports WHERE idCampaing = $idCamp AND Date >= '2020-08-11' AND Date <= '2020-08-17'";
				$queryRep = $db->query($sql);
				if($db->num_rows($queryRep) > 0){
					while($Rep = $db->fetch_array($queryRep)){
						$idRep = $Rep['id'];
						$Revenue = $Rep['Revenue'];
						$Rebate = $Revenue * $RebatePercent / 100;
						
						$sql = "UPDATE reports SET Rebate = $Rebate WHERE id = '$idRep' LIMIT 1";
						//echo $sql . "\n";
						$db->query($sql);
						
					}
				}
			}
		}
		
	//}
	
	
	
	
	
	