<?php

@session_start();
ini_set('display_errors', 0);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST', 1);
define('DEBUG', true);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require('/var/www/html/login/config_local.php');
}
require('/var/www/html/login/reports_/adv/config.php');
require('/var/www/html/login/db.php');

$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

$campaignBudgets = [];
$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
require('/var/www/html/login/reports_/adv/common.php');
require('/var/www/html/login/admin/lkqdimport/common_staging.php');

$fromDate = new DateTime(date('2022-04-30 00:00'));
$toDate   = new DateTime(date('2022-04-30 23:00'));

// synchronizeCampaignsWithNewBudget();
// $campaignIds = getCampaignsIdsWithBudgetOverflow();
// synchronizeCampaignsWithBudgetOverflow($campaignIds);
$campaignIds = syncReport($fromDate, $toDate, [11832]);
// synchronizeCampaignsWithBudgetOverflow($campaignIds);
// updateReportCards($db3, $fromDate->format('Y-m-d'));

function calcPercents($Perc, $Impressions, $Complete)
{
    if ($Perc == 25) {
        $VarP = rand(2100, 2400) / 1000;
    } elseif ($Perc == 50) {
        $VarP = rand(1500, 1640) / 1000;
    } else {
        $VarP = rand(1150, 1260) / 1000;
    }

    $Diff = $Impressions - $Complete;
    $Result = $Impressions - round(($Diff / $VarP));

    if ($Result < $Impressions) {
        if ($Result > $Complete) {
            return $Result;
        } else {
            return $Complete;
        }
    } else {
        return $Impressions;
    }
}

function getCampaignBudgets()
{
    global $db;
    global $campaignBudgets;

    if (!$campaignBudgets) {
        $sql = "SELECT campaign_id, budget, budget_consumed, available_budget FROM campaign_budget_info";
        $allBudgets = $db->getAll($sql);

        if ($allBudgets) {
            $campaignBudgets = array_column($allBudgets, null, 'campaign_id');
        }
    }

    return $campaignBudgets;
}

function campaignHasAvailableBudget($idCampaign)
{
    $campaignBudgets = getCampaignBudgets($idCampaign);

    return isset($campaignBudgets[$idCampaign]) && $campaignBudgets[$idCampaign]['budget'] > 0;
}

function getCampaignAvailableBudget($idCampaign)
{
    $campaignBudgets = getCampaignBudgets($idCampaign);

    if (!isset($campaignBudgets[$idCampaign])) {
        return 0;
    }

    $campaignBudget = $campaignBudgets[$idCampaign];

    if ($campaignBudget['budget_consumed'] <= 0) {
        return $campaignBudget['budget'];
    }

    return $campaignBudget['available_budget'] <= 0 ? 0 : $campaignBudget['available_budget'];
}

function updateCampaignBudget($idCampaign, $value)
{
    global $campaignBudgets;

    if (
        !isset($campaignBudgets[$idCampaign])
        || $campaignBudgets[$idCampaign]['available_budget'] <= 0
    ) {
        return;
    }

    $campaignBudgets[$idCampaign]['budget_consumed'] += $value;
    $campaignBudgets[$idCampaign]['available_budget'] -= $value;
}

function synchronizeCampaignsWithBudgetOverflow($campaignIds)
{
    $time =  microtime(true);
    debug('Start: synchronizeCampaignsWithBudgetOverflow');
    if ($campaignIds) {
        sanitizeReportsBudgetConsumed($campaignIds);
        sanitizeRebate($campaignIds);
    }
    debug('Finish: synchronizeCampaignsWithBudgetOverflow');
    debugTime($time);
}

function getCampaignsIdsWithBudgetOverflow(): array
{
    global $db;
    $sql = 'SELECT 
                c.id
            FROM 
                campaign c,
                campaign_budget_info cbi
            WHERE
                cbi.campaign_id = c.id
            AND	available_budget < 0';

    $campaignIds = $db->getAll($sql, 'id');
    return $campaignIds;
}

