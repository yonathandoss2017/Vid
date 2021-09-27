<?php	
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common5.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	$db3 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
		
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