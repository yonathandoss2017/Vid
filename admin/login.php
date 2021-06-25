<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$ShowCaptcha = false;
	if(isset($_SESSION['LoginFail'])){
		if($_SESSION['LoginFail'] >= 5){
			$ShowCaptcha = true;
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
    <script type="text/javascript">
	function formSubmit() {
		document.getElementById('loginform').submit();
	}    
	</script>
	<?php if($ShowCaptcha) { ?><script src='https://www.google.com/recaptcha/api.js'></script><?php } ?>
</head>
<body>

	<!--<lgn-cn>-->
	<div class="lgn-cn ctr b06c05d04">
		<div class="logo"><img src="img/vidoomy-logo.png" alt="vidoomy"></div>
		<!--<bx>-->
		<div class="bx-cn">
			<div class="bx-hd dfl b-fx">
				<span class="md-20">Panel de Administración</span>
			</div>
			<div class="bx-bd">
				<form action="index.php" class="frm-login" id="loginform" method="post">
					<div class="frm-group">
						<input type="text" placeholder="Usuario" name="user" />
					</div>					
					<div class="frm-group">
						<input type="password" placeholder="Contraseña" name="pass" />
					</div>
                    <div class="frm-group">
                        <button class="md-20 login-btn" type="submit" onclick="return formSubmit();">Acceder</button>
                    </div>
                    <?php if($ShowCaptcha) { ?>
                    <div class="frm-group">
                    	<div class="g-recaptcha" data-sitekey="6Lf2fGoUAAAAADY872AI_c5oUAMYOkBH2II_M9oP"></div>
					</div>
                    <?php } ?>
				</form>
			</div>
		</div>
		<!--</bx>-->
	</div>
	<!--</lgn-cn>-->
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
</body>
</html>