<?php
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	$Ads = file_get_contents('https://cgn.inf.br/ads.txt');
	
	if(strpos($Ads, 'vidoomy.com') !== false){
		echo "Lo tiene";
	}else{
		echo "No lo tiene";
	
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
		$mail->addAddress('marcos.cuesta@vidoomy.com', 'Marcos');
		$mail->addAddress('raquel.fernandez@vidoomy.com', 'Raquel');
		$mail->addAddress('eric.raventos@vidoomy.com', 'Eric');
		$mail->AddBCC('federico.izuel@vidoomy.com');
		
		$mail->Subject = 'ALERTA: Ads.txt cgn.inf.br';
		$mail->msgHTML('https://cgn.inf.br/ads.txt no tiene las lineas de Vidoomy.');
		$mail->send();
	
	}