<?php
	$ReportsTable = '{ReportsTable}';
	
	$DimensionsSQL = array(
		'supply_source' => array(
			'Name'	=>	"concat(supplytag.TagName, '--',supplytag.id) AS SupplySource",
			'SearchName'	=>	"supplytag.TagName",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"SupplySource",
			'OrderVal'		=>	"SupplySource",
			'Checked'		=> true,
			'InvalInner'	=> ''
		),
		'domain' => array(
			'Name'	=>	"reports_domain_names.Name AS Domain",
			'SearchName'	=>	"reports_domain_names.Name",
			'InnerJoin'		=> 	array('reports_domain_names' => "INNER JOIN reports_domain_names ON reports_domain_names.id = $ReportsTable.Domain "),
			'GroupBy'		=>	"Domain",
			'OrderVal'		=>	"Domain",
			'Checked'		=> false,
			'InvalInner'	=> ''
		),
		'country' => array(
			'Name'	=>	"reports_country_names.Name AS Country",
			'SearchName'	=>	"reports_country_names.Name",
			'InnerJoin'		=> 	array('reports_country_names' => "INNER JOIN reports_country_names ON reports_country_names.id = $ReportsTable.Country "),
			'GroupBy'		=>	"Country",
			'OrderVal'		=>	"Country",
			'Checked'		=> false,
			'InvalInner'	=> ''
		),
		'publisher_manager' => array(
			'Name'	=>	"acc_managers.Name AS AccountManager",
			'SearchName'	=>	"acc_managers.Name",
			'InnerJoin'		=> 	array(
				'users' => "INNER JOIN users ON users.id = $ReportsTable.idUser ",
				'acc_managers' => "INNER JOIN acc_managers ON acc_managers.id = users.AccM "
			),
			'GroupBy'		=>	"AccountManager",
			'OrderVal'		=>	"AccountManager",
			'Checked'		=> false,
			'InvalInner'	=> 'supply_partner'
		),
		'supply_partner' => array(
			'Name'	=> 	"users.user AS SupplyPartner",
			'SearchName'	=> 	"users.user",
			'InnerJoin'		=> 	array('users' => "INNER JOIN users ON users.id = $ReportsTable.idUser "),
			'GroupBy'		=>	"SupplyPartner",
			'OrderVal'		=>	"SupplyPartner",
			'Checked'		=> false,
			'InvalInner'	=> ''
		),
		'environment' => array(
			'Name'	=>	"supplytag.PlatformType AS Environment",
			'SearchName'	=>	"",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"Environment",
			'OrderVal'		=>	"Environment",
			'Checked'		=> false,
			'InvalInner'	=> ''
		)
	);
	
	$TimesSQL = array(
		'monthly' => array(
			'Name'	=> 	"concat(MONTHNAME($ReportsTable.Date), ' ', YEAR($ReportsTable.Date)) AS Month, concat(YEAR($ReportsTable.Date), MONTH($ReportsTable.Date)) AS MonthN",
			'SearchName'	=> 	"",
			'InnerJoin'		=> 	"",
			'ShowName'		=> 	"Month",
			'GroupBy'		=>	"MonthN",
			'OrderVal'		=>	"MonthN"
		),
		'daily' => array(
			'Name'	=> 	"$ReportsTable.Date AS Date",
			'SearchName'	=> 	"",
			'InnerJoin'		=> 	"",
			'ShowName'		=> 	"Date",
			'GroupBy'		=>	"$ReportsTable.Date",
			'OrderVal'		=>	"Date"
		),
		'hourly' => array(
			'Name'	=> 	"concat(DATE($ReportsTable.Date), ', ', LPAD($ReportsTable.Hour,2,'0'), 'Hs') AS HourO",
			'SearchName'	=> 	"",
			'InnerJoin'		=> 	"",
			'ShowName'		=> 	"HourO",
			'GroupBy'		=>	"HourO",
			'OrderVal'		=>	"HourO"
		),
		'overall' => array(
			'Name'	=> 	"'Overall' AS Overall",
			'SearchName'	=> 	"",
			'InnerJoin'		=> 	"",
			'ShowName'		=> 	"Overall",
			'GroupBy'		=>	false,
			'OrderVal'		=>	"Overall"
		)
	);
	
	$MetricsSQL = array(
		'formatloads' 			=> array(
			'SQL' 		=>	", SUM($ReportsTable.formatLoads) AS formatLoads ",
			'Name'		=>	"formatLoads",
			'OrderVal' 	=>	"formatLoads",
			'Base' 		=>	array("formatLoads"),
			'NumberF'	=>	true
		),
		'impressions' 			=> array(
			'SQL' 		=>	", SUM($ReportsTable.Impressions) AS Impressions ",
			'Name'		=>	"Impressions",
			'OrderVal' 	=>	"Impressions",
			'Base' 		=>	array("Impressions"),
			'NumberF'	=>	true
		),
		'formatloads_fill'	=> array(
			'SQL' 		=>	", concat( round( (SUM($ReportsTable.Impressions) / SUM($ReportsTable.formatLoads) * 100), 2),'%') AS formatLoadFill ",
			'Name'		=>	"formatLoadFill",
			'OrderVal' 	=>	"formatLoadFill",
			'Base' 		=>	array("Impressions","formatLoads"),
			'NumberF'	=>	false
		),
		'opportunities'		 	=> array(
			'SQL' 		=>	", SUM($ReportsTable.Opportunities) AS Opportunities ",
			'Name'		=>	"Opportunities",
			'OrderVal' 	=>	"Opportunities",
			'Base' 		=>	array("Opportunities"),
			'NumberF'	=>	true
		),
		'cpm'					=> array(
			'SQL' 		=>	", concat('$',round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.Impressions) * 1000),2)) AS CPM ",
			'Name'		=>	"CPM",
			'OrderVal' 	=>	"CPM",
			'Base' 		=>	array("Revenue","Impressions"),
			'NumberF'	=>	false
		),
		'revenue'	 			=> array(
			'SQL' 		=>	", concat('$',FORMAT(SUM($ReportsTable.Revenue),2)) AS Revenue, SUM($ReportsTable.Revenue) AS RevenueOrder ",
			'Name'		=>	"Revenue",
			'OrderVal' 	=>	"RevenueOrder",
			'Base' 		=>	array("Revenue"),
			'NumberF'	=>	false
			
		),
		'media_cost' 			=> array(
			'SQL' 		=>	", concat('$',FORMAT(SUM($ReportsTable.Coste),2)) AS Coste, SUM($ReportsTable.Coste) AS CosteOrder ",
			'Name'		=>	"Coste",
			'OrderVal' 	=>	"CosteOrder",
			'Base' 		=>	array("Coste"),
			'NumberF'	=>	false
		),
		/*'extraprima_cost' 		=> array(
			'SQL' 		=>	", concat('$',FORMAT(SUM($ReportsTable.Extraprima),2)) AS Extraprima, SUM($ReportsTable.Extraprima) AS ExtraprimaOrder ",
			'Name'		=>	"Extraprima",
			'OrderVal' 	=>	"ExtraprimaOrder",
			'Base' 		=>	array("Extraprima"),
			'NumberF'	=>	false
		),*/
		'profit'	 			=> array(
			'SQL' 		=>	", concat('$',FORMAT((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)),2)) AS Profit, (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) AS ProfitOrder ", // - SUM($ReportsTable.Extraprima)
			'Name'		=>	"Profit",
			'OrderVal' 	=>	"ProfitOrder",
			'Base' 		=>	array("Revenue","Coste"), //,"Extraprima"
			'NumberF'	=>	false
		),
		'profit_margin'	 		=> array(
			'SQL' 		=>	", concat(round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) / SUM($ReportsTable.Revenue) ) * 100 ,2), '%') AS ProfitMargin, round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) / SUM($ReportsTable.Revenue) ) * 100 ,2) AS ProfitMarginOrder",  // - SUM($ReportsTable.Extraprima)
			'Name'		=>	"ProfitMargin",
			'OrderVal' 	=>	"ProfitMarginOrder",
			'Base' 		=>	array("Revenue","Coste"), //,"Extraprima"
			'NumberF'	=>	false
		),
		'ctr' 					=> array(
			'SQL' 		=>	", concat(round( (SUM($ReportsTable.Clicks) / SUM($ReportsTable.Impressions) * 100) ,2), '%') AS CTR ",
			'Name'		=>	"CTR",
			'OrderVal' 	=>	"CTR",
			'Base' 		=>	array("Clicks","Impressions"),
			'NumberF'	=>	false
		),
		'vtr'					=> array(
			'SQL' 		=>	", concat(round( (SUM($ReportsTable.CompletedViews) / SUM($ReportsTable.Impressions) * 100) ,2), '%') AS VTR ",
			'Name'		=>	"VTR",
			'OrderVal' 	=>	"VTR",
			'Base' 		=>	array("CompletedViews","Impressions"),
			'NumberF'	=>	false
		)/*,
		'Viewability Rate'		=> array(
			'SQL' 		=>	"",
			'Name'		=>	"ViewabilityR",
			'OrderVal' 	=>	"ViewabilityR",
			'NumberF'	=>	false
		)*/
	);

