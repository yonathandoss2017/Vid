<?php
	define('CONST',1);
	require_once('/var/www/html/login/config.php');
	require_once('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	$mem_var = new Memcached('lp');
	
	//$mem_var->resetServerList();
	
	$Servers = $mem_var->getServerList();
	
	print_r($Servers);
	
	if(count($Servers) == 0){
		$mem_var->addServer("127.0.0.1", 11211);
		echo 'ADDED';
	}
	
	//sleep(1);
	//print_r($mem_var->getResultCode());
	
	$Servers = $mem_var->getServerList();
	print_r($Servers);
	
	print_r($mem_var->getStats());
	
	print_r($mem_var->getResultCode());
	//exit(0);