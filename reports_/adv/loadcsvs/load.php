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
	
	
	
	$csvData = array_map('str_getcsv', file('uw.csv'));
	
	foreach($csvData as $Data){
		$Date = $Data[0];
		if($Date != 'Time'){
			
			$DateO = DateTime::createFromFormat('d/m/Y', $Date);
			$FormatDate = $DateO->format('Y-m-d');
			
			$Name = $Data[1];
			
			$sql = "SELECT id FROM campaign WHERE name LIKE '$Name'";
			$idCampaign = $db2->getOne($sql);
			if($idCampaign > 0){
				$sql = "SELECT country_id FROM campaign_country_new WHERE campaign_id = $idCampaign";
				$idCountry = $db->getOne($sql);
				
				$Requests = intval(str_replace('.', '', $Data[2]));
				$Impressions = intval(str_replace('.', '', $Data[3]));
				$Complete25 = intval(str_replace('.', '', $Data[4]));
				$Complete50 = intval(str_replace('.', '', $Data[5]));
				$Complete75 = intval(str_replace('.', '', $Data[6]));
				$CompleteV = intval(str_replace('.', '', $Data[7]));
				$VImpressions = intval(str_replace('.', '', $Data[8]));
				$Clicks = intval(str_replace('.', '', $Data[9]));
				
				$Revenue = str_replace(',', '.', str_replace('$', '', $Data[10]));
				$Rebate = str_replace(',', '.', str_replace('$', '', $Data[11]));
//				$Rebate = 0;
				
				
				if($Impressions >= $CompleteV){
					if($Impressions >= $VImpressions){
						if($Complete50 >= $Complete75){
							if($Complete25 >= $Complete50){	
								
							}else{
								echo "Hay más Complete25 que Complete50. $FormatDate";
								exit();
							}
						}else{
							echo "Hay más Complete50 que Complete75. $FormatDate $Complete75 > $Complete50";
							exit();
						}
					}else{
						echo "Hay más Impresiones Visibles que Impresiones. $FormatDate";
						exit();
					}
				}else{
					echo "Hay más Complete Views que impresiones. $FormatDate";
					exit();
				}
				
				
				$sql = "DELETE FROM reports WHERE Date = '$FormatDate' AND idCampaing = '$idCampaign'";
				echo $sql . "\n";
				$db->query($sql);
				
				$sql = "INSERT reports (idCampaing,idCountry,Requests,Impressions,Complete25,Complete50,Complete75,CompleteV,VImpressions,Clicks,Revenue,Rebate,Bids,SSP,Date,Hour) 
				VALUES ($idCampaign,$idCountry,'$Requests','$Impressions','$Complete25','$Complete50','$Complete75','$CompleteV','$VImpressions','$Clicks','$Revenue','$Rebate','0','4','$FormatDate',23)";
				
				echo $sql . "\n";
				$db->query($sql);
			}
			
		}
		
	}