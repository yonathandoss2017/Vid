<?php	
	exit(0);
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$mem_var = new Memcached('reps');
	$mem_var->addServer("localhost", 11211);
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	//if($_SESSION['Admin']!=1){
		if($_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8000' || $_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8001' || $_SERVER['HTTP_ORIGIN'] == 'http://localhost:8000' || $_SERVER['HTTP_ORIGIN'] == 'http://localhost:8001' || $_SERVER['HTTP_ORIGIN'] == 'https://finance.vidoomy.com' || $_SERVER['HTTP_ORIGIN'] == 'https://newlogin.vidoomy.com' || $_SERVER['HTTP_ORIGIN'] == 'https://dev.vidoomy.com'){
			//exit();
		}else{
			header('Location: https://login.vidoomy.com/admin/login.php');
			exit(0);
		}
	//}
	
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
	
	if(isset($_POST['PDate'])){
		$Dates = $_POST['PDate'];
		if(is_array($Dates)){
			if(count($Dates) == 2){
				$DateFrom = DateTime::createFromFormat('d/m/Y', $Dates[0]);
				$DFrom = $DateFrom->format('Y-m-d 00:00:00');
				$StartMonth = $DateFrom->format('Ym');
				$DateTo = DateTime::createFromFormat('d/m/Y', $Dates[1]);
				$DTo = $DateTo->format('Y-m-d 23:59:59');
				$EndMonth = $DateTo->format('Ym');
				$DatesOK = true;						
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
		
		if($RepType != 'hourly'){
			$BaseTable = 'reports_resume';
		}else{
			$BaseTable = 'reports';
		}
		if($RepType != 'overall' || count($Dimensions) == 0){
			$IncludeTime = true;
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
		
	}
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
		
		if(isset($_POST['order'])){
			if(is_array($_POST['order'])){
				foreach($_POST['order'] as $O){
					$OrderC = $O['column'];
					$OrderD = $O['dir'];
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
		$CGroup = "GROUP BY ";
		$C = "";
		$SQLDimensions = "";
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
			if($No == $OrderC){
				$OrderName = $TimesSQL[$RepType]['OrderVal'];
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
				if($No == $OrderC){
					$OrderName = $DimensionsSQL[$DimensionName]['OrderVal'];
				}
				$No++;
			}
		}
		//print_r($Dimensions);
		//exit(0);

		foreach($ToFilter as $KInclude => $KFilterVal){
			foreach($KFilterVal as $KFilter => $FilterVals){
				if(!in_array($KFilter, $Dimensions)){
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
				$KeySearch = $DimensionsSQL[$KFilter]['SearchName'];
				$SQLWhere .= " AND (";
				
				$Or = ""; 
				if($KInclude == 'exclude'){
					foreach($FilterVals as $FVal){
						if($KFilter != 'publisher_manager' && $KFilter != 'country'){
							$arFv = explode(' ', $FVal);
							$FVal = str_replace('*', '%', $arFv[0]);
							$SQLWhere .= $Or . $KeySearch . " NOT LIKE '$FVal'";
						}else{
							$SQLWhere .= $Or . $KeySearch . " NOT LIKE '%$FVal%'";
						}
						$Or = " OR "; 
					}
				}else{
					foreach($FilterVals as $FVal){
						if($KFilter != 'publisher_manager' && $KFilter != 'country'){
							$arFv = explode(' ', $FVal);
							$FVal = str_replace('*', '%', $arFv[0]);
							$SQLWhere .= $Or . $KeySearch . " LIKE '$FVal'";
						}else{
							$SQLWhere .= $Or . $KeySearch . " LIKE '%$FVal%'";
						}
						$Or = " OR "; 
					}
					
				}
				$SQLWhere .= ") ";
			}
		}
		
		$SQLMetrics = "";
		$Bases = array();
		$SQLBases = "";
		foreach($Metrics as $MetricName){
			$MetricName = trim($MetricName);
			$SQLMetrics .= $MetricsSQL[$MetricName]['SQL'];
			$Base = $MetricsSQL[$MetricName]['Base'];
			if(count($Base) > 0){
				foreach($Base as $B){
					if(!in_array($B, $Bases)){
						$Bases[] = $B;
						$SQLBases .= ", {ReportsTable}." . $B;
					}
				}
			}
			if($No == $OrderC){
				$OrderName = $MetricsSQL[$MetricName]['OrderVal'];
			}
			$No++;
		}
		
		/*
		if($_SESSION['Type'] == 1){
			$idAccM = $_SESSION['idAdmin'];
			$SQLWhere .= " AND users2.AccM = '$idAccM' ";
			if(!in_array('Supply Partner', $Dimensions) && !in_array('Supply Partner', $ToFilter)){
				$SQLInnerJoins .= " INNER JOIN users2 ON users2.id = {ReportsTable}.idUser ";
			}
		}
		*/
		
		if($OrderName != ""){
			$OrderParam = $OrderName . ' ' . strtoupper($OrderD);
		}
		
		//$SQLSuperQueryCount = "SELECT $SQLDimensions FROM $ReportsTable $SQLInnerJoins WHERE $ReportsTable.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere GROUP BY $SQLGroups";
		//$SQLSuperQueryCount = str_replace('{ReportsTable}', $ReportsTable, $SQLSuperQueryCount);
		//$SuperQueryCount = $db->query($SQLSuperQueryCount);
		//$CntTotal = $db->num_rows($SuperQueryCount);
					
		$Nd = 0;
		//CALCULA LOS TOTALES
		$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM {ReportsTable} INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoins WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere $EnvFilter ";
		
		if(count($UnionTables) > 1){
			$Union = "";
			$SQLQueryT = "";
			if($Overall){
				$SQLMetrics = str_replace('{ReportsTable}', 'R', $SQLMetrics);
				$SQLQueryT = "SELECT $SQLMetrics FROM (";
				foreach($UnionTables as $Table){
					$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
					$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoins);
					$SQLQueryT .= "$Union (SELECT $SQLBasesTo FROM $Table INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere $EnvFilter) ";
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
		
		if($CachedTotals === false){
			
			if($IncludeTime){
				$DataT[$Nd][] = "";
			}
			
			
			$SuperQuery = $db->query($SQLQueryT);
			while($Da = $db->fetch_array($SuperQuery)){
				if($AddDimensions){
					foreach($Dimensions as $DimensionName){
						$DataT[$Nd][] = "";
					}
				}
				foreach($Metrics as $MetricName){
					$MetricName = trim($MetricName);
					if($MetricsSQL[$MetricName]['NumberF']){
						$DataT[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
					}else{
						$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
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
			
		$Nd = 0;
		//CALCULA EL RESTO DE LA TABLA
		$SQLSuperQuery = "SELECT SQL_CALC_FOUND_ROWS $SQLDimensions $SQLMetrics FROM {ReportsTable} INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoins WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere $EnvFilter $SQLGroups";
		if(count($UnionTables) > 1){
			$Union = "";
			$SQLQuery = "";
			if($Overall){
				$SQLMetrics = str_replace('{ReportsTable}', 'R', $SQLMetrics);
				$SQLQuery = "SELECT SQL_CALC_FOUND_ROWS $SQLDimensionsOverall $SQLMetrics FROM (";
				foreach($UnionTables as $Table){
					$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
					$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoins);
					$SQLQuery .= "$Union (SELECT $SQLDimensions $SQLBasesTo FROM $Table INNER JOIN supplytag ON supplytag.id = {ReportsTable}.idTag $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere $EnvFilter) ";
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
		
		$SQLQuery .= " ORDER BY $OrderParam";
		if($Start !== false || $Length !== false){
			$SQLQuery .= " LIMIT $Start, $Length";
		}
		
		$QueryKey = md5($SQLQuery);
		$CachedReport = $mem_var->get('report_prefix_' . $QueryKey);
		
		if($CachedReport === false){
			$Cached = 0;
			$SuperQuery = $db->query($SQLQuery);
			
			$sqlCount = 'SELECT FOUND_ROWS();';
			$CntTotal = $db->getOne($sqlCount);
			
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
							}elseif($DimensionName == 'supply_source'){
								$arSS = explode('--',$DimensionValue);
								$Data[$Nd][] = $arSS[0];
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
						$Data[$Nd][] = number_format($Da[$MetricSName], 0, '', ',');
					}else{
						if(($MetricSName == 'CTR' || $MetricSName == 'VTR' || $MetricSName == 'ProfitMargin') && floatval(str_replace('%','',$Da[$MetricSName])) == 0){
							$Data[$Nd][] = '0.00%';
						}elseif($MetricSName == 'CPM' && floatval(str_replace('$','',$Da[$MetricSName])) == 0){
							$Data[$Nd][] = '$0.00';
						}else{
							$Data[$Nd][] = $Da[$MetricSName];
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
		echo 'Error';
	}
	mysqli_close ( $db->link );
	//print_r($Data);
	//exit(0);
?>{
  "draw": <?php echo intval($_POST['draw']); ?>,
  "recordsTotal": <?php echo $CntTotal; ?>,
  "recordsFiltered": <?php echo $CntTotal; ?>,
  "data": <?php echo json_encode($Data); ?>,
  "dataT": <?php echo json_encode($DataT); ?>,
  "sql": "<?php echo $SQLQuery; ?>",
  "sqlT": "<?php echo $SQLQueryT; ?>",
  "cached": "<?php echo $Cached; ?>",
  "cachedT": "<?php echo $CachedT; ?>"
}