<?php

@session_start();
ini_set('display_errors', 0);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST', 1);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require('../../config_local.php');
}
require('../../reports_/adv/config.php');
require('../../db.php');

$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
$cookie_file = '../../admin/lkqdimport/cookie.txt';
require('../../reports_/adv/common.php');
require('../../admin/lkqdimport/common_staging.php');

// TODO change back campaign and reports table name when goint to prod
$fromDate = new DateTime(date('Y-m-d H:00', time() - (3600 * 1)));
$toDate   = new DateTime(date('Y-m-d 23:00'));

synchronizeCampaignsWithNewBudgets();
$CampaignsIds = syncReport($fromDate, $toDate);
sanitizeReport($CampaignsIds);
updateReportCards($db3, $fromDate->format('Y-m-d'));

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

function calculateBudgetConsumed($idCampaign, $campaignBudget, $revenue)
{
    global $db;

    if (!$campaignBudget) {
        return $revenue;
    }

    $sql = "SELECT c.budget - sum(budgetConsumed) FROM  campaign_test c, reports_test r WHERE c.id = $idCampaign AND r.idCampaing = c.id";
    $availableBudget = $db->getOne($sql);

    if ($availableBudget === 0) {
        return 0;
    }

    if (!$availableBudget || $revenue <= $campaignBudget) {
        return $revenue;
    }

    return $availableBudget;
}

function sanitizeReport(array $campaignIds)
{
    if ($campaignIds) {
        //synchronizeCampaignsWithNewBudgets();
        sanitizeReportsBudgetConsumed($campaignIds);
        sanitizeRebate($campaignIds);
    }
}

function synchronizeCampaignsWithNewBudgets()
{
    $campaigns = getCampaignsWithNewBudgets();
    if ($campaigns) {
        foreach ($campaigns as $campaign) {
            restartBudgetConsumed($campaign['idCampaing']);
            $fromDate = new DateTime(sprintf('%s %s:00', $campaign['date'], $campaign['hour']));
            $fromDate->modify('+ 1 hour');
            $toDate   = new DateTime(date('Y-m-d 23:00'));
            syncReport($fromDate, $toDate, [$campaign['deal_id']]);
        }
    }
}

function restartBudgetConsumed($campaignId)
{
    global $db;
    $sql = 'UPDATE reports_test r
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
    $sql = 'SELECT * FROM (
                SELECT 
                    r.idCampaing,
                    c.deal_id,
                    sum(budgetConsumed) AS budgetConsumed,
                    CONVERT(c.budget, DECIMAL) as budget,
                    r.date,
                    r.hour
                FROM 
                    reports_test r,
                    campaign_test c
                WHERE	
                    c.id = r.idCampaing
                    AND r.idCampaing IN (SELECT idCampaing from reports_test where budgetReached = true)
                GROUP BY 
                    idCampaing
            ) AS oudated_report_rows 
            WHERE
                budget > budgetConsumed';

    return $db->getAll($sql) ?? [];
}

