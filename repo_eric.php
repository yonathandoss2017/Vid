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
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$DatesOK = false;
	$MetricsOK = false;
	$DimensionsOK = false;
	$ToFilter = array();
	
	$ReportsTable = 'reports_resume';
	
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
	
	if($DatesOK){
		if(isset($_POST['Metrics'])){
			$Metrics = $_POST['Metrics'];
			if(is_array($Metrics)){
				if(count($Metrics) > 0){
					$MetricsOK = true;
				}
			}
		}
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
		
		$DimensionsSQL = array(
			'Supply Partner' => array(
				'Name'	=> 	"users.user AS SupplyPartner",
				'SearchName'	=> 	"users.user",
				'InnerJoin'		=> 	"INNER JOIN users ON users.id = $ReportsTable.idUser ",
				'GroupBy'		=>	"users.id",
				'OrderVal'		=>	"SupplyPartner"
			),
			'Supply Source' => array(
				'Name'	=>	"supplytag.TagName AS SupplySource",
				'SearchName'	=>	"supplytag.TagName",
				'InnerJoin'		=> 	"INNER JOIN supplytag ON supplytag.id = $ReportsTable.idTag ",
				'GroupBy'		=>	"supplytag.id",
				'OrderVal'		=>	"SupplySource"
			),
			'Domain' => array(
				'Name'	=>	"reports_domain_names.Name AS Domain",
				'SearchName'	=>	"reports_domain_names.Name",
				'InnerJoin'		=> 	"INNER JOIN reports_domain_names ON reports_domain_names.id = $ReportsTable.Domain ",
				'GroupBy'		=>	"reports_domain_names.id",
				'OrderVal'		=>	"Domain"
			),
			'Country' => array(
				'Name'	=>	"reports_country_names.Name AS Country",
				'SearchName'	=>	"reports_country_names.Name",
				'InnerJoin'		=> 	" INNER JOIN reports_country_names ON reports_country_names.id = $ReportsTable.Country ",
				'GroupBy'		=>	"reports_country_names.id",
				'OrderVal'		=>	"Country"
			)
		);
		
		$MetricsSQL = array(
			'Format Loads' 			=> array(
				'SQL' 		=>	", SUM($ReportsTable.formatLoads) AS formatLoads ",
				'Name'		=>	"formatLoads",
				'OrderVal' 	=>	"formatLoads",
				'NumberF'	=>	true
			),
			'Impressions' 			=> array(
				'SQL' 		=>	", SUM($ReportsTable.Impressions) AS Impressions ",
				'Name'		=>	"Impressions",
				'OrderVal' 	=>	"Impressions",
				'NumberF'	=>	true
			),
			'Format Load Fillrate'	=> array(
				'SQL' 		=>	", concat( round( (SUM($ReportsTable.Impressions) / SUM($ReportsTable.formatLoads) * 100), 2),'%') AS formatLoadFill ",
				'Name'		=>	"formatLoadFill",
				'OrderVal' 	=>	"formatLoadFill",
				'NumberF'	=>	false
			),
			'Opportunities'		 	=> array(
				'SQL' 		=>	", SUM($ReportsTable.Opportunities) AS Opportunities ",
				'Name'		=>	"Opportunities",
				'OrderVal' 	=>	"Opportunities",
				'NumberF'	=>	true
			),
			'CPM'					=> array(
				'SQL' 		=>	", concat(round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.Impressions) * 1000),2),'%') AS CPM ",
				'Name'		=>	"CPM",
				'OrderVal' 	=>	"CPM",
				'NumberF'	=>	false
			),
			'Revenue'	 			=> array(
				'SQL' 		=>	", concat('$',round(SUM($ReportsTable.Revenue),2)) AS Revenue, SUM($ReportsTable.Revenue) AS RevenueOrder ",
				'Name'		=>	"Revenue",
				'OrderVal' 	=>	"RevenueOrder",
				'NumberF'	=>	false
			),
			'Media Cost' 			=> array(
				'SQL' 		=>	", concat('$',round(SUM($ReportsTable.Coste),2)) AS Coste, SUM($ReportsTable.Coste) AS CosteOrder ",
				'Name'		=>	"Coste",
				'OrderVal' 	=>	"CosteOrder",
				'NumberF'	=>	false
			),
			'Extraprima Cost' 		=> array(
				'SQL' 		=>	", concat('$',round(SUM($ReportsTable.Extraprima),2)) AS Extraprima, SUM($ReportsTable.Extraprima) AS ExtraprimaOrder ",
				'Name'		=>	"Extraprima",
				'OrderVal' 	=>	"ExtraprimaOrder",
				'NumberF'	=>	false
			),
			'Profit'	 			=> array(
				'SQL' 		=>	", concat('$',round((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste) - SUM($ReportsTable.Extraprima)),2)) AS Profit, (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) AS ProfitOrder ",
				'Name'		=>	"Profit",
				'OrderVal' 	=>	"ProfitOrder",
				'NumberF'	=>	false
			),
			'Profit Margin'	 		=> array(
				'SQL' 		=>	", concat(round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste) - SUM($ReportsTable.Extraprima)) / SUM($ReportsTable.Revenue) ) * 100 ,2), '%') AS ProfitMargin, round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste) - SUM($ReportsTable.Extraprima)) / SUM($ReportsTable.Revenue) ) * 100 ,2) AS ProfitMarginOrder",
				'Name'		=>	"ProfitMargin",
				'OrderVal' 	=>	"ProfitMarginOrder",
				'NumberF'	=>	false
			),
			'CTR' 					=> array(
				'SQL' 		=>	", concat(round( (SUM($ReportsTable.Clicks) / SUM($ReportsTable.Impressions)) * 100) ,2), '%') AS CTR ",
				'Name'		=>	"CTR",
				'OrderVal' 	=>	"CTR",
				'NumberF'	=>	false
			),
			'VTR'					=> array(
				'SQL' 		=>	", concat(round( (SUM($ReportsTable.CompletedViews) / SUM($ReportsTable.Impressions) * 100) ,2), '%') AS VTR ",
				'Name'		=>	"VTR",
				'OrderVal' 	=>	"VTR",
				'NumberF'	=>	false
			),
			'Viewability Rate'		=> array(
				'SQL' 		=>	"",
				'Name'		=>	"ViewabilityR",
				'OrderVal' 	=>	"ViewabilityR",
				'NumberF'	=>	false
			)
		);
		
		$No = 0;
		$OrderName = "";
		$OrderParam = "";
		$C = "";
		$SQLDimensions = "";
		$SQLInnerJoins = "";
		$SQLGroups = "";
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
		$SuperQueryCount = $db->query($SQLSuperQueryCount);
		$CntTotal = $db->num_rows($SuperQueryCount);
		
		//echo '<br/><br/>' . $CntTotal . '<br/><br/>';
				
		$Nd = 0;
		$SQLSuperQuery = "SELECT $SQLDimensions $SQLMetrics FROM $ReportsTable $SQLInnerJoins WHERE $ReportsTable.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere GROUP BY $SQLGroups ORDER BY $OrderParam LIMIT $Start, $Length";
		//echo $SQLSuperQuery;
		//exit(0);
		
		$SuperQuery = $db->query($SQLSuperQuery);
		while($Da = $db->fetch_array($SuperQuery)){
			foreach($Dimensions as $DimensionName){
				$Data[$Nd][] = $Da[$DimensionsSQL[$DimensionName]['OrderVal']];
			}
			foreach($Metrics as $MetricName){
				if($MetricsSQL[$MetricName]['NumberF']){
					$Data[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
				}else{
					$Data[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
				}
			}
			$Nd++;
		}
		
		
	}else{
		echo 'Error';
	}
?>{
  "draw": <?php echo intval($_POST['draw']); ?>,
  "recordsTotal": <?php echo $CntTotal; ?>,
  "recordsFiltered": <?php echo $CntTotal; ?>,
  "data": <?php echo json_encode($Data); ?>
}