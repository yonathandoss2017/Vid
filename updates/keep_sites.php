<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/libs/display.lib.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	/*
	$sql = "SELECT publisher.user_id, website.* FROM website
	INNER JOIN publisher ON publisher.id = website.publisher_id 
	WHERE website.id >= 10000";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($S = $db2->fetch_array($query)){
			
			$idSite = $S['id'];
			
			$sql = "SELECT COUNT(*) FROM sites WHERE id = '$idSite' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$SiteName = $S['sitename'];
				$URL = $S['url'];
				$Filename = $S['filename'];
				$idUser = $S['user_id'];
				$Category = $S['content_type'];
				
				$sql = "INSERT INTO sites 
				(id, idUser, sitename, siteurl, category, image, filename, eric, time, date) 
				VALUES 
				('$idSite', '$idUser', '$SiteName', '$URL','$Category', '', '$Filename', '1', '$Time', '$Date')";
				$db->query($sql);
				
				newGenerateJS($idSite);
			}
		}
	}
	*/
	$DateTime = date('Y-m-d H:i:s', time() - 300);
	
	$sql = "SELECT publisher.user_id, website.* FROM website
	INNER JOIN publisher ON publisher.id = website.publisher_id 
	WHERE website.updated_at >= '$DateTime'";

	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($S = $db2->fetch_array($query)){
			$idSite = $S['id'];
			$UA = $S['updated_at'];
			
			$sql = "SELECT updated FROM sites WHERE id = '$idSite' LIMIT 1";
			$MyUpdated = $db->getOne($sql);
			
			if($MyUpdated != $UA){
			
				$Sitename = $S['sitename'];
				$URL = $S['url'];
				$Cat = $S['content_type'];
				$Test = $S['is_test_mode'];
				
				$sql = "UPDATE sites SET sitename = '$Sitename', siteurl = '$URL', category = '$Cat', test = '$Test', updated = '$UA' WHERE id = '$idSite' LIMIT 1";
				$db->query($sql);
				
				newGenerateJS($idSite);
			}
		}
	}