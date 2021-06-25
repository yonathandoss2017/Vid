<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//echo date("H:i:s\n", time());
	
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	$Date = date('Y-m-d', time());
	$Hour = date('G', time());
	//$Hour = date('G', time());
	//$Hour = 4;
	$HFrom = date('G', time() - 8000);
	//$HFrom = 2;
	echo $Date . ': ' . $HFrom . ' - ' . $Hour . "\n";


	$DGid = 29;
	
	//Check rules and create update arrays
	$DomainsListsUpdate = array();
	//exit(0);
	
	//$sql = "SELECT * FROM demandrules $Wh ORDER BY Priority ASC, id ASC";
	
	$sql = "SELECT dr.Type AS Type, dr.KPI AS KPI, dt.DemandTagID AS DemandTagID, dto.DomainListId AS DomainListId FROM demandtagrules dr
	INNER JOIN demandtags dt ON dt.id = dr.idTag
	INNER JOIN demandtags dto ON dto.id = dr.idTagO
	INNER JOIN demandgroup dg ON dg.id = dt.idGroup
	WHERE dg.Active = 1 AND dg.id = $DGid";
	
	// ADD TO DOMAIN LIST RULES
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		//$RId = $Row['id'];
		$DTag = $Row['DemandTagID'];
		$DomainListId = $Row['DomainListId'];
		$Type = $Row['Type'];
		$KPI = $Row['KPI'];
		
		/*
		if(!array_key_exists($DomainListId, $DomainsListsUpdate)){
			$DomainsListsUpdate[$DomainListId]['add'] = array();
			$DomainsListsUpdate[$DomainListId]['remove'] = array();
		}
		*/
		
		$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date' GROUP BY Domain ORDER BY `Impressions` DESC";
		//$sql = "SELECT * FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date'";
		$Query2 = $db->query($sql);
		while($Row2 = $db->fetch_array($Query2)){

					
			if($Type == 1){
				if($Row2['Impressions'] > 0 && $Row2['Requests'] > 0){
					$Fill = $Row2['Impressions'] / $Row2['Requests'] * 100;
				}else{
					$Fill = 0;
				}
			}elseif($Type == 2){
				$Fill = $Row2['Impressions'];
			}elseif($Type == 3){
				$Fill = $Row2['Requests'];
			}

			if($Fill >= $KPI){
				$DomainsListsUpdate[$DomainListId]['add'][] = $Row2['Domain'];
			}
		}
	}
	
	if($Hour > 2){
		//$sql = "SELECT * FROM demandrules WHERE ToDo = 'add' ORDER BY Priority ASC, id ASC";
		$sql = "SELECT dr.Type AS Type, dr.KPI AS KPI, dt.DemandTagID AS DemandTagID, dto.DomainListId AS DomainListId FROM demandtagrules dr
		INNER JOIN demandtags dt ON dt.id = dr.idTag
		INNER JOIN demandtags dto ON dto.id = dr.idTagO
		INNER JOIN demandgroup dg ON dg.id = dt.idGroup
		WHERE dg.Active = 1 AND dg.id = $DGid";
		$Query = $db->query($sql);
		while($Row = $db->fetch_array($Query)){
			$DTag = $Row['DemandTagID'];
			$DomainListId = $Row['DomainListId'];
			$Type = $Row['Type'];
			$KPI = $Row['KPI'];
			
			$Hto = $Hour - 1;
			$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date' AND (Hour = '$HFrom' OR Hour = '$Hto') GROUP BY Domain ORDER BY `Impressions` DESC";
			//$sql = "SELECT * FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date'";
			$Query2 = $db->query($sql);
			while($Row2 = $db->fetch_array($Query2)){
				if($Type == 1){
					if($Row2['Impressions'] > 0 && $Row2['Requests'] > 0){
						$Fill = $Row2['Impressions'] / $Row2['Requests'] * 100;
					}else{
						$Fill = 0;
					}
				}elseif($Type == 2){
					$Fill = $Row2['Impressions'];
				}elseif($Type == 3){
					$Fill = $Row2['Requests'];
				}
	
				if($Fill >= $KPI){
					$DomainsListsUpdate[$DomainListId]['add'][] = $Row2['Domain'];
				}
			}
		}
	}
	
	if($Hour > 4){
		$sql = "SELECT * FROM demandtags dt INNER JOIN demandgroup dg ON dg.id = dt.idGroup WHERE dt.Open != 1 AND dt.DomainListId > 0 AND dg.Active = 1 AND dg.id = $DGid";
		//";
		$Query = $db->query($sql);
		while($Row = $db->fetch_array($Query)){
			$MinFill = $Row['MinFill'];
			$MinRequests = $Row['MinRequests'];
			$DomainListId = $Row['DomainListId'];
			$DTag = $Row['DemandTagID'];
			
			$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM `demandreport` WHERE DemandTagID = '$DTag' GROUP BY Domain;";
			$Query2 = $db->query($sql);
			while($Row2 = $db->fetch_array($Query2)){
			
				if($Row2['Impressions'] > 0 && $Row2['Requests'] > 0){
					$Fill = $Row2['Impressions'] / $Row2['Requests'] * 100;
				}else{
					$Fill = 0;
				}
				
				if($Fill < $MinFill && $Row2['Requests'] >= $MinRequests){
					/*
					echo $Row2['Requests'];
					echo "\n";
					echo $Fill;
					echo "\n";
					*/
					$DomainsListsUpdate[$DomainListId]['remove'][] = $Row2['Domain'];
				}
			
			}
		}
	}
	print_r($DomainsListsUpdate);
	exit(0);
	
	//sleep(rand(20,60));
	
	//Update Domains Lists
	foreach($DomainsListsUpdate as $WLId => $Domains){

		print_r($Domains);
		//$JsonDomains = json_encode($Domains);
		
		$headers = array(
		    'Content-Type:application/json',
		    'Authorization: Basic '. base64_encode("U0qJXH2r9FCaPdZBr1WXvN1TQdxoEX7D:2fJL9Fx1ft6mAEHbz0112RlCjvEJm_k1EObfVgTtbDc") // <---
		);
		$post = array(
			"entries" => array(
				'adds' => $Domains['add'],
				'removes' => $Domains['remove']
			)
		);
		
		$json_encode = json_encode($post);
		
		//exit(0);
		$url = 'https://api.lkqd.com/restrictions/domain-lists/' . $WLId;
		//$url = 'https://api.lkqd.com/restrictions/domain-lists';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
		$result = curl_exec($ch);
		curl_close($ch);  
		
		//$decoded_result = json_decode($result);
		//print_r($result);
		
	}