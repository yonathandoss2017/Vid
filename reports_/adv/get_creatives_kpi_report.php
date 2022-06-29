<?php

@session_start();
define('CONST',1);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require('../../config_local.php');
}

require('../../db.php');

$db = new SQL($dbhost, 'vidoomy_adv', $dbuser, $dbpass);

if (!isset($_POST['date_from']) || !isset($_POST['date_to']) || !isset($_POST['creativities'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Could not process the request. Missing parameters']);

    exit(0);
}

$creativitiesIds = $_POST['creativities'];
$fromDateTimeString = $_POST['date_from'];

$dateFrom = substr($fromDateTimeString, 0, 10);
$hourFrom = substr($fromDateTimeString, 11, 2);

$toDateTimeString = $_POST['date_to'];
$dateTo = substr($toDateTimeString, 0, 10);
$hourTo = substr($toDateTimeString, 11, 2);

$SQL = sprintf("SELECT reports.idCampaing AS campaignId,
            demand_tag.demand_tag_id AS creativityId,
            demand_tag.name AS creativityName,
            reports.Date AS date,
            SUM(reports.Impressions) AS impressions,
            SUM(reports.VImpressions) AS viewableImpressions,
            SUM(reports.Clicks) AS clicks,
            SUM(reports.CompleteV) AS completeViews,
            ROUND((SUM(reports.CompleteV) / SUM(reports.Impressions) * 100), 20) AS VTR,
            ROUND((SUM(reports.Clicks) / SUM(reports.Impressions) * 100), 2) AS CTR,
            ROUND((SUM(reports.VImpressions) / SUM(reports.Impressions) * 100), 2) AS viewabilityPercent
        FROM reports
        INNER JOIN campaign ON campaign.id = reports.idCampaing
        LEFT JOIN demand_tag ON demand_tag.id = reports.idCreativity
        WHERE (
            ((reports.Date >= '%s' AND reports.Date < '%s') AND reports.Hour >= 0)
            OR ((reports.Date > '%s' AND reports.Date <= '%s') AND reports.Hour < '%s'))
        AND (
          demand_tag.demand_tag_id IN (%s)
        )
        GROUP BY campaignId, creativityId, date",
        $dateFrom, $dateTo,
        $dateFrom, $dateTo, $hourTo,
        $creativitiesIds
    )
;

$query = $db->query($SQL);

$results = [];
if(0 < $db->num_rows($query)) {
    while($rows = $db->fetch_array($query)) {
        $results[] = [
            'campaign_id' => $rows['campaignId'],
            'creativity_id' => $rows['creativityId'],
            'creativity_name' => $rows['creativityName'],
            'impressions' => $rows['impressions'],
            'viewable_impressions' => $rows['viewableImpressions'],
            'clicks' => $rows['clicks'],
            'complete_views' => $rows['completeViews'],
            'vtr' => $rows['VTR'],
            'ctr' => $rows['CTR'],
            'viewability_percent' => $rows['viewabilityPercent'],
            'date' => $rows['date'],
        ];
    }
}

header('Content-Type: application/json');

echo json_encode($results);
