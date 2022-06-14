<?php	
	@session_start();
	define('CONST',1);

    if (file_exists('/var/www/html/login/config.php')) {
        require('/var/www/html/login/config.php');
    } else {
        require('../config_local.php');
    }
//	exit(0);
	require('../db.php');
	require('libs/common_adserver.php');
	$mem_var = new Memcached('reps');
	$mem_var->addServer("localhost", 11211);
	//print_r($_POST);
	if(!isset($_POST['uuid']) || !isset($_POST['env'])){
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denied';
		exit(0);
	}

	if ($_POST['env'] == 'dev' || (array_key_exists("APP_ENV", $_ENV) && $_ENV["APP_ENV"] == 'local')) {
		$db2 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} elseif ($_POST['env'] == 'dev1') {
		$db2 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} elseif ($_POST['env'] == 'dev2') {
		$db2 = new SQL($pubDev02['host'], $pubDev02['db'], $pubDev02['user'], $pubDev02['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} elseif ($_POST['env'] == 'dev3') {
		$db2 = new SQL($pubDev03['host'], $pubDev03['db'], $pubDev03['user'], $pubDev03['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} elseif ($_POST['env'] == 'pre' || $_POST['env'] == 'staging') {
		$db2 = new SQL($pubStaging['host'], $pubStaging['db'], $pubStaging['user'], $pubStaging['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} elseif ($_POST['env'] == 'integration') {
		$db2 = new SQL($pubIntegration['host'], $pubIntegration['db'], $pubIntegration['user'], $pubIntegration['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} elseif ($_POST['env'] == 'prod' || $_POST['env'] == 'local' || $_POST['env'] == 'pro') {
		$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	} else {
		header($_SERVER["SERVER_PROTOCOL"] . ' Wrong ENV', true, 500);
		exit(0);
	}

	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	$UUID = mysqli_real_escape_string($db2->link, $_POST['uuid']);
	$sql = "SELECT report_key.*, user.roles AS URoles FROM report_key INNER JOIN user ON user.id = report_key.user_id WHERE report_key.unique_id = '$UUID' LIMIT 1";//AND report_key.status = 0
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		$Repo = $db2->fetch_array($query);
		$RepId = $Repo['id'];
		$UserId = $Repo['user_id'];
		//$SOOS = $Repo['show_only_own_stats'];
		$RolesJSON = unserialize($Repo['URoles']);
		$sql = "SELECT Name FROM user WHERE id = '$UserId' LIMIT 1";
		$UserName = $db->getOne($sql);
		$sql = "UPDATE report_key SET status = 1 WHERE id = '$RepId' LIMIT 1";
		$db2->query($sql);
	}else{
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denied';
		exit(0);
	}
	
	$AdvRep = false;
	if(in_array('ROLE_ADMIN', $RolesJSON)){

	}else{
		exit(0);
	}
		
	
	
	$Data = array();
	$DatesOK = false;
	$MetricsOK = false;
	$DimensionsOK = false;
	$TypeOK = false;
	$IncludeTime = false;
	$AddDimensions = false;
	$TimeName = "";
	$ToFilter = array();
	$Overall = false;
	$ThereAreFilters = false;
	$arrayCamps = array();
	$arrayCountries = array();
    $AddHourRange = '';

	if(isset($_POST['PDate'])){
		$Dates = $_POST['PDate'];
		if(is_array($Dates)){
			if(count($Dates) == 2){
                $DateFrom = DateTime::createFromFormat('d/m/Y H', $Dates[0]);
                $DFrom = $DateFrom->format('Y-m-d H:i:00');
                $StartMonth = $DateFrom->format('Ym');
                $StartHour = $DateFrom->format('H');

                $DateTo = DateTime::createFromFormat('d/m/Y H', $Dates[1]);
                $DTo = $DateTo->format('Y-m-d H:i:59');
                $EndMonth = $DateTo->format('Ym');
                $EndHour = $DateTo->format('H');

                $DatesOK = true;

                if ($StartHour > 0 || $EndHour < 23) {
                    $ForceHourTable = true;
                    $AddHourRange = " AND EXTRACT(HOUR FROM __time) >= $StartHour AND EXTRACT(HOUR FROM __time) <= $EndHour ";
                }
			}
		}
	}
	
	if(isset($_POST['Dimensions'])){
		$Dimensions = $_POST['Dimensions'];
		if(!is_array($Dimensions)){
			$Dimensions = array();
		}
	}else{
		$Dimensions = array();
	}
	$DimensionsOK = true;
	
	if(isset($_POST['ReportType'])){
		$TypeOK = true;
		$RepType = $_POST['ReportType'];
		
		if($RepType != 'overall' || count($Dimensions) == 0){
			$IncludeTime = true;
		}
		if($RepType == 'overall'){
			$Overall = true;
		}					
	}else{
		$TypeOK = true;
	}
	
	$CSVResponse = false;
	if(isset($_POST['csv'])){
		if($_POST['csv'] == 'true'){
			$CSVResponse = true;
		}
	}
	
	if($DatesOK && $TypeOK){
		if(isset($_POST['Metrics'])){
			$Metrics = $_POST['Metrics'];
			if(is_array($Metrics)){
				if(count($Metrics) > 0){
					$MetricsOK = true;
				}
			}
		}
	}
	
	if($DimensionsOK){
		if(isset($_POST['Filters'])){
			if(is_array($_POST['Filters'])){
				foreach($_POST['Filters'] as $Fi){				
					if(isset($Fi['label']) && isset($Fi['value']) && isset($Fi['include'])){
						if($Fi['label'] != '' && $Fi['value'] != ''){
							$ToFilter[$Fi['include']][$Fi['label']][] = $Fi['value'];
						}
					}
				}
			}
		}
		
		$EnvFilter = "";
		$DevList = array();
		$Devices = $_POST['Devices'];
		if(isset($Devices)){
			if(is_array($Devices)){
				if(count($Devices) > 0 && count($Devices) < 4){
					$DevList['DE'] = 'Desktop';
					$DevList['MW'] = 'Mobile';
					$DevList['MA'] = 'InApp';
					$DevList['TV'] = 'TV';
					
					$EnvFilter = " AND (";
					$OrE = "";
					foreach($Devices as $Device){
						if(array_key_exists($Device, $DevList)){
							$idDev = $DevList[$Device];
							$EnvFilter .= $OrE . "Device = '$idDev'";
							$OrE = " OR ";
						}
					}
					$EnvFilter .= ")";
					if($OrE == ""){
						$EnvFilter = "";
					}
				}
			}
		}
		
		$OrderC = array();
		if(isset($_POST['order'])){
			if(is_array($_POST['order'])){
				foreach($_POST['order'] as $O){
					$OrderC[$O['column']] = $O['dir'];
				}
			}
		}
		
		if(isset($_POST['length'])){
			$Length = intval($_POST['length']);
		}else{
			$Length = false;
		}
		
		if(isset($_POST['start'])){
			$Start = intval($_POST['start']);
		}else{
			$Start = false;
		}
		
		$No = 0;
		$OrderName = "";
		$OrderParam = "";
		$OrderComa = "";
		$CGroup = "GROUP BY ";
		$C = "";
		$SQLDimensions = "";
		$SQLDimensionsOverall = "";
		$SQLInnerJoins = "";
		$SQLGroups = "";
		
		if($IncludeTime){
			
			$SQLDimensions .= $TimesSQL[$RepType]['Name'];
			$SQLDimensionsOverall .= $TimesSQL[$RepType]['Name'];
			if($TimesSQL[$RepType]['GroupBy'] !== false){
				$SQLGroups = "GROUP BY ";
				$SQLGroups .= $TimesSQL[$RepType]['GroupBy'];
				$CGroup = ", ";
			}
			
			if(count($OrderC) > 0){
				if(array_key_exists($No, $OrderC)){
					$OrderName = $TimesSQL[$RepType]['OrderVal'];
					$OrderParam .= $OrderComa . $OrderName . " " . $OrderC[$No];
					$OrderComa = ", ";
				}
			}
			$TimeName = $TimesSQL[$RepType]['ShowName'];
			
			$No++;
			$C = ", ";
		}
		
		if(count($Dimensions) > 0){
			$AddDimensions = true;
			
			foreach($Dimensions as $DimensionName){

				$SQLDimensions .= $C . $DimensionsSQL[$DimensionName]['Name'];
				$SQLDimensionsOverall .= $C . $DimensionsSQL[$DimensionName]['GroupBy'] . " AS " . $DimensionsSQL[$DimensionName]['GroupBy'];					
				$SQLGroups .= $CGroup . $DimensionsSQL[$DimensionName]['GroupBy'];
				$C = ", ";
				$CGroup = ", ";
				
				if(count($OrderC) > 0){
					if(array_key_exists($No, $OrderC)){
						$OrderName = $DimensionsSQL[$DimensionName]['OrderVal'];
						$OrderParam .= $OrderComa . $OrderName . " " . $OrderC[$No];
						$OrderComa = ", ";
					}
				}
				
				if($DimensionName == 'campaign_name'){
					$sql = "SELECT name, deal_id FROM campaign WHERE status = 1";
					$query = $db2->query($sql);
					if($db2->num_rows($query) > 0){
						while($Camp = $db2->fetch_array($query)){
							if(strpos($Camp['deal_id'], '(')){
								$arDid = explode('(', $Camp['deal_id']);
								$Camp['deal_id'] = $arDid[0];
							}
							
							$arrayCamps[$Camp['deal_id']] = $Camp['name'];
						}
					}
				}
				
				if($DimensionName == 'country'){
					$sql = "SELECT nice_name, iso FROM country ";
					$query = $db2->query($sql);
					if($db2->num_rows($query) > 0){
						while($Camp = $db2->fetch_array($query)){
							$arrayCountries[$Camp['iso']] = $Camp['nice_name'];
						}
					}
				}
				
				$No++;
			}
		}
		//print_r($Dimensions);
		//exit(0);
		
		$SQLWhere = "";
		foreach($ToFilter as $KInclude => $KFilterVal){
			foreach($KFilterVal as $KFilter => $FilterVals){
				/*
				if(!in_array($KFilter, $Dimensions)){
					$SQLDimensions .= $C . $DimensionsSQL[$KFilter]['Name'];
				}
				*/
				$ThereAreFilters = true;
				$KeySearch = $DimensionsSQL[$KFilter]['SearchName'];
				$SQLWhere .= " AND (";
				
				$And = ""; 
				$Or = "";
				if($KInclude == 'exclude'){
					foreach($FilterVals as $FVal){
						if($KFilter != 'country' && $KFilter != 'dsp' && $KFilter != 'ssp' && $KFilter != 'type' && $KFilter != 'device' && $KFilter != 'hour-range'){
							if(strpos($FVal, ' (') !== false){
								$arFv = explode('(', $FVal);
								$FVal = str_replace('*', '%', trim($arFv[0]));
							}
							
							if($KFilter == 'campaign_name'){
								$FVal = mysqli_real_escape_string($db2->link, $FVal);
								$sql = "SELECT deal_id FROM campaign WHERE name LIKE '$FVal'";
								$query = $db2->query($sql);
								if($db2->num_rows($query) > 0){
									while($Camp = $db2->fetch_array($query)){
										$FVal = mysqli_real_escape_string($db2->link, $Camp['deal_id']);
										$SQLWhere .= $Or . $KeySearch . " NOT LIKE '$FVal'";
										$And = " AND ";
									}
								}
							}else {
								$FVal = mysqli_real_escape_string($db->link, $FVal);
								$SQLWhere .= $And . $KeySearch . " NOT LIKE '$FVal'";
							}
						}elseif ($KFilter == 'hour-range'){
                            $arFv = explode('-', $FVal);
                            $FilterHFrom = $arFv[0];
                            $FilterHTo = $arFv[1];
                            $SQLWhere .= $Or . " EXTRACT(HOUR FROM __time ) NOT BETWEEN '$FilterHFrom' AND '$FilterHTo'";
                            $ForceHourTable = true;
						}
						else{

							if($KFilter == 'type'){
								if($FVal == 'Deal'){
									$FVal = 1;
								}else{
									$FVal = 2;
								}
							}
							if($KFilter == 'device'){
								if($FVal == 'Desktop'){
									$FVal = 'DT';
								}elseif($FVal == 'Mobile'){
									$FVal = 'MW';
								}
							}
							if($KFilter == 'country'){
								$sql = "SELECT iso FROM country WHERE id = $FVal LIMIT 1";
								$FVal = $db2->getOne($sql);
							}
							$SQLWhere .= $And . $KeySearch . " != '$FVal'";
						}
						$And = " AND "; 
					}
				}else{
					foreach($FilterVals as $FVal){
						if($KFilter != 'country' && $KFilter != 'dsp' && $KFilter != 'ssp' && $KFilter != 'type' && $KFilter != 'device' && $KFilter != 'hour-range'){
							if(strpos($FVal, ' (') !== false){
								$arFv = explode('(', $FVal);
								$FVal = str_replace('*', '%', trim($arFv[0]));
							}
							
							if($KFilter == 'deal_id' && strpos($FVal, '%') === false){
								$FVal = $FVal . '%';
							}
							
							if($KFilter == 'campaign_name'){
								$FVal = mysqli_real_escape_string($db2->link, $FVal);
								$sql = "SELECT deal_id FROM campaign WHERE name LIKE '$FVal'";
								$query = $db2->query($sql);
								if($db2->num_rows($query) > 0){
									while($Camp = $db2->fetch_array($query)){
										$FVal = mysqli_real_escape_string($db2->link, $Camp['deal_id']);
										$SQLWhere .= $Or . $KeySearch . " LIKE '$FVal'";
										$Or = " OR "; 
									}
								}
							}else{
								$FVal = mysqli_real_escape_string($db->link, $FVal);
								$SQLWhere .= $Or . $KeySearch . " LIKE '$FVal'";
							}
						}
						elseif($KFilter == 'hour-range'){
                            $arFv = explode('-', $FVal);
                            $FilterHFrom = $arFv[0];
                            $FilterHTo = $arFv[1];
                            $SQLWhere .= $Or . " EXTRACT(HOUR FROM __time) BETWEEN '$FilterHFrom' AND '$FilterHTo'";
                            $ForceHourTable = true;
                        }
						else {
							if($KFilter == 'type'){
								if($FVal == 'Deal'){
									$FVal = 1;
								}else{
									$FVal = 2;
								}
							}
							if($KFilter == 'device'){
								if($FVal == 'Desktop'){
									$FVal = 'DT';
								}elseif($FVal == 'Mobile'){
									$FVal = 'MW';
								}
							}
							if($KFilter == 'country'){
								$sql = "SELECT iso FROM country WHERE id = $FVal LIMIT 1";
								$FVal = $db2->getOne($sql);
							}
							$SQLWhere .= $Or . $KeySearch . " = '$FVal'";
						}
						$Or = " OR "; 
					}
					
				}
				$SQLWhere .= ") ";
			}
		}

		$SQLMetrics = "";

		foreach($Metrics as $MetricName){
			$MetricName = trim($MetricName);
			if($CSVResponse){
				$SQLMetrics .= $MetricsSQL[$MetricName]['SQLCSV'];
			}else{
				$SQLMetrics .= $MetricsSQL[$MetricName]['SQL'];
			}
			
			if(count($OrderC) > 0){
				if(array_key_exists($No, $OrderC)){
					$OrderName = $MetricsSQL[$MetricName]['OrderVal'];
					$OrderParam .= $OrderComa . $OrderName . " " . $OrderC[$No];
					$OrderComa = ", ";
				}
			}
				
			$No++;
		}
		
		$DruidTable = 'production_enriched_event_demand_2';
		if(in_array('formatloads', $Metrics)){
			$DruidTable = 'production_enriched_event_supply';
		}
		
		//SI HAY FILTROS, CALCULA LOS TOTALES SIN FILRTOS
		$Nd = 0;
		if($ThereAreFilters){
			$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM $DruidTable WHERE __time BETWEEN TIMESTAMP '$DFrom' AND TIMESTAMP '$DTo' $AddHourRange";
			if($IncludeTime){
				$DataT[$Nd][] = "";
			}

			$Row = druidQuery($SQLSuperQueryT);
			$Keys = $Row[0];
			array_shift($Row);
							
			foreach($Row as $Da){
				$Da = array_combine($Keys, $Da);
				
				if($AddDimensions){
					foreach($Dimensions as $DimensionName){
						$DataT[$Nd][] = "";
					}
				}
				foreach($Metrics as $MetricName){
					$MetricName = trim($MetricName);
					
					if(is_array($Da)){
						if(array_key_exists($MetricsSQL[$MetricName]['Name'], $Da)){
						
							if($MetricsSQL[$MetricName]['NumberF']){
								//$DataT[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
								$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
							}else{
								$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
							}
							
						}else{
							break;
						}
					}else{
						break;
					}
				}
				$Nd++;
			}
		}
					
		//CALCULA LOS TOTALES CON FILTROS
		$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM $DruidTable WHERE __time BETWEEN TIMESTAMP '$DFrom' AND TIMESTAMP '$DTo' $AddHourRange $SQLWhere $EnvFilter  ";
		//error_log($SQLSuperQueryT);
		
		if($IncludeTime){
			$DataT[$Nd][] = "";
		}
		$Row = druidQuery($SQLSuperQueryT);
		//var_dump($Row);
		$Keys = $Row[0];
		array_shift($Row);
						
		foreach($Row as $Da){
			$Da = array_combine($Keys, $Da);
			
			if($AddDimensions){
				foreach($Dimensions as $DimensionName){
					$DataT[$Nd][] = "";
				}
			}
			foreach($Metrics as $MetricName){
				$MetricName = trim($MetricName);
				
				if(is_array($Da)){
					if(array_key_exists($MetricsSQL[$MetricName]['Name'], $Da)){
					
						if($MetricsSQL[$MetricName]['NumberF']){
							//$DataT[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
							$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
						}else{
							$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
						}
						
					}else{
						break;
					}
				}else{
					break;
				}
			}
			$Nd++;
		}
						
			
		$Nd = 0;
		//CALCULA EL RESTO DE LA TABLA
		$SQLQuery = "SELECT $SQLDimensions $SQLMetrics FROM $DruidTable WHERE __time BETWEEN TIMESTAMP '$DFrom' AND TIMESTAMP '$DTo' $AddHourRange $SQLWhere $EnvFilter $SQLGroups";
		if($OrderParam != ""){
			$SQLQuery .= " ORDER BY $OrderParam";
			if($Start !== false || $Length !== false){
				//$SQLQuery .= " LIMIT $Start, $Length";
			}
		}
		
		//echo $SQLQuery;
		//exit(0);
		
		$Row = druidQuery($SQLQuery);
		$Keys = $Row[0];
		array_shift($Row);
		$CntTotal = count($Row);
		$TDim = 0;
						
		foreach($Row as $Da){
			$Da = array_combine($Keys, $Da);

			if($IncludeTime){
				$Data[$Nd][] = $Da[$TimeName];
				$TDim++;
			}
			if($AddDimensions){
				foreach($Dimensions as $DimensionName){
					
					$DimensionValue = $Da[$DimensionsSQL[$DimensionName]['OrderVal']];
					if($DimensionValue != ''){
						if($DimensionName == 'campaign_name'){
							if(array_key_exists($DimensionValue, $arrayCamps)){
								$Data[$Nd][] = $arrayCamps[$DimensionValue];
							}else{
								$Data[$Nd][] = $DimensionValue;
							}
						}elseif($DimensionName == 'country'){
							if(array_key_exists($DimensionValue, $arrayCountries)){
								$Data[$Nd][] = $arrayCountries[$DimensionValue];
							}else{
								$Data[$Nd][] = $DimensionValue;
							}
						}elseif($DimensionName == 'device'){
							if($DimensionValue == 'DT'){
								$Data[$Nd][] = 'Desktop';
							}elseif($DimensionValue == 'MW'){
								$Data[$Nd][] = 'Mobile';
							}else{
								$Data[$Nd][] = $DimensionValue;
							}
						}elseif($DimensionName == 'sync' || $DimensionName == 'gdprcs' || $DimensionName == 'gdpr'){
							if(intval($DimensionValue) == 1){
								$Data[$Nd][] = 'Yes';
							}else{
								$Data[$Nd][] = 'No';
							}
						}else{
							$Data[$Nd][] = $DimensionValue;
						}
					}else{
						$Data[$Nd][] = 'Unknown';
					}
					$TDim++;
				}
			}
			foreach($Metrics as $MetricName){
				$MetricName = trim($MetricName);
				$MetricSName = $MetricsSQL[$MetricName]['Name'];
				if($MetricsSQL[$MetricName]['NumberF']){
					//$Data[$Nd][] = number_format($Da[$MetricSName], 0, '', ',');
					$Data[$Nd][] = $Da[$MetricSName];
				}else{
					if(($MetricSName == 'FIRST' || $MetricSName == 'MID' || $MetricSName == 'THIRD' || $MetricSName == 'Complete25' || $MetricSName == 'Complete50' || $MetricSName == 'Complete75') && $Da[$MetricSName] === NULL){
						$Data[$Nd][] = "-";
					}elseif(($MetricSName == 'CTR' || $MetricSName == 'VTR' || $MetricSName == 'FIRST' || $MetricSName == 'MID' || $MetricSName == 'THIRD' || $MetricSName == 'RebatePercent' || $MetricSName == 'ViewabilityPercent' || $MetricSName == 'MesuredPercent') && floatval(str_replace('%','',$Da[$MetricSName])) == 0 && $CSVResponse === true){
						$Data[$Nd][] = '0.00%';
					}elseif(($MetricSName == 'CPM' || $MetricSName == 'Rebate' || $MetricSName == 'Revenue' || $MetricSName == 'NetRevenue') && floatval(str_replace('$','',$Da[$MetricSName])) == 0 && $CSVResponse === true){
						$Data[$Nd][] = '$0.00';
					}else{
						$Data[$Nd][] = $Da[$MetricSName];
					}
				}
			}
			$Nd++;
		}
		
	}else{
		header('HTTP/1.0 404');
		exit(0);
	}

	if(isset($_POST['csv'])){
		if(intval($_POST['csv']) == 1){
			header("Content-type: text/csv");
			
			$ComaH = "";
			
			if($IncludeTime){
				echo "Time";
				$ComaH = ",";
			}
			
			if($AddDimensions){
				foreach($Dimensions as $DimensionName){
					echo $ComaH . $DimensionsSQL[$DimensionName]['HeadName'];
					$ComaH = ",";
				}
			}
			
			foreach($Metrics as $MetricName){
				echo $ComaH . $MetricsSQL[$MetricName]['HeadName'];
				$ComaH = ",";
			}
			
			echo "\n";
			
			foreach($Data as $Dcv){
				$Coma = '';
				foreach($Dcv as $V){
					echo $Coma . '"' . $V . '"';
					$Coma = ',';
				}
				echo "\n";
			}
			
			exit(0);
		}
	}
	
?>{
  "draw": <?php if(isset($_POST['draw'])){ echo intval($_POST['draw']); } else { echo "0"; } ?>,
  "recordsTotal": <?php echo $CntTotal; ?>,
  "recordsFiltered": <?php echo $CntTotal; ?>,
  "data": <?php echo safe_json_encode($Data); ?>,
  "dataT": <?php echo json_encode($DataT); ?>,
  "D": "<?php echo $SQLSuperQueryT; ?>"
}
