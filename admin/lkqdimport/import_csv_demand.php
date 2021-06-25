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

	$Date = date('Y-m-d', time() - 800);
	$Hour = date('G', time() - 800);
	//$Hour = date('G', time());
	//$Hour = 4;
	$HFrom = date('G', time() - 8000);
	//$HFrom = 2;
	echo $Date . ': ' . $HFrom . ' - ' . $Hour . "\n";

	if($Hour <= 4){
		$Wh = "WHERE ToDo = 'add' ";
		if($Hour <= 2){
			$sql = "TRUNCATE demandreport";
			$db->query($sql);
		}
	}else{
		$Wh = "";
	}
	
	//exit(0);
	//Import new data from Demand Reports
	
	/*
	
	sleep(rand(1,300));

	$ImportData = getDateDemandReportCSV($Date, $HFrom, $Hour);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getDateDemandReportCSV($Date, $HFrom, $Hour);
	}
	
	//print_r($ImportData);
	//exit(0);
	
	if($ImportData !== false){
		$N = 0;
		$Last = false;
		foreach($ImportData as $DataK => $DataL){
			$Nn = 0;
			foreach($DataL as $Line){
				if($N > 0){
					if($Nn == 0){
						if(strpos($Line, 'T') !== false){
							$arTime = explode("T", $Line);
							$Hour = $arTime[1];
							$Date = $arTime[0];
						}else{
							$Last = true;
							break;
						}
					}
					if($Nn == 1){ $TagId = $Line; }
					if($Nn == 3){ $Domain = $Line; }
					//if($Nn == 4){ $CountryCode = $Line; }
					if($Nn == 4){ $Requests = takeComa($Line); }
					if($Nn == 5){ $Impressions = takeComa($Line); }
				}
				$Nn++;
			}

			//if(isset($Impressions) && isset($Requests)){
			//	$Fill = $Impressions / $Requests * 100;
			//}else{
			//	$Fill = 0;
			//}

			if($N > 0 && $Last === false){
				$sql = "INSERT INTO demandreport (DemandTagID, Domain, Requests, Impressions, Date, Hour) VALUES ('$TagId', '$Domain', '$Requests', '$Impressions', '$Date', '$Hour');";
				$db->query($sql);
			}
			$N++;
		}
		
		$mail = new PHPMailer;
								
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = "alta@vidoomy.com";
		$mail->Password = "RegVidoom1-2";
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('alta@vidoomy.com', 'Vidoomy');
		$mail->addReplyTo('alta@vidoomy.com', 'Vidoomy');
		$mail->addAddress('federico.izuel@vidoomy.com');
		
		$Subject = 'OK';
		
		$mail->Subject = $Subject;
		
		$message = 'Actualizacion realizada.';
		
		$mail->msgHTML($message);
		$mail->send();
	}else{
		$mail = new PHPMailer;
								
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = "alta@vidoomy.com";
		$mail->Password = "RegVidoom1-2";
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('alta@vidoomy.com', 'Vidoomy');
		$mail->addReplyTo('alta@vidoomy.com', 'Vidoomy');
		$mail->addAddress('federico.izuel@vidoomy.com');
		
		$Subject = 'Login error LKQD';
		
		$mail->Subject = $Subject;
		
		$message = 'No se pudo realizar la actualizacion de listados de dominios.';
		
		$mail->msgHTML($message);
		$mail->send();
		
		die('Import demand report fail.');
	}
	
	*/
	
	//Check rules and create update arrays
	$DomainsListsUpdate = array();
	//exit(0);
	
	$sql = "SELECT * FROM demandrules $Wh ORDER BY Priority ASC, id ASC";
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		$RId = $Row['id'];
		$DTag = $Row['DemandTagID'];
		$DomainListId = $Row['DomainListId'];
		$ToDo = $Row['ToDo'];
		
		if(!array_key_exists($DomainListId, $DomainsListsUpdate)){
			$DomainsListsUpdate[$DomainListId]['add'] = array();
			$DomainsListsUpdate[$DomainListId]['remove'] = array();
		}
		
		$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date' GROUP BY Domain ORDER BY `Impressions` DESC";
		//$sql = "SELECT * FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date'";
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
					
					if($Field == 'Fill'){
						if($Row2['Impressions'] > 0 && $Row2['Requests'] > 0){
							$Fill = $Row2['Impressions'] / $Row2['Requests'] * 100;
						}else{
							$Fill = 0;
						}
					}
					if($Field == 'Requests'){
						$Fill = $Row2['Requests'];
					}
					
					if(myOperator($Fill, $Value, $Operation) === false){
						$AcomplishRule = false;
					}
				}
			}
			
			if($AcomplishRule){
				$DomainsListsUpdate[$DomainListId][$ToDo][] = $Row2['Domain'];
			}
		}
	}
	
	if($Hour > 2){
		$sql = "SELECT * FROM demandrules WHERE ToDo = 'add' ORDER BY Priority ASC, id ASC";
		$Query = $db->query($sql);
		while($Row = $db->fetch_array($Query)){
			$RId = $Row['id'];
			$DTag = $Row['DemandTagID'];
			$DomainListId = $Row['DomainListId'];
			$ToDo = $Row['ToDo'];
			
			$Hto = $Hour - 1;
			$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date' AND (Hour = '$HFrom' OR Hour = '$Hto') GROUP BY Domain ORDER BY `Impressions` DESC";
			//$sql = "SELECT * FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date'";
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
						
						if($Field == 'Fill'){
							if($Row2['Impressions'] > 0 && $Row2['Requests'] > 0){
								$Fill = $Row2['Impressions'] / $Row2['Requests'] * 100;
							}else{
								$Fill = 0;
							}
						}
						if($Field == 'Requests'){
							$Fill = $Row2['Requests'];
						}
						
						if(myOperator($Fill, $Value, $Operation) === false){
							$AcomplishRule = false;
						}
					}
				}
				
				if($AcomplishRule){
					$DomainsListsUpdate[$DomainListId][$ToDo][] = $Row2['Domain'];
				}
			}
		}
	}
	
	
	
	//print_r($DomainsListsUpdate);
	exit(0);
	sleep(rand(20,60));
	
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