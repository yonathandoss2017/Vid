<?php	
	@session_start();
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('../../db.php');
	
	require('config.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$CID = mysqli_real_escape_string($db->link, $_GET['c']);
	
	$sql = "SELECT videoURL FROM creativities WHERE creativityId = '$CID' LIMIT 1";
	$videoURL = $db->getOne($sql);
	
	if($videoURL != ''){
		header('Location: ' . $videoURL);
	}else{
		echo "Error";
	}