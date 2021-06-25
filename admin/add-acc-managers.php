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
	
	if(@$_SESSION['Admin'] == 1 && $_SESSION['Type'] == 3){
		$name = '';
		$email = '';
		$type = 0;
		$nameError = '';
		$nameErrorc = '';
		$nameErrors = '';
		$emailError = '';
		$emailErrorc = '';
		$emailErrors = '';
		$typeError = '';
		$typeErrorc = '';
		$typeErrors = '';
		$passwordError = '';
		$passwordErrorc = '';
		$password2Error = '';
		$password2Errorc = '';
		
		//print_r($_POST);		
		
		if(isset($_POST['save'])){
			$sigue = true;
			
			if($_POST['save'] != ''){
				if($_POST['name'] != ''){
					$name = my_clean($_POST['name']);
				}else{
					$sigue = false;
					$nameError = ' data-error="Debe completar el Nombre."';
					$nameErrorc = ' frm-rrr';
					$nameErrors = ' style="margin-bottom:20px;"';
				}
				
				if($_POST['email'] != ''){
					$email = my_clean($_POST['email']);
				}else{
					$emailError = ' data-error="Debe completar el E-Mail."';
					$emailErrorc = ' frm-rrr';
					$emailErrors = ' style="margin-bottom:20px;"';
				}
				
				if($_POST['type'] != ''){
					$type = intval($_POST['type']);
				}else{
					$typeError = ' data-error="Debe completar el Tipo."';
					$typeErrorc = ' frm-rrr';
					$typeErrors = ' style="margin-bottom:20px;"';
				}
				
				if(strlen($_POST['password']) >= 8){
					if($_POST['password'] == $_POST['password2']){
						$newpassword = md5($_POST['password']);
					}else{
						$sigue = false;
						$password2Error = ' data-error="La contraseñas deben coincidir."';
						$password2Errorc = ' frm-rrr';
					}
				}else{
					$sigue = false;
					$passwordError = ' data-error="La contraseña debe tener al menos 8 caractéres."';
					$passwordErrorc = ' frm-rrr';
				}
				
				if($sigue){
					$Date = date('Y-m-d');
					$Time = time();
										
					$sql = "INSERT INTO " . ACC_MANAGERS . " (Email, Password, Name, Type, LastLogin, IP, Date, Time, Deleted) VALUES ('$email', '$newpassword', '$name', '$type', '0', '', '$Date', '$Time', '0')";
					$db->query($sql);
								
					header('Location: acc-managers.php?new=1');
					exit(0);
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

	<!--<all>-->
	<div class="all">
	
		<?php include 'header.php'; ?>
		
		<!--<bdcn>-->
		<div class="bdcn">
			<div class="cnt">
				<!--<cls>-->
				<div class="cls c-fx">
                                        
					<!--<main>-->
					<main class="clmc12 c-flx1">
						<!--<Estadisticas Avanzadas>-->
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx">
								<div class="titl">Añadir Account Manager</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Añadir Account Manager</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="name" value="<?php echo $name; ?>" />
														</label>
													</div>
												</div>
												<!--</Nombre>-->
												<!--<Email>-->
												<div class="frm-group d-fx lbl-lf<?php echo $emailErrorc; ?>">
													<label<?php echo $emailErrors; ?>>E-Mail</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $emailError; ?>>
															<input type="text" name="email" value="<?php echo $email; ?>" />
														</label>
													</div>
												</div>
												<!--</URL>-->
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $typeErrorc; ?>">
													<label>Tipo</label>
													<div class="d-flx1">
														<label<?php echo $typeError; ?>>
															<select name="type" id="type" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo">
																	<option value="1">Publishers</option>
																	<option value="2">Advertisers</option>
																	<option value="3">Administrador</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Tipo>-->
											</div>
											<div class="clmd06">
                                                <!--<Nueva>-->
												<div class="frm-group d-fx lbl-lf<?php echo $passwordErrorc; ?>">
													<label>Contraseña</label>
													<div class="d-flx1">
														<label<?php echo $passwordError; ?>>
															<input type="password" name="password" />
														</label>
													</div>
												</div>
												<!--</Nueva>-->

                                                <!--<Repetir Nueva>-->
												<div class="frm-group d-fx lbl-lf<?php echo $password2Errorc; ?>">
													<label>Repetir</label>
													<div class="d-flx1">
														<label<?php echo $password2Error; ?>>
															<input type="password" name="password2" />
														</label>
													</div>
												</div>
												<!--</Repetir Nueva>-->
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar" name="save" /> 	
										</div>
									</form>
									</div>
								</div>
								
							</div>
						</div>
						<!--</Estadisticas Avanzadas>-->
					</main>
					<!--<main>-->
					
				</div>
				<!--</cls>-->
			</div>
		</div>
		<!--</bdcn>-->
		
		<?php include 'footer.php'; ?>
			
	</div>
	<!--</all>-->
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    <script>
	    
	jQuery(document).ready(function($){
		<?php if($type > 0){ ?>
		$("#type").val(<?php echo $type; ?>).trigger("change");
		<?php } ?>
	});
	
	</script>
</body>
</html>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>