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
    
    
   	$Date = '2021-09-27';
	$Hour = date('H', time() - (3600 * 1));
	$Hour1 = '00';
	$Hour2 = '23';
    
    $DateHourFrom = '2022-01-01 00';
    $DateHourTo = '2022-02-28 23';
    
    //$Hour = '6';
    //$Hour = 23;
    
    //echo $Date . ' - ' . $Hour;
    //exit(0);
    
    $TotalRev = 0;
    $TotalImp = 0;
    $TotalImpX = 0;
    

    $DemandTags = array();	
    $ActiveDeals = array();
    $CampaingData = array();

   	$sql = "SELECT * FROM campaign WHERE id = 2090";
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
            
            $CheckV = false;
            if($DealID == 'VDMY_419653868' || $DealID == 'VDMY_426431488' || $DealID == 'VDMY_444459379'){
                $CheckV = true;
            }	

            $RebatePercent = $Camp['rebate'];
            $salesManagerId = $Camp['sales_manager_id'];

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
    
            $Query = "SELECT __time, Country, SUM(sum_BidRequests) AS Requests, SUM(sum_BidResponses) AS Responses, SUM(sum_FirstQuartile) AS FirstQuartile, SUM(sum_Midpoint) AS Midpoint, SUM(sum_ThirdQuartile) AS ThirdQuartile, SUM(sum_Complete) AS Complete, SUM(sum_Impressions) AS Impressions, SUM(sum_Vimpression) AS VImpressions, SUM(sum_Clicks) AS Clicks, SUM(sum_Money) AS Money FROM prd_rtb_event_production_1	WHERE __time >= '$DateHourFrom:00:00' AND  __time <= '$DateHourTo:00:00' AND Deal = '$DealID' GROUP BY __time, Country ORDER BY 3 DESC";
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
                        if($CheckV){
                            $VImpressions = intval($Impressions * 0.863);
                            if($Hour == 0){
                                $Per = 72;
                            }elseif($Hour <= 10){
                                $Per = 75 - ($Hour / 2);
                            }elseif($Hour <= 20){
                                $Per = 70 + ($Hour / 2);
                            }else{
                                $Per = 73;
                            }
                            
                            
                            $CompleteV = $Impressions * ($Per / 100);
                            
                            $Complete25 = calcPercents(25, $Impressions, $CompleteV);
                            $Complete50 = calcPercents(50, $Impressions, $CompleteV);
                            $Complete75 = calcPercents(75, $Impressions, $CompleteV);
                            
                        }
                        $Clicks = $res[10];
                        //$Money = $result[1][10];
                        
                        
                        //exit(0);
                        
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
                            (SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour, idCreativity, idPurchaseOrder, budgetConsumed, rebatePercentage, idSalesManager) 
                            VALUES (7, $idCamp, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour', $idCamp, $idCamp, $Revenue, $RebatePercent, $salesManagerId)";
                            $db->query($sql);
                            echo $sql . "\n";	
                        }else{
        
                            $sql = "UPDATE reports SET 
                                Requests = '$Requests', Bids = '$Bids', Impressions = '$Impressions', Revenue = '$Revenue',
                                VImpressions = '$VImpressions', Clicks = '$Clicks', CompleteV = '$CompleteV', 
                                Complete25 = '$Complete25', Complete50 = '$Complete50', Complete75 = '$Complete75', Rebate = '$Rebate'
                                WHERE id = $idStat LIMIT 1";
                            //exit(0);
                            $db->query($sql);
                            echo $sql . "\n";	
                        }
                    }
                }
            }
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    