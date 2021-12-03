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

function csvToJson($fname) {
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    $key = fgetcsv($fp,"1024",",");
    $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
        $json[] = array_combine($key, $row);
    }
    fclose($fp);
    
    return json_encode($json);
}
	
	
	$Date = date('Y-m-d', time() - (3600 * 1));
	$Date = '2021-11-09';
	$Hour = date('H', time() - (3600 * 1));
	//$Hour = '6';
	$Hour = 23;
	
	$JsonReport = json_decode(csvToJson('Report_ssp-type_2021-11-09_2021-11-09.csv'));
	
//	var_dump($JsonReport);

	$RevenueRot = 0;
	$addImpsTot = 0;
	$addResponsesTot = 0;
	$addReqTot = 0;
	
	foreach($JsonReport as $DealData){
		
		
		if($DealData->Imps > 0){
			$DealID = $DealData->{'Deal ID'};
			$Responses = $DealData->{'Bid Responses'};
			$Requests = $DealData->{'Bid Requests'};
			
			$sql = "SELECT id FROM campaign WHERE deal_id LIKE '$DealID%' LIMIT 1";
			$idCamp = $db3->getOne($sql);
			
			$sql = "SELECT SUM(Impressions) AS Imp FROM reports WHERE idCampaing = $idCamp AND Date = '$Date'";
			$currentImp = $db->getOne($sql);
			
			$sql = "SELECT SUM(Requests) AS Requests FROM reports WHERE idCampaing = $idCamp AND Date = '$Date'";
			$currentReq = $db->getOne($sql);
			
			$sql = "SELECT SUM(Bids) AS Bids FROM reports WHERE idCampaing = $idCamp AND Date = '$Date'";
			$currentBids = $db->getOne($sql);
			
			if($currentImp < $DealData->Imps){
				$addRequests = $Requests - $currentReq;
				$addResponses = $Responses - $currentBids;
				$addImps = $DealData->Imps - $currentImp;
				
				if($addImps > 3){
				
					if($addResponses < $addImps){
						$addResponses = $addImps * 2;
					}
					
					if($addRequests < $addResponses){
						$addRequests = $addResponses * 2;
					}
					
					echo "$DealID - Requests: $addRequests  - Responses: $addResponses  - Imp: $addImps \n";
					//exit(0);
					
					
					$sql = "SELECT cpm FROM campaign WHERE id = $idCamp LIMIT 1";
					$CPM = floatval($db3->getOne($sql));
					
					$sql = "SELECT rebate FROM campaign WHERE id = $idCamp LIMIT 1";
					$RebatePercent = $db3->getOne($sql);
					
					$sql = "SELECT (SUM(CompleteV) / SUM(Impressions) ) FROM reports WHERE idCampaing = $idCamp AND Date = '$Date'";
					$VTR = $db->getOne($sql);
					if($VTR > 0.4){
					
						$sql = "SELECT (SUM(VImpressions) / SUM(Impressions) ) FROM reports WHERE idCampaing = $idCamp AND Date = '$Date'";
						$View = $db->getOne($sql);
						
						$sql = "SELECT (SUM(Clicks) / SUM(Impressions) ) FROM reports WHERE idCampaing = $idCamp AND Date = '$Date'";
						$CTR = $db->getOne($sql);
						
						$idCountry = 999;
						$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
						if($db3->getOne($sql) == 1){
							$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
							$idCountry = $db3->getOne($sql);
						}
						
						//$VTR = $CompletesNow / $currentImp * 100;
						
						for($I = 0; $I <= 8; $I++){
							
							if(rand(1,2)==1){
								$Requests = ceil($addRequests / 2);
								$Responses = ceil($addResponses / 2);
								$addImpsD4 = ceil($addImps / 2);
							}else{
								$Requests = floor($addRequests / 2);
								$Responses = floor($addResponses / 2);
								$addImpsD4 = floor($addImps / 2); 
							}
							
							$Completes = ceil($addImpsD4 * $VTR);
							//echo "$addImpsD4 > $Completes \n";
							$VImp = ceil($addImpsD4 * $View);
							$Clicks = ceil($addImpsD4 * $CTR);
							
							$Revenue = $CPM * $addImpsD4 / 1000;
							$Rebate = $RebatePercent * $Revenue / 100;
							
							$Complete25 = calcPercents(25 , $addImpsD4, $Completes);
							$Complete50 = calcPercents(50 , $addImpsD4, $Completes);
							$Complete75 = calcPercents(75 , $addImpsD4, $Completes);
							
							$sql = "INSERT INTO reports
							(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, Rebate, Date, Hour) 
							VALUES (7, $idCamp, $idCountry, '$Requests', '$Responses', '$addImpsD4', '$Revenue', '$VImp', '$Clicks', '$Completes', '$Complete25', '$Complete50', $Complete75, '$Rebate', '$Date', '$I')";
							echo $sql . "\n";
							//$db->query($sql);
							
							
							$addReqTot += $Requests;
							$addResponsesTot += $Responses;
							$addImpsTot += $addImpsD4;
							//$RevenueRot += $Revenue;
							
							
							//echo "$DealID $addImps $VTR $View \n";
							
							//print_r($DealData);
						}
					}else{
						
						echo "NO DATA\n";
						
					}
					
					//exit(0);
					
				}
					
			}else{
				echo "$DealID - NO \n";
			}
			
		}
		
		
		
	}
	echo "Totales: Requests: $addReqTot  - Responses: $addResponsesTot  - Imps: $addImpsTot \n";
	
	exit(0);
	
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCamp = $Camp['id'];
			//$Camp['deal_id'] = "VDMY_CC_10395(1050826-1050825)";
			
			if(strpos($Camp['deal_id'], '-') !== false && strpos($Camp['deal_id'], '(') !== false && strpos($Camp['deal_id'], ')') !== false){
				$arD = explode('(', $Camp['deal_id']);
				$DealID = $arD[0];
			}else{
				$DealID = $Camp['deal_id'];
			}
				

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
			
			/*
			$countryId = 999;
			$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
			if($db3->getOne($sql) == 1){
				$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
				$countryId = $db3->getOne($sql);
			}
				
			$CampaingData[$idCamp]['Country'] = $countryId;
			*/
				
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
			
			
			
			$ch = curl_init( 'http://vdmdruidadmin:U9%3DjPvAPuyH9EM%40%26@ec2-3-120-137-168.eu-central-1.compute.amazonaws.com:8888/druid/v2/sql' );
	
			$Query = "SELECT __time, Country, SUM(sum_BidRequests) AS Requests, SUM(sum_BidResponses) AS Responses, SUM(sum_FirstQuartile) AS FirstQuartile, SUM(sum_Midpoint) AS Midpoint, SUM(sum_ThirdQuartile) AS ThirdQuartile, SUM(sum_Complete) AS Complete, SUM(sum_Impressions) AS Impressions, SUM(sum_Vimpression) AS VImpressions, SUM(sum_Clicks) AS Clicks, SUM(sum_Money) AS Money FROM prd_rtb_event_production_1	WHERE __time >= '$Date $Hour:00:00' AND  __time <= '$Date $Hour:00:00' AND Deal = '$DealID' GROUP BY __time, Deal, Country ORDER BY 1 DESC";
			//echo $Query . "\n\n";
			
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
			
			//print_r($result);
			
			if(array_key_exists(1, $result)){
			
				$Time = $result[1][0];
				$Country = $result[1][1];
				$Requests = $result[1][2];
				$Bids = $result[1][3];
				$Complete25 = $result[1][4];
				$Complete50 = $result[1][5];
				$Complete75 = $result[1][6];
				$CompleteV = $result[1][7];
				$Impressions = $result[1][8];
				$VImpressions = $result[1][9];
				$Clicks = $result[1][10];
				//$Money = $result[1][10];
				
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
				
				$sql = "SELECT id FROM reports WHERE SSP = 7 AND idCampaing = $idCamp AND Date = '$Date' AND Hour = '$Hour' AND DemangTagId = '' LIMIT 1";
				$idStat = $db->getOne($sql);
				
				$sql = "SELECT id FROM country WHERE iso = '$Country' LIMIT 1";
				$idCountry = $db->getOne($sql);
				
				$CompleteVPerc = 0;
	
				
				if(intval($idStat) == 0){
					$sql = "INSERT INTO reports
					(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour, DemangTagId) 
					VALUES (7, $idCamp, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour', '')";
					$db->query($sql);
					//echo $sql . "\n";	
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	