<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	$db1 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	//exit(0);
	
	$db2 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$DemandTags = array();	
	$ActiveDeals = array();
	$CampaingData = array();
	
	//$sql = "SELECT * FROM campaign WHERE  status = 1 AND id = $idCampaing";//ssp_id = 4 AND
	$sql = "SELECT * FROM `vidoomy-advertisers-panel`.campaign WHERE deal_id LIKE '%_Old' AND ssp_id = 4 AND id != 6351 ORDER BY id DESC;";
	//$sql = "SELECT * FROM campaign WHERE id = 923 OR id = 924 OR id = 925 OR id = 926 OR id = 927 OR id = 928 OR id = 929 OR id = 930 OR id = 931 OR id = 932";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Camp = $db2->fetch_array($query)){
			$idCamp = $Camp['id'];
			
			$ActiveDeals[$idCamp] = $Camp['deal_id'];
			$DemandTags[] = $Camp['deal_id'];
			
			$CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
			$CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];
			$CampaingData[$idCamp]['Type'] = $Camp['type'];
			if($Camp['cpm'] > 0){
				$CampaingData[$idCamp]['CPM'] = $Camp['cpm'];
			}else{
				$CampaingData[$idCamp]['CPM'] = 0;
			}
			if($Camp['cpv'] > 0){
				$CampaingData[$idCamp]['CPV'] = $Camp['cpv'];
			}else{
				$CampaingData[$idCamp]['CPV'] = 0;
			}
			$CampaingData[$idCamp]['Type'] = $Camp['type'];
			$CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];
			$idAgency = $Camp['agency_id'];
			
			if($Camp['vtr_from'] > 0 && $Camp['vtr_to'] > 0){
				$CampaingData[$idCamp]['VTRFrom'] = $Camp['vtr_from'];
				$CampaingData[$idCamp]['VTRTo'] = $Camp['vtr_to'];
				$CampaingData[$idCamp]['CVTR'] = true;
			}else{
				$CampaingData[$idCamp]['CVTR'] = false;
			}
				
			if($Camp['ctr_from'] > 0 && $Camp['ctr_to'] > 0){
				$CampaingData[$idCamp]['CTRFrom'] = $Camp['ctr_from'];
				$CampaingData[$idCamp]['CTRTo'] = $Camp['ctr_to'];
				$CampaingData[$idCamp]['CCTR'] = true;
			}else{
				$CampaingData[$idCamp]['CCTR'] = false;
			}
			
			if($Camp['viewability_from'] > 0 && $Camp['viewability_to'] > 0){
				$CampaingData[$idCamp]['ViewFrom'] = $Camp['viewability_from'];
				$CampaingData[$idCamp]['ViewTo'] = $Camp['viewability_to'];
				$CampaingData[$idCamp]['CView'] = true;
			}else{
				$CampaingData[$idCamp]['CView'] = false;
			}
			
			$sql = "UPDATE `vidoomy-advertisers-panel`.campaign SET status = 3 WHERE id = $idCamp";
			//$db3->query($sql);
			
			
			$sql = "SELECT sales_manager_id FROM `vidoomy-advertisers-panel`.agency WHERE id = $idAgency";
			$idSalesManager = $db3->getOne($sql);
			
			
			$sql1 = "SELECT * FROM reports_bk WHERE idCampaing = $idCamp";
			echo $sql1 . "\n";
			$query1 = $db1->query($sql1);
			if($db1->num_rows($query1) > 0){
				while($Rec = $db1->fetch_array($query1)){
					$idRec = $Rec['id'];

					$sql = "UPDATE reports SET idSalesManager = $idSalesManager WHERE reports.id = $idRec" ;// 
					echo $sql . "\n";
					$db->query($sql);
					//exit(0);
				}
			}
			
			//exit(0);
		}
	}
