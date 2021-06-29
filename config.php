<?php
	@require_once("secure.php");
	require_once("../vendor/autoload.php");

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

	$advDev = [
		"user" => $_ENV["ADVERTISERS_DEV_USER"],
		"pass" => $_ENV["ADVERTISERS_DEV_PASSWORD"],
		"host" => $_ENV["ADVERTISERS_DEV_HOST"],
		"db" => $_ENV["ADVERTISERS_DEV_DB"],
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