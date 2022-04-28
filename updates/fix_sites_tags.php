<?php

	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST', 1);
	
	require '/var/www/html/login/config.php';
	require '/var/www/html/login/constantes.php';
	require '/var/www/html/login/db.php';
	require '/var/www/html/login/common.lib.php';
	require '/var/www/html/login/admin/libs/display.lib.php';
	
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db1 = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	$db3 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	mysqli_set_charset($db->link, 'utf8');
	mysqli_set_charset($db2->link, 'utf8');
	
	
	$sql = "SELECT * FROM `sites` WHERE idUser = 0 AND id = 19580 ORDER BY `sites`.`id` DESC";
	$query = $db1->query($sql);
	if ($db1->num_rows($query) > 0) {
	    while ($Site = $db1->fetch_array($query)) {
		    $idSite = $Site['id'];
		    
		    $sql = "SELECT * FROM website WHERE id = $idSite";
		    $query2 = $db2->query($sql);
		    $SiteInfo = $db2->fetch_array($query2);
		    
		    $idPub = $SiteInfo['publisher_id'];
		    $FileName = $SiteInfo['filename'];
		    
		    $sql = "SELECT user_id FROM publisher WHERE id = $idPub LIMIT 1";
		    $idUser = $db3->getOne($sql);
		    
		   echo  $sql = "UPDATE sites SET idUser = $idUser, filename = '$FileName' WHERE id = $idSite";
		    $db1->query($sql);

		    $sql = "UPDATE supplytag SET idUser = $idUser WHERE idSite = $idSite ";
		    $db1->query($sql);

		    newGenerateJS($idSite);
		    
		    //print_r($SiteInfo);
		    exit(0);
		}
	}