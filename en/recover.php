<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	require '../include/PHPMailer/PHPMailerAutoload.php';
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vidoomy</title>
    <meta name="description" content="Vidoomy">
    <meta name="keywords" content="Vidoomy">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Cabin:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/fa.css">
    <link rel="stylesheet" href="css/css.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<!--<lgn-cn>-->
	<div class="lgn-cn ctr b06c05d04">
		<div class="logo"><img src="img/vidoomy-logo.png" alt="vidoomy"></div>
		<!--<bx>-->
		<div class="bx-cn">
			<div class="bx-hd dfl b-fx">
				<span class="md-20">Recover password</span>
			</div>
			<div class="bx-bd"><?php
				$showform = true;
				$showmessage = false;
				if(isset($_POST['email'])){
					$showmessage = true;
					$error = true;
					$errorText = 'Invalid E-mail address.';
					if(strlen($_POST['email']) > 3){
						if(check_mail($_POST['email'])){
							$emailc = my_clean($_POST['email']);
							$sql = "SELECT id FROM " . USERS . " WHERE email LIKE '$emailc' LIMIT 1";
							$idUser = $db->getOne($sql);
							if($idUser > 0){
								$sql = "SELECT email FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
								$to = $db->getOne($sql);
								
								$Code = md5(time() . 'ASD' . $idUser);
								$Time = time();
					
								$sql = "INSERT INTO " . PASSWORDRECOVER . " (idUser, Code, Time) VALUES ('$idUser', '$Code', '$Time')";
								$db->query($sql);
								
								$error = false;
								$errorText = 'We have sent an email to your box indicating the instructions to recover your password.';
								$showform = false;
								
								$subject = 'Recover password Vidoomy.com';
								
								/*
								$headers = "From: info@vidoomy.com\r\n";
								$headers .= "Reply-To: info@vidoomy.com\r\n";
								$headers .= "MIME-Version: 1.0\r\n";
								$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
								*/
								
								$message = '<html><body>';
								$message .= '<img src="https://login.vidoomy.com/img/vidoomy-logo.png" /><br /><br /><br />';
								$message .= "<p>To recover your password <a href='https://login.vidoomy.com/en/rec.php?code=$Code'>click here</a></p>";
											
								$message .= "<p>If it does not work you can copy the following link and paste it into your browser: <strong>https://login.vidoomy.com/rec.php?code=$Code</strong></p>";
								$message .= "<p>This link will only work for 6 hours.</p>";
								$message .= "</body></html>";
											
								$mail = new PHPMailer;

								$mail->SMTPDebug = 0;                               // Enable verbose debug output
								$mail->CharSet = 'UTF-8';
								$mail->isSMTP();                                      // Set mailer to use SMTP
								$mail->Host = 'smtp.gmail.com'; 					  // Specify main and backup SMTP servers
								$mail->SMTPAuth = true;                               // Enable SMTP authentication
								$mail->Username = 'recover@vidoomy.com'; 		      // SMTP username
								$mail->Password = 'Vid2ijji24';                       // SMTP password
								$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
								$mail->Port = 465;                                    // TCP port to connect to

								$mail->setFrom('recover@vidoomy.com', 'Vidoomy');
								//$mail->addAddress('info@vidoomy.com', 'Vidoomy');     // Add a recipient
								//$mail->addAddress('federico.izuel@vidoomy.com');
								$mail->addAddress($to);
								$mail->isHTML(true);                                  // Set email format to HTML

								$mail->Subject = utf8_decode($subject);
								$mail->Body    = $message;
								//$mail->AltBody = 'Nombre: '.$nombre.' - Telefono: '.$tlf.' - Email: '.$email.' - Campaing: '.$camp. '- Mensaje: '.$cnt.'';
								
								if ($mail->send()) {
									//echo 'ok';
								} else {
									//echo 'ko';
									//echo "Mailer Error: " . $mail->ErrorInfo;
								}
								
								
								
							}
						}
					}
				}
				
				if($showmessage){
					if($error){
						?><p style="color:red; text-align:center; padding-top:20px;"><?php echo $errorText; ?></p><?php
					}else{
						?><p style="text-align:center; padding-top:20px; padding-bottom:20px;"><?php echo $errorText; ?></p><?php
					}
				}
					
				if($showform){
					?><form action="" method="post" class="frm-login">
						<div class="frm-group">
							<input type="text" placeholder="E-mail" name="email" />
						</div>					
						<div class="frm-group">
	                        <input class="md-20 login-btn" type="submit" value="Request Password" style="width: 100%" />
	                    </div>
					</form><?php
				}
			?></div>
		</div>
		<!--</bx>-->
	</div>
	<!--</lgn-cn>-->
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
</body>
</html>