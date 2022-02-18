<?php

/**
 * PHP version 7
 *
 * @category API
 * @package  Campaign
 * @author   Author <gadiel.reyesdelrosario@vidoomy.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://demand.vidoomy.com/creativity
 */

@session_start();
define('CONST', 1);

if (file_exists('/var/www/html/login/config.php')) {
    include '/var/www/html/login/config.php';
} else {
    include '../../config_local.php';
}

require '../../db.php';

$db = new SQL($dbhost, 'vidoomy_adv', $dbuser, $dbpass);

$unixTime = time();
$timeZone = new \DateTimeZone('Europe/Madrid');

$time = new \DateTime();
$time->setTimestamp($unixTime)->setTimezone($timeZone);

$today = $time->format('Y-m-d');
$yesterday = date_modify($time, '-1 day')->format('Y-m-d');

$sql = <<<SQL
SELECT
    reports.Impressions,
    campaign.id,
    campaign.name,
    campaign.deal_id,
    campaign.type
FROM
    reports
INNER JOIN
    campaign ON reports.idCampaing = campaign.id
WHERE
    reports.Impressions > 0
    AND campaign.end_at > "{$today}"
    AND Date = "{$yesterday}"
    AND Hour <= 6
    AND campaign.id NOT IN (
        SELECT
            idCampaing
        FROM
            reports
        WHERE
            Date = "{$today}"
            AND Impressions > 0
    )
GROUP BY campaign.id
SQL;

$query = $db->query($sql);

$campaigns = [];

if (0 < $db->num_rows($query)) {
    while ($campaign = $db->fetch_array($query)) {
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
