<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../../config.php');
	require('../../constantes.php');
	require('../../db.php');
	require('../../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	
	$WL = array();
	
	$Date = date('Y-m-d', time() - 3600);
	
	$DomainsListsUpdate = array();
	
	if(date('H') <= 4){
		$Wh = "WHERE ToDo = 'add' ";
	}else{
		$Wh = "";
	}
	
	$sql = "SELECT * FROM demandrules $Wh ORDER BY Priority ASC, id ASC";
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		$RId = $Row['id'];
		$DTag = $Row['DemandTagID'];
		$DomainListId = $Row['DomainListId'];
		$ToDo = $Row['ToDo'];
				

		$sql = "SELECT * FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date'";
		$Query2 = $db->query($sql);
		while($Row2 = $db->fetch_array($Query2)){
			$AcomplishRule = false;
			
			$sql = "SELECT * FROM demandifs WHERE idRule = '$RId'";
			$Query3 = $db->query($sql);
			if($db->num_rows($Query3) > 0){
				$AcomplishRule = true;
				while($Row3 = $db->fetch_array($Query3)){
					$Field = $Row3['Field'];
					$Value = $Row3['Value'];
					$Operation = $Row3['Operation'];
					//echo $Row2[$Field] . '<br/>';
					
					if(!myOperator($Row2[$Field], $Value, $Operation)){
						$AcomplishRule = false;
					}
					
				}
			}
			
			if($AcomplishRule){
				
				$DomainsListsUpdate[$DomainListId][$ToDo][] = $Row2['Domain'];
				
			}
			
		}
	}
	
	//sleep(20);
	
	//print_r($DomainsListsUpdate);
	foreach($DomainsListsUpdate as $WL){
		foreach($WL as $WLId => $Domains){
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
			print_r($result);
		}
	}