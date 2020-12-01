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
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "vidoopre-pass_2020";
	$dbhost2 = "aa1nh4ao2doeo1w.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy-advertisers-panel";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

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



	$dbuser2 = "root";
	$dbpass2 = "vidoopre-pass_2020";
	$dbhost2 = "aa1nh4ao2doeo1w.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy-advertisers-panel";
	

	$dblink2=mysqli_connect('127.0.0.1', 'root', 'VidoomyDB99');
	mysqli_select_db($dblink2, 'vidoomy_adv');
	
	$dblink1=mysqli_connect($dbhost2, 'root', $dbpass2);
	mysqli_select_db($dblink1, $dbname2);
	
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