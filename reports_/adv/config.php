<?php
	@require_once("secure.php");

	$dbuser2="root";
	$dbpass2="VidoomyDB99";
	$dbhost2="localhost";
	$dbname2="vidoomy_adv";

    if (in_array("APP_ENV", $_ENV) && $_ENV["APP_ENV"] == 'local') {
        $dbuser2=$_ENV["ADVERTISERS_LOCAL_USER"];
        $dbpass2=$_ENV["ADVERTISERS_LOCAL_PASSWORD"];
        $dbhost2=$_ENV["ADVERTISERS_LOCAL_HOST"];
        $dbname2=$_ENV["ADVERTISERS_LOCAL_DB"];
    }

	date_default_timezone_set('UTC');
?>