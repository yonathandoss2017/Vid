<?php

@session_start();
define('CONST',1);
require('./config.php');
require('../../db.php');
$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

if(!isset($_POST['uuid']) || !isset($_POST['env']) || !isset($_POST['date'])){
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit(0);
}

/*
if($_POST['env'] == 'prod'){
    $dbuser2 = "root";
    $dbpass2 = "ViDo0-PROD_2020";
    $dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
    $dbname2 = "vidoomy";
    $db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
}else{
    $dbuser2 = "root";
    $dbpass2 = "vidooDev-Pass_2020";
    $dbhost2 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
    $dbname2 = "vidoomy";
    $db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
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

$sql = "SELECT purchase_order_id, SUM(Revenue) AS revenue FROM vidoomy_adv.reports_test WHERE Date = '{$_POST["date"]}' GROUP BY purchase_order_id";
$query = $db->query($sql);
$campaigns = $query->fetch_all(MYSQLI_ASSOC);

?>{
    "purchase_orders": <?php echo json_encode($campaigns); ?>
}
