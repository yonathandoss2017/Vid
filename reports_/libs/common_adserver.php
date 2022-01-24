<?php

	$DimensionsSQL = array(
		'publisher' => array(
			'Name'	=>	"Publisher AS SupplyPartner",
			'SearchName'	=>	"Publisher",
			'GroupBy'		=>	"Publisher",
			'OrderVal'		=>	"SupplyPartner",
			'HeadName'		=> 'Supply Partner'
		),
		'website' => array(
			'Name'	=>	"Website",
			'SearchName'	=>	"Website",
			'GroupBy'		=>	"Website",
			'OrderVal'		=>	"Website",
			'HeadName'		=> 'Website'
		),
		'zone' => array(
			'Name'			=>	"Zone",
			'SearchName'	=>	"Zone",
			'GroupBy'		=>	"Zone",
			'OrderVal'		=>	"Zone",
			'HeadName'		=> 'Supply Soruce'
		),
		'country' => array(
			'Name'			=>	"Country",
			'SearchName'	=>	"Country",
			'GroupBy'		=>	"Country",
			'OrderVal'		=>	"Country",
			'HeadName'		=> 'Country'
		),
		'domain' => array(
			'Name'			=>	"Domain",
			'SearchName'	=> 	"Domain",
			'GroupBy'		=>	"Domain",
			'OrderVal'		=>	"Domain",
			'HeadName'		=> 'Domain'
		),		
		'device' => array(
			'Name'	=>	"Device",
			'SearchName'	=> 	"Device",
			'GroupBy'		=>	"Device",
			'OrderVal'		=>	"Device",
			'HeadName'		=> 'Device'
		),
		'os' => array(
			'Name'	=>	"Os",
			'SearchName'	=> 	"Os",
			'GroupBy'		=>	"Os",
			'OrderVal'		=>	"Os",
			'HeadName'		=> 'Os'
		),
		'browser' => array(
			'Name'			=>	"Browser",
			'SearchName'	=> 	"Browser",
			'GroupBy'		=>	"Browser",
			'OrderVal'		=>	"Browser",
			'HeadName'		=> 'Browser'
		),
		'adserver_player' => array(
			'Name'			=>	"Player",
			'SearchName'	=> 	"Player",
			'GroupBy'		=>	"Player",
			'OrderVal'		=>	"Player",
			'HeadName'		=>  'Player'
		),
		'demand_tag' => array(
			'Name'	=>	"DemandTag",
			'SearchName'	=> 	"DemandTag",
			'GroupBy'		=>	"DemandTag",
			'OrderVal'		=>	"DemandTag",
			'HeadName'		=> 'DemandTag'
		)
	);

	$TimesSQL = array(
		'monthly' => array(
			'Name'	=> 	"TIME_FORMAT(__time, 'MMMM YYYY') AS \"Month\"",
			'ShowName'		=> 	"Month",
			'GroupBy'		=>	"TIME_FORMAT(__time, 'MMMM YYYY')",
			'OrderVal'		=>	"TIME_FORMAT(__time, 'MMMM YYYY')"
		),
		'daily' => array(
			'Name'	=> 	"TIME_FORMAT(__time, 'YYYY-MM-dd') AS \"Date\"",
			'ShowName'		=> 	"Date",
			'GroupBy'		=>	"TIME_FORMAT(__time, 'YYYY-MM-dd')",
			'OrderVal'		=>	"TIME_FORMAT(__time, 'YYYY-MM-dd')"
		),
		'hourly' => array(
			'Name'	=> 	"TIME_FORMAT(__time, 'YYYY-MM-dd, haa') AS \"Hour\"",
			'ShowName'		=> 	"Hour",
			'GroupBy'		=>	"TIME_FORMAT(__time, 'YYYY-MM-dd, haa')",
			'OrderVal'		=>	"TIME_FORMAT(__time, 'YYYY-MM-dd, haa')"
		),
		'overall' => array(
			'Name'	=> 	"'Overall' AS Overall",
			'ShowName'		=> 	"Overall",
			'GroupBy'		=>	false,
			'OrderVal'		=>	"Overall"
		)
	);

	$MetricsSQL = array(
		'formatloads' 	=> array(
			'SQL' 		=>	", SUM(sum_FormatLoads) AS FormatLoads ",
			'SQLCSV' 	=>	", SUM(sum_FormatLoads) AS FormatLoads ",
			'Name'		=>	"FormatLoads",
			'OrderVal' 	=>	"FormatLoads",
			'NumberF'	=>	true,
			'HeadName'	=> 'Format Loads'
		),
		'formatloads_fill'	 	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Impressions) / (SUM(sum_FormatLoads) * 1.0) * 100, 2) AS FormatLoadsFill ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Impressions) / (SUM(sum_FormatLoads) * 1.0) * 100, 2) AS FormatLoadsFill ",
			'Name'		=>	"FormatLoadsFill",
			'OrderVal' 	=>	"FormatLoadsFill",
			'NumberF'	=>	true,
			'HeadName'	=> 'Format Loads Fillrate'
		),
		'tag_requests' 	=> array(
			'SQL' 		=>	", SUM(sum_TagRequests) AS Requests ",
			'SQLCSV' 	=>	", SUM(sum_TagRequests) AS Requests ",
			'Name'		=>	"Requests",
			'OrderVal' 	=>	"Requests",
			'NumberF'	=>	true,
			'HeadName'	=> 'Requests'
		),
		'tr_fill'	 	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Impressions) / (SUM(sum_TagRequests) * 1.0) * 100, 2) AS TagRequestFill ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Impressions) / (SUM(sum_TagRequests) * 1.0) * 100, 2) AS TagRequestFill ",
			'Name'		=>	"TagRequestFill",
			'OrderVal' 	=>	"TagRequestFill",
			'NumberF'	=>	true,
			'HeadName'	=> 'Tag Request Fillrate'
		),
		'impressions' => array(
			'SQL' 		=>	", SUM(sum_Impressions) AS Impressions ",
			'SQLCSV' 	=>	", SUM(sum_Impressions) AS Impressions ",
			'Name'		=>	"Impressions",
			'OrderVal' 	=>	"Impressions",
			'NumberF'	=>	true,
			'HeadName'		=> 'Impressions'
		),
		'viewable_impressions' => array(
			'SQL' 		=>	", SUM(sum_AdViewableImpression) AS VImpressions ",
			'SQLCSV' 	=>	", SUM(sum_AdViewableImpression) AS VImpressions ",
			'Name'		=>	"VImpressions",
			'OrderVal' 	=>	"VImpressions",
			'NumberF'	=>	true,
			'HeadName'		=> 'Viewable Impressions'
		),
		'revenue'	=> array(
			'SQL' 		=>	", SUM(sum_UsdRevenue) as Revenue ",
			'SQLCSV' 	=>	", SUM(sum_UsdRevenue) as Revenue ",
			'Name'		=>	"Revenue",
			'OrderVal' 	=>	"Revenue",
			'NumberF'	=>	false,
			'HeadName'		=> 'Revenue'
		),
		'media_cost'	=> array(
			'SQL' 		=>	", SUM(sum_UsdCost) as Cost ",
			'SQLCSV' 	=>	", SUM(sum_UsdCost) as Cost ",
			'Name'		=>	"Cost",
			'OrderVal' 	=>	"Cost",
			'NumberF'	=>	false,
			'HeadName'		=> 'Cost'
		),
		'clicks' 	=> array(
			'SQL' 		=>	", SUM(sum_ClickThrus) AS Clicks ",
			'SQLCSV' 	=>	", SUM(sum_ClickThrus) AS Clicks ",
			'Name'		=>	"Clicks",
			'OrderVal' 	=>	"Clicks",
			'NumberF'	=>	true,
			'HeadName'	=> 'Clicks'
		),
		'100view' => array(
			'SQL' 		=>	", SUM(sum_VideoCompletes) AS CompleteV ",
			'SQLCSV' 	=>	", SUM(sum_VideoCompletes) AS CompleteV ",
			'Name'		=>	"CompleteV",
			'OrderVal' 	=>	"CompleteV",
			'NumberF'	=>	true,
			'HeadName'	=> 'Complete Views'
		),
		'25view' => array(
			'SQL' 		=>	", SUM(sum_FirstQuartiles) AS Complete25 ",
			'SQLCSV' 	=>	", SUM(sum_FirstQuartiles) AS Complete25 ",
			'Name'		=>	"Complete25",
			'OrderVal' 	=>	"Complete25",
			'NumberF'	=>	false,
			'HeadName'	=> '25% Views'
		),
		'50view' => array(
			'SQL' 		=>	", SUM(sum_MidPoints) AS Complete50 ",
			'SQLCSV' 	=>	", SUM(sum_MidPoints) AS Complete50 ",
			'Name'		=>	"Complete50",
			'OrderVal' 	=>	"Complete50",
			'NumberF'	=>	false,
			'HeadName'	=> '50% Views'
		),
		'75view' => array(
			'SQL' 		=>	", SUM(sum_ThirdQuartiles) AS Complete75 ",
			'SQLCSV' 	=>	", SUM(sum_ThirdQuartiles) AS Complete75 ",
			'Name'		=>	"Complete75",
			'OrderVal' 	=>	"Complete75",
			'NumberF'	=>	false,
			'HeadName'	=> '75% Views'
		),
		'cpm'	=> array(
			'SQL' 		=>	", SUM(sum_UsdCost) / SUM(sum_Impressions) *  1000 AS CPM ",
			'SQLCSV' 	=>	", SUM(sum_UsdCost) / SUM(sum_Impressions) *  1000 AS CPM ",
			'Name'		=>	"CPM",
			'OrderVal' 	=>	"CPM",
			'NumberF'	=>	false,
			'HeadName'		=> 'CPM'
		),
		'viewability_rate'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_AdViewableImpression) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS ViewabilityPercent ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_AdViewableImpression) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS ViewabilityPercent ",
			'Name'		=>	"ViewabilityPercent",
			'OrderVal' 	=>	"ViewabilityPercent",
			'NumberF'	=>	false,
			'HeadName'		=> 'Viewability Percent'
		),
		'ctr' 		=> array(
			'SQL' 		=>	", ROUND(SUM(sum_ClickThrus) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS CTR ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_ClickThrus) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS CTR ",
			'Name'		=>	"CTR",
			'OrderVal' 	=>	"CTR",
			'NumberF'	=>	false,
			'HeadName'		=> 'CTR'
		),
		'vtr'		=> array(
			'SQL' 		=>	", ROUND(SUM(sum_VideoCompletes) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS VTR ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_VideoCompletes) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS VTR ",
			'Name'		=>	"VTR",
			'OrderVal' 	=>	"VTR",
			'NumberF'	=>	false,
			'HeadName'		=> 'VTR'
		),
		'25rate'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_FirstQuartiles) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS FIRST ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_FirstQuartiles) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS FIRST ",
			'Name'		=>	"FIRST",
			'OrderVal' 	=>	"FIRST",
			'Base' 		=>	array("Complete25", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> '25%'
		),
		'50rate'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Midpoints) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS MID ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Midpoints) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS MID ",
			'Name'		=>	"MID",
			'OrderVal' 	=>	"MID",
			'NumberF'	=>	false,
			'HeadName'		=> '50%'
		),
		'75rate'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_ThirdQuartiles) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS THIRD ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_ThirdQuartiles) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS THIRD ",
			'Name'		=>	"THIRD",
			'OrderVal' 	=>	"THIRD",
			'NumberF'	=>	false,
			'HeadName'		=> '75%'
		),
		'close'	=> array(
			'SQL' 		=>	", SUM(sum_Close) AS Closed ",
			'SQLCSV' 	=>	", SUM(sum_Close) AS Closed ",
			'Name'		=>	"Closed",
			'OrderVal' 	=>	"Closed",
			'NumberF'	=>	false,
			'HeadName'		=> 'Closed'
		),
		'pause'	=> array(
			'SQL' 		=>	", SUM(sum_Pause) AS Paused ",
			'SQLCSV' 	=>	", SUM(sum_Pause) AS Paused ",
			'Name'		=>	"Paused",
			'OrderVal' 	=>	"Paused",
			'NumberF'	=>	false,
			'HeadName'		=> 'Paused'
		)
	);


function druidQuery($Query, $Limit = 30000){
	$ch = curl_init( 'http://vdmdruidadmin:U9%3DjPvAPuyH9EM%40%26@ec2-3-120-137-168.eu-central-1.compute.amazonaws.com:8888/druid/v2/sql' );
		
	$context = new \stdClass();
	$context->sqlOuterLimit = $Limit;
	
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
	$resultT = json_decode($result) ;
	
	return $resultT;
}

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