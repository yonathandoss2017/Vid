<?php
function safe_json_encode($value, $options = 0, $depth = 512) {
    $encoded = json_encode($value, $options, $depth);
    if ($encoded === false && $value && json_last_error() == JSON_ERROR_UTF8) {
        $encoded = json_encode(utf8ize($value), $options, $depth);
    }
    return $encoded;
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}
	$Locale = 'es_AR';
	$ReportsTable = '{ReportsTable}';
	
	$DimensionsSQL = array(
		'supply_source' => array(
			'Name'	=>	"concat(supplytag.TagName, '--',supplytag.id) AS SupplySource",
			'SearchName'	=>	"supplytag.TagName",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"SupplySource",
			'OrderVal'		=>	"SupplySource",
			'Checked'		=> true,
			'InvalInner'	=> '',
			'HeadName'		=> 'Supply Source'
		),
		'domain' => array(
			'Name'	=>	"reports_domain_names.Name AS Domain",
			'SearchName'	=>	"reports_domain_names.Name",
			'InnerJoin'		=> 	array('reports_domain_names' => "INNER JOIN reports_domain_names ON reports_domain_names.id = $ReportsTable.Domain "),
			'GroupBy'		=>	"Domain",
			'OrderVal'		=>	"Domain",
			'Checked'		=> false,
			'InvalInner'	=> '',
			'HeadName'		=> 'Domain'
		),
		'country' => array(
			'Name'	=>	"reports_country_names.Name AS Country",
			'SearchName'	=>	"reports_country_names.idVidoomy",
			'InnerJoin'		=> 	array('reports_country_names' => "INNER JOIN reports_country_names ON reports_country_names.id = $ReportsTable.Country "),
			'GroupBy'		=>	"Country",
			'OrderVal'		=>	"Country",
			'Checked'		=> false,
			'InvalInner'	=> '',
			'HeadName'		=> 'Country'
		),
		'publisher_manager' => array(
			'Name'	=>	"acc_managers.Nick AS AccountManager",
			'SearchName'	=>	"acc_managers.id",
			'InnerJoin'		=> 	array(
				'users' 		=> "INNER JOIN users ON users.id = $ReportsTable.idUser ",
				'acc_managers'  => "INNER JOIN acc_managers ON acc_managers.id = users.AccM "
			),
			'GroupBy'		=>	"AccountManager",
			'OrderVal'		=>	"AccountManager",
			'Checked'		=> false,
			'InvalInner'	=> 'supply_partner',
			'HeadName'		=> 'Publisher Manager'
		),
		'supply_partner' => array(
			'Name'	=> 	"users.user AS SupplyPartner",
			'SearchName'	=> 	"users.user",
			'InnerJoin'		=> 	array('users' => "INNER JOIN users ON users.id = $ReportsTable.idUser "),
			'GroupBy'		=>	"SupplyPartner",
			'OrderVal'		=>	"SupplyPartner",
			'Checked'		=> false,
			'InvalInner'	=> '',
			'HeadName'		=> 'Supply Partner'
		),
		'environment' => array(
			'Name'	=>	"supplytag.PlatformType AS Environment",
			'SearchName'	=>	"",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"Environment",
			'OrderVal'		=>	"Environment",
			'Checked'		=> false,
			'InvalInner'	=> '',
			'HeadName'		=> 'Device'
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
			'Name'	=> 	"concat(DATE($ReportsTable.Date), ', ', TIME_FORMAT(CONCAT($ReportsTable.Hour,':00:00'), '%l%p')) AS HourO, CONCAT($ReportsTable.Date, ' ' ,LPAD($ReportsTable.Hour,2,'0') , ':00:00') AS HourOrder",
			//'Name'	=> 	"concat(DATE($ReportsTable.Date), ', ', LPAD($ReportsTable.Hour,2,'0'), 'Hs') AS HourO",
			'SearchName'	=> 	"",
			'InnerJoin'		=> 	"",
			'ShowName'		=> 	"HourO",
			'GroupBy'		=>	"HourOrder",
			'OrderVal'		=>	"HourOrder"
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
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.formatLoads), 0, '$Locale') AS formatLoads ",
			'Name'		=>	"formatLoads",
			'OrderVal' 	=>	"formatLoads",
			'Base' 		=>	array("formatLoads"),
			'NumberF'	=>	true,
			'HeadName'		=> 'Format Loads'
		),
		'impressions' 			=> array(
			'SQL' 		=>	", SUM($ReportsTable.Impressions) AS Impressions ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Impressions), 0, '$Locale') AS Impressions ",
			'Name'		=>	"Impressions",
			'OrderVal' 	=>	"Impressions",
			'Base' 		=>	array("Impressions"),
			'NumberF'	=>	true,
			'HeadName'		=> 'Impressions'
		),
		'formatloads_fill'	=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Impressions) / SUM($ReportsTable.formatLoads) * 100), 2) AS formatLoadFill ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Impressions) / SUM($ReportsTable.formatLoads) * 100), 2, '$Locale') , '%') AS formatLoadFill ",
			'Name'		=>	"formatLoadFill",
			'OrderVal' 	=>	"formatLoadFill",
			'Base' 		=>	array("Impressions","formatLoads"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Fillrate'
		),
		'opportunities'		 	=> array(
			'SQL' 		=>	", SUM($ReportsTable.Opportunities) AS Opportunities ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Opportunities), 0, '$Locale') AS Opportunities ",
			'Name'		=>	"Opportunities",
			'OrderVal' 	=>	"Opportunities",
			'Base' 		=>	array("Opportunities"),
			'NumberF'	=>	true,
			'HeadName'		=> 'Opportunities'
		),
		'cpm'					=> array(
			'SQL' 		=>	", round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.Impressions) * 1000),2) AS CPM ",
			'SQLCSV' 	=>	", concat('$', FORMAT( ( SUM($ReportsTable.Revenue) / SUM($ReportsTable.Impressions) * 1000), 2, '$Locale') ) AS CPM ",
			'Name'		=>	"CPM",
			'OrderVal' 	=>	"CPM",
			'Base' 		=>	array("Revenue","Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'CPM'
		),
		'revenue'	 			=> array(
			'SQL' 		=>	", SUM($ReportsTable.Revenue) AS Revenue, SUM($ReportsTable.Revenue) AS RevenueOrder ",
			'SQLCSV' 	=>	", concat('$', FORMAT( SUM($ReportsTable.Revenue), 2, '$Locale') ) AS Revenue, SUM($ReportsTable.Revenue) AS RevenueOrder ",
			'Name'		=>	"Revenue",
			'OrderVal' 	=>	"RevenueOrder",
			'Base' 		=>	array("Revenue"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Revenue'
		),
		'media_cost' 			=> array(
			'SQL' 		=>	", SUM($ReportsTable.Coste) AS Coste, SUM($ReportsTable.Coste) AS CosteOrder ",
			'SQLCSV' 	=>	", concat('$', FORMAT(SUM($ReportsTable.Coste), 2, '$Locale') ) AS Coste, SUM($ReportsTable.Coste) AS CosteOrder ",
			'Name'		=>	"Coste",
			'OrderVal' 	=>	"CosteOrder",
			'Base' 		=>	array("Coste"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Cost'
		),
		/*'extraprima_cost' 		=> array(
			'SQL' 		=>	", concat('$',FORMAT(SUM($ReportsTable.Extraprima),2)) AS Extraprima, SUM($ReportsTable.Extraprima) AS ExtraprimaOrder ",
			'Name'		=>	"Extraprima",
			'OrderVal' 	=>	"ExtraprimaOrder",
			'Base' 		=>	array("Extraprima"),
			'NumberF'	=>	false
		),*/
		'profit'	 			=> array(
			'SQL' 		=>	", (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) AS Profit, (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) AS ProfitOrder ",
			'SQLCSV' 	=>	", concat('$', FORMAT( (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)), 2, '$Locale') ) AS Profit, (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) AS ProfitOrder ",
			'Name'		=>	"Profit",
			'OrderVal' 	=>	"ProfitOrder",
			'Base' 		=>	array("Revenue","Coste"), //,"Extraprima"
			'NumberF'	=>	false,
			'HeadName'		=> 'Profit'
		),
		'profit_margin'	 		=> array(
			'SQL' 		=>	", round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) / SUM($ReportsTable.Revenue) ) * 100 ,2) AS ProfitMargin, round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) / SUM($ReportsTable.Revenue) ) * 100 ,2) AS ProfitMarginOrder",
			'SQLCSV' 	=>	", concat( FORMAT( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) / SUM($ReportsTable.Revenue) ) * 100 , 2, '$Locale'), '%') AS ProfitMargin, round( ((SUM($ReportsTable.Revenue) - SUM($ReportsTable.Coste)) / SUM($ReportsTable.Revenue) ) * 100 ,2) AS ProfitMarginOrder",
			'Name'		=>	"ProfitMargin",
			'OrderVal' 	=>	"ProfitMarginOrder",
			'Base' 		=>	array("Revenue","Coste"), //,"Extraprima"
			'NumberF'	=>	false,
			'HeadName'		=> 'Profit Margin'
		),
		'ctr' 					=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Clicks) / SUM($ReportsTable.Impressions) * 100) ,2) AS CTR ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Clicks) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS CTR ",
			'Name'		=>	"CTR",
			'OrderVal' 	=>	"CTR",
			'Base' 		=>	array("Clicks","Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'CTR'
		),
		'vtr'					=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.CompletedViews) / SUM($ReportsTable.Impressions) * 100) ,2) AS VTR ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.CompletedViews) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS VTR ",
			'Name'		=>	"VTR",
			'OrderVal' 	=>	"VTR",
			'Base' 		=>	array("CompletedViews","Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'VTR'
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
