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

$sql = sprintf(
        "SELECT idCampaing AS campaignId, idCreativity AS creativityId,
            SUM(Impressions) AS impressions, SUM(VImpressions) AS viewableImpressions,
            SUM(Clicks) AS clicks, SUM(CompleteV) AS completeViews, Date AS date
        FROM reports r
        WHERE (Date = '%s'
        OR (Date = '%s' AND Hour <= '%s'))
        AND idCreativity IN (%s)
        GROUP BY campaignId, creativityId, date
    ",
    $dateFrom, $dateTo, $hourTo, $creativitiesIds
);

$query = $db->query($sql);

$results = [];
if(0 < $db->num_rows($query)) {
    while($rows = $db->fetch_array($query)) {
        $results[] = [
            'campaign_id' => $rows['campaignId'],
            'creativity_id' => $rows['creativityId'],
            'impressions' => $rows['impressions'],
            'viewable_impressions' => $rows['viewableImpressions'],
            'clicks' => $rows['clicks'],
            'complete_views' => $rows['completeViews'],
            'date' => $rows['date'],
        ];
    }
}

header('Content-Type: application/json');

echo json_encode($results);
