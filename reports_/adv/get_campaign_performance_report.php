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

$today = date("Y-m-d");
$yesterday = date("Y-m-d", strtotime("yesterday"));

if ($dt->format('H') < 18) {
        $sql = "SELECT r.idCampaing AS id, yesterday_kpi.impressions, yesterday_kpi.viewability, yesterday_kpi.clicks, yesterday_kpi.completes, SUM(r.Impressions) AS total_impressions, SUM(r.VImpressions) AS total_viewability, SUM(r.CompleteV) AS total_completes , SUM(r.Clicks) AS total_clicks
            FROM reports r,
            (
              SELECT idCampaing, SUM(Impressions) AS impressions, SUM(VImpressions) AS viewability, SUM(Clicks) AS clicks, SUM(CompleteV) AS completes
                  FROM reports
                  WHERE date = '$yesterday'
                  GROUP BY idCampaing
            ) yesterday_kpi
            WHERE r.idCampaing = yesterday_kpi.idCampaing
            AND r.date < '$today'
            GROUP BY r.idCampaing
            ORDER BY r.idCampaing ASC;
        ";
} else {
    $sql = "SELECT r.idCampaing AS id, today_kpi.impressions, today_kpi.viewability, today_kpi.clicks, today_kpi.completes, SUM(r.Impressions) AS total_impressions, SUM(r.VImpressions) AS total_viewability, SUM(r.CompleteV) AS total_completes , SUM(r.Clicks) AS total_clicks
            FROM reports r,
            (
              SELECT idCampaing, SUM(Impressions) AS impressions, SUM(VImpressions) AS viewability, SUM(Clicks) AS clicks, SUM(CompleteV) AS completes
                  FROM reports
                  WHERE date = '$today'
                  GROUP BY idCampaing
            ) today_kpi
            WHERE r.idCampaing = today_kpi.idCampaing
            AND r.date <= '$today'
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
            'impressions' => $campaign['impressions'],
            'viewability' => $campaign['viewability'],
            'clicks' => $campaign['clicks'],
            'completes' => $campaign['completes'],
            'total_impressions' => $campaign['total_impressions'],
            'total_viewability' => $campaign['total_viewability'],
            'total_completes' => $campaign['total_completes'],
            'total_clicks' => $campaign['total_clicks'],
        ];
    }
}

header('Content-Type: application/json');

echo json_encode($campaigns);
