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

$dt = new DateTime();
$dt->setTimezone(new DateTimeZone('Europe/Berlin'));
$dateTime = $dt->format('H') <= 12 ? date("Y-m-d H:i:s\n", strtotime("yesterday 15:00")) : date("Y-m-d H:i:s\n", strtotime("today 12:00"));
$currentDateTime = $dt->format('Y-m-d H:i:s');

$sql = "SELECT r.idCampaing AS id, total.total_revenue, SUM(r.CompleteV)/SUM(r.Impressions)*100 AS VTR, SUM(r.VImpressions)/SUM(r.Impressions)*100 AS Viewability, SUM(r.Clicks)/SUM(r.Impressions)*100 AS CTR
        FROM reports r,
        (
          SELECT idCampaing, SUM(revenue) AS total_revenue
              FROM reports
              GROUP BY idCampaing
        ) total
        WHERE r.idCampaing = total.idCampaing
        AND TIMESTAMP(r.date, SEC_TO_TIME(r.hour*3600)) BETWEEN '$dateTime' AND '$currentDateTime'
        GROUP BY r.idCampaing
        ORDER BY r.idCampaing ASC;
    ";

$query = $db->query($sql);

$campaigns = [];
if(0 < $db->num_rows($query)) {
    while($campaign = $db->fetch_array($query)) {
        $campaigns[] = [
            'id' => $campaign['id'],
            'VTR' => $campaign['VTR'],
            'Viewability' => $campaign['Viewability'],
            'CTR' => $campaign['CTR'],
            'total_revenue' => $campaign['total_revenue'],
        ];
    }
}

header('Content-Type: application/json');

echo json_encode($campaigns);
