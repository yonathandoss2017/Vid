<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$DatesOK = false;
	$MetricsOK = false;
	$DimensionsOK = false;
	$TypeOK = false;
	$IncludeTime = false;
	$AddDimensions = false;
	$TimeName = "";
	$ToFilter = array();
	
	if(isset($_POST['PDate'])){
		$Dates = $_POST['PDate'];
		if(is_array($Dates)){
			if(count($Dates) == 2){
				foreach($Dates as $V => $D){
					if($V == 0){
						$arD = explode('/',$D);
						$DFrom = $arD[2] . '-' . $arD[1] . '-' . $arD[0];
					}else{
						$arD = explode('/',$D);
						$DTo = $arD[2] . '-' . $arD[1] . '-' . $arD[0];
						$DatesOK = true;
					}
				}
			}
		}
	}
	
	if(isset($_POST['ReportType'])){
		$TypeOK = true;
		$RepType = $_POST['ReportType'];
		
		if($RepType != 'Hourly Report'){
			$ReportsTable = 'reports_resume';
		}else{
			$ReportsTable = 'reports';
		}
		
		if($RepType != 'Overall Report'){
			$IncludeTime = true;
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
	
	if($IncludeTime){
		$DimensionsOK = true;
	}
		
	if($DatesOK && $MetricsOK){
		if(isset($_POST['Dimensions'])){
			$Dimensions = $_POST['Dimensions'];
			if(is_array($Dimensions)){
				if(count($Dimensions) > 0){
					$DimensionsOK = true;
				}
			}
		}
	}
	
	//print_r($_POST);
	if($DimensionsOK){
		if(isset($_POST['Filters'])){
			if(is_array($_POST['Filters'])){
				foreach($_POST['Filters'] as $Fi){				
					if(isset($Fi['label']) && isset($Fi['value'])){
						if($Fi['label'] != '' && $Fi['value'] != ''){
							$ToFilter[$Fi['label']][] = $Fi['value'];
						}
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
			$Length = 30;
		}
		
		if(isset($_POST['start'])){
			$Start = intval($_POST['start']);
		}else{
			$Start = 0;
		}
		
		$No = 0;
		$OrderName = "";
		$OrderParam = "";
		$C = "";
		$SQLDimensions = "";
		$SQLInnerJoins = "";
		$SQLGroups = "";
		
		if($IncludeTime){
			$SQLDimensions .= $TimesSQL[$RepType]['Name'];
			$SQLGroups .= $TimesSQL[$RepType]['GroupBy'];
			if($No == $OrderC){
				$OrderName = $TimesSQL[$RepType]['OrderVal'];
			}
			$TimeName = $TimesSQL[$RepType]['OrderVal'];
			$C = ", ";
			$No++;
		}
		
		if(isset($Dimensions)){
			if(is_array($Dimensions)){
				if(count($Dimensions) > 0){
					$AddDimensions = true;
					foreach($Dimensions as $DimensionName){
						$SQLDimensions .= $C . $DimensionsSQL[$DimensionName]['Name'];
						$SQLInnerJoins .= $DimensionsSQL[$DimensionName]['InnerJoin'];
						$SQLGroups .= $C . $DimensionsSQL[$DimensionName]['GroupBy'];
						$C = ", ";
						if($No == $OrderC){
							$OrderName = $DimensionsSQL[$DimensionName]['OrderVal'];
						}
						$No++;
					}
				}
			}
		}
		//print_r($Dimensions);
		
		$SQLWhere = "";
		$Or = "";
		foreach($ToFilter as $KFilter => $FilterVals){
			if(!in_array($KFilter, $Dimensions)){
				$SQLDimensions .= $C . $DimensionsSQL[$KFilter]['Name'];
				$SQLInnerJoins .= $DimensionsSQL[$KFilter]['InnerJoin'];
			}
			$KeySearch = $DimensionsSQL[$KFilter]['SearchName'];
			$SQLWhere .= " AND (";
			foreach($FilterVals as $FVal){
				$SQLWhere .= $Or . $KeySearch . " LIKE '$FVal'";
				$Or = " OR "; 
			}
			$SQLWhere .= ") ";
		}
		
		$SQLMetrics = "";
		foreach($Metrics as $MetricName){
			$SQLMetrics .= $MetricsSQL[$MetricName]['SQL'];
			if($No == $OrderC){
				$OrderName = $MetricsSQL[$MetricName]['OrderVal'];
			}
			$No++;
		}
		
		if($OrderName != ""){
			$OrderParam = $OrderName . ' ' . strtoupper($OrderD);
		}
		
		$SQLSuperQueryCount = "SELECT $SQLDimensions FROM $ReportsTable $SQLInnerJoins WHERE $ReportsTable.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere GROUP BY $SQLGroups";
		$SQLSuperQueryCount = str_replace('{ReportsTable}', $ReportsTable, $SQLSuperQueryCount);
		$SuperQueryCount = $db->query($SQLSuperQueryCount);
		$CntTotal = $db->num_rows($SuperQueryCount);
		
		//echo '<br/><br/>' . $CntTotal . '<br/><br/>';
				
		$Nd = 0;
		$Totals = array();
		$SQLSuperQuery = "SELECT $SQLDimensions $SQLMetrics FROM $ReportsTable $SQLInnerJoins WHERE $ReportsTable.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere GROUP BY $SQLGroups ORDER BY $OrderParam LIMIT $Start, $Length";
		$SQLSuperQuery = str_replace('{ReportsTable}', $ReportsTable, $SQLSuperQuery);
		//echo $SQLSuperQuery;
		//exit(0);
		
		$SuperQuery = $db->query($SQLSuperQuery);
		while($Da = $db->fetch_array($SuperQuery)){
			$TDim = 0;
			if($IncludeTime){
				$Data[$Nd][] = $Da[$TimeName];
				$TDim++;
			}
			if($AddDimensions){
				foreach($Dimensions as $DimensionName){
					$Data[$Nd][] = $Da[$DimensionsSQL[$DimensionName]['OrderVal']];
					$TDim++;
				}
			}
			foreach($Metrics as $MetricName){
				if($MetricsSQL[$MetricName]['NumberF']){
					$Data[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
				}else{
					$Data[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
				}
				//$Totals[$MetricsSQL[$MetricName]['Name']] += $Da[$MetricsSQL[$MetricName]['Name']];
			}
			$Nd++;
		}
		
		//$Data[0]
	}else{
		echo 'Error';
	}
	
	//print_r($Data);
	//exit(0);
?>{
  "draw": <?php echo intval($_POST['draw']); ?>,
  "recordsTotal": <?php echo $CntTotal; ?>,
  "recordsFiltered": <?php echo $CntTotal; ?>,
  "data": <?php echo json_encode($Data); ?>
}