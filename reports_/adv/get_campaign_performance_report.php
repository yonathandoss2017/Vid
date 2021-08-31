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
$dt->setTimezone(new DateTimeZone('UTC'));

if ($dt->format('H') <= 7){
    $dateTimeConsumedBudgetFrom = date("Y-m-d H:i:s", strtotime("yesterday -1 day 00:00:00"));
    $dateTimeConsumedBudgetTo = date("Y-m-d H:i:s", strtotime("yesterday -1 day 23:59:59"));
    $dateTimeFrom = date("Y-m-d H:i:s", strtotime("yesterday 00:00:00"));
    $dateTimeTo = date("Y-m-d H:i:s", strtotime("yesterday 23:59:59"));

    $checkConsumedBudgetData = "SELECT idCampaing AS id FROM reports
     WHERE TIMESTAMP(date, SEC_TO_TIME(hour*3600)) BETWEEN '$dateTimeConsumedBudgetFrom' AND '$dateTimeConsumedBudgetTo' ";

    if (0 < $db->num_rows($db->query($checkConsumedBudgetData))) {

        $sql = "SELECT r.idCampaing AS id, consumedBugdet.consumed_impressions, consumedBugdet.consumed_viewability, consumedBugdet.consumed_clicks, consumedBugdet.consumed_completes, SUM(r.Impressions) AS actual_impressions, SUM(r.VImpressions) AS actual_viewability, SUM(r.CompleteV) AS actual_completes , SUM(r.Clicks) AS actual_clicks
            FROM reports r,
            (
              SELECT idCampaing, SUM(Impressions) AS consumed_impressions, SUM(VImpressions) AS consumed_viewability, SUM(Clicks) AS consumed_clicks, SUM(CompleteV) AS consumed_completes
                  FROM reports
                  WHERE TIMESTAMP(date, SEC_TO_TIME(hour*3600)) BETWEEN '$dateTimeConsumedBudgetFrom' AND '$dateTimeConsumedBudgetTo'
                  GROUP BY idCampaing
            ) consumedBugdet
            WHERE r.idCampaing = consumedBugdet.idCampaing
            AND TIMESTAMP(r.date, SEC_TO_TIME(r.hour*3600)) BETWEEN '$dateTimeFrom' AND '$dateTimeTo'
            GROUP BY r.idCampaing
            ORDER BY r.idCampaing ASC;
        ";
    }
    else {
        $sql = "SELECT r.idCampaing AS id, SUM(r.Impressions) AS actual_impressions, SUM(r.VImpressions) AS actual_viewability, SUM(r.CompleteV) AS actual_completes , SUM(r.Clicks) AS actual_clicks
            FROM reports r
            WHERE r.idCampaing
            AND TIMESTAMP(r.date, SEC_TO_TIME(r.hour*3600)) BETWEEN '$dateTimeFrom' AND '$dateTimeTo'
            GROUP BY r.idCampaing
            ORDER BY r.idCampaing ASC;
        ";
    }
}
else{
    $dateTimeFrom = date("Y-m-d H:i:s", strtotime("today 00:00:00"));
    $dateTimeTo = date("Y-m-d H:i:s", strtotime("today 13:00:00"));

    $sql = "SELECT r.idCampaing AS id, SUM(r.Impressions) AS actual_impressions, SUM(r.VImpressions) AS actual_viewability, SUM(r.CompleteV) AS actual_completes , SUM(r.Clicks) AS actual_clicks
        FROM reports r
        WHERE r.idCampaing
        AND TIMESTAMP(r.date, SEC_TO_TIME(r.hour*3600)) BETWEEN '$dateTimeFrom' AND '$dateTimeTo'
        GROUP BY r.idCampaing
        ORDER BY r.idCampaing ASC;
    ";
}

$query = $db->query($sql);

$campaigns = [];
if(0 < $db->num_rows($query)) {
    while($campaign = $db->fetch_array($query)) {
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
