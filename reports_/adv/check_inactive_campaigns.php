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

$sql = "SELECT DISTINCT(idCampaing) AS id FROM reports WHERE Impressions = 0";

$query = $db->query($sql);

$campaigns = [];
if(0 < $db->num_rows($query)) {
    while($campaign = $db->fetch_array($query)) {
        $campaigns[] = $campaign['id'];
    }
}

header('Content-Type: application/json');

echo json_encode($campaigns);