function sanitizeReportsBudgetConsumed(array $campaignIds)
{
    global $db;
    foreach ($campaignIds as $campaignId) {
        /* UPDATE THE FIRST REPORT OVERFLOWED ROW
           BASED ON THE CAMPAIGN BUDGET */
        $sql = 'UPDATE reports_test r
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
                            campaign_test AS c,
                            (
                            SELECT 
                                r.id as report_id,
                                r.idCampaing as campaing_id,
                                r.budgetConsumed,
                                r.date,
                                r.hour,
                                @b := @b + CAST(r.budgetConsumed AS DECIMAL(10,5)) AS balance
                            FROM
                            (SELECT @b := 0.0) AS dummy 
                            CROSS JOIN reports_test AS r
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

        $sql = 'UPDATE reports_test r
                INNER JOIN (SELECT * FROM (
                    SELECT 
                        r.id AS report_id,
                        r.idCampaing AS campaing_id,
                        r.budgetConsumed,
                        @b := @b + CAST(r.budgetConsumed AS DECIMAL(10,5)) AS balance,
                        c.budget,
                        date,
                        hour
                    FROM
                    (SELECT @b := 0.0) AS dummy 
                    CROSS JOIN reports_test AS r
                    INNER JOIN campaign_test AS c ON c.id = r.idCampaing
                    WHERE 
                        r.idCampaing = %campaign_id%
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
            reports_test r
        SET
            r.rebate = (r.budgetConsumed * r.rebatePercentage)
        WHERE
            r.rebatePercentage > 0
            AND r.idCampaing IN (%campaign_ids%);';

    $sql = str_replace("%campaign_ids%", implode(', ', $campaignIds), $sql);
    $db->query($sql);
}

function syncReport(DateTime $fromDate, DateTime $toDate, $filterCampaignIds = []): array
{
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

    $sql = "SELECT 
                c.*,
                dt.demand_tag_id,
                po.sales_manager_id
            FROM 
                campaign_test c,
                demand_tag dt,
                purchase_order po
            WHERE 
                dt.campaign_id = c.id
            AND po.id = c.purchase_order_id
            AND dt.demand_tag_id IS NOT NULL
            AND c.ssp_id = 4 
            AND c.status = 1
            $avoidCreativitiesIdsSQL
            $filterCampaignSQL
    ";

    $results = $db->query($sql);

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

    $ImportData = getAdvertiserDemandReportCSVByDateRange($fromDate, $toDate, $filterCampaignIds);

    if ($ImportData === false) {
        logIn();
        $ImportData = getAdvertiserDemandReportCSVByDateRange($fromDate, $toDate, $filterCampaignIds);
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
                        $Rebate = $Revenue * $RebatePercent / 100;
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
                    $sql = "SELECT id FROM reports_test WHERE SSP = 4 AND idCreativity = (SELECT id from demand_tag where demand_tag_id = '$TagId' ORDER BY ID DESC LIMIT 1) AND Date = '$Date' AND Hour = '$Hour' LIMIT 1";
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

                            if ($RebatePercent > 0 && $Revenue > 0) {
                                $Rebate = $Revenue * $RebatePercent / 100;
                            } else {
                                $Rebate = 0;
                            }
                        }

                        $sql = "INSERT INTO reports_test
                        (SSP, idSalesManager, idPurchaseOrder, idCampaing, idCreativity, idCountry, Requests, Bids, Impressions, Revenue, budgetConsumed, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, rebatePercentage, Date, Hour) 
                        VALUES (4, $salesManagerId, $PurchaseOrderId, $idCampaing, (SELECT id from demand_tag where demand_tag_id = '$TagId' ORDER BY ID DESC LIMIT 1), $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, $RebatePercent, '$Date', '$Hour')";
                        $db->query($sql);
                    } else {
                        if ($Type == 2) {
                            $Impressions = intval($Impressions);

                            $sql = "SELECT Impressions FROM reports_test WHERE id = $idStat LIMIT 1";
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
                                    $sql = "SELECT CompleteVPer FROM reports_test WHERE id = '$idStat' LIMIT 1";
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
                                    $sql = "SELECT CompleteV FROM reports_test WHERE id = $idStat LIMIT 1";
                                    $ExistingCompleteV = $db->getOne($sql);

                                    $NewCompleteV = $CompleteV - $ExistingCompleteV;

                                    $AddRevenue = $NewCompleteV * $CPV;
                                } elseif ($CPC > 0) {
                                    $sql = "SELECT Clicks FROM reports_test WHERE id = $idStat LIMIT 1";
                                    $ExistingClicks = $db->getOne($sql);

                                    $NewClicks = $Clicks - $ExistingClicks;

                                    $AddRevenue = $NewClicks * $CPC;
                                } elseif ($vCPM > 0) {
                                    $sql = "SELECT VImpressions FROM reports_test WHERE id = $idStat LIMIT 1";
                                    $ExistingVImpressions = $db->getOne($sql);

                                    $NewVImpressions = $VImpressions - $ExistingVImpressions;

                                    $AddRevenue = $NewVImpressions * $vCPM;
                                } else {
                                    $AddRevenue = 0;
                                }

                                if ($RebatePercent > 0 && $AddRevenue > 0) {
                                    $AddRebate = $AddRevenue * $RebatePercent / 100;
                                } else {
                                    $AddRebate = 0;
                                }

                                $Revenue = "Revenue + $AddRevenue";

                                $sql = "UPDATE reports_test SET 
                                    Requests = $Requests, 
                                    Bids = $Bids, 
                                    Impressions = $Impressions, 
                                    Revenue = $Revenue, 
                                    budgetConsumed = Revenue,
                                    VImpressions = $VImpressions,
                                    Clicks = $Clicks,
                                    CompleteV = $CompleteV,
                                    Complete25 = $Complete25,
                                    Complete50 = $Complete50,
                                    Complete75 = $Complete75,
                                    CompleteVPer = $CompleteVPerc,
                                    Rebate = Rebate + $AddRebate
                                WHERE id = '$idStat' LIMIT 1";

                                $db->query($sql);
                            } else {
                                //echo "No New I CPM $CPM \n";
                            }
                        } else {
                            $sql = "UPDATE reports_test SET 
                                Requests = $Requests, 
                                Bids = $Bids, 
                                Impressions = $Impressions, 
                                Revenue = $Revenue,
                                budgetConsumed = $Revenue,
                                VImpressions = $VImpressions,
                                Clicks = $Clicks,
                                CompleteV = $CompleteV,
                                Complete25 = $Complete25,
                                Complete50 = $Complete50,
                                Complete75 = $Complete75,
                                CompleteVPer = $CompleteVPerc,
                                Rebate = $Rebate
                            WHERE id = '$idStat' LIMIT 1";

                            $db->query($sql);
                        }
                    }
                }
            }
            $N++;
        }
    }

    return $CampaignsIds;
}
