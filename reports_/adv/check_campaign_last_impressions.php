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

$sql = "SELECT r.idCampaing AS id, r.date, SUM(r.Impressions) AS impressions
        FROM reports r,
        (
	        SELECT idCampaing, MAX(date) AS max_date
		        FROM reports
		        WHERE Impressions > 0
		        GROUP BY idCampaing
		        ORDER BY max_date DESC
        ) max
        WHERE r.idCampaing = max.idCampaing
        AND r.date = max.max_date
        GROUP BY r.idCampaing, r.date
        ORDER BY r.idCampaing ASC;
    ";

$query = $db->query($sql);

$campaigns = [];
if(0 < $db->num_rows($query)) {
    while($campaign = $db->fetch_array($query)) {
        $campaigns[] = [
            'id' => $campaign['id'],
            'last_purchase' => $campaign['date'],
            'impressions' => $campaign['impressions'],
        ];
    }
}

header('Content-Type: application/json');

echo json_encode($campaigns);
