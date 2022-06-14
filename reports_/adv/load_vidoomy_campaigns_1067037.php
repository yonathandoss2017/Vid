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
	
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
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
	
	
	$Date = date('Y-m-d', time() - (3600 * 1));
	//$Date = '2021-05-01';
	$Hour = date('H', time() - (3600 * 1));
	
	$DateHourFrom = date('Y-m-d H', time() - (3600 * 6));
	$DateHourTo = date('Y-m-d H', time() - (3600 * 1));
	
	//$Hour = '6';
	//$Hour = 23;
	
	//echo $Date . ' - ' . $Hour;
	//exit(0);
	
	$TotalRev = 0;
	$TotalImp = 0;
	$TotalImpX = 0;
	
	/* 
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	

	$sql = "SELECT * FROM campaign WHERE id = 6937";//
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCamp = $Camp['id'];

			$RebatePercent = $Camp['rebate'];

			if($Camp['cpm'] > 0){
				$CPM = $Camp['cpm'];
			}else{
				$CPM = 0;
			}
			if($Camp['cpv'] > 0){
				$CPV = $Camp['cpv'];
			}else{
				$CPV = 0;
			}
			$Type = $Camp['type'];
			$AgencyId = $Camp['agency_id'];
			
				
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
			
			
			
			$ch = curl_init( $druidUrl );
	
			$Query = "SELECT  __time, Country, SUM(sum_TagRequests) AS TagRequests, 0, SUM(sum_FirstQuartiles) AS FirstQuartile, SUM(sum_MidPoints) AS Midpoint, SUM(sum_ThirdQuartiles) AS ThirdQuartile, SUM(sum_VideoCompletes) AS Complete, SUM(sum_Impressions) AS Impressions, SUM(sum_AdViewableImpression) AS VImpressions, SUM(sum_ClickThrus) AS Clicks FROM production_enriched_event_demand_2 WHERE __time >= CURRENT_TIMESTAMP - INTERVAL '1' DAY AND DemandTag = 'FCH_SCJ_GeoIP:Latam_(Almapp&PHD)_CPM_USD:3.35_BRL:17.63_GeoIP:BR_vtr:70_va:80_SCJohnson_ExposisSaoPaulo_1.080.000_13Feb' GROUP BY __time, Country ORDER BY 1 DESC";
			//echo $Query . "\n\n";
			//exit(0);
			
			$context = new \stdClass();
			$context->sqlOuterLimit = 30000;
			
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
			
			//print_r($result);
			
			if(array_key_exists(1, $result)){
				foreach($result as $kres => $res){
					$Time = $res[0];
					
					if($kres >= 1){
						
						//print_r($res);
						$arT = explode('T',$Time);
						$arArT = explode(':', $arT[1]);
						$Hour = intval($arArT[0]);
						$Date = $arT[0];
				
						$Time = $res[0];
						$Country = $res[1];
						$Requests = $res[2];
						$Bids = $res[3];
						$Complete25 = $res[4];
						$Complete50 = $res[5];
						$Complete75 = $res[6];
						$CompleteV = $res[7];
						$Impressions = $res[8];
						$VImpressions = $res[9];
						$Clicks = $res[10];
						
						if($Impressions > 0 && $CPM > 0){
							$Revenue = $Impressions * $CPM / 1000;
						}elseif($CompleteV > 0 && $CPV > 0){
							$Revenue = $CompleteV * $CPV;
						}else{
							$Revenue = 0;
						}
						
						if($RebatePercent > 0 && $Revenue > 0){
							$Rebate = $Revenue * $RebatePercent / 100;
						}else{
							$Rebate = 0;
						}
						
						$sql = "SELECT id FROM country WHERE iso = '$Country' LIMIT 1";
						$idCountry = $db->getOne($sql);
						
						$sql = "SELECT id FROM reports WHERE SSP = 7 AND idCampaing = $idCamp AND idCountry = $idCountry AND Date = '$Date' AND Hour = '$Hour' LIMIT 1";
						$idStat = $db->getOne($sql);
						
						$CompleteVPerc = 0;
			
						if(intval($idStat) == 0){
							$sql = "INSERT INTO reports
							(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
							VALUES (7, $idCamp, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
							$db->query($sql);
							//echo $sql . "\n";	
						}else{
		
							$sql = "UPDATE reports SET 
								Requests = '$Requests', Bids = '$Bids', Impressions = '$Impressions', Revenue = '$Revenue',
								VImpressions = '$VImpressions', Clicks = '$Clicks', CompleteV = '$CompleteV', 
								Complete25 = '$Complete25', Complete50 = '$Complete50', Complete75 = '$Complete75', Rebate = '$Rebate'
								WHERE id = $idStat LIMIT 1";
							//exit(0);
							$db->query($sql);
							//echo $sql . "\n";	
						}
					}
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	