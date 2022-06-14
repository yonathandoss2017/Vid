<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$SumImp = 0;
	$SumImpPlus = 0;
	
	$YoDecido = '"Nombre","Nombre","Email","Teléfono","Edad","Comentario"' . "\n";
	$Basta = '"Nombre","Edad","Comentario"' . "\n";
	
	$Date = date('Y-m-d');
	
	$Path = "/var/www/html/login/updates/answers/";
	$YoDecidoFilename = "YoDecido-" . date('Ymd') . ".csv";
	$BastaFilename = "Basta-" . date('Ymd') . ".csv";
	
	$sql = "SELECT * FROM basta ";
	$query = $db->query($sql);
	
	if($db->num_rows($query) > 0){
		
		while($Row = $db->fetch_array($query)){
						
			$Data = json_decode($Row['Content']);
			
			if(!property_exists($Data, 'email')){
				
				//print_r($Data);
				$Basta .= '"' . $Data->name . '","' . $Data->age . '","' . $Data->comments . '"' . "\n";
				
			}else{
				
				$YoDecido .= '"' . $Data->name . '","' . $Data->email . '","' . $Data->phone . '","' . $Data->age . '","' . $Data->comments . '"' . "\n";
				
			}
			
		}
	}
	
	
	file_put_contents($Path . $YoDecidoFilename, $YoDecido);
	file_put_contents($Path . $BastaFilename, $Basta);
	
	$mail = new PHPMailer;
								
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Username = $emailing1Cred['user'];
	$mail->Password = $emailing1Cred['password'];
	$mail->CharSet = 'UTF-8';
	$mail->setFrom($emailing1Cred['user'], 'Vidoomy');
	$mail->addReplyTo($emailing1Cred['user'], 'Vidoomy');
	$mail->addAddress('ernesto.gonzalez@vidoomy.com');
	$mail->AddCC('mayte.santos@vidoomy.com');
	
	$mail->AddBCC('federico.izuel@vidoomy.com');
	$mail->AddBCC('eric.raventos@vidoomy.com');
	
	$mail->AddAttachment($Path . $YoDecidoFilename, $YoDecidoFilename);
	$mail->AddAttachment($Path . $BastaFilename, $BastaFilename);
	
	$mail->Subject = "$Date Respuestas campaña Juntos por el Cambio";
	$mail->msgHTML("$Date Respuestas campaña Juntos por el Cambio");
	$mail->send();
	