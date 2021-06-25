<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	//$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('/var/www/html/ads/httpsites.php');
	
	$dbuser2 = "root";
	$dbpass2 = "123123123";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

	
	$dbuser3 = "root";
	$dbpass3 = "123123123";
	$dbhost3 = "backed-up-second-option-hope-last.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	
	$sql = "SELECT * FROM daily_stats WHERE date <= '2019-11-26'";
	$query = $db3->query($sql);
	while($S = $db3->fetch_array($query)){
		$formatloads = $S['formatloads'];
		$impressions = $S['impressions'];
		$clicks = $S['clicks'];
		$first_quartiles = $S['first_quartiles'];
		$mid_points = $S['mid_points'];
		$third_quartiles = $S['third_quartiles'];
		$completes = $S['completes'];
		$usd_revenue = $S['usd_revenue'];
		$eur_revenue = $S['eur_revenue'];
		$usd_cost = $S['usd_cost'];
		$eur_cost = $S['eur_cost'];
		$date = $S['date'];
		$starts = $S['starts'];
		$user_closes = $S['user_closes'];
		$website_zone_id = $S['website_zone_id'];
		$website_id = $S['website_id'];
		$publisher_id = $S['publisher_id'];
		
		$sql = "INSERT INTO supply_daily_stats 
		(formatloads, impressions, clicks, first_quartiles, mid_points, third_quartiles, completes, usd_revenue, eur_revenue, usd_cost, eur_cost, date, starts, user_closes, website_zone_id, website_id, publisher_id) 
		VALUES 
		('$formatloads', '$impressions', '$clicks', '$first_quartiles', '$mid_points', '$third_quartiles', '$completes', '$usd_revenue', '$eur_revenue', '$usd_cost', '$eur_cost', '$date', '$starts', '$user_closes', '$website_zone_id', '$website_id', '$publisher_id')";
		$db2->query($sql);
		//echo $sql;
		//exit(0);
	}