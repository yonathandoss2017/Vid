<?php
	
	if(date('d', time() - 3600) == 27){
		echo 'IGUAL';
	}
	
	
	
	
/*
	
	@session_start();
	define('CONST',1);
	require('config.php');
	require('../../db.php');
	require('../libs/common_adv.php');
	require('../../config.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);

	$UserId = 5;
	
	$sql = "SELECT roles FROM user WHERE id = $UserId LIMIT 1";
	$Roles = $db->getOne($sql);
	
	$RolesJSON = json_decode($Roles);
	
	if(in_array('ROLE_ADMIN', $RolesJSON)){
		echo 'ADMIN';
	}else{
		if(in_array('ROLE_SALES_MANAGER_HEAD', $RolesJSON)){
			echo 'HEAD';	
		}else{
			echo 'SALES';
		}
	}


	$dblink2=mysqli_connect('127.0.0.1', 'root', 'VidoomyDB99');
	mysqli_select_db($dblink2, 'vidoomy_adv');
	
	$dblink1=mysqli_connect($advPre['host'], 'root', $advPre['pass']);
	mysqli_select_db($dblink1, $advPre['db']);
	
	$table = 'campaign_country';
	
	$Q = mysqli_query($dblink1, "SHOW CREATE TABLE $table");
	
	$tableinfo = mysqli_fetch_array($Q); // get structure from table on server 1
	
	//mysqli_query($dblink2, " $tableinfo[1] "); // use found structure to make table on server 2
	
	$result = mysqli_query($dblink1, "SELECT * FROM $table  "); // select all content		
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$sql = "INSERT INTO $table (".implode(", ",array_keys($row)).") VALUES ('".implode("', '",array_values($row))."')";
		//echo $sql;
	    mysqli_query($dblink2, $sql);
	}
	
	mysqli_close($dblink1); 
	mysqli_close($dblink2);
	
*/