function isLocked($Table){
	global $db;
	$sql = "SELECT Status FROM lock_tables WHERE TableName = '$Table' LIMIT 1";
	if($db->getOne($sql) == 1){
		return true;
	}else{
		return false;
	}
}

function waitUnlock($Table){
	$Locked = isLocked($Table);
	if($Locked){
		while($Locked === true){
			sleep(1);
			$Locked = isLocked($Table);
		}
	}
	return;
}

function getLiveData($Date){
	global $db;
	
	waitUnlock('reports');
	
	$sql = "SELECT 
		round(SUM(Revenue), 2) AS Revenue, 
		SUM(Impressions) AS Impressions, 
		SUM(formatLoads) AS formatLoads, 
		round((SUM(Revenue) - SUM(Coste) - SUM(Extraprima)),2) AS Profit,
		round( (SUM(Impressions) / SUM(formatLoads) * 100), 2) AS formatLoadFill
		FROM `reports_resume201909` WHERE Date = '$Date'";
	$query = $db->query($sql);
	$Data = $db->fetch_array($query);
	
	return $Data;
}
function checkTableExists($TableName){
	global $db;
	
	$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $db->dbname . "' AND table_name = '" . $TableName . "' LIMIT 1";
	$chck = $db->getOne($sql);
	if($chck == 0){
		return false;
	}else{
		return true;
	}
}
