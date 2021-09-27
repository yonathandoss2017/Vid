<?php

@session_start();
define('CONST',1);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require('../../config_local.php');
}

$campaigns = [];
if (!empty($_POST)) {
    $campaigns = $_POST['campaigns'];
}

$activeCampaigns = [];
$inactiveCampaigns = [];
if (0 !== count($campaigns)) {
    $campaignIds = implode(', ', $campaigns);

    require('../../db.php');

    $db = new SQL($dbhost, $dbAdvName, $dbuser, $dbpass);

    $sql = "SELECT idCampaing, SUM(Impressions) AS impressions FROM reports"
        . " WHERE idCampaing IN (" . $campaignIds . ")"
        . " GROUP BY idCampaing"
    ;

    $query = $db->query($sql);

    if (0 < $db->num_rows($query)) {
        while ($campaign = $db->fetch_array($query)) {
            if ($campaign['impressions'] === '0') {
                $inactiveCampaigns[] = (int)$campaign['idCampaing'];

                continue;
            }

            $activeCampaigns[] = (int)$campaign['idCampaing'];
        }
    }

    foreach ($campaigns as $campaign) {
        if (!in_array($campaign, $activeCampaigns) &&
            !in_array($campaign, $inactiveCampaigns)
        ) {
            $inactiveCampaigns[] = $campaign;
        }
    }
}

header('Content-Type: application/json');

echo json_encode($inactiveCampaigns);
