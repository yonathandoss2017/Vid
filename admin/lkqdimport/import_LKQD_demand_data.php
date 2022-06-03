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
	//echo "$dbhost, $dbname, $dbuser, $dbpass";
	//exit(0);
	//echo date("H:i:s\n", time());
	
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
	
	$Die = false;
	$Date = date('Y-m-d');
	$Date2 = date('Y-m-d');
	$Hour = intval(date('G'));
	//$Hour = date('G', time());
	//$Hour = 10;
	//$HFrom = $Hour - 2;
	//$HFrom = 2;
	$HFrom = 0;
	
	if($Hour == 0){
		$Date = date('Y-m-d', time() - 20000);
		$Hour = 24;
		$HFrom = 22;
	}
	
	echo $Date . ': ' . $HFrom . ' - ' . $Hour . "\n";
	
	
	if($Hour <= 2){
		$sql = "TRUNCATE demandreport";
		$db->query($sql);
	}
	
	//exit(0);
	//Import new data from Demand Reports
	
	//sleep(rand(1,300));

	$ImportData = getDateDemandReportCSV($Date, $HFrom, $Hour);

	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getDateDemandReportCSV($Date, $HFrom, $Hour);
	}
	
	
	//print_r($ImportData);
	if(count($ImportData) == 0){
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
		$mail->Subject = 'LKQD Optimize Error';
		$mail->msgHTML('No results were found.');
		$mail->send();
		exit(0);
	}
	
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
							$Hourr = $arTime[1];
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

			if($N > 0 && $Last === false && $Date != ''){
				$sql = "INSERT INTO demandreport (DemandTagID, Domain, Requests, Impressions, Date, Hour) VALUES ('$TagId', '$Domain', '$Requests', '$Impressions', '$Date', '$Hourr');";
				$db->query($sql);
			}
			$N++;
		}

		$Subject = 'OK 1';
		$message = 'Actualizacion realizada.';
		
	}else{

		$Subject = 'Login error LKQD';
		$message = 'No se pudo realizar la actualizacion de listados de dominios.';
		
		$Die = true;
		
	}
	
	//exit(0);
	
	$mail = new PHPMailer;
								
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Username = "notify@vidoomy.net";
	$mail->Password = "NosdFiY-#98";
	$mail->CharSet = 'UTF-8';
	$mail->setFrom('notify@vidoomy.net', 'Vidoomy');
	$mail->addReplyTo('notify@vidoomy.net', 'Vidoomy');
	$mail->addAddress('federico.izuel@vidoomy.com');
	$mail->Subject = $Subject;
	$mail->msgHTML($message);
	$mail->send();
	
	if($Die){
		die('Import demand report fail.');
	}
	
	//Check rules and create update arrays
	$DomainsListsUpdate = array();
	//exit(0);
	
	//$sql = "SELECT * FROM demandrules $Wh ORDER BY Priority ASC, id ASC";
	
	$sql = "SELECT dr.Type AS Type, dr.KPI AS KPI, dt.DemandTagID AS DemandTagID, dto.DomainListId AS DomainListId FROM demandtagrules dr
	INNER JOIN demandtags dt ON dt.id = dr.idTag
	INNER JOIN demandtags dto ON dto.id = dr.idTagO
	INNER JOIN demandgroup dg ON dg.id = dt.idGroup
	WHERE dg.Active = 1";
	
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
		
		$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date2' GROUP BY Domain ORDER BY `Impressions` DESC";
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
		WHERE dg.Active = 1";
		$Query = $db->query($sql);
		while($Row = $db->fetch_array($Query)){
			$DTag = $Row['DemandTagID'];
			$DomainListId = $Row['DomainListId'];
			$Type = $Row['Type'];
			$KPI = $Row['KPI'];
			
			$Hto = $Hour - 1;
			$sql = "SELECT Domain, SUM(Requests) AS Requests, SUM(Impressions) AS Impressions FROM demandreport WHERE DemandTagID = '$DTag' AND Date = '$Date2' AND (Hour = '$HFrom' OR Hour = '$Hto') GROUP BY Domain ORDER BY `Impressions` DESC";
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
		$sql = "SELECT * FROM `demandtags` WHERE Open != 1 AND DomainListId > 0";
		//INNER JOIN demandgroup dg ON dg.id = dt.idGroup WHERE dg.Active = 1";
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
					
					$sql = "DELETE FROM demandreport WHERE DemandTagID = '$DTag' AND Domain LIKE '" . $Row2['Domain'] . "'";
					$db->query($sql);
				}
			
			}
		}
	}
	//print_r($DomainsListsUpdate);
	//exit(0);
	
	//sleep(rand(20,60));
	
	//Update Domains Lists
	foreach($DomainsListsUpdate as $WLId => $Domains){

		//print_r($Domains);
		//$JsonDomains = json_encode($Domains);
		
		$headers = array(
		    'Content-Type:application/json',
		    'Authorization: Basic '. base64_encode("wX2ZJqf1xkesZnSw8KsZIHyTjeyumwKc:UevGoz-SOIAd2xFm-uyXaZbOxXnI8ccs-4FR7KxhfNY") // <---
		);
		if(isset($Domains['remove'])){
			$post = array(
				"entries" => array(
					'adds' => $Domains['add'],
					'removes' => $Domains['remove']
				)
			);
		}else{
			$post = array(
				"entries" => array(
					'adds' => $Domains['add'],
					'removes' => null
				)
			);

		}
		
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
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
		$result = curl_exec($ch);
		curl_close($ch);  
		
		//$decoded_result = json_decode($result);
		print_r($result);
		//exit(0);
		
	}
	
	$LogFileName = date('Ymd-G');
	
	file_put_contents("/var/www/html/login/admin/lkqdimport/log/$LogFileName", print_r($DomainsListsUpdate, true));
	