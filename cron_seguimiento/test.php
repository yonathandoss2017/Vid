<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	$UserEmail = 'federico.izuel@vidoomy.com';
	//$UserEmail = 'email'.$idAccM.'@vidoom.com';
	$UserName = $Name;
	
	$mail = new PHPMailer;
	
	$mail->isSMTP();
	$mail->SMTPDebug = 1;
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
					
	$mail->addAddress($UserEmail, $UserName);
	$mail->AddBCC('federico.izuel@vidoomy.com');
	
	$MailContent = '<h1><TESTING/h1>';
	
	//$mail->Subject = 'Reporte de variaciones para ' . $Name;
	$mail->Subject = "TEST Reporte de variaciones";
	$mail->msgHTML($MailContent);
	$mail->send();
	
