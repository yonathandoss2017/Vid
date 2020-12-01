<?php
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$mem_var = new Memcached();
	$mem_var->addServer("localhost", 11211);
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	if($_SESSION['Admin']!=1){
		if($_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8001' || $_SERVER['HTTP_ORIGIN'] == 'http://localhost:8001' || $_SERVER['HTTP_ORIGIN'] == 'https://native.vidoomy.com'){
			//exit();
		}else{
			header('Location: https://login.vidoomy.com/admin/login.php');
			exit(0);
		}
	}
	
	
	
	
	
	
	
	