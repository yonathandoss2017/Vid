<?php
	@require_once("secure.php");

    if (file_exists("/var/www/html/vendor/autoload.php")) {
        require_once("/var/www/html/vendor/autoload.php");
    } else {
        require_once("../vendor/autoload.php");
    }

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$dbuser = $_ENV["LOCAL_USER"];
	$dbpass = $_ENV["LOCAL_PASSWORD"];
	$dbhost = $_ENV["LOCAL_HOST"];
	$dbname = $_ENV["LOCAL_DB"];
	$dbAdvName = $_ENV["LOCAL_DB_ADV"];
	
	$prefix="vidoomy_";

	$advProd = [
		"user" => $_ENV["ADVERTISERS_PROD_USER"],
		"pass" => $_ENV["ADVERTISERS_PROD_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_PROD_HOST"],
		"db" => $_ENV["ADVERTISERS_PROD_DB"]
	];

	$advStaging = [
		"user" => $_ENV["ADVERTISERS_STAGING_USER"],
		"pass" => $_ENV["ADVERTISERS_STAGING_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_STAGING_HOST"],
		"db" => $_ENV["ADVERTISERS_STAGING_DB"]
	];

	$advPre = [
		"user" => $_ENV["ADVERTISERS_PRE_USER"],
		"pass" => $_ENV["ADVERTISERS_PRE_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_PRE_HOST"],
		"db" => $_ENV["ADVERTISERS_PRE_DB"],
	];

	$advDev01 = [
		"user" => $_ENV["ADVERTISERS_DEV_01_USER"],
		"pass" => $_ENV["ADVERTISERS_DEV_01_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_DEV_01_HOST"],
		"db" => $_ENV["ADVERTISERS_DEV_01_DB"],
	];

	$advDev02 = [
		"user" => $_ENV["ADVERTISERS_DEV_02_USER"],
		"pass" => $_ENV["ADVERTISERS_DEV_02_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_DEV_02_HOST"],
		"db" => $_ENV["ADVERTISERS_DEV_02_DB"],
	];

	$advDev03 = [
		"user" => $_ENV["ADVERTISERS_DEV_03_USER"],
		"pass" => $_ENV["ADVERTISERS_DEV_03_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_DEV_03_HOST"],
		"db" => $_ENV["ADVERTISERS_DEV_03_DB"],
	];

	$pubDev02 = [
		"user" => $_ENV["PUBLISHER_DEVELOPMENT_02_USER"],
		"pass" => $_ENV["PUBLISHER_DEVELOPMENT_02_PASSWORD"],
		"host" => $_ENV["PUBLISHER_DEVELOPMENT_02_HOST"],
		"db" => $_ENV["PUBLISHER_DEVELOPMENT_02_DB"],
	];

	$pubIntegration = [
		"user" => $_ENV["PUBLISHER_INTEGRATION_USER"],
		"pass" => $_ENV["PUBLISHER_INTEGRATION_PASSWORD"],
		"host" => $_ENV["PUBLISHER_INTEGRATION_HOST"],
		"db" => $_ENV["PUBLISHER_INTEGRATION_DB"],
	];

	$pubStaging = [
		"user" => $_ENV["PUBLISHER_STAGING_USER"],
		"pass" => $_ENV["PUBLISHER_STAGING_PASSWORD"],
		"host" => $_ENV["PUBLISHER_STAGING_HOST"],
		"db" => $_ENV["PUBLISHER_STAGING_DB"],
	];

	$pubProd = [
		"user" => $_ENV["PUBLISHER_PROD_USER"],
		"pass" => $_ENV["PUBLISHER_PROD_PASSWORD"],
		"host" => $_ENV["PUBLISHER_PROD_HOST"],
		"db" => $_ENV["PUBLISHER_PROD_DB"],
	];

	$mongoUrl = "mongodb://admin:NPJWtBzZPbZ7qWZv@34.222.80.6:27017";
	
	date_default_timezone_set('US/Eastern');
?>