<?php

	$DimensionsSQL = array(
		'campaign_name' => array(
			'Name'	=>	"Deal AS CampaignName",
			'SearchName'	=>	"Deal",
			'GroupBy'		=>	"Deal",
			'OrderVal'		=>	"Deal",
			'HeadName'		=> 'Campaign'
		),
		'deal_id' => array(
			'Name'	=>	"Deal",
			'SearchName'	=>	"Deal",
			'GroupBy'		=>	"Deal",
			'OrderVal'		=>	"Deal",
			'HeadName'		=> 'Deal ID'
		),
		/*
		'agency' => array(
			'Name'	=>	"agency.name AS Agency",
			'SearchName'	=>	"agency.name",
			'InnerJoin'		=> 	array(),//array('agency' => "INNER JOIN agency ON agency.id = campaign.agency_id "),
			'GroupBy'		=>	"Agency",
			'OrderVal'		=>	"Agency",
			'InvalInner'	=> '',
			'HeadName'		=> 'Agency'
		),
		'advertiser' => array(
			'Name'	=>	"advertiser.name AS Advertiser",
			'SearchName'	=>	"advertiser.name",
			'InnerJoin'		=> 	array('advertiser' => "INNER JOIN advertiser ON advertiser.id = campaign.advertiser_id "),
			'GroupBy'		=>	"Advertiser",
			'OrderVal'		=>	"Advertiser",
			'InvalInner'	=> '',
			'HeadName'		=> 'Advertiser'
		),
		*/
		'country' => array(
			'Name'	=>	"Country",
			'SearchName'	=>	"Country",
			'GroupBy'		=>	"Country",
			'OrderVal'		=>	"Country",
			'HeadName'		=> 'Country'
		),
		'sales_vp' => array(
			'Name'	=>	"vp.nick AS SalesVP",
			'SearchName'	=>	"vp.id",
			'InnerJoin'		=> 	array(
				//'agency' => "INNER JOIN agency ON agency.id = campaign.agency_id ",
				'manager' => "INNER JOIN user manager ON manager.id = agency.sales_manager_id ",
				'manager_head' 	=> "INNER JOIN user as manager_head ON manager_head.id = manager.manager_id ",
				'vp' 	=> "INNER JOIN user as vp ON vp.id = manager_head.manager_id ",
			),
			'GroupBy'		=>	"SalesVP",
			'OrderVal'		=>	"SalesVP",
			'InvalInner'	=> '',
			'HeadName'		=> 'VP'
		),
		'sales_manager_head' => array(
			'Name'	=>	"manager_head.nick AS SalesManagerHead",
			'SearchName'	=>	"manager_head.id",
			'InnerJoin'		=> 	array(
				//'agency' => "INNER JOIN agency ON agency.id = campaign.agency_id ",
				'manager' => "INNER JOIN user manager ON manager.id = agency.sales_manager_id ",
				'manager_head' 	=> "INNER JOIN user as manager_head ON manager_head.id = manager.manager_id ",
				// 'smh'	=> "INNER JOIN user smh ON sm.sales_manager_head_id = smh.id "
			),
			'GroupBy'		=>	"SalesManagerHead",
			'OrderVal'		=>	"SalesManagerHead",
			'InvalInner'	=> '',
			'HeadName'		=> 'Sales Manager Head'
		),
		'sales_manager' => array(
			'Name'	=>	"user.nick AS SalesManager",
			'SearchName'	=>	"user.id",
			'InnerJoin'		=> 	array(
				//'agency' => "INNER JOIN agency ON agency.id = campaign.agency_id ",
				'user' => "INNER JOIN user ON user.id = agency.sales_manager_id ",
				//'sm' 	=> "INNER JOIN user sm ON sm.id = purchase_order.sales_manager_id "
			),
			'GroupBy'		=>	"SalesManager",
			'OrderVal'		=>	"SalesManager",
			'InvalInner'	=> '',
			'HeadName'		=> 'Sales Manager'
		),
		'dsp' => array(
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
	);

	$TimesSQL = array(
		'monthly' => array(
			'Name'	=> 	"DATE_TRUNC('month', __time) AS \"Month\"",
			'ShowName'		=> 	"Month",
			'GroupBy'		=>	"DATE_TRUNC('month', __time)",
			'OrderVal'		=>	"DATE_TRUNC('month', __time)"
		),
		'daily' => array(
			'Name'	=> 	"DATE_TRUNC('day', __time) AS \"Date\"",
			'ShowName'		=> 	"Date",
			'GroupBy'		=>	"DATE_TRUNC('day', __time)",
			'OrderVal'		=>	"DATE_TRUNC('day', __time)"
		),
		'hourly' => array(
			'Name'	=> 	"__time",
			'ShowName'		=> 	"Hour",
			'GroupBy'		=>	"__time",
			'OrderVal'		=>	"__time"
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
			'SQL' 		=>	", ROUND(SUM(sum_Vimpression) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS ViewabilityPercent ",
			'SQLCSV' 	=>	", ROUND(SUM(sum_Vimpression) / (SUM(sum_Impressions) * 1.0) * 100, 2) AS ViewabilityPercent ",
			'Name'		=>	"ViewabilityPercent",
			'OrderVal' 	=>	"ViewabilityPercent",
			'NumberF'	=>	false,
			'HeadName'		=> 'Viewability Percent'
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
		)
	);
