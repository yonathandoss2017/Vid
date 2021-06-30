<?php

require_once("vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbuser = $_ENV["LOCAL_USER"];
$dbpass = $_ENV["LOCAL_PASSWORD"];
$dbhost = $_ENV["LOCAL_HOST"];
$dbname = $_ENV["LOCAL_DB"];
$dbAdvName = $_ENV["LOCAL_DB_ADV"];

$prefix="vidoomy_";

$advDev = [
    "user" => $_ENV["ADVERTISERS_LOCAL_USER"],
    "pass" => $_ENV["ADVERTISERS_LOCAL_PASSWORD"],
    "host" => $_ENV["ADVERTISERS_LOCAL_HOST"],
    "db" => $_ENV["ADVERTISERS_LOCAL_DB"],
];

date_default_timezone_set('US/Eastern');
