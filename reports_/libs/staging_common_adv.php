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
	$Locale = 'en_EN';
	$ReportsTable = '{ReportsTable}';

	$DimensionsSQL = array(
		'campaign_name' => array(
			'Name'	=>	"concat(campaign.name, '--',campaign.id) AS CampaignName",
			'SearchName'	=>	"campaign.name",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"CampaignName",
			'OrderVal'		=>	"CampaignName",
			'InvalInner'	=> '',
			'HeadName'		=> 'Campaign Name'
		),
		'campaign_id' => array(
			'Name'	=>	"campaign.id AS CampaignID",
			'SearchName'	=>	"campaign.id",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"CampaignID",
			'OrderVal'		=>	"CampaignID",
			'InvalInner'	=> '',
			'HeadName'		=> 'Campaign ID'
		),
		'campaign_start_at' => array(
			'Name'	=>	"campaign.start_at AS CampaignStartAt",
			'SearchName'	=>	"campaign.start_at",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"CampaignStartAt",
			'OrderVal'		=>	"CampaignStartAt",
			'InvalInner'	=> '',
			'HeadName'		=> 'Campaign Starts At'
		),
		'campaign_end_at' => array(
			'Name'	=>	"campaign.end_at AS CampaignEndAt",
			'SearchName'	=>	"campaign.end_at",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"CampaignEndAt",
			'OrderVal'		=>	"CampaignEndAt",
			'InvalInner'	=> '',
			'HeadName'		=> 'Campaign Ends At'
		),
		'deal_id' => array(
			'Name'	=>	"campaign.deal_id AS DealID",
			'SearchName'	=>	"campaign.deal_id",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"DealID",
			'OrderVal'		=>	"DealID",
			'InvalInner'	=> '',
			'HeadName'		=> 'Deal ID'
		),
		'type' => array(
			'Name'	=>	"REPLACE(REPLACE(campaign.type, '1', 'Deal'), '2', 'Campaing') AS Type",
			'SearchName'	=>	"campaign.type",
			'InnerJoin'		=> 	array(),
			'GroupBy'		=>	"Type",
			'OrderVal'		=>	"Type",
			'InvalInner'	=> '',
			'HeadName'		=> 'Type'
		),
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
		'country' => array(
			'Name'	=>	"country.nice_name AS Country",
			'SearchName'	=>	"country.id",
			'InnerJoin'		=> 	array('country' => "INNER JOIN country ON country.id = $ReportsTable.idCountry "),
			'GroupBy'		=>	"Country",
			'OrderVal'		=>	"Country",
			'InvalInner'	=> '',
			'HeadName'		=> 'Country'
		),
		'sales_vp' => array(
			'Name'	=>	"(case when (manager.roles like '%ROLE_ADMIN%' or manager.roles like '%ROLE_SALES_VP%') then manager.nick when manager.roles like '%ROLE_COUNTRY_MANAGER%' then COALESCE(manager_head.nick, manager.nick) when manager.roles like '%ROLE_SALES_MANAGER_HEAD%' then COALESCE(country_manager.nick, COALESCE(manager_head.nick, manager.nick)) else COALESCE(vp.nick, COALESCE(country_manager.nick, COALESCE(manager_head.nick, manager.nick))) end) AS SalesVP",
			'SearchName'	=>	"case when (manager.roles like '%ROLE_ADMIN%' or manager.roles like '%ROLE_SALES_VP%') then manager.id when manager.roles like '%ROLE_COUNTRY_MANAGER%' then COALESCE(manager_head.id, manager.id) when manager.roles like '%ROLE_SALES_MANAGER_HEAD%' then COALESCE(country_manager.id, COALESCE(manager_head.id, manager.id)) else COALESCE(vp.id, COALESCE(country_manager.id, COALESCE(manager_head.id, manager.id))) end",
			'InnerJoin'		=> 	array(
				//'agency' => "INNER JOIN agency ON agency.id = campaign.agency_id ",
				'manager'         => "LEFT JOIN user as manager ON manager.id = agency.sales_manager_id ",
				'manager_head'    => "LEFT JOIN user as manager_head ON manager_head.id = manager.manager_id ",
				'country_manager' => "LEFT JOIN user as country_manager ON country_manager.id = manager_head.manager_id ",
				'vp' 	          => "LEFT JOIN user as vp ON vp.id = country_manager.manager_id ",
			),
			'GroupBy'		=>	"SalesVP",
			'OrderVal'		=>	"SalesVP",
			'InvalInner'	=> '',
			'HeadName'		=> 'VP'
		),
		'sales_country_manager' => array(
			'Name'	=>	"(case when (manager.roles like '%ROLE_ADMIN%' or manager.roles like '%ROLE_SALES_VP%' or manager.roles like '%ROLE_COUNTRY_MANAGER%') then manager.nick when manager.roles like '%ROLE_SALES_MANAGER_HEAD%' then manager_head.nick else if (manager_head.roles like '%ROLE_COUNTRY_MANAGER%' or manager_head.roles like '%ROLE_SALES_VP%' or manager_head.roles like '%ROLE_COUNTRY_ADMIN%', manager_head.nick, country_manager.nick) end) AS SalesCountryManager",
			'SearchName'	=>	"case when (manager.roles like '%ROLE_ADMIN%' or manager.roles like '%ROLE_SALES_VP%' or manager.roles like '%ROLE_COUNTRY_MANAGER%') then manager.id when manager.roles like '%ROLE_SALES_MANAGER_HEAD%' then manager_head.id else if (manager_head.roles like '%ROLE_COUNTRY_MANAGER%' or manager_head.roles like '%ROLE_SALES_VP%' or manager_head.roles like '%ROLE_COUNTRY_ADMIN%', manager_head.id, country_manager.id) end",
			'InnerJoin'		=> 	array(
				'manager' => "LEFT JOIN user as manager ON manager.id = agency.sales_manager_id ",
				'manager_head' 	=> "LEFT JOIN user as manager_head ON manager_head.id = manager.manager_id ",
				'country_manager' 	=> "LEFT JOIN user as country_manager ON country_manager.id = manager_head.manager_id ",
			),
			'GroupBy'		=>	"SalesCountryManager",
			'OrderVal'		=>	"SalesCountryManager",
			'InvalInner'	=> '',
			'HeadName'		=> 'Sales Country Manager'
		),
		'sales_manager_head' => array(
			'Name'	=>	"(case when (manager.roles like '%ROLE_ADMIN%' or manager.roles like '%ROLE_SALES_VP%' or manager.roles like '%ROLE_COUNTRY_MANAGER%' or manager.roles like '%ROLE_SALES_MANAGER_HEAD%') then manager.nick else manager_head.nick end) AS SalesManagerHead",
			'SearchName'	=>	"case when (manager.roles like '%ROLE_ADMIN%' or manager.roles like '%ROLE_SALES_VP%' or manager.roles like '%ROLE_COUNTRY_MANAGER%' or manager.roles like '%ROLE_SALES_MANAGER_HEAD%') then manager.id else manager_head.id end",
			'InnerJoin'		=> 	array(
				//'agency' => "INNER JOIN agency ON agency.id = campaign.agency_id ",
				'manager' => "LEFT JOIN user as manager ON manager.id = agency.sales_manager_id ",
				'manager_head' 	=> "LEFT JOIN user as manager_head ON manager_head.id = manager.manager_id ",
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
		'ssp' => array(
			'Name'	=> 	"ssp.name AS SSP",
			'SearchName'	=> 	"$ReportsTable.SSP",
			'InnerJoin'		=> 	array('ssp' => "INNER JOIN ssp ON ssp.id = $ReportsTable.SSP "),
			'GroupBy'		=>	"SSP",
			'OrderVal'		=>	"SSP",
			'InvalInner'	=> '',
			'HeadName'		=> 'SSP'
		),
		'dsp' => array(
			'Name'	=>	"dsp.name AS DSP",
			'SearchName'	=> 	"dsp.id",
			'InnerJoin'		=> 	array('dsp' => "INNER JOIN dsp ON dsp.id = campaign.dsp_id "),
			'GroupBy'		=>	"DSP",
			'OrderVal'		=>	"DSP",
			'InvalInner'	=> '',
			'HeadName'		=> 'DSP'
		),
		'purchase_order' => array(
			'Name'	=>	"purchase_order.name AS PurchaseOrder",
			'SearchName'	=>	"purchase_order.name",
			'InnerJoin'		=> 	array('purchase_order' => "INNER JOIN purchase_order ON purchase_order.id = $ReportsTable.idPurchaseOrder "),
			'GroupBy'		=>	"PurchaseOrder",
			'OrderVal'		=>	"PurchaseOrder",
			'InvalInner'	=> '',
			'HeadName'		=> 'Purchase Order'
		),
		'cid' => array(
			'Name'	=>	"purchase_order.id AS PurchaseOrderId",
			'SearchName'	=>	"purchase_order.cid",
			'InnerJoin'		=> 	array('purchase_order' => "INNER JOIN purchase_order ON purchase_order.id = $ReportsTable.idPurchaseOrder "),
			'GroupBy'		=>	"PurchaseOrderId",
			'OrderVal'		=>	"PurchaseOrderId",
			'InvalInner'	=> '',
			'HeadName'		=> 'CID'
		),
		'creativity' => array(
			'Name'	=>	"creativity.name AS Creativity",
			'SearchName'	=>	"creativity.name",
			'InnerJoin'		=> 	array('creativity' => "INNER JOIN creativity ON creativity.campaign_id = $ReportsTable.idCreativity "),
			'GroupBy'		=>	"Creativity",
			'OrderVal'		=>	"Creativity",
			'InvalInner'	=> '',
			'HeadName'		=> 'Creativity'
		),
		'creativity_id' => array(
            'Name' => "creativity.lkqd_id AS CreativityId",
			'SearchName'	=>	"creativity.lkqd_id",
			'InnerJoin'		=> 	array('creativity' => "INNER JOIN creativity ON creativity.campaign_id = $ReportsTable.idCreativity "),
			'GroupBy'		=>	"CreativityId",
			'OrderVal'		=>	"CreativityId",
			'InvalInner'	=> '',
			'HeadName'		=> 'Creativity ID'
		),
        'reporting_view_users' => array(
            'Name'	=>	"(case when (user.id IN({{ReportingViewUsers}})) then user.nick else 'N/A' end) AS ReportingViewUser",
            'SearchName'	=>	"user.id",
            'InnerJoin'		=> 	array('user' => "INNER JOIN user ON user.id = agency.sales_manager_id "),
            'GroupBy'		=>	"ReportingViewUser",
            'OrderVal'		=>	"ReportingViewUser",
            'InvalInner'	=> '',
            'HeadName'		=> 'Reporting View User'
        ),
        'country_viewer' => array(
            'Name'	=>	"(case when (country.id IN({{CountryViewer}})) then country.nice_name else 'N/A' end) AS CountryViewer",
            'SearchName'	=>	"country.id",
            'InnerJoin'		=> 	array('country' => "INNER JOIN country ON country.id = reports.idCountry "),
            'GroupBy'		=>	"CountryViewer",
            'OrderVal'		=>	"CountryViewer",
            'InvalInner'	=> '',
            'HeadName'		=> 'Country Viewer'
        ),
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
		'request' 	=> array(
			'SQL' 		=>	", SUM($ReportsTable.Requests) AS Requests ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Requests), 0, '$Locale') AS Requests ",
			'Name'		=>	"Requests",
			'OrderVal' 	=>	"Requests",
			'Base' 		=>	array("Requests"),
			'NumberF'	=>	true,
			'HeadName'	=> 'Requests'
		),
		'impressions' => array(
			'SQL' 		=>	", SUM($ReportsTable.Impressions) AS Impressions ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Impressions), 0, '$Locale') AS Impressions ",
			'Name'		=>	"Impressions",
			'OrderVal' 	=>	"Impressions",
			'Base' 		=>	array("Impressions"),
			'NumberF'	=>	true,
			'HeadName'		=> 'Impressions'
		),
		'viewable_impressions' => array(
			'SQL' 		=>	", SUM($ReportsTable.VImpressions) AS VImpressions ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.VImpressions), 0, '$Locale') AS VImpressions ",
			'Name'		=>	"VImpressions",
			'OrderVal' 	=>	"VImpressions",
			'Base' 		=>	array("VImpressions"),
			'NumberF'	=>	true,
			'HeadName'		=> 'Viewable Impressions'
		),
		'revenue'	=> array(
			'SQL' 		=>	", SUM($ReportsTable.Revenue) AS Revenue, SUM($ReportsTable.Revenue) AS RevenueOrder ",
			'SQLCSV' 	=>	", concat('$', FORMAT( SUM($ReportsTable.Revenue), 2, '$Locale') ) AS Revenue, SUM($ReportsTable.Revenue) AS RevenueOrder ",
			'Name'		=>	"Revenue",
			'OrderVal' 	=>	"RevenueOrder",
			'Base' 		=>	array("Revenue"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Revenue'
		),
		'bids' 		=> array(
			'SQL' 		=>	", SUM($ReportsTable.Bids) AS Bids ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Bids), 0, '$Locale') AS Bids ",
			'Name'		=>	"Bids",
			'OrderVal' 	=>	"Bids",
			'Base' 		=>	array("Bids"),
			'NumberF'	=>	true,
			'HeadName'	=> 'Bids'
		),
		'clicks' 	=> array(
			'SQL' 		=>	", SUM($ReportsTable.Clicks) AS Clicks ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Clicks), 0, '$Locale') AS Clicks ",
			'Name'		=>	"Clicks",
			'OrderVal' 	=>	"Clicks",
			'Base' 		=>	array("Clicks"),
			'NumberF'	=>	true,
			'HeadName'	=> 'Clicks'
		),
		'complete_views' => array(
			'SQL' 		=>	", SUM($ReportsTable.CompleteV) AS CompleteV ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.CompleteV), 0, '$Locale') AS CompleteV ",
			'Name'		=>	"CompleteV",
			'OrderVal' 	=>	"CompleteV",
			'Base' 		=>	array("CompleteV"),
			'NumberF'	=>	true,
			'HeadName'	=> 'Complete Views'
		),
		'viewed25' => array(
			'SQL' 		=>	", SUM($ReportsTable.Complete25) AS Complete25 ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Complete25), 0, '$Locale') AS Complete25 ",
			'Name'		=>	"Complete25",
			'OrderVal' 	=>	"Complete25",
			'Base' 		=>	array("Complete25"),
			'NumberF'	=>	false,
			'HeadName'	=> '25% Views'
		),
		'viewed50' => array(
			'SQL' 		=>	", SUM($ReportsTable.Complete50) AS Complete50 ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Complete50), 0, '$Locale') AS Complete50 ",
			'Name'		=>	"Complete50",
			'OrderVal' 	=>	"Complete50",
			'Base' 		=>	array("Complete50"),
			'NumberF'	=>	false,
			'HeadName'	=> '50% Views'
		),
		'viewed75' => array(
			'SQL' 		=>	", SUM($ReportsTable.Complete75) AS Complete75 ",
			'SQLCSV' 	=>	", FORMAT(SUM($ReportsTable.Complete75), 0, '$Locale') AS Complete75 ",
			'Name'		=>	"Complete75",
			'OrderVal' 	=>	"Complete75",
			'Base' 		=>	array("Complete75"),
			'NumberF'	=>	false,
			'HeadName'	=> '75% Views'
		),
		'rebate_cost' 			=> array(
			'SQL' 		=>	", SUM($ReportsTable.Rebate) AS Rebate, SUM($ReportsTable.Rebate) AS RebateOrder ",
			'SQLCSV' 	=>	", concat('$', FORMAT(SUM($ReportsTable.Rebate), 2, '$Locale') ) AS Rebate, SUM($ReportsTable.Rebate) AS RebateOrder ",
			'Name'		=>	"Rebate",
			'OrderVal' 	=>	"RebateOrder",
			'Base' 		=>	array("Rebate"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Rebate Cost'
		),
		'cpm'	=> array(
			'SQL' 		=>	", round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.Impressions) * 1000),2) AS CPM ",
			'SQLCSV' 	=>	", concat('$', FORMAT( ( SUM($ReportsTable.Revenue) / SUM($ReportsTable.Impressions) * 1000), 2, '$Locale') ) AS CPM ",
			'Name'		=>	"CPM",
			'OrderVal' 	=>	"CPM",
			'Base' 		=>	array("Revenue","Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'CPM'
		),
		'cpv'	=> [
			'SQL' 		=>	", round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.CompleteV)),2) AS CPV ",
			'SQLCSV' 	=>	", concat('$', FORMAT( ( SUM($ReportsTable.Revenue) / SUM($ReportsTable.CompleteV)), 2, '$Locale') ) AS CPV ",
			'Name'		=>	"CPV",
			'OrderVal' 	=>	"CPV",
			'Base' 		=>	["Revenue","CompleteV"],
			'NumberF'	=>	false,
			'HeadName'		=> 'CPV'
		],
		'cpc'	=> [
			'SQL' 		=>	", round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.Clicks)),2) AS CPC ",
			'SQLCSV' 	=>	", concat('$', FORMAT( ( SUM($ReportsTable.Revenue) / SUM($ReportsTable.Clicks)), 2, '$Locale') ) AS CPC ",
			'Name'		=>	"CPC",
			'OrderVal' 	=>	"CPC",
			'Base' 		=>	["Revenue","Clicks"],
			'NumberF'	=>	false,
			'HeadName'		=> 'CPC'
		],
		'vcpm'	=> [
			'SQL' 		=>	", round((SUM($ReportsTable.Revenue)/SUM($ReportsTable.VImpressions) * 1000),2) AS vCPM ",
			'SQLCSV' 	=>	", concat('$', FORMAT( ( SUM($ReportsTable.Revenue) / SUM($ReportsTable.VImpressions) * 1000), 2, '$Locale') ) AS vCPM ",
			'Name'		=>	"vCPM",
			'OrderVal' 	=>	"vCPM",
			'Base' 		=>	["Revenue","VImpressions"],
			'NumberF'	=>	false,
			'HeadName'		=> 'vCPM'
		],
		'viewability_percent'	=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.VImpressions) / SUM($ReportsTable.Impressions) * 100), 2) AS ViewabilityPercent ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.VImpressions) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale') , '%') AS ViewabilityPercent ",
			'Name'		=>	"ViewabilityPercent",
			'OrderVal' 	=>	"ViewabilityPercent",
			'Base' 		=>	array("Impressions","VImpressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Viewability Percent'
		),
		'rebate_percent' => array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Rebate) / SUM($ReportsTable.Revenue) * 100), 2) AS RebatePercent ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Rebate) / SUM($ReportsTable.Revenue) * 100), 2, '$Locale') , '%') AS RebatePercent ",
			'Name'		=>	"RebatePercent",
			'OrderVal' 	=>	"RebatePercent",
			'Base' 		=>	array("Rebate", "Revenue"),
			'NumberF'	=>	true,
			'HeadName'		=> 'Rebate Percent'
		),
		'net_revenue' => array(
			'SQL' 		=>	", (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Rebate)) AS NetRevenue, (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Rebate)) AS NetRevenueOrder ",
			'SQLCSV' 	=>	", concat('$', FORMAT( (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Rebate)), 2, '$Locale') ) AS NetRevenue, (SUM($ReportsTable.Revenue) - SUM($ReportsTable.Rebate)) AS NetRevenueOrder ",
			'Name'		=>	"NetRevenue",
			'OrderVal' 	=>	"NetRevenueOrder",
			'Base' 		=>	array("Revenue","Rebate"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Net Revenue'
		),
		'ctr' 		=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Clicks) / SUM($ReportsTable.Impressions) * 100), 2) AS CTR ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Clicks) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS CTR ",
			'Name'		=>	"CTR",
			'OrderVal' 	=>	"CTR",
			'Base' 		=>	array("Clicks", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'CTR'
		),
		'vtr'		=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.CompleteV) / SUM($ReportsTable.Impressions) * 100) ,2) AS VTR ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.CompleteV) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS VTR ",
			'Name'		=>	"VTR",
			'OrderVal' 	=>	"VTR",
			'Base' 		=>	array("CompleteV", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'VTR'
		),
		'25perc'	=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Complete25) / SUM($ReportsTable.Impressions) * 100) ,2) AS FIRST ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Complete25) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS FIRST ",
			'Name'		=>	"FIRST",
			'OrderVal' 	=>	"FIRST",
			'Base' 		=>	array("Complete25", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> '25%'
		),
		'50perc'	=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Complete50) / SUM($ReportsTable.Impressions) * 100) ,2) AS MID ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Complete50) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS MID ",
			'Name'		=>	"MID",
			'OrderVal' 	=>	"MID",
			'Base' 		=>	array("Complete50", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> '50%'
		),
		'75perc'	=> array(
			'SQL' 		=>	", round( (SUM($ReportsTable.Complete75) / SUM($ReportsTable.Impressions) * 100) ,2) AS THIRD ",
			'SQLCSV' 	=>	", concat( FORMAT( (SUM($ReportsTable.Complete75) / SUM($ReportsTable.Impressions) * 100), 2, '$Locale'), '%') AS THIRD ",
			'Name'		=>	"THIRD",
			'OrderVal' 	=>	"THIRD",
			'Base' 		=>	array("Complete75", "Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> '75%'
		),
		'budget_oc'		=> array(
			'SQL' 		=>	", ROUND( IFNULL( $ReportsTable.Budget, '0' ), 2 ) AS Budget ",
			'SQLCSV' 	=>	", concat('$', FORMAT( IFNULL( $ReportsTable.Budget, '0' ), 2, '$Locale') ) AS Budget ",
			'Name'		=>	"Budget",
			'OrderVal' 	=>	"Budget",
			'Base' 		=>	array("Revenue","Impressions"),
			'NumberF'	=>	false,
			'HeadName'		=> 'Budget',
			'InnerJoin'		=> 	array(
				'purchase_order' => "INNER JOIN purchase_order ON purchase_order.id = campaign.purchase_order_id ",
				'budget' => "INNER JOIN budget ON budget.purchase_order_id = purchase_order.id "
			),
		),
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