function dd(...$messages)
{
    foreach ($messages as $message) {
        debug($message);
    }

    exit();
}

function debug($message)
{
    if (! DEBUG) {
        return;
    }

    if (is_array($message)) {
        var_dump($message);
    } else {
        echo($message);
    }

    echo PHP_EOL;
}

function debugTime($time)
{
    if (!DEBUG) {
        return;
    }

    echo "\e[0;32mTotal Execution Time: \e[0m" . ( number_format(microtime(true) - $time, 2)) . ' Sec '
         . PHP_EOL . '--------------------------------------------------------' . PHP_EOL;
}

function synchronizeCampaignsWithNewBudget()
{
    $time =  microtime(true);
    debug('Start synchronizeCampaignsWithNewBudget');
    $campaigns = getCampaignsWithNewBudgets();
    debug(sprintf('Campaigns with new budgets: %s ', sizeof($campaigns)));

    if ($campaigns) {
        foreach ($campaigns as $campaign) {
            restartBudgetConsumed($campaign['idCampaing']);
        }
    }

    debug('Finish synchronizeCampaignsWithNewBudget');
    debugTime($time);
}

function restartBudgetConsumed($campaignId)
{
    global $db;
    $sql = 'UPDATE reports r
            SET 
                budgetConsumed = revenue,
                budgetReached = FALSE
            WHERE r.idCampaing = %campaign_id%';

    $sql = str_replace("%campaign_id%", $campaignId, $sql);
    $db->query($sql);
}

function getCampaignsWithNewBudgets(): array
{
    global $db;

    $sql = 'SELECT idCampaing from reports where budgetReached = true';

    $campaignIds = $db->getAll($sql, 'idCampaing') ?? [];

    if (!$campaignIds) {
        return [];
    }

    $sql = 'SELECT * FROM (
                SELECT 
                    r.idCampaing,
                    c.deal_id,
                    sum(budgetConsumed) AS budgetConsumed,
                    CONVERT(c.budget, DECIMAL) as budget,
                    r.date,
                    r.hour
                FROM 
                    reports r,
                    campaign c
                WHERE	
                    c.id = r.idCampaing
                    AND r.idCampaing IN (%campaign_ids%)
                GROUP BY 
                    idCampaing
            ) AS oudated_report_rows 
            WHERE
                budget > budgetConsumed';

    $sql = str_replace("%campaign_ids%", implode(',', $campaignIds), $sql);

    return $db->getAll($sql) ?? [];
}

function sanitizeReportsBudgetConsumed(array $campaignIds)
{
    global $db;
    foreach ($campaignIds as $campaignId) {
        /* UPDATE THE FIRST REPORT OVERFLOWED ROW
           BASED ON THE CAMPAIGN BUDGET */
        $sql = 'UPDATE reports r
                    INNER JOIN (
                        SELECT 
                            report_id,
                            c.id,
                            c.budget,
                            budget_historic.budgetConsumed,
                            budget_historic.balance,
                            c.budget - (budget_historic.balance - budgetConsumed) as realBudgetConsumed,
                            budget_historic.date,
                            budget_historic.hour
                        FROM 
                            campaign AS c,
                            (
                            SELECT 
                                r.id as report_id,
                                r.idCampaing as campaing_id,
                                r.budgetConsumed,
                                r.date,
                                r.hour,
                                @b := CAST(@b + r.budgetConsumed AS DECIMAL(10,5)) AS balance
                            FROM
                            (SELECT @b := 0.0) AS dummy 
                            CROSS JOIN reports AS r
                            WHERE r.idCampaing = %campaign_id%
                            ORDER BY date, hour ASC
                            ) AS budget_historic
                        WHERE 
                        c.id = budget_historic.campaing_id
                        AND c.ssp_id = 4
                        AND c.budget > 0
                        AND budget_historic.balance >= c.budget
                        GROUP BY id
                        ORDER BY date, hour ASC
                    ) b ON r.id = b.report_id
                    SET r.budgetConsumed = b.realBudgetConsumed,
                        r.budgetReached  = true';

        $sql = str_replace("%campaign_id%", $campaignId, $sql);
        $db->query($sql);

        $sql = 'UPDATE reports r
                INNER JOIN (SELECT * FROM (
                    SELECT 
                        r.id AS report_id,
                        r.idCampaing AS campaing_id,
                        r.budgetConsumed,
                        @b := CAST(@b + r.budgetConsumed AS DECIMAL(10,5)) AS balance,
                        c.budget,
                        date,
                        hour
                    FROM
                    (SELECT @b := 0.0) AS dummy 
                    CROSS JOIN reports AS r
                    INNER JOIN campaign AS c ON c.id = r.idCampaing
                    WHERE 
                        r.idCampaing = %campaign_id%
                        AND c.budget > 0
                        ORDER BY date, hour
                ) AS report_log WHERE balance > budget) b ON r.id = b.report_id 
                SET r.budgetConsumed = 0';
        $sql = str_replace("%campaign_id%", $campaignId, $sql);
        $db->query($sql);
    }
}


