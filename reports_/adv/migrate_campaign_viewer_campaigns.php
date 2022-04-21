<?php

@session_start();
ini_set('display_errors', 0);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST', 1);
require('/var/www/html/login/config.php');
require('/var/www/html/login/reports_/adv/config.php');
require('/var/www/html/login/db.php');
//exit(0);

$db2 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);

$sql = <<<SQL
SELECT
	cvc.*,
    oldC.deal_id,
    cr.id creativity_id,
    newC.id new_campaign_id
FROM
	campaign_viewer_campaigns cvc
INNER JOIN campaign oldC ON oldC.id = cvc.campaign_id
INNER JOIN creativity cr ON cr.demand_tag_id = oldC.deal_id
INNER JOIN campaign newC ON newC.id = cr.campaign_id
WHERE
	oldC.ssp_id = 4
ORDER BY user_id, new_campaign_id;
SQL;

$result = $db2->getAll($sql);
foreach ($result as $entry) {
    $userId = $entry['user_id'];
    $oldCampaignId = $entry['campaign_id'];
    $newCampaignId = $entry['new_campaign_id'];

    if (!empty($userId) && !empty($oldCampaignId) && !empty($newCampaignId)) {
        $sql = "DELETE FROM campaign_viewer_campaigns WHERE user_id = {$userId} AND campaign_id = {$oldCampaignId};\n";
        $db2->query($sql);

        $sql = "INSERT INTO campaign_viewer_campaigns (user_id, campaign_id) VALUES ({$userId}, {$newCampaignId})";
        $db2->query($sql);
    }
}
