<?php	
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common5.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$mem_var = new Memcached('reps');
	$mem_var->addServer("localhost", 11211);
	
	if(!isset($_POST['uuid']) || !isset($_POST['env'])){
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denied';
		exit(0);
	}
	
	if($_POST['env'] == 'prod'){
		/*
		$dbuser2 = "root";
		$dbpass2 = "ViDo0-PROD_2020";
		$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
		$dbname2 = "vidoomy";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		*/
		$dbuser2 = "root";
		$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
		$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
		$dbname2 = "vidoomy";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		
	}else{
		/*
		$dbuser2 = "root";
		$dbpass2 = "vidooDev-Pass_2020";
		$dbhost2 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
		$dbname2 = "vidoomy";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		*/
		$dbuser2 = "root";
		$dbpass2 = "N6kdTJ66kFjNHByUU9tJW5V";
		$dbhost2 = "vidoomy-integration.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
		$dbname2 = "staging";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		
	}
	
	$UUID = mysqli_real_escape_string($db2->link, $_POST['uuid']);
	
	$sql = "SELECT report_key.*, user.show_only_own_stats FROM report_key INNER JOIN user ON user.id = report_key.user_id WHERE report_key.unique_id = '$UUID' LIMIT 1";//AND report_key.status = 0
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		$Repo = $db2->fetch_array($query);
		$RepId = $Repo['id'];
		$UserId = $Repo['user_id'];
		$SOOS = $Repo['show_only_own_stats'];

		$sql = "UPDATE report_key SET status = 1 WHERE id = '$RepId' LIMIT 1";
		//$db2->query($sql);
	}else{
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denied';
		exit(0);
	}
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
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
	$StartMonth = '';
	$EndMonth = '';
	$ReportsTable = '';
	$UnionTables = array();
	$Overall = false;
	$UsedInnerJ = array();
	$CacheArrayT = array();
	$CacheArray = array();
	$ForceHourTable = false;
	
	if(isset($_POST['PDate'])){
		$Dates = $_POST['PDate'];
		if(is_array($Dates)){
			if(count($Dates) == 2){
				$arDa1 = explode(' ', $Dates[0]);
				$DateFrom = DateTime::createFromFormat('d/m/Y', $arDa1[0]);
				$DFrom = $DateFrom->format('Y-m-d 00:00:00');
				$StartMonth = $DateFrom->format('Ym');
				$StartHour = intval($arDa1[1]);
				
				$arDa2 = explode(' ', $Dates[1]);
				$DateTo = DateTime::createFromFormat('d/m/Y', $arDa2[0]);
				$DTo = $DateTo->format('Y-m-d 23:59:59');
				$EndMonth = $DateTo->format('Ym');
				$EndHour = intval($arDa2[1]);
				
				$DatesOK = true;
				
				if($StartHour > 0 || $EndHour < 23){
					$ForceHourTable = true;
					$AddHourRange = " AND {ReportsTable}.Hour >= $StartHour AND {ReportsTable}.Hour <= $EndHour ";
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
	}
	if($RepType != 'overall' || count($Dimensions) == 0){
		$IncludeTime = true;
	}
	
	$CSVResponse = false;
	if(isset($_POST['csv'])){
		if($_POST['csv'] == 1){
			$CSVResponse = true;
		}
	}
	//$CSVResponse = true;
	
	//print_r($UnionTables);
	//exit(0);
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
	
	//print_r($_POST);
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
		$Devices = $_POST['Devices'];
		if(isset($Devices)){
			if(is_array($Devices)){
				if(count($Devices) > 0){
					$DevList['DE'] = 1;
					$DevList['MW'] = 2;
					$DevList['MA'] = 3;
					$DevList['TV'] = 4;
					
					$EnvFilter = " AND (";
					$OrE = "";
					foreach($Devices as $Device){
						if(array_key_exists($Device, $DevList)){
							$idDev = $DevList[$Device];
							if($idDev > 0){
								$EnvFilter .= $OrE . "supplytag.PlatformType = $idDev";
								$OrE = " OR ";
							}
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
		$SQLInnerJoinsTotals = "";
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
				$SQLDimensionsOverall .= $C . 'R.' . $DimensionsSQL[$DimensionName]['GroupBy'] . " AS " . $DimensionsSQL[$DimensionName]['GroupBy'];
				
				if(count($DimensionsSQL[$DimensionName]['InnerJoin']) > 0){
					foreach($DimensionsSQL[$DimensionName]['InnerJoin'] as $JoinTable => $JoinSQL){
						if(!in_array($JoinTable, $UsedInnerJ)) {
							$SQLInnerJoins .= $JoinSQL;
							$UsedInnerJ[] = $JoinTable;
						}
					}
				}
				
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
				
				$No++;
			}
		}
		//print_r($Dimensions);
		//exit(0);
		
		$ThereAreFilters = false;
		$SQLWhere = "";
		foreach($ToFilter as $KInclude => $KFilterVal){
			foreach($KFilterVal as $KFilter => $FilterVals){
				if(!in_array($KFilter, $Dimensions) && $KFilter != 'hour-range'){
					$SQLDimensions .= $C . $DimensionsSQL[$KFilter]['Name'];

					if(count($DimensionsSQL[$KFilter]['InnerJoin']) > 0){
						foreach($DimensionsSQL[$KFilter]['InnerJoin'] as $JoinTable => $JoinSQL){
							if(!in_array($JoinTable, $UsedInnerJ)) {
								$SQLInnerJoins .= $JoinSQL;
								$UsedInnerJ[] = $JoinTable;
							}
						}
					}
					//$SQLInnerJoins .= $DimensionsSQL[$KFilter]['InnerJoin'];
				}
				
				$ThereAreFilters = true;
				$KeySearch = $DimensionsSQL[$KFilter]['SearchName'];
				$SQLWhere .= " AND (";
				
				$And = ""; 
				$Or = "";
				if($KInclude == 'exclude'){
					foreach($FilterVals as $FVal){
						if($KFilter != 'publisher_manager' && $KFilter != 'publisher_manager_head' && $KFilter != 'country' && $KFilter != 'hour-range'){
							$arFv = explode(' ', $FVal);
							$FVal = str_replace('*', '%', $arFv[0]);
							$SQLWhere .= $And . $KeySearch . " NOT LIKE '$FVal'";
						}elseif($KFilter == 'hour-range'){
							$arFv = explode('-', $FVal);
							$FilterHFrom = $arFv[0];
							$FilterHTo = $arFv[1];
							$SQLWhere .= $Or . " Hour NOT BETWEEN '$FilterHFrom' AND '$FilterHTo'";
							$ForceHourTable = true;
						}else{
							$SQLWhere .= $And . $KeySearch . " != '$FVal'";
						}
						$And = " AND "; 
					}
				}else{
					foreach($FilterVals as $FVal){
						if($KFilter != 'publisher_manager' && $KFilter != 'publisher_manager_head' && $KFilter != 'country' && $KFilter != 'hour-range'){
							$arFv = explode(' ', $FVal);
							$FVal = str_replace('*', '%', $arFv[0]);
							$SQLWhere .= $Or . $KeySearch . " LIKE '$FVal'";
						}elseif($KFilter == 'hour-range'){
							$arFv = explode('-', $FVal);
							$FilterHFrom = $arFv[0];
							$FilterHTo = $arFv[1];
							$SQLWhere .= $Or . " Hour BETWEEN '$FilterHFrom' AND '$FilterHTo'";
							$ForceHourTable = true;
						}else{
							$SQLWhere .= $Or . $KeySearch . " = '$FVal'";
						}
						$Or = " OR "; 
					}
					
				}
				$SQLWhere .= ") ";
			}
		}
		
		//DEFINE LA TABLA DE DONDE BUSCAR LOS DATOS
		if($RepType != 'hourly' && $ForceHourTable !== true){
			$BaseTable = 'reports_resume';
		}else{
			$BaseTable = 'reports';
		}
		
		if($RepType == 'overall'){
			$Overall = true;
		}
		
		$NewTable = $BaseTable . $StartMonth;
		if(checkTableExists($NewTable)){
			$UnionTables[] = $NewTable;
		}
		if($StartMonth != $EndMonth){
			$start = $DateFrom->modify('first day of this month');
			$end = $DateTo->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period = new DatePeriod($start, $interval, $end);
			
			foreach ($period as $dt) {
				$NewTable = $BaseTable . $dt->format("Ym");
				if(!in_array($NewTable, $UnionTables)){
					if(checkTableExists($NewTable)){
						$UnionTables[] = $NewTable;
					}
			    }
			}
		}
		
		
		$PubManFilter = "";
		if($SOOS != 0){
			foreach($DimensionsSQL['publisher_manager']['InnerJoin'] as $JoinTable => $JoinSQL){
				if(!in_array($JoinTable, $UsedInnerJ)) {
					$SQLInnerJoins .= $JoinSQL;
					$UsedInnerJ[] = $JoinTable;
				}
				$SQLInnerJoinsTotals .= $JoinSQL;
			}
			
			$PubManFilter = " AND acc_managers.id = '$UserId' ";
			
		}
		
		$SQLMetrics = "";
		$Bases = array();
		$SQLBases = "";
		foreach($Metrics as $MetricName){
			$MetricName = trim($MetricName);
			if($CSVResponse){
				$SQLMetrics .= $MetricsSQL[$MetricName]['SQLCSV'];
			}else{
				$SQLMetrics .= $MetricsSQL[$MetricName]['SQL'];
			}
			$Base = $MetricsSQL[$MetricName]['Base'];
			if(count($Base) > 0){
				foreach($Base as $B){
					if(!in_array($B, $Bases)){
						$Bases[] = $B;
						$SQLBases .= ", {ReportsTable}." . $B;
					}
				}
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
		
		//SI HAY FILTROS, CALCULA LOS TOTALES SIN FILRTOS
		$Nd = 0;
		if($ThereAreFilters){

			$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM {ReportsTable} INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoinsTotals WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $AddHourRange $PubManFilter ";
			if(count($UnionTables) > 1){
				$Union = "";
				$SQLQueryT = "";
				if($Overall || 1==1){
					$SQLMetricsT = str_replace('{ReportsTable}', 'R', $SQLMetrics);
					$SQLQueryT = "SELECT '' $SQLMetricsT FROM (";
					foreach($UnionTables as $Table){
						$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
						$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoinsTotals);
						$SQLQueryT .= "$Union (SELECT '' $SQLBasesTo FROM $Table INNER JOIN supplytag ON supplytag.id = $Table.idTag $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $AddHourRange $PubManFilter) ";
						$Union = "UNION ALL";
					}    
					$SQLQueryT .= ")  AS R ";
					
				}else{
					foreach($UnionTables as $Table){
						$SQLQueryT .= "$Union (" . str_replace('{ReportsTable}', $Table, $SQLSuperQueryT) . ") ";
						$Union = "UNION ALL";
					}
				}
			}else{
				$SQLQueryT = str_replace('{ReportsTable}', $UnionTables[0], $SQLSuperQueryT);
			}
			
			//echo $SQLQueryT;
			//exit(0);
			
			$Prefix = intval($mem_var->get('total_prefix'));
			$QueryKeyT = $Prefix . md5($SQLQueryT);
			$CachedTotalsNF = $mem_var->get($QueryKeyT);
			
			if($CachedTotalsNF === false || 1 == 1){
				
				if($IncludeTime){
					$DataT[$Nd][] = "";
				}
				
				$SuperQueryT = $db->query($SQLQueryT);
				while($Da = $db->fetch_array($SuperQueryT)){
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
				
				if(array_key_exists($Nd, $DataT)){
					$CacheArrayT['DataNF'] = $DataT[$Nd];
				}
				
				$mem_var->set($QueryKeyT, $CacheArrayT, 30 * 60);
				$CachedTN = 0;
			}else{
				$CachedTN = 1;
				$DataT[$Nd] = $CachedTotalsNF['DataNF'];
			}
		}
		
		//CALCULA LOS TOTALES CON FILTROS
		$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM {ReportsTable} INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoins WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $AddHourRange $SQLWhere $EnvFilter $PubManFilter";
		
		if(count($UnionTables) > 1){
			$Union = "";
			$SQLQueryT = "";
			if($Overall || 1==1){
				$SQLMetricsT = str_replace('{ReportsTable}', 'R', $SQLMetrics);
				$SQLQueryT = "SELECT '' $SQLMetricsT FROM (";
				foreach($UnionTables as $Table){
					$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
					$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoins);
					$SQLQueryT .= "$Union (SELECT '' $SQLBasesTo FROM $Table INNER JOIN supplytag ON supplytag.id = $Table.idTag $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $AddHourRange $SQLWhere $EnvFilter $PubManFilter) ";
					$Union = "UNION ALL";
				}    
				$SQLQueryT .= ")  AS R ";
				
			}else{
				foreach($UnionTables as $Table){
					$SQLQueryT .= "$Union (" . str_replace('{ReportsTable}', $Table, $SQLSuperQueryT) . ") ";
					$Union = "UNION ALL";
				}
			}
		}else{
			$SQLQueryT = str_replace('{ReportsTable}', $UnionTables[0], $SQLSuperQueryT);
		}
		
		//echo $SQLQueryT;
		//exit(0);
		
		$Prefix = intval($mem_var->get('total_prefix'));
		$QueryKeyT = $Prefix . md5($SQLQueryT);
		$CachedTotals = $mem_var->get($QueryKeyT);
		
		if($CachedTotals === false || 1 == 1){
			
			if($IncludeTime){
				$DataT[$Nd][] = "";
			}
			
			$SuperQueryT = $db->query($SQLQueryT);
			while($Da = $db->fetch_array($SuperQueryT)){
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
			
			$CacheArrayT['Data'] = $DataT;
			
			$mem_var->set($QueryKeyT, $CacheArrayT, 30 * 60);
			$CachedT = 0;
		}else{
			$CachedT = 1;
			$DataT = $CachedTotals['Data'];
		}
		
		/*
		echo $SQLDimensions;
		echo "\n";
		echo $SQLMetrics;
		exit(0);
		*/
			
		$Nd = 0; 
		//CALCULA EL RESTO DE LA TABLA
		$SQLSuperQuery = "SELECT SQL_CALC_FOUND_ROWS $SQLDimensions $SQLMetrics FROM {ReportsTable} INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoins WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $AddHourRange $SQLWhere $EnvFilter $PubManFilter $SQLGroups";
		if(count($UnionTables) > 1){
			$Union = "";
			$SQLQuery = "";
			if($Overall){
				$SQLMetrics = str_replace('{ReportsTable}', 'R', $SQLMetrics);
				$SQLQuery = "SELECT SQL_CALC_FOUND_ROWS $SQLDimensionsOverall $SQLMetrics FROM (";
				foreach($UnionTables as $Table){
					$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
					$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoins);
					$SQLQuery .= "$Union (SELECT $SQLDimensions $SQLBasesTo FROM $Table INNER JOIN supplytag ON supplytag.id = $Table.idTag $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $AddHourRange $SQLWhere $EnvFilter $PubManFilter) ";
					$Union = "UNION ALL";
				}    
				$SQLQuery .= ")  AS R $SQLGroups";
				
			}else{
				foreach($UnionTables as $Table){
					if($Union != ""){
						$SQLSuperQuery = str_replace('SQL_CALC_FOUND_ROWS', '', $SQLSuperQuery);
					}
					$SQLQuery .= "$Union (" . str_replace('{ReportsTable}', $Table, $SQLSuperQuery) . ") ";
					$Union = "UNION ALL";
				}
			}
		}else{
			$SQLQuery = str_replace('{ReportsTable}', $UnionTables[0], $SQLSuperQuery);
		}
		
		if($OrderParam != ""){
			$SQLQuery .= " ORDER BY $OrderParam";
			if($Start !== false || $Length !== false){
				$SQLQuery .= " LIMIT $Start, $Length";
			}
		}
		
		$QueryKey = md5($SQLQuery);
		$CachedReport = $mem_var->get('report_prefix_' . $QueryKey);
		
		if($CachedReport === false || 1 == 1){
			$Cached = 0;
			$SuperQuery = $db->query($SQLQuery);
			
			$sqlCount = 'SELECT FOUND_ROWS();';
			$CntTotal = $db->getOne($sqlCount);
			$TDim = 0;
			while($Da = $db->fetch_array($SuperQuery)){
				if($IncludeTime){
					$Data[$Nd][] = $Da[$TimeName];
					$TDim++;
				}
				if($AddDimensions){
					foreach($Dimensions as $DimensionName){
						
						$DimensionValue = $Da[$DimensionsSQL[$DimensionName]['OrderVal']];
						if($DimensionValue != ''){
							if($DimensionName == 'environment'){
								if($DimensionValue == 1){
									$EnvName = 'Desktop';
								}elseif($DimensionValue == 2){
									$EnvName = 'Mobile';
								}else{
									$EnvName = 'Unknown';
								}
								$Data[$Nd][] = $EnvName;
							}elseif($DimensionName == 'supply_source' || $DimensionName == 'supply_partner'){
								$arSS = explode('--',$DimensionValue);
								$Data[$Nd][] = array(
									'name' => $arSS[0],
									'id' => $arSS[1]
								);
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
						if(($MetricSName == 'CTR' || $MetricSName == 'VTR' || $MetricSName == 'ProfitMargin') && floatval(str_replace('%','',$Da[$MetricSName])) == 0 && $CSVResponse === true){
							$Data[$Nd][] = '0,00%';
						}elseif(($MetricSName == 'CPM' || $MetricSName == 'Profit' || $MetricSName == 'Revenue' || $MetricSName == 'Coste') && floatval(str_replace('$','',$Da[$MetricSName])) == 0 && $CSVResponse === true){
							$Data[$Nd][] = '$0,00';
						}else{
							/*if(floatval($Da[$MetricSName]) == 0){
								$Data[$Nd][] = '0';
							}else{*/
								$Data[$Nd][] = $Da[$MetricSName];
							//}
						}
					}
				}
				$Nd++;
			}
			
			$CacheArray['Data'] = $Data;
			$CacheArray['Total'] = $CntTotal;
			
			$mem_var->set('report_prefix_' . $QueryKey, $CacheArray, 30 * 60);
		}else{
			$Cached = 1;
			$Data = $CachedReport['Data'];
			$CntTotal = $CachedReport['Total'];
		}
		//$Data[0]
	}else{
		header('HTTP/1.0 404');
		exit(0);
	}
	mysqli_close ( $db->link );
	
	//echo $SQLQueryT;
	//print_r($Data);
	//exit();
	
	
	if($CSVResponse){	
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
				if(is_array($V)){
					$VData = $V['name'];
				}else{
					$VData = $V;
				}
				echo $Coma . '"' . $VData . '"';
				$Coma = ',';
			}
			echo "\n";
		}
		
		exit(0);
		
	}
?>{
  "draw": <?php if(isset($_POST['draw'])){ echo intval($_POST['draw']); } else { echo "0"; } ?>,
  "recordsTotal": <?php echo $CntTotal; ?>,
  "recordsFiltered": <?php echo $CntTotal; ?>,
  "data": <?php echo safe_json_encode($Data); ?>,
  "dataT": <?php echo json_encode($DataT); ?>
}