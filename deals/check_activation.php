<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	$db3 = new SQL($advProd["host"], $advProd["db"], $advProd["user"], $advProd["pass"]);
	
	/*
		$db3 = new SQL($advPre["host"], $advPre["db"], $advPre["user"], $advPre["pass"]);
	*/
	

	require('/var/www/html/login/reports_/adv/common.php');
	
	$sql = "SELECT * FROM activation_deals_fw WHERE status = 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Activation = $db3->fetch_array($query)){
			$idAct = $Activation['id'];
			$DSPID = $Activation['buyer_id'];
			$idAccountManager = $Activation['created_by'];

			if(is_numeric($DSPID)){
				$BuyersJson = json_decode(file_get_contents('https://sfx.freewheel.tv/api/inbound/buyer?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b'));
				
				$isActive = false;
				
				foreach($BuyersJson->results as $ActiveBIds){
					//echo $DSPID;
					if($ActiveBIds->{'seat-id'} == $DSPID){
						$isActive = true;
					}	
				}
				
				if($isActive){
					$sql = "UPDATE activation_deals_fw SET status = 2 WHERE id = $idAct LIMIT 1";
					$db3->query($sql);
							
					$sql = "SELECT * FROM user WHERE id = $idAccountManager LIMIT 1";
					$query2 = $db3->query($sql);
					if($db3->num_rows($query2) > 0){
						$AccountData = $db3->fetch_array($query2);
		
						$AccountName = $AccountData['name'] . ' ' . $AccountData['last_name'];
						$AccountEmail = $AccountData['email'];
					}
				
					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->SMTPDebug = false;
					$mail->Debugoutput = 'html';
					
					$mail->Host = 'smtp.gmail.com';
					$mail->Port = 465;
					$mail->SMTPSecure = 'ssl';
					$mail->SMTPAuth = true;
					//$mail->Username = "notify@vidoomy.net";
					//$mail->Password = "NosdFiY-98";
					$mail->Username = "notifysystem@vidoomy.net";
					$mail->Password = "NoTyFUCK05-1";
					$mail->CharSet = 'UTF-8';
					$mail->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
					$mail->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');
										
					$mail->addAddress($AccountEmail, $AccountName);
					$mail->AddBCC('federico.izuel@gmail.com');
					$mail->AddBCC('eric.raventos@vidoomy.com');
					$mail->AddBCC('antonio.simarro@vidoomy.com');
					$mail->AddBCC('marcos.cuesta@vidoomy.com');
										
					$EmailText = "Hola $AccountName,<br/>
					<br/>
					Freewheel ya ha activado el Buyer ID \"$DSPID\", ya puedes crear un Deal con este Buyer ID!<br/>
					<br/>
					Saludos!";
				
				
					$mail->Subject = 'Buyer ID Activado';
					$mail->msgHTML($EmailText);
					$mail->send();
					
				}
			}
		}
	}