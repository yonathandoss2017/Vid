<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	/*
	$dbuser2 = "root";
	$dbpass2 = "vidoopre-pass_2020";
	$dbhost2 = "aa1nh4ao2doeo1w.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy-advertisers-panel";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	*/
	
	$dbuser3 = "root";
	$dbpass3 = "vidooprod-pass_2020";
	//$dbhost3 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbhost3 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	require('/var/www/html/login/reports_/adv/common.php');	
	
	$Date = date('Y-m-d', time() - 3600);
	
	updateReportCards($db3, $Date);
	//updateReportCards($db2, $Date);