<?php

require_once("vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbuser = $_ENV["LOCAL_USER"];
$dbpass = $_ENV["LOCAL_PASSWORD"];
$dbhost = $_ENV["LOCAL_HOST"];
$dbname = $_ENV["LOCAL_DB"];
$dbAdvName = $_ENV["LOCAL_DB_ADV"];

$prefix = "vidoomy_";

// This is the local reports server for advertisers database info
$advProd = $advPre = $advIntegration = $advDev01 = $advDev02 = $advDev03  = [
    "user" => $_ENV["ADVERTISERS_LOCAL_USER"],
    "pass" => $_ENV["ADVERTISERS_LOCAL_PASSWORD"],
    "host" => $_ENV["ADVERTISERS_LOCAL_HOST"],
    "db" => $_ENV["ADVERTISERS_LOCAL_DB"],
];

// This is the local advertisers panel database info
$advPanelLocal = [
    "user" => $_ENV["ADVERTISERS_PANEL_LOCAL_USER"],
    "pass" => $_ENV["ADVERTISERS_PANEL_LOCAL_PASSWORD"],
    "host" => $_ENV["ADVERTISERS_PANEL_LOCAL_HOST"],
    "db" => $_ENV["ADVERTISERS_PANEL_LOCAL_DB"],
];

$lkqdCred = [
    "userId" => $_ENV["LKQD_USER"],
    "password" => $_ENV["LKQD_PASS"],
];

date_default_timezone_set('US/Eastern');
