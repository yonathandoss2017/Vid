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
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/reports_/adv/common.php');

	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	$db3 = new SQL($advProd["host"], $advProd["db"], $advProd["user"], $advProd["pass"]);
	
	$sql = "SELECT * FROM activation_deals_fw WHERE status = 0";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Activation = $db3->fetch_array($query)){
			$idAct = $Activation['id'];
			
			$TradingDesk = $Activation['trading_desk_country'];
			$DSP = $Activation['deal_id'];
			$DSPID = $Activation['buyer_id'];
			$idAccountManager = $Activation['created_by'];
			
			$sql = "SELECT * FROM user WHERE id = $idAccountManager LIMIT 1";
			$query2 = $db3->query($sql);
			if($db3->num_rows($query2) > 0){
				$AccountData = $db3->fetch_array($query2);

				$AccountName = $AccountData['name'] . ' ' . $AccountData['last_name'];
				$AccountEmail = $AccountData['email'];
			}
			
			$sql = "UPDATE activation_deals_fw SET status = 1 WHERE id = $idAct LIMIT 1";
			$db3->query($sql);
			
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPDebug = false;
			$mail->Debugoutput = 'html';
			
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->Username = $emailing2Cred['user'];
			$mail->Password = $emailing2Cred['password'];
			$mail->CharSet = 'UTF-8';
			$mail->setFrom($emailing2Cred['user'], 'Vidoomy');
			$mail->addReplyTo($emailing2Cred['user'], 'Vidoomy');
			
			//$EmailPubManager = 'federicoizuel@gmail.com';
			$ToMail = 'federicoizuel@gmail.com';
			$ToName = 'Federico';
			
			$mail->addAddress($ToMail, $ToName);
			$mail->AddBCC($AccountEmail, $AccountName);
			$mail->AddBCC('eric.raventos@vidoomy.com');
			$mail->AddBCC('antonio.simarro@vidoomy.com');
			$mail->AddBCC('marcos.cuesta@vidoomy.com');

			
			//To: Alicia Rodriguez Gascon <arodriguezgascon@freewheel.com>
			//Cc: Victor Solis <vsolis@freewheel.com>
			
			
			$EmailText = "Hola,<br/>
			<br/>
			Me ayudas porfa con este nuevo buyer:<br/>
			<br/>
			Trading Desk: $TradingDesk<br/>
			DSP: $DSP<br/>
			DSP ID: $DSPID<br/>
			<br/>
			Saludos";
		
		
			$mail->Subject = 'Nuevo Buyer';
			$mail->msgHTML($EmailText);
			$mail->send();
			
			
		}
	}