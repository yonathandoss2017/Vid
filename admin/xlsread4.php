<?php	
	//@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	/*if($_SESSION['Admin']!=1){
		header('Location: /');
		exit(0);
	}*/
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	//require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	require_once 'simplexlsx.class.php';
	
	$n = 0;
	//$xlsx = new SimpleXLSX('xls.xlsx');
	$xlsx = new SimpleXLSX('data_1.xlsx');
	foreach( $xlsx->rows(1) as $row){
		$n++;
		if($n > 3){
			$APP_NAME = $row[0];
			$BUNDLE = $row[1];
			$STORE_URL = $row[2];
			$WIDTH = $row[3];
			$HEIGHT = $row[4]; 
			
			echo $sql = "INSERT INTO 2xls2 (APP_NAME, BUNDLE, STORE_URL, WIDTH, HEIGHT) VALUES ('$APP_NAME','$BUNDLE','$STORE_URL','$WIDTH','$HEIGHT');";
			echo '<br/>';
			$db->query($sql);
			echo mysqli_errno($db->link) . ": " . mysqli_error($db->link) . "\n";

			
			//exit(0);
			
		}
		//print_r($row);
		
		if($n >= 19){
			exit(0);
		}
	}
	
	
	
	
?>