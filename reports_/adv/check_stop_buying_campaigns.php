<?php

@session_start();
define('CONST',1);

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
} else {
    require('../../config_local.php');
}

require('../../db.php');

$db = new SQL($dbhost, 'vidoomy-adv', $dbuser, $dbpass);

$unixTime = time();
$timeZone = new \DateTimeZone('Europe/Madrid');

$time = new \DateTime();
$time->setTimestamp($unixTime)->setTimezone($timeZone);

$today = $time->format('Y-m-d');
$yesterday = date_modify($time,'-1 day')->format('Y-m-d');

$sql = "SELECT
            `vidoomy-adv`.reports.Impressions,
            `vidoomy-adv`.campaign.id,
            `vidoomy-adv`.campaign.name,
            `vidoomy-adv`.campaign.deal_id, 
            `vidoomy-adv`.campaign.type
        FROM `vidoomy-adv`.reports
        INNER JOIN `vidoomy-adv`.campaign
            ON `vidoomy-adv`.reports.idCampaing = `vidoomy-adv`.campaign.id
        WHERE `vidoomy-adv`.reports.Impressions > 0
        AND `vidoomy-adv`.campaign.end_at > '$today'
        AND Date = '$yesterday' AND Hour <= 6
            AND `vidoomy-adv`.campaign.id NOT IN (
                SELECT idCampaing FROM `vidoomy-adv`.reports
                WHERE Date = '$today'
                    AND Impressions > 0
                );
";

$query = $db->query($sql);

$campaigns = [];

if(0 < $db->num_rows($query)) {
    while($campaign = $db->fetch_array($query)) {
        $campaigns[] = [
            'id' => $campaign['id'],
            'deal_id' => $campaign['deal_id'],
            'name' => $campaign['name'],
            'type' => $campaign['type']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['campaigns' => $campaigns]);