function sanitizeRebate(array $campaignIds)
{
    global $db;
    $sql = 'UPDATE 
            reports r
        SET
            r.rebate = CAST(r.budgetConsumed * r.rebatePercentage / 100 AS DECIMAL(10,5))
        WHERE
            r.rebatePercentage > 0
            AND r.idCampaing IN (%campaign_ids%);';

    $sql = str_replace("%campaign_ids%", implode(', ', $campaignIds), $sql);
    $db->query($sql);
}

function syncReport(DateTime $fromDate, DateTime $toDate, $filterCampaignIds = []): array
{
    $time =  microtime(true);
    debug('Start: syncReport');

    global $db, $db3;
    $Date = $fromDate->format('Y-m-d');
    $Hour = $toDate->format('H');

    $Multipliers = array(
        '1056632' => 2,//
        '1056635' => 2,//
        '1056631' => 2,//
        '1056636' => 2,//
        '1056634' => 2,//
        '1056633' => 50,//
        '1056630' => 30,//
        '1056427' => 120,
        '1056629' => 13,//
        // '1056905' => 20,
        '1056907' => 29,
        '1056906' => 29,
        '1056824' => 80,//
        '1060885' => 5,
        '1066238' => 4,
        '1066240' => 4,
        '1066237' => 4,
        '1066236' => 4,
    );

    $ActiveDemandTags = [];
    $ActiveDemandTags2 = [];
    $CampaignsIds = [];
    $CampaingData = [];
    $avoidCreativitiesIds = $_ENV['LOAD_LKQD_AVOID_CREATIVITIES_IDS'] ?? [];

    $filterCampaignSQL = '';
    $avoidCreativitiesIdsSQL = '';

    if ($avoidCreativitiesIds) {
        $avoidCreativitiesIdsSQL = " AND dt.id NOT IN ($avoidCreativitiesIds)";
    }

    if ($filterCampaignIds) {
        $filterCampaignSQL = ' AND c.id IN (' . implode(',', $filterCampaignIds) . ')';
    };

    $filterDemandTagsIds = [];

    if ($filterCampaignIds) {
        $campaignIds = implode(',', $filterCampaignIds);
        $filterCampaignSQL = ' AND c.id IN (' . $campaignIds . ')';
        $filterDemandTagsIds = $db3->getAll(
            "SELECT demand_tag_id FROM creativity dt WHERE campaign_id IN ($campaignIds) AND demand_tag_id IS NOT NULL",
            'demand_tag_id'
        );
    };
$filterDemandTagsIds = [1071516];
    $sql = "SELECT 
                c.*,
                dt.demand_tag_id,
                po.sales_manager_id
            FROM 
                campaign c,
                creativity dt,
                purchase_order po
            WHERE 
                dt.campaign_id = c.id
            AND po.id = c.purchase_order_id
            AND dt.demand_tag_id IS NOT NULL
            AND c.ssp_id = 4 

            $filterCampaignSQL
    ";
    //            $avoidCreativitiesIdsSQL

    $results = $db3->query($sql);

    if ($db3->num_rows($results) > 0) {
        $camps = [];

        while ($Camp = $db3->fetch_array($results)) {
            $idCamp = $Camp['id'];
            $ActiveDemandTags[$idCamp] = $Camp['demand_tag_id'];
            $ActiveDemandTags2[$Camp['demand_tag_id']] = $idCamp;

            $CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
            $CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];
            $CampaingData[$idCamp]['Type'] = $Camp['type'];
            $CampaingData[$idCamp]['Budget'] = $Camp['budget'];
            $CampaingData[$idCamp]['PurchaseOrderId'] = $Camp['purchase_order_id'];
            $CampaingData[$idCamp]['SalesManagerId'] = $Camp['sales_manager_id'];

            if (isset($Camp['cpm']) && $Camp['cpm'] > 0) {
                $CampaingData[$idCamp]['CPM'] = $Camp['cpm'];
            } else {
                $CampaingData[$idCamp]['CPM'] = 0;
            }
            if (isset($Camp['cpv']) && $Camp['cpv'] > 0) {
                $CampaingData[$idCamp]['CPV'] = $Camp['cpv'];
            } else {
                $CampaingData[$idCamp]['CPV'] = 0;
            }
            if (isset($Camp['cpc']) && $Camp['cpc'] > 0) {
                $CampaingData[$idCamp]['CPC'] = $Camp['cpc'];
            } else {
                $CampaingData[$idCamp]['CPC'] = 0;
            }
            if (isset($Camp['vcpm']) && $Camp['vcpm'] > 0) {
                $CampaingData[$idCamp]['vCPM'] = $Camp['vcpm'];
            } else {
                $CampaingData[$idCamp]['vCPM'] = 0;
            }
            $CampaingData[$idCamp]['Type'] = $Camp['type'];
            $CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];

            $countryId = 999;
            $sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
            if ($db3->getOne($sql) == 1) {
                $sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
                $countryId = $db3->getOne($sql);
            }

            $CampaingData[$idCamp]['Country'] = $countryId;

            if ((isset($Camp['vtr_from']) && $Camp['vtr_from'] > 0) && (isset($Camp['vtr_to']) && $Camp['vtr_to'] > 0)) {
                $CampaingData[$idCamp]['VTRFrom'] = $Camp['vtr_from'];
                $CampaingData[$idCamp]['VTRTo'] = $Camp['vtr_to'];
                $CampaingData[$idCamp]['CVTR'] = true;
            } else {
                $CampaingData[$idCamp]['CVTR'] = false;
            }

            if (isset($Camp['ctr_to']) && $Camp['ctr_to'] > 0) {
                $CampaingData[$idCamp]['CTRFrom'] = $Camp['ctr_from'];
                $CampaingData[$idCamp]['CTRTo'] = $Camp['ctr_to'];
                $CampaingData[$idCamp]['CCTR'] = true;
            } else {
                $CampaingData[$idCamp]['CCTR'] = false;
            }

            if ((isset($Camp['viewability_from']) && $Camp['viewability_from'] > 0) && (isset($Camp['viewability_to']) && $Camp['viewability_to'] > 0)) {
                $CampaingData[$idCamp]['ViewFrom'] = $Camp['viewability_from'];
                $CampaingData[$idCamp]['ViewTo'] = $Camp['viewability_to'];
                $CampaingData[$idCamp]['CView'] = true;
            } else {
                $CampaingData[$idCamp]['CView'] = false;
            }
        }
    }

    $ImportData = getAdvertiserDemandReportCSVByDateRange($fromDate, $toDate, $filterDemandTagsIds);

    if ($ImportData === false) {
        logIn();
        $ImportData = getAdvertiserDemandReportCSVByDateRange($fromDate, $toDate, $filterDemandTagsIds);
    }

    $Bids = 0;

    if ($ImportData !== false) {
        $N = 0;
        $Last = false;
        foreach ($ImportData as $DataK => $DataL) {
            $Nn = 0;
            foreach ($DataL as $Line) {
                if ($N > 0) {
                    if ($Nn == 0) {
                        if (strpos($Line, 'T') !== false) {
                            $arTime = explode("T", $Line);
                            $Hour = $arTime[1];
                            $Date = $arTime[0];
                        } else {
                            $Last = true;
                            break;
                        }
                    }

                    if ($Nn == 1) {
                        $TagId = $Line;
                    }

                    if ($Nn == 3) {
                        $Requests = takeComa($Line);
                    }
                    if ($Nn == 4) {
                        $Impressions = takeComa($Line);
                    }
                    if ($Nn == 5) {
                        $VImpressions = takeComa($Line);
                    }
                    if ($Nn == 6) {
                        $CompleteV = takeComa($Line);
                    }
                    if ($Nn == 7) {
                        $Clicks = takeComa($Line);
                    }
                    if ($Nn == 8) {
                        $Revenue = takeMoney(takeComa($Line));
                    }
                    if ($Nn == 9) {
                        $Complete25 = takeComa($Line);
                    }
                    if ($Nn == 10) {
                        $Complete50 = takeComa($Line);
                    }
                    if ($Nn == 11) {
                        $Complete75 = takeComa($Line);
                    }
                }
                $Nn++;
            }

            if ($N > 0 && $Last === false) {
                if (isset($ActiveDemandTags2[$TagId])) {
                    $idCampaing = $ActiveDemandTags2[$TagId];
                    $CampaignsIds[$idCampaing] = $idCampaing;
                    $RebatePercent = $CampaingData[$idCampaing]['Rebate'];
                    $DealID = $CampaingData[$idCampaing]['DealId'];
                    $idCountry = $CampaingData[$idCampaing]['Country'];
                    $Type = $CampaingData[$idCampaing]['Type'];
                    $CPM = $CampaingData[$idCampaing]['CPM'];
                    $CPV = $CampaingData[$idCampaing]['CPV'];
                    $CPC = $CampaingData[$idCampaing]['CPC'];
                    $vCPM = $CampaingData[$idCampaing]['vCPM'];
                    $AgencyId = $CampaingData[$idCampaing]['AgencyId'];
                    $PurchaseOrderId = $CampaingData[$idCampaing]['PurchaseOrderId'];
                    $campaignBudget = $CampaingData[$idCampaing]['Budget'];
                    $salesManagerId = $CampaingData[$idCampaing]['SalesManagerId'];

                    $CVTR = $CampaingData[$idCampaing]['CVTR'];
                    $CCTR = $CampaingData[$idCampaing]['CCTR'];
                    $CView = $CampaingData[$idCampaing]['CView'];

                    if ($RebatePercent > 0 && $Revenue > 0) {
                        $Rebate = ($Revenue * $RebatePercent) / 100;
                    } else {
                        $Rebate = 0;
                    }

                    $Multi = 1;
                    if (array_key_exists($TagId, $Multipliers)) {
                        $Multi = $Multipliers[$TagId];

                        if ($Multi > 100) {
                            if (intval($Hour) % 2 == 0) {
                                $Multi = $Multi - $Hour;
                            } else {
                                $Multi = $Multi + $Hour;
                            }
                        } else {
                            if (intval($Hour) % 2 == 0) {
                                $Multi = $Multi - ceil($Hour / 5);
                            } else {
                                $Multi = $Multi + ceil($Hour / 5);
                            }
                        }

                        $Requests = $Requests * $Multi;
                        $Impressions = $Impressions * $Multi;
                        $VImpressions = $VImpressions * $Multi;
                        $CompleteV = $CompleteV * $Multi;

                        // No multiply clicks for this deal id
                        if ('1060885' != $TagId) {
                            $Clicks = $Clicks * $Multi;
                        }

                        $Revenue = $Revenue * $Multi;
                        $Complete25 = $Complete25 * $Multi;
                        $Complete50 = $Complete50 * $Multi;
                        $Complete75 = $Complete75 * $Multi;
                    }

                    $CompleteVPerc = 0;

                    /*
                    $RandVI = rand(7300,7500)/10000; //Cheil Panama
                    $RandVI2 = rand(7400,7800)/10000; //RR_GrupoP
                    $RandVI3 = rand(3900,4200)/10000; //AM_adbid_CO_Claro_ParaTiPrimero_Marzo
                    $RandVI4 = rand(3400,3800)/10000; //AM_adbid_CO_Claro_EstrategiaDigital
                    $RandVI5 = rand(7100,7400)/10000;
                    $RandVI6 = rand(7100,7300)/10000; //MediacaOnline_BR_MOL_Video1_75%_completes - Dickens_Prudence_MX_10
                    $RandVI7 = rand(7100,7300)/10000; //MediacaOnline_BR_MOL_Video2_25%_completes
                    */
                    $sql = "SELECT id FROM reports WHERE SSP = 4 AND idCreativity = (SELECT id from demand_tag where demand_tag_id = '$TagId' ORDER BY ID DESC LIMIT 1) AND Date = '$Date' AND Hour = '$Hour' LIMIT 1";
                    $idStat = $db->getOne($sql);

                    if (intval($idStat) == 0) {
                        if ($Type == 2) {
                            if ($CCTR === true) {
                                $CTRFrom = $CampaingData[$idCampaing]['CTRFrom'] * 100;
                                $CTRTo = $CampaingData[$idCampaing]['CTRTo'] * 100;

                                $RandCTR = rand($CTRFrom, $CTRTo) / 10000;
                                $Clicks = intval($Impressions * $RandCTR);
                            }

                            if ($CVTR === true) {
                                $VTRFrom = $CampaingData[$idCampaing]['VTRFrom'] * 100;
                                $VTRTo = $CampaingData[$idCampaing]['VTRTo'] * 100;

                                $RandVTR = rand($VTRFrom, $VTRTo) / 10000;
                                $CompleteV = intval($Impressions * $RandVTR);
                                $CompleteVPerc = $RandVTR;

                                $Complete25 = calcPercents(25, $Impressions, $CompleteV);
                                $Complete50 = calcPercents(50, $Impressions, $CompleteV);
                                $Complete75 = calcPercents(75, $Impressions, $CompleteV);
                            }

                            if ($CView === true) {
                                $ViewFrom = $CampaingData[$idCampaing]['ViewFrom'] * 100;
                                $ViewTo = $CampaingData[$idCampaing]['ViewTo'] * 100;

                                $RandView = rand($ViewFrom, $ViewTo) / 10000;
                                $VImpressions = intval($Impressions * $RandView);
                            }

                            if ($Impressions > 0 && $CPM > 0) {
                                $Revenue = $Impressions * $CPM / 1000;
                            } elseif ($CompleteV > 0 && $CPV > 0) {
                                $Revenue = $CompleteV * $CPV;
                            } elseif ($Clicks > 0 && $CPC > 0) {
                                $Revenue = $Clicks * $CPC;
                            } elseif ($VImpressions > 0 && $vCPM > 0) {
                                $Revenue = $VImpressions * $vCPM / 1000;
                            } else {
                                $Revenue = 0;
                            }
                        }

                        $budgetConsumed = $Revenue;
                        $budgetReached = 0;

                        if ($Revenue > 0 && campaignHasAvailableBudget($idCampaing)) {
                            $availableBudget = getCampaignAvailableBudget($idCampaing);

                            if ($Revenue >= $availableBudget) {
                                $budgetConsumed = $availableBudget;
                                if ($budgetConsumed > 0) {
                                    $budgetReached = 1;
                                }
                            }

                            if ($RebatePercent > 0 && $budgetConsumed > 0) {
                                $Rebate = ($budgetConsumed * $RebatePercent) / 100;
                            }
                        }

                        $sql = "INSERT INTO reports
                        (SSP, budgetReached, idSalesManager, idPurchaseOrder, idCampaing, idCreativity, idCountry, Requests, Bids, Impressions, Revenue, budgetConsumed, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, rebatePercentage, Date, Hour) 
                        VALUES (4, $budgetReached, $salesManagerId, $PurchaseOrderId, $idCampaing, (SELECT id from demand_tag where demand_tag_id = '$TagId' ORDER BY ID DESC LIMIT 1), $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$budgetConsumed', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, $RebatePercent, '$Date', '$Hour')";
                        $db->query($sql);
                        echo $sql;
                        //updateCampaignBudget($idCampaing, $budgetConsumed);
                    } else {
                        if ($Type == 2) {
                            $Impressions = intval($Impressions);

                            $sql = "SELECT Impressions FROM reports WHERE id = $idStat LIMIT 1";
                            $ExistingImpressions = $db->getOne($sql);

                            $arD = explode('-', $Date);
                            $NewImpressions = $Impressions - $ExistingImpressions;
                            if ($NewImpressions > 0) {
                                // ($CPM > 0 || $CPV > 0)

                                if ($CCTR === true) {
                                    $CTRFrom = $CampaingData[$idCampaing]['CTRFrom'] * 100;
                                    $CTRTo = $CampaingData[$idCampaing]['CTRTo'] * 100;

                                    $RandCTR = rand($CTRFrom, $CTRTo) / 10000;
                                    $Clicks = intval($Impressions * $RandCTR);
                                }

                                if ($CVTR === true) {
                                    $sql = "SELECT CompleteVPer FROM reports WHERE id = '$idStat' LIMIT 1";
                                    $CompleteVPerc = $db->getOne($sql);

                                    if ($CompleteVPerc > 0) {
                                        $RandVTR = $CompleteVPerc;
                                    } else {
                                        $VTRFrom = $CampaingData[$idCampaing]['VTRFrom'] * 100;
                                        $VTRTo = $CampaingData[$idCampaing]['VTRTo'] * 100;
                                        $RandVTR = rand($VTRFrom, $VTRTo) / 10000;
                                        $CompleteVPerc = $RandVTR;
                                    }

                                    $CompleteV = intval($Impressions * $RandVTR);

                                    $Complete25 = calcPercents(25, $Impressions, $CompleteV);
                                    $Complete50 = calcPercents(50, $Impressions, $CompleteV);
                                    $Complete75 = calcPercents(75, $Impressions, $CompleteV);
                                }

                                if ($CView === true) {
                                    $ViewFrom = $CampaingData[$idCampaing]['ViewFrom'] * 100;
                                    $ViewTo = $CampaingData[$idCampaing]['ViewTo'] * 100;

                                    $RandView = rand($ViewFrom, $ViewTo) / 10000;
                                    $VImpressions = intval($Impressions * $RandView);
                                }

                                if ($CPM > 0) {
                                    $AddRevenue = $NewImpressions * $CPM / 1000;
                                } elseif ($CPV > 0) {
                                    $sql = "SELECT CompleteV FROM reports WHERE id = $idStat LIMIT 1";
                                    $ExistingCompleteV = $db->getOne($sql);

                                    $NewCompleteV = $CompleteV - $ExistingCompleteV;

                                    $AddRevenue = $NewCompleteV * $CPV;
                                } elseif ($CPC > 0) {
                                    $sql = "SELECT Clicks FROM reports WHERE id = $idStat LIMIT 1";
                                    $ExistingClicks = $db->getOne($sql);

                                    $NewClicks = $Clicks - $ExistingClicks;

                                    $AddRevenue = $NewClicks * $CPC;
                                } elseif ($vCPM > 0) {
                                    $sql = "SELECT VImpressions FROM reports WHERE id = $idStat LIMIT 1";
                                    $ExistingVImpressions = $db->getOne($sql);

                                    $NewVImpressions = $VImpressions - $ExistingVImpressions;

                                    $AddRevenue = $NewVImpressions * $vCPM;
                                } else {
                                    $AddRevenue = 0;
                                }

                                if ($RebatePercent > 0 && $AddRevenue > 0) {
                                    $AddRebate = ($AddRevenue * $RebatePercent) / 100;
                                } else {
                                    $AddRebate = 0;
                                }

                                $Revenue = "Revenue + $AddRevenue";
                                $budgetConsumed = "budgetConsumed + $AddRevenue";
                                $Rebate = "Rebate + $AddRebate";
                                $budgetReached = "budgetReached";

                                if ($AddRevenue > 0 && campaignHasAvailableBudget($idCampaing)) {
                                    $sql = "SELECT Revenue FROM reports WHERE id = $idStat";
                                    $currentRevenue = $db->getOne($sql);
                                    $budgetConsumed = $currentRevenue + $AddRevenue;
                                    $availableBudget = getCampaignAvailableBudget($idCampaing);

                                    if ($budgetConsumed >= $availableBudget) {
                                        $budgetConsumed = $availableBudget;
                                        if ($budgetConsumed > 0) {
                                            $budgetReached = 1;
                                        }
                                    }

                                    if ($RebatePercent > 0 && $budgetConsumed > 0) {
                                        $Rebate = ($budgetConsumed * $RebatePercent) / 100;
                                    }
                                }

                                $sql = "UPDATE reports SET 
                                    Requests = $Requests, 
                                    Bids = $Bids, 
                                    Impressions = $Impressions, 
                                    Revenue = $Revenue, 
                                    budgetConsumed = $budgetConsumed,
                                    VImpressions = $VImpressions,
                                    Clicks = $Clicks,
                                    CompleteV = $CompleteV,
                                    Complete25 = $Complete25,
                                    Complete50 = $Complete50,
                                    Complete75 = $Complete75,
                                    CompleteVPer = $CompleteVPerc,
                                    Rebate = $Rebate,
                                    budgetReached = $budgetReached
                                WHERE id = '$idStat' LIMIT 1";
                                echo $sql;
                                $db->query($sql);
                                //updateCampaignBudget($idCampaing, $budgetConsumed);
                            } else {
                                //echo "No New I CPM $CPM \n";
                            }
                        } else {
                            $budgetConsumed = $Revenue;
                            $budgetReached = 0;

                            if ($Revenue > 0 && campaignHasAvailableBudget($idCampaing)) {
                                $availableBudget = getCampaignAvailableBudget($idCampaing);

                                if ($Revenue >= $availableBudget) {
                                    $budgetConsumed = $availableBudget;
                                    if ($budgetConsumed > 0) {
                                        $budgetReached = 1;
                                    }
                                }

                                if ($RebatePercent > 0 && $budgetConsumed > 0) {
                                    $Rebate = ($budgetConsumed * $RebatePercent) / 100;
                                }
                            }

                            $sql = "UPDATE reports SET 
                                Requests = $Requests, 
                                Bids = $Bids, 
                                Impressions = $Impressions, 
                                Revenue = $Revenue,
                                budgetConsumed = $budgetConsumed,
                                VImpressions = $VImpressions,
                                Clicks = $Clicks,
                                CompleteV = $CompleteV,
                                Complete25 = $Complete25,
                                Complete50 = $Complete50,
                                Complete75 = $Complete75,
                                CompleteVPer = $CompleteVPerc,
                                Rebate = $Rebate
                            WHERE id = '$idStat' LIMIT 1";
                            echo $sql;
                            $db->query($sql);
                            //updateCampaignBudget($idCampaing, $budgetConsumed);
                        }
                    }
                }
            }
            $N++;
        }
    }

    debug('Finish: syncReport');
    debugTime($time);

    return $CampaignsIds;
}
