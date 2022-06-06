<?php

@session_start();
define('CONST', 1);
define('CURRENCY_USD', 1);
define('CURRENCY_EUR', 2);
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require '/var/www/html/login/db.php';
require '/var/www/html/login/admin/lkqdimport/common_staging.php';
require '/var/www/html/login/config.php';
require '/var/www/html/site/constantes.php';
require '/var/www/html/site/common.lib.php';
require '/var/www/html/login/reports_/adv/config.php';

$conexion = sprintf('mysql:host=%d;dbname=%s', $dbhost2, $dbname);
$pdo = new PDO($conexion, $dbuser2, $dbpass2);

$date = new DateTime();
$date->modify('-1 month');

$year = $date->format('Y');
$month = $date->format('m');

$sql = <<<SQL
SELECT
    users.user AS 'Suppy Partner',
    SUM(reports_resume{$year}{$month}.Impressions) AS Impresiones,
    CONCAT('$', ROUND ( SUM(reports_resume{$year}{$month}.Coste) , 2)) AS Coste
FROM
    reports_resume{$year}{$month}
INNER JOIN users ON users.id = reports_resume{$year}{$month}.idUser
INNER JOIN supplytag ON reports_resume{$year}{$month}.idTag = supplytag.id
WHERE
    supplytag.currency = 1
GROUP BY users.id
ORDER BY Impresiones DESC
SQL;

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

var_dump($data);
