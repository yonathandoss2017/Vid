<?php
	define('CONST',1);
	require_once('/var/www/html/login/config.php');
	require_once('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	$mem_var = new Memcached('lp');
	$Servers = $mem_var->getServerList();
	if(count($Servers) == 0){
		$mem_var->addServer("localhost", 11211);
	}
	
	//print_r($mem_var->getStats());
	//exit(0);
	
	$Date = date('Y-m-d-G', time() - 120);
	
	$DateIns = date('Y-m-d', time() - 120);
	$HourIns = date('G', time() - 120);
	
	$sql = "SELECT * FROM sites WHERE deleted = 0 ORDER BY id ASC"; //id = 5986 OR id = 4698 OR id = 5987 OR id = 5985 OR id = 345
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			
			$Count = intval($mem_var->get("FL$idSite-$Date"));
			
			
			
			if($Count > 0){
				echo "FL$idSite-$Date : $Count \n";
				
				$sql = "SELECT id FROM ourformatloads WHERE idSite = '$idSite' AND Date = '$DateIns' AND Hour = '$HourIns'";
				$idIns = $db->getOne($sql);
				
				if($idIns > 0){
					$sql = "UPDATE ourformatloads SET FormatLoads = $Count WHERE id = '$idIns' LIMIT 1";
					$db->query($sql);
				}else{
					$sql = "INSERT INTO ourformatloads (idSite, Date, Hour, FormatLoads) VALUES ('$idSite', '$DateIns', '$HourIns', '$Count')";
					$db->query($sql);
				}
			}
		}
	}
	
	
	
	