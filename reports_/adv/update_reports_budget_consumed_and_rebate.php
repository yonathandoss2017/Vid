<?php

@session_start();
define('CONST', 1);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require('/var/www/html/login/db.php');
require('/var/www/html/login/reports_/libs/common_adv.php');
require('/var/www/html/login/config.php');
require('/var/www/html/login/reports_/adv/config.php');

$conexion = sprintf('mysql:host=%d;dbname=%s', $dbhost2, $dbname2);
$pdo = new PDO($conexion, $dbuser2, $dbpass2);

$userId = 529;
$sql = <<<SQL
SELECT
    id
FROM
    campaign_test
WHERE
    created_by = {$userId}
    AND start_at > '2021-09-01'
SQL;

$stmt = $pdo->query($sql);
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
$campaignIds = array_column($campaigns, 'id');


sanitizeReport($campaignIds);

function sanitizeReport(array $campaignIds)
{
    if ($campaignIds) {
        sanitizeReportsBudgetConsumed($campaignIds);
        sanitizeRebate($campaignIds);
    }
}

function sanitizeReportsBudgetConsumed(array $campaignIds)
{
    global $pdo;
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
                                @b := CAST(@b + r.budgetConsumed AS DECIMAL(10,5)) AS balance
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
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'UPDATE reports_test r
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
                    CROSS JOIN reports_test AS r
                    INNER JOIN campaign_test AS c ON c.id = r.idCampaing
                    WHERE 
                        r.idCampaing = %campaign_id%
                        ORDER BY date, hour
                ) AS report_log WHERE balance > budget) b ON r.id = b.report_id 
                SET r.budgetConsumed = 0';
        $sql = str_replace("%campaign_id%", $campaignId, $sql);
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
}


function sanitizeRebate(array $campaignIds)
{
    global $pdo;
    $sql = 'UPDATE 
            reports_test r
        SET
            r.rebate = (r.budgetConsumed * r.rebatePercentage)
        WHERE
            r.rebatePercentage > 0
            AND r.idCampaing IN (%campaign_ids%);';

    $sql = str_replace("%campaign_ids%", implode(', ', $campaignIds), $sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
