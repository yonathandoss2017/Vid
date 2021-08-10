<?php	
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common5.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
		$dbuser2 = "root";
		$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
		$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
		$dbname2 = "vidoomy";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		$db3 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		
	$sql = "SELECT * FROM vidoomy.publisher WHERE receive_monthly_report IS NULL;";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Camp = $db2->fetch_array($query)){
			$idPub = $Camp['id'];
			$sql2 = "UPDATE vidoomy.publisher SET receive_monthly_report = 0 WHERE id = '$idPub' LIMIT 1";
			$db3->query($sql2);
			echo $sql2 . "\n";
		}
	}