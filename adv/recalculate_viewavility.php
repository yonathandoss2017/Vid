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
	
	$Date = '2021-04-05';
	$idCampaing = 2285;
	$ViewableImpressions = 8808;
	
	//exit(0);
	/*
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	
	$DemandTags = array();	
	$ActiveDeals = array();
	$CampaingData = array();
	
	$sql = "SELECT * FROM campaign WHERE  status = 1 AND id = $idCampaing";//ssp_id = 4 AND
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

	$TotalImpressions = 0;	

	$sql = "SELECT * FROM reports WHERE idCampaing = $idCampaing AND Date = '$Date'" ;
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			
			$ImpressionsByRowId[$Row['id']] = $Row['Impressions'];
			
			$TotalImpressions += $Row['Impressions'];

		}
	}
	
	echo "Total Impressions: " .  $TotalImpressions . "\n";
	
	$TotalNewImpressions = 0;	
	foreach($ImpressionsByRowId as $idRow => $Imp){
		
		$NewView = intval($Imp / $TotalImpressions * $ViewableImpressions);
		
		$TotalNewImpressions += $NewView;
		
		$sql = "UPDATE reports SET VImpressions = $NewView WHERE id = $idRow LIMIT 1";
		echo $sql . "\n";
		$db->query($sql);
	}
	
	echo 'New VI: ' . $TotalNewImpressions;