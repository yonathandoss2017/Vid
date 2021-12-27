<?php

@session_start();
define('CONST',1);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require('../../config_local.php');
}

require('../../db.php');

$db = new SQL($dbhost, $dbAdvName, $dbuser, $dbpass);

$dt = new DateTime();
$dt->setTimezone(new DateTimeZone('CET'));

if ($dt->format('H') < 13) {
    $dayBeforeYesterday = date("Y-m-d", strtotime("yesterday -1 day"));
    $yesterday = date("Y-m-d", strtotime("yesterday"));

    $checkConsumedBudgetData = "SELECT idCampaing AS id FROM reports
     WHERE date BETWEEN '$dayBeforeYesterday' AND '$dayBeforeYesterday' AND hour BETWEEN 0 AND 23 ";

    if (0 < $db->num_rows($db->query($checkConsumedBudgetData))) {

        $sql = "SELECT r.idCampaing AS id, consumedBugdet.consumed_impressions, consumedBugdet.consumed_viewability, consumedBugdet.consumed_clicks, consumedBugdet.consumed_completes, SUM(r.Impressions) AS actual_impressions, SUM(r.VImpressions) AS actual_viewability, SUM(r.CompleteV) AS actual_completes , SUM(r.Clicks) AS actual_clicks
            FROM reports r,
            (
              SELECT idCampaing, SUM(Impressions) AS consumed_impressions, SUM(VImpressions) AS consumed_viewability, SUM(Clicks) AS consumed_clicks, SUM(CompleteV) AS consumed_completes
                  FROM reports
                  WHERE date BETWEEN '$dayBeforeYesterday' AND '$dayBeforeYesterday' AND
                  hour BETWEEN 0 AND 23
                  GROUP BY idCampaing
            ) consumedBugdet
            WHERE r.idCampaing = consumedBugdet.idCampaing
            AND r.date BETWEEN '$yesterday' AND '$yesterday'
            AND r.hour BETWEEN 0 AND 23
            GROUP BY r.idCampaing
            ORDER BY r.idCampaing ASC;
        ";
    } else {
        $sql = "SELECT r.idCampaing AS id, SUM(r.Impressions) AS actual_impressions, SUM(r.VImpressions) AS actual_viewability, SUM(r.CompleteV) AS actual_completes , SUM(r.Clicks) AS actual_clicks
            FROM reports r
            WHERE r.idCampaing
            AND r.date BETWEEN '$yesterday' AND '$yesterday'
            AND r.hour BETWEEN 0 AND 23
            GROUP BY r.idCampaing
            ORDER BY r.idCampaing ASC;
        ";
    }
} else {
    $today = date("Y-m-d");

    $sql = "SELECT r.idCampaing AS id, SUM(r.Impressions) AS actual_impressions, SUM(r.VImpressions) AS actual_viewability, SUM(r.CompleteV) AS actual_completes , SUM(r.Clicks) AS actual_clicks
        FROM reports r
        WHERE r.idCampaing AND
        r.date BETWEEN '{$today}' AND '{$today}' AND
        r.Hour BETWEEN 0 AND 13
        GROUP BY r.idCampaing
        ORDER BY r.idCampaing ASC;
    ";
}

$query = $db->query($sql);

$campaigns = [];
if (0 < $db->num_rows($query)) {
    while ($campaign = $db->fetch_array($query)) {
        $campaigns[] = [
            'id' => $campaign['id'],
            'consumed_impressions' => $campaign['consumed_impressions'],
            'consumed_viewability' => $campaign['consumed_viewability'],
            'consumed_clicks' => $campaign['consumed_clicks'],
            'consumed_completes' => $campaign['consumed_completes'],
            'actual_impressions' => $campaign['actual_impressions'],
            'actual_viewability' => $campaign['actual_viewability'],
            'actual_completes' => $campaign['actual_completes'],
            'actual_clicks' => $campaign['actual_clicks'],
        ];
    }
}

header('Content-Type: application/json');

echo json_encode($campaigns);
