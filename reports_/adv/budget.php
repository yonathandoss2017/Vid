<?php

@session_start();
define('CONST', 1);
require '/var/www/html/login/config.php';
require './config.php';

$conexion = sprintf('mysql:host=%d;dbname=%s', $dbhost2, $dbname2);
$pdo = new PDO($conexion, $dbuser2, $dbpass2);

if (
    !isset($_POST['uuid'])
    || !isset($_POST['env'])
    || !isset($_POST['from_datetime'])
    || !isset($_POST['to_datetime'])
) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit(0);
}

/*
if($_POST['env'] == 'prod' || $_POST['env'] == 'pro'){
    $db2 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
}else{
    $db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
}

$UUID = mysqli_real_escape_string($db2->link, $_POST['uuid']);

$sql = "SELECT report_key.* FROM report_key WHERE report_key.unique_id = '$UUID' LIMIT 1";
$query = $db2->query($sql);
if($db2->num_rows($query) > 0){
    $Repo = $db2->fetch_array($query);
    $RepId = $Repo['id'];

    $sql = "UPDATE report_key SET status = 1 WHERE id = '$RepId' LIMIT 1";
    $db2->query($sql);
} else {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit(0);
}
*/

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");

$campaignId = $_POST['campaign_id'];
$dateFrom = new DateTime($_POST['from_datetime']);
$dateTo   = new DateTime($_POST['to_datetime']);
$dateFromString = $dateFrom->format('Y-m-d');
$dateToString = $dateTo->format('Y-m-d');
$hourFrom = $dateFrom->format('H');
$hourTo = $dateTo->format('H');

$campaignFilter = '';
if ($campaignId) {
    $campaignFilter = 'AND idCampaing = ' . $campaignId;
}

$hourFilter = '';
if ($dateFromString !== $dateToString) {
    $hourFilter = "AND (((reports.Date >= '{$dateFromString}' AND reports.Date < '{$dateToString}') AND reports.Hour >= {$hourFrom}) OR ((reports.Date > '{$dateFromString}' AND reports.Date <= '{$dateToString}') AND reports.Hour <= {$hourTo}))";
} else {
    $hourFilter = sprintf('AND (reports.Hour >= %s AND reports.Hour <= %s)', $hourFrom, $hourTo);
}


$sql = <<<SQL
SELECT
    idCreativity AS creativity_id,
    idCampaing AS campaign_id,
    SUM(Revenue) AS revenue,
    CONCAT(Date, ' ', CONCAT(Hour, ':59')) AS revenue_at,
    Hour AS revenue_at_hour
FROM
    vidoomy_adv.reports
WHERE
    Date BETWEEN '{$dateFromString}' AND '{$dateToString}'
    {$hourFilter}
    {$campaignFilter}
GROUP BY
    idCreativity,
    idCampaing,
    Date,
    Hour
SQL;

$stmt = $pdo->query($sql);
$creativities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($creativities);
