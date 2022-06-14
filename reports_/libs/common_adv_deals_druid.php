<?php

	$DimensionsSQL = array(
		'campaign_name' => array(
			'Name'	=>	"Deal AS CampaignName",
			'SearchName'	=>	"Deal",
			'GroupBy'		=>	"Deal",
			'OrderVal'		=>	"CampaignName",
			'HeadName'		=> 'Deal'
		),
		'deal_id' => array(
			'Name'	=>	"Deal",
			'SearchName'	=>	"Deal",
			'GroupBy'		=>	"Deal",
			'OrderVal'		=>	"Deal",
			'HeadName'		=> 'Deal ID'
		),
		'adomain' => array(
			'Name'	=>	"Adomain",
			'SearchName'	=>	"Adomain",
			'GroupBy'		=>	"Adomain",
			'OrderVal'		=>	"Adomain",
			'InvalInner'	=> '',
			'HeadName'		=> 'Advertiser Domain'
		),
		'country' => array(
			'Name'	=>	"Country",
			'SearchName'	=>	"Country",
			'GroupBy'		=>	"Country",
			'OrderVal'		=>	"Country",
			'HeadName'		=> 'Country'
		),
		'dsp_rtb' => array(
			'Name'	=>	"Dsp",
			'SearchName'	=> 	"Dsp",
			'GroupBy'		=>	"Dsp",
			'OrderVal'		=>	"Dsp",
			'HeadName'		=> 'Dsp'
		),
		'domain' => array(
			'Name'	=>	"Domain",
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
		'creativity' => array(
			'Name'	=>	"Crid",
			'SearchName'	=> 	"Crid",
			'GroupBy'		=>	"Crid",
			'OrderVal'		=>	"Crid",
			'HeadName'		=> 'Creativity ID'
		),
		'category' => array(
			'Name'	=>	"Category",
			'SearchName'	=> 	"Category",
			'GroupBy'		=>	"Category",
			'OrderVal'		=>	"Category",
			'HeadName'		=> 'Category'
		),
		'adsize' => array(
			'Name'	=>	"AdSize",
			'SearchName'	=> 	"AdSize",
			'GroupBy'		=>	"AdSize",
			'OrderVal'		=>	"AdSize",
			'HeadName'		=> 'Ad Size'
		),
		'bidfilteredreason' => array(
			'Name'	=>	"BidFilteredReason",
			'SearchName'	=> 	"BidFilteredReason",
			'GroupBy'		=>	"BidFilteredReason",
			'OrderVal'		=>	"BidFilteredReason",
			'HeadName'		=> 'Bid Filted Reason'
		),
		'blockedreason' => array(
			'Name'	=>	"BlockedReason",
			'SearchName'	=> 	"BlockedReason",
			'GroupBy'		=>	"BlockedReason",
			'OrderVal'		=>	"BlockedReason",
			'HeadName'		=> 'Blocked Reason'
		),
		'gdpr' => array(
			'Name'	=>	"Gdpr",
			'SearchName'	=> 	"Gdpr",
			'GroupBy'		=>	"Gdpr",
			'OrderVal'		=>	"Gdpr",
			'HeadName'		=> 'GDPR'
		),
		'gdprcs' => array(
			'Name'	=>	"Gdprcs",
			'SearchName'	=> 	"Gdprcs",
			'GroupBy'		=>	"Gdprcs",
			'OrderVal'		=>	"Gdprcs",
			'HeadName'		=> 'GDPR Consent String'
		),
		'os' => array(
			'Name'	=>	"Os",
			'SearchName'	=> 	"Os",
			'GroupBy'		=>	"Os",
			'OrderVal'		=>	"Os",
			'HeadName'		=> 'OS'
		),
		'publisherid' => array(
			'Name'	=>	"PublisherId",
			'SearchName'	=> 	"PublisherId",
			'GroupBy'		=>	"PublisherId",
			'OrderVal'		=>	"PublisherId",
			'HeadName'		=> 'Publisher ID'
		),
		'sync' => array(
			'Name'	=>	"Sync",
			'SearchName'	=> 	"Sync",
			'GroupBy'		=>	"Sync",
			'OrderVal'		=>	"Sync",
			'HeadName'		=> 'Synced User'
		),
		'wseat' => array(
			'Name'	=>	"Wseat",
			'SearchName'	=> 	"Wseat",
			'GroupBy'		=>	"Wseat",
			'OrderVal'		=>	"Wseat",
			'HeadName'		=> 'WSeat'
		),
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
		'request' 	=> array(
			'SQL' 		=>	", SUM(sum_BidRequests) AS Requests ",
			'SQLCSV' 	=>	", SUM(sum_BidRequests) AS Requests ",
			'Name'		=>	"Requests",
			'OrderVal' 	=>	"Requests",
			'NumberF'	=>	true,
			'HeadName'	=> 'Requests'
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
			'SQL' 		=>	", SUM(sum_Vimpression) AS VImpressions ",
			'SQLCSV' 	=>	", SUM(sum_Vimpression) AS VImpressions ",
			'Name'		=>	"VImpressions",
			'OrderVal' 	=>	"VImpressions",
			'NumberF'	=>	true,
			'HeadName'		=> 'Viewable Impressions'
		),
		'mesurable_impressions' => array(
			'SQL' 		=>	", SUM(sum_Uimpression) AS MImpressions ",
			'SQLCSV' 	=>	", SUM(sum_Uimpression) AS MImpressions ",
			'Name'		=>	"MImpressions",
			'OrderVal' 	=>	"MImpressions",
			'NumberF'	=>	true,
			'HeadName'		=> 'Messurable Impressions'
		),
		'revenue'	=> array(
			'SQL' 		=>	", SUM(sum_Money) as Revenue ",
			'SQLCSV' 	=>	", SUM(sum_Money) as Revenue ",
			'Name'		=>	"Revenue",
			'OrderVal' 	=>	"Revenue",
			'NumberF'	=>	false,
			'HeadName'		=> 'Revenue'
		),
		'bids' 		=> array(
			'SQL' 		=>	", SUM(sum_BidResponses) AS Bids ",
			'SQLCSV' 	=>	", SUM(sum_BidResponses) AS Bids ",
			'Name'		=>	"Bids",
			'OrderVal' 	=>	"Bids",
			'NumberF'	=>	true,
			'HeadName'	=> 'Bids'
		),
		'clicks' 	=> array(
			'SQL' 		=>	", SUM(sum_Clicks) AS Clicks ",
			'SQLCSV' 	=>	", SUM(sum_Clicks) AS Clicks ",
			'Name'		=>	"Clicks",
			'OrderVal' 	=>	"Clicks",
			'NumberF'	=>	true,
			'HeadName'	=> 'Clicks'
		),
		'complete_views' => array(
			'SQL' 		=>	", SUM(sum_Complete) AS CompleteV ",
			'SQLCSV' 	=>	", SUM(sum_Complete) AS CompleteV ",
			'Name'		=>	"CompleteV",
			'OrderVal' 	=>	"CompleteV",
			'NumberF'	=>	true,
			'HeadName'	=> 'Complete Views'
		),
		'viewed25' => array(
			'SQL' 		=>	", SUM(sum_FirstQuartile) AS Complete25 ",
			'SQLCSV' 	=>	", SUM(sum_FirstQuartile) AS Complete25 ",
			'Name'		=>	"Complete25",
			'OrderVal' 	=>	"Complete25",
			'NumberF'	=>	false,
			'HeadName'	=> '25% Views'
		),
		'viewed50' => array(
			'SQL' 		=>	", SUM(sum_Midpoint) AS Complete50 ",
			'SQLCSV' 	=>	", SUM(sum_Midpoint) AS Complete50 ",
			'Name'		=>	"Complete50",
			'OrderVal' 	=>	"Complete50",
			'NumberF'	=>	false,
			'HeadName'	=> '50% Views'
		),
		'viewed75' => array(
			'SQL' 		=>	", SUM(sum_ThirdQuartile) AS Complete75 ",
			'SQLCSV' 	=>	", SUM(sum_ThirdQuartile) AS Complete75 ",
			'Name'		=>	"Complete75",
			'OrderVal' 	=>	"Complete75",
			'NumberF'	=>	false,
			'HeadName'	=> '75% Views'
		),
		'rebate_cost' 			=> array(
			'SQL' 		=>	"",
			'SQLCSV' 	=>	"",
			'Name'		=>	"Rebate",
			'OrderVal' 	=>	"Rebate",
			'NumberF'	=>	false,
			'HeadName'		=> 'Rebate Cost'
		),
		'cpm'	=> array(
			'SQL' 		=>	", SUM(sum_Money) / SUM(sum_Impressions) *  1000 AS CPM ",
			'SQLCSV' 	=>	", SUM(sum_Money) / SUM(sum_Impressions) *  1000 AS CPM ",
			'Name'		=>	"CPM",
			'OrderVal' 	=>	"CPM",
			'NumberF'	=>	false,
			'HeadName'		=> 'CPM'
		),
		'viewability_percent'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Vimpression) / (SUM(sum_Uimpression) * 1.0) * 100, 2) AS ViewabilityPercent ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Vimpression) / (SUM(sum_Uimpression) * 1.0) * 100, 2) AS ViewabilityPercent ",
			'Name'		=>	"ViewabilityPercent",
			'OrderVal' 	=>	"ViewabilityPercent",
			'NumberF'	=>	false,
			'HeadName'		=> 'Viewability Percent'
		),
		'mesurable_percent'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Uimpression) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS MesuredPercent ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Uimpression) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS MesuredPercent ",
			'Name'		=>	"MesuredPercent",
			'OrderVal' 	=>	"MesuredPercent",
			'NumberF'	=>	false,
			'HeadName'		=> 'Imp View Mesured Percent'
		),
		'rebate_percent' => array(
			'SQL' 		=>	"",
			'SQLCSV' 	=>	"",
			'Name'		=>	"RebatePercent",
			'OrderVal' 	=>	"RebatePercent",
			'NumberF'	=>	true,
			'HeadName'		=> 'Rebate Percent'
		),
		'net_revenue' => array(
			'SQL' 		=>	"",
			'SQLCSV' 	=>	"",
			'Name'		=>	"NetRevenue",
			'OrderVal' 	=>	"NetRevenueOrder",
			'NumberF'	=>	false,
			'HeadName'		=> 'Net Revenue'
		),
		'ctr' 		=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Clicks) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS CTR ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Clicks) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS CTR ",
			'Name'		=>	"CTR",
			'OrderVal' 	=>	"CTR",
			'NumberF'	=>	false,
			'HeadName'		=> 'CTR'
		),
		'vtr'		=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Complete) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS VTR ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Complete) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS VTR ",
			'Name'		=>	"VTR",
			'OrderVal' 	=>	"VTR",
			'NumberF'	=>	false,
			'HeadName'		=> 'VTR'
		),
		'25perc'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_FirstQuartile) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS FIRST ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_FirstQuartile) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS FIRST ",
			'Name'		=>	"FIRST",
			'OrderVal' 	=>	"FIRST",
			'Base' 		=>	array("Complete25", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> '25%'
		),
		'50perc'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_Midpoint) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS MID ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Midpoint) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS MID ",
			'Name'		=>	"MID",
			'OrderVal' 	=>	"MID",
			'NumberF'	=>	false,
			'HeadName'		=> '50%'
		),
		'75perc'	=> array(
			'SQL' 		=>	", ROUND(SUM(sum_ThirdQuartile) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS THIRD ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_ThirdQuartile) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS THIRD ",
			'Name'		=>	"THIRD",
			'OrderVal' 	=>	"THIRD",
			'NumberF'	=>	false,
			'HeadName'		=> '75%'
		),
		'bids_cpm'	=> array(
			'SQL' 		=>	", SUM(sum_PMoney) / SUM(sum_BidResponses) AS BidResponseCPM ",
			'SQLCSV' 	=>	", SUM(sum_PMoney) / SUM(sum_BidResponses) AS BidResponseCPM ",
			'Name'		=>	"BidResponseCPM",
			'OrderVal' 	=>	"BidResponseCPM",
			'NumberF'	=>	false,
			'HeadName'		=> 'Bid Response CPM'
		),
		'bids_error'	=> array(
			'SQL' 		=>	", SUM(sum_BidError) AS BidError ",
			'SQLCSV' 	=>	", SUM(sum_BidError) AS BidError ",
			'Name'		=>	"BidError",
			'OrderVal' 	=>	"BidError",
			'NumberF'	=>	false,
			'HeadName'		=> 'Bid Error'
		),
		'bids_filtered'	=> array(
			'SQL' 		=>	", SUM(sum_BidsFiltered) AS BidsFiltered ",
			'SQLCSV' 	=>	", SUM(sum_BidsFiltered) AS BidsFiltered ",
			'Name'		=>	"BidsFiltered",
			'OrderVal' 	=>	"BidsFiltered",
			'NumberF'	=>	false,
			'HeadName'		=> 'Bids Filtered'
		),
		'bids_timeout'	=> array(
			'SQL' 		=>	", SUM(sum_BidsTimedOut) AS BidsTimedOut ",
			'SQLCSV' 	=>	", SUM(sum_BidsTimedOut) AS BidsTimedOut ",
			'Name'		=>	"BidsTimedOut",
			'OrderVal' 	=>	"BidsTimedOut",
			'NumberF'	=>	false,
			'HeadName'		=> 'Bids Timed Out'
		),
		'bids_unanswered'	=> array(
			'SQL' 		=>	", SUM(sum_BidsUnanswered) AS BidsUnanswered ",
			'SQLCSV' 	=>	", SUM(sum_BidsUnanswered) AS BidsUnanswered ",
			'Name'		=>	"BidsUnanswered",
			'OrderVal' 	=>	"BidsUnanswered",
			'NumberF'	=>	false,
			'HeadName'		=> 'Bids Unanswered'
		),
		'blocked'	=> array(
			'SQL' 		=>	", SUM(sum_Blocked)  AS Blocked ",
			'SQLCSV' 	=>	", SUM(sum_Blocked) AS Blocked ",
			'Name'		=>	"Blocked",
			'OrderVal' 	=>	"Blocked",
			'NumberF'	=>	false,
			'HeadName'		=> 'Blocked'
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
	global $druidUrl;
	$ch = curl_init( $druidUrl );
		
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