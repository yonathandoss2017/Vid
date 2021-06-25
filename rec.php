<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(isset($_GET['code'])){
		$Code = my_clean($_GET['code']);
		$sql = "SELECT id FROM " . PASSWORDRECOVER . " WHERE Code = '$Code' LIMIT 1";
		$idCode = $db->getOne($sql);
		
		if($idCode > 0){
			$Expired = false;
			$sql = "SELECT Time FROM " . PASSWORDRECOVER . " WHERE id = '$idCode' LIMIT 1";
			if($db->getOne($sql) > (time() + 21600)){
				$Expired = true;
			}
		}else{
			header('Location: /');
			exit(0);
		}
	}else{
		header('Location: /');
		exit(0);
	}
	
	$Changed = false;
	$error = false;
	$errorText = '';
	if(isset($_POST['submit']) && $Expired === false){
		if(isset($_POST['password']) && isset($_POST['password2'])){
			if(strlen($_POST['password']) >= 8){
				if($_POST['password'] == $_POST['password2']){
					$sql = "SELECT idUser FROM " . PASSWORDRECOVER . " WHERE id = '$idCode' LIMIT 1";
					$idUser = $db->getOne($sql);
					
					$newPassword = md5($_POST['password']);
					$sql = "UPDATE " . USERS . " SET password = '$newPassword' WHERE id = '$idUser' LIMIT 1";
					$db->query($sql);
					
					$Changed = true;
				}else{
					$errorText = 'Las contraseñas no coinciden.';
					$error = true;
				}
			}else{
				$errorText = 'Las contraseña debe tener al menos 8 caracteres.';
				$error = true;
			}
		}
	}
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
				<span class="md-20">Recuperar Contraseña</span>
			</div>
			<div class="bx-bd"><?php
				
				if($error){
					?><p style="color:red; text-align:center; padding-top:20px;"><?php echo $errorText; ?></p><?php
				}
			
					
				if($Expired){
					?><p style="text-align:center; padding-top:20px; padding-bottom:20px;">El codigo ya expiro.</p><?php
				}elseif($Changed){
					?><p style="text-align:center; padding-top:20px; padding-bottom:20px;">Contraseña cambiada correctamente.<br/><a href="/">Login</a></p><?php
				}else{
					?><form action="" method="post" class="frm-login">
						<div class="frm-group">
							<input type="password" placeholder="Nueva contraseña" name="password" />
						</div>
						<div class="frm-group">
							<input type="password" placeholder="Repetir contraseña" name="password2" />
						</div>							
						<div class="frm-group">
	                        <input class="md-20 login-btn" type="submit" name="submit" value="Cambiar contraseña" style="width: 100%" />
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