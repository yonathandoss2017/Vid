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
	//exit(0);
	
	$db2 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');

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
	
	//$Date = date('Y-m-d', time() - (3600 * 4));
	//$Date = date('2020-03-28');
	//$Hour = date('H');
	//$Hour = 23;
	
	$Date1 = '2021-12-30';
	$Date2 = '2021-12-31';
	$idCampaing = 6174;
	
	/*
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	
	$DemandTags = array();	
	$ActiveDeals = array();
	$CampaingData = array();
	
	/*
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:PT_vtr:70_va:80_MaxColchon_Familia_137.500_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:ES_vtr:70_va:80_MaxColchon_Todos_225.000_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:ES_vtr:70_va:80_MaxColchon_Pareja_225.000_30Jun";	
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:ES_vtr:70_va:80_MaxColchon_Chica_225.000_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:FR_vtr:70_va:80_MaxColchon_Chica_137.500_30Jun";	
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:FR_vtr:70_va:80_MaxColchon_Familia_137.500_30Jun";	
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:ES_vtr:70_va:80_MaxColchon_Familia_225.000_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:PT_vtr:70_va:80_MaxColchon_Todos_137.500_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:PT_vtr:70_va:80_MaxColchon_Chica_137.500_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:PT_vtr:70_va:80_MaxColchon_Pareja_137.500_30Jun";	
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:FR_vtr:70_va:80_MaxColchon_Todos_137.500_30Jun";
	$CampNames[] = "JRU_MediterraneaDeMedios_ES_All_CPV_USD:0.012_EUR:0.01_GeoIP:FR_vtr:70_va:80_MaxColchon_Pareja_137.500_30Jun";
	
	$MultiCamp = "";
	$Orr = "";
	foreach($CampNames as $CampName){
		$sql = "SELECT id FROM campaign WHERE name LIKE '$CampName' LIMIT 1";
		$idCampaing = $db2->getOne($sql);
		
		$MultiCamp .= " $Orr id = $idCampaing ";
		$Orr = " OR ";
	}
	*/
		
	$sql = "SELECT * FROM campaign WHERE ssp_id = 4 AND status = 1 AND id = $idCampaing";
	//$sql = "SELECT * FROM campaign WHERE ssp_id = 4 AND status = 1 AND (deal_id = '1053196' OR deal_id = '1053199' OR deal_id = '1053198' OR deal_id = '1053197' OR deal_id = '1053200' OR deal_id = '1053203' OR deal_id = '1053202' OR deal_id = '1053201' OR deal_id = '1053204' OR deal_id = '1053207' OR deal_id = '1053206' OR deal_id = '1053205')";
	//$sql = "SELECT * FROM campaign WHERE ssp_id = 4 AND status = 1 AND ($MultiCamp)";
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
			if ($Camp['cpc'] > 0) {
				$CampaingData[$idCamp]['CPC'] = $Camp['cpc'];
			} else {
				$CampaingData[$idCamp]['CPC'] = 0;
			}
			if ($Camp['vcpm'] > 0) {
				$CampaingData[$idCamp]['vCPM'] = $Camp['vcpm'];
			} else {
				$CampaingData[$idCamp]['vCPM'] = 0;
			}
			$CampaingData[$idCamp]['Type'] = $Camp['type'];
			$CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];
			
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
		}
	}
	
	$CampsSQL = "";
	$OR = "";
	foreach($ActiveDeals as $idC => $CID){
		$CampsSQL .= " $OR reports.idCampaing = $idC ";
		
		$OR = " OR ";
	}
	
	
	$sql = "SELECT reports.* FROM reports WHERE $CampsSQL AND reports.Date BETWEEN '$Date1' AND '$Date2'";
	
	//exit(0);
	//echo "\n\n";
	

	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			$idRow = $Row['id'];
			$idCampaing = $Row['idCampaing'];
			
			
			$CVTR = $CampaingData[$idCampaing]['CVTR'];
			$CCTR = $CampaingData[$idCampaing]['CCTR'];
			$CView = $CampaingData[$idCampaing]['CView'];
			$CPM = $CampaingData[$idCampaing]['CPM'];
			$CPV = $CampaingData[$idCampaing]['CPV'];
			$CPC = $CampaingData[$idCampaing]['CPC'];
			$vCPM = $CampaingData[$idCampaing]['vCPM'];
			$RebatePer = $CampaingData[$idCamp]['Rebate'];
			
			$PercCh = 1;
			$PercCh2 = 1;
			
			//$Requests = intval($Row['Requests'] * $PercCh);
			//$Bids = intval($Row['Bids'] * $PercCh);
			$Impressions = intval($Row['Impressions'] * $PercCh);
			$CompleteV = intval($Row['CompleteV'] * $PercCh);
			
			if($CCTR === true){
				$CTRFrom = $CampaingData[$idCampaing]['CTRFrom'] * 100;
				$CTRTo = $CampaingData[$idCampaing]['CTRTo'] * 100;
				
				$RandCTR = rand($CTRFrom, $CTRTo) / 10000;
				$Clicks = intval($Impressions * $RandCTR);
			}else{
				$Clicks = intval($Row['Clicks'] * $PercCh);
			}
			/*
			if($CVTR === true){
				$VTRFrom = $CampaingData[$idCampaing]['VTRFrom'] * 100;
				$VTRTo = $CampaingData[$idCampaing]['VTRTo'] * 100;
				
				$RandVTR = rand($VTRFrom, $VTRTo) / 10000;
				$CompleteV = intval($Impressions * $RandVTR);
				$CompleteVPerc = $RandVTR;
					
				$Complete25 = calcPercents(25, $Impressions, $CompleteV);
				$Complete50 = calcPercents(50, $Impressions, $CompleteV);
				$Complete75 = calcPercents(75, $Impressions, $CompleteV);
			}else{
				$CompleteV = intval($Row['CompleteV'] * $PercCh);
				$Complete25 = intval($Row['Complete25'] * $PercCh);
				$Complete50 = intval($Row['Complete50'] * $PercCh);
				$Complete75 = intval($Row['Complete75'] * $PercCh);
			}
			*/
			if($CView === true){
				$ViewFrom = $CampaingData[$idCampaing]['ViewFrom'] * 100;
				$ViewTo = $CampaingData[$idCampaing]['ViewTo'] * 100;
				
				$RandView = rand($ViewFrom, $ViewTo) / 10000;
				$VImpressions = intval($Impressions * $RandView);
			}else{
				$VImpressions = intval($Row['VImpressions'] * $PercCh);
			}
			
			//$CPM = 2.5;
			
			if ($Impressions > 0 && $CPM > 0) {
				$Revenue = $Impressions * $CPM / 1000;
				echo "CPM: " . $CPM . "\n";
			} elseif ($CompleteV > 0 && $CPV > 0) {
				$Revenue = $CompleteV * $CPV;
				echo "CPV: $CompleteV * $CPV = $Revenue \n";
			} elseif ($Clicks > 0 && $CPC > 0) {
				$Revenue = $Clicks * $CPC;
				echo "CPC: $Clicks * $CPC = $Revenue \n";
			} elseif ($VImpressions > 0 && $vCPM > 0) {
				$Revenue = $VImpressions * $vCPM / 1000;
				echo "vCPM: $VImpressions * $vCPM / 1000 = $Revenue \n";
			} else {
				$Revenue = $Row['Revenue'] * $PercCh;
			}
			
			$Rebate = $RebatePer * $Revenue / 100;
			
			
			$sql = "UPDATE reports SET  Revenue = '$Revenue', Rebate = $Rebate WHERE id = $idRow LIMIT 1";
			//echo $Impressions . ": " . $sql . "\n";
			
			//
			echo $sql . "\n";
			
			$db->query($sql);
		}
	}