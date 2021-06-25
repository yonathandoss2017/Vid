<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

function migrateSite($idSite){
	global $db, $db2;
	$sql = "SELECT * FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
	$query = $db->query($sql);
	$SiteData = $db->fetch_array($query);
	
	$idUser = $SiteData['idUser'];
	
	$sql = "SELECT AccM FROM users WHERE id = '$idUser'";
	$AccM = $db->getOne($sql);
	
	$sql = "SELECT id FROM publisher WHERE user_id = '$idUser'";
	//echo '<br/>';
	$idPublisher = $db2->getOne($sql);
	if($idPublisher > 0) {
	
		$SiteName = $SiteData['sitename'];
		$SiteURL = $SiteData['siteurl'];
		$TestMode = $SiteData['test'];
		$ContentType = $SiteData['category'];
		
		$FileName = $SiteData['filename'];
		
		if(intval($SiteData['time']) > 0){
			$DateTime = date('Y-m-d H:i:s', $SiteData['time']);
		}else{
			$DateTime = '2018-01-01 00:00:00';
		}
		
		if($SiteData['deleted'] == 1){
			$Status = 3;
		}elseif($SiteData['aproved'] > 0 || $SiteData['eric'] == 3){
			$Status = 4;
		}elseif($SiteData['eric'] == 1){
			$Status = 5;
		}else{
			$Status = 1;
		}
		
		$sql = "SELECT COUNT(*) FROM website WHERE id = '$idSite'";
		if($db2->getOne($sql) == 0){
			$sql = "INSERT INTO website (
				id, 
				created_by,
				sitename, 
				url, 
				filename, 
				status, 
				created_at, 
				publisher_id, 
				content_type,
				is_test_mode
			) 
			VALUES (
				$idSite,
				$idUser,
				'$SiteName',
				'$SiteURL',
				'$FileName',
				'$Status',
				'$DateTime',
				'$idPublisher',
				'$ContentType',
				'$TestMode'
			)";
			
			//echo $sql;
			$db2->query($sql);
			
			$sql = "INSERT INTO account_manager_website (
				account_manager_id,
				website_id
			) 
			VALUES (
				$AccM,
				$idSite
			)";
			$db2->query($sql);
		}
	}
}	
	/*
	$idUser = 3314;
	
	$sql = "SELECT id FROM sites WHERE idUser = '$idUser'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($S = $db->fetch_array($query)){
			migrateSite($S['id']);
		}
	}
	*/
	
	migrateSite(11824);