<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('../admin/libs/display.lib.php');

	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
		
			
	newGenerateJS(17747);