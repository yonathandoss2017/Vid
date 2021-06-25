<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(@$_SESSION['Admin']==1){
		if(isset($_GET['iduser'])){
			$idUser = intval($_GET['iduser']);
			$firstp = '';
			$secondp = '';
			$thirdp = '';
			if($_SESSION['Type'] == 3){
				$WM = "";
			}elseif($_SESSION['Type'] == 1){
				$idAccM = $_SESSION['idAdmin'];
				$WM = " AND AccM = '$idAccM'";
			}else{
				header('Location: index.php');
				exit(0);
			}
				
			$sql = "SELECT id FROM " . USERS . " WHERE id = '$idUser' $WM LIMIT 1";
			if($db->getOne($sql) > 0){
				$userError = '';
				$userErrorc = '';
				$oldError = '';
				$oldErrorc = '';
				$passwordError = '';
				$passwordErrorc = '';
				$password2Error = '';
				$password2Errorc = '';
				$emailError = '';
				$emailErrorc = '';
				$nameError = '';
				$nameErrorc = '';
				$lastnameError = '';
				$lastnameErrorc = '';
				$phoneError = '';
				$phoneErrorc = '';
				$movilError = '';
				$movilErrorc = '';
				$companyError = '';
				$companyErrorc = '';
				$efError = '';
				$efErrorc = '';
				$nifError = '';
				$nifErrorc = '';
				$countryError = '';
				$countryErrorc = '';
				$provinceError = '';
				$provinceErrorc = '';
				$cityError = '';
				$cityErrorc = '';
				$cpError = '';
				$cpErrorc = '';
				$addressError = '';
				$addressErrorc = '';
				$paymenttypeError = '';
				$paymenttypeErrorc = '';
				$banknameError = '';
				$banknameErrorc = '';
				$bankcountryError = '';
				$bankcountryErrorc = '';
				$bankaddressError = '';
				$bankaddressErrorc = '';
				$swiftError = '';
				$swiftErrorc = '';
				$ibanError = '';
				$ibanErrorc = '';
				$lkqdError = '';
				$lkqdErrorc = '';
				$springserverError = '';
				$springserverErrorc = '';
				$showiError = '';
				$showiErrorc = '';
				$accMError = '';
				$accMErrorc = '';
				$nettError = '';
				$nettErrorc = '';
				$firstpError = '';
				$firstpErrorc = '';
				$secondpError = '';
				$secondpErrorc = '';
				$thirdpError = '';
				$thirdpErrorc = '';
				
				
				$showfp = false;
				$showsp = false;
				$showtp = false;
				$adde1 = false;
				$adde2 = false;
				$adde3 = false;
				
				if(isset($_POST['update'])){
					if($_POST['update'] == 1){
						$sigue = true;
						/*
						if($_POST['user'] != ''){
							$user = my_clean($_POST['user']);
							$sql = "SELECT COUNT(*) FROM " . USERS . " WHERE user LIKE '$user' AND id != '$idUser' LIMIT 1";
							if($db->getOne($sql) > 0){
								$sigue = false;
								$userError = ' data-error="El nombre de usuario ya existe."';
								$userErrorc = ' frm-rrr';
							}
						}else{
							$sigue = false;
							$userError = ' data-error="Debe completar este campo."';
							$userErrorc = ' frm-rrr';
						}
						*/
						$sqlAd = '';
						if($_POST['password'] != ''){
							if(strlen($_POST['password']) >= 8){
								if($_POST['password'] == $_POST['password2']){
									$newpassword = md5($_POST['password']);
									$sqlAd = ", password = '$newpassword' ";
								}else{
									$password2Error = ' data-error="La contraseña Nueva y Repetir Nueva deben coincidir."';
									$password2Errorc = ' frm-rrr';
									$sigue = false;
								}
							}else{
								$passwordError = ' data-error="La nueva contraseña debe tener al menos 8 caractéres."';
								$passwordErrorc = ' frm-rrr';
								$sigue = false;
							}
						}
							
						if($sigue){
							$sql = "SELECT nick FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
							$currentNick = $db->getOne($sql);
							if($currentNick == ''){
								$nick = $_POST['nick'];
							}else{
								$nick = $currentNick;
							}
							
							$sql = "UPDATE " . USERS . " SET nick = '$nick' $sqlAd WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
							
						}
					}elseif($_POST['update'] == 2){
						$sigue = true;
						if($_POST['name'] != ''){
							
						}else{
							$sigue = false;
							$nameError = ' data-error="Debe completar este campo."';
							$nameErrorc = ' frm-rrr';
						}
						if($_POST['lastname'] != ''){
							
						}else{
							$sigue = false;
							$lastnameError = ' data-error="Debe completar este campo."';
							$lastnameErrorc = ' frm-rrr';
						}
						if($_POST['email'] != ''){
							if(check_mail($_POST['email'])){
								
							}else{
								$sigue = false;
								$emailError = ' data-error="El email ingresado no es valido."';
								$emailErrorc = ' frm-rrr';
							}
						}else{
							$sigue = false;
							$emailError = ' data-error="Debe completar este campo."';
							$emailErrorc = ' frm-rrr';
						}
						if($_POST['phone'] != ''){
							
						}else{
							$sigue = false;
							$nameError = ' data-error="Debe completar este campo."';
							$nameErrorc = ' frm-rrr';
						}
						
						if($sigue){
							$name = my_clean($_POST['name']);
							$lastname = my_clean($_POST['lastname']);
							$email = my_clean($_POST['email']);
							$phone = my_clean($_POST['phone']);
							$movil = my_clean($_POST['movil']);
							$skype = my_clean($_POST['skype']);
							if(isset($_POST['whatsapp'])){$whatsapp = intval($_POST['whatsapp']);}else{$whatsapp = 0;}
							if(isset($_POST['remember'])){$remember = intval($_POST['remember']);}else{$remember = 0;}
							
							$sql = "UPDATE " . USERS . " SET name = '$name', lastname = '$lastname', email = '$email', phone = '$phone', movil = '$movil', sykpe = '$skype', whatsapp = '$whatsapp', remember = '$remember' WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
						}
					}elseif($_POST['update'] == 3){
						$sigue = true;
						$ef = intval($_POST['ef']);
						if($ef > 0){
							
						}else{
							$sigue = false;
							$efError = ' data-error="Debe completar este campo."';
							$efErrorc = ' frm-rrr';
						}
						if($_POST['nif'] != ''){
							
						}else{
							$sigue = false;
							$nifError = ' data-error="Debe completar este campo."';
							$nifErrorc = ' frm-rrr';
						}
						if($_POST['company'] != ''){
		
						}else{
							$sigue = false;
							$companyError = ' data-error="Debe completar este campo."';
							$companyErrorc = ' frm-rrr';
						}
						
						$country = intval($_POST['country']);
						if($country > 0){
							
						}else{
							$sigue = false;
							$counrtyError = ' data-error="Debe completar este campo."';
							$counrtyErrorc = ' frm-rrr';
						}
						
						if($_POST['city'] != ''){
							
						}else{
							$sigue = false;
							$cityError = ' data-error="Debe completar este campo."';
							$cityErrorc = ' frm-rrr';
						}
						
						if($_POST['province'] != ''){
							
						}else{
							$sigue = false;
							$provinceError = ' data-error="Debe completar este campo."';
							$provinceErrorc = ' frm-rrr';
						}
						
						if($_POST['cp'] != ''){
							
						}else{
							$sigue = false;
							$cpError = ' data-error="Debe completar este campo."';
							$cpErrorc = ' frm-rrr';
						}
						
						if($_POST['address'] != ''){
							
						}else{
							$sigue = false;
							$addressError = ' data-error="Debe completar este campo."';
							$addressErrorc = ' frm-rrr';
						}
						
						if($sigue){
							$nif = my_clean($_POST['nif']);
							$company = my_clean($_POST['company']);
							$city = my_clean($_POST['city']);
							$province = my_clean($_POST['province']);
							$cp = my_clean($_POST['cp']);
							$address = my_clean($_POST['address']);
							
							$sql = "UPDATE " . USERS . " SET ef = '$ef', nifcif = '$nif', company = '$company', country = '$country', city = '$city', province = '$province', cp = '$cp', address = '$address' WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
						}
					}elseif($_POST['update'] == 4){
						
						$sigue = true;
						$paymenttype = intval($_POST['payment-type']);
						if($paymenttype > 0){
							if($paymenttype == 2){
								$iban = my_clean($_POST['iban']);
								$swift = my_clean($_POST['swift']);
								$bankname = my_clean($_POST['bankname']);
								$bankcountry = intval($_POST['bankcountry']);
								$bankaddress = my_clean($_POST['bankaddress']);
								$changeswift = ", iban = '$iban', swift = '$swift', bankname = '$bankname', bankcountry = '$bankcountry', bankaddress = '$bankaddress'";
							}else{
								$changeswift = '';
							}
						}else{
							$paymenttypeError = ' data-error="Debe elegir un Tipo de pago."';
							$paymenttypeErrorc = ' frm-rrr';
						}
						
						//NetTerms
						if(isset($_POST['nett'])){
							if(is_numeric($_POST['nett'])){
								$nett = intval($_POST['nett']);
							}else{
								$sigue = false;
								$nettError = ' data-error="Debe ingresar un valor numérico."';
								$nettErrorc = ' frm-rrr';
							}
						}else{
							$sigue = false;
							$nettError = ' data-error="Debe completar este campo."';
							$nettErrorc = ' frm-rrr';
						}
						
						if(isset($_POST['firstpc'])){
							if($_POST['firstpc'] == 1){
								if(is_numeric($_POST['firstp'])){
									$firstp = intval($_POST['firstp']);
									$adde1 = true;
								}else{
									$firstp = my_clean($_POST['firstp']);
									$sigue = false;
									$firstpError = ' data-error="Debe ingresar un valor numérico."';
									$firstpErrorc = ' frm-rrr';
								}
							}
						}
						
						if(isset($_POST['secondpc'])){
							if($_POST['secondpc'] == 1){
								if(is_numeric($_POST['secondp'])){
									$secondp = intval($_POST['secondp']);
									$adde2 = true;
								}else{
									$secondp = my_clean($_POST['secondp']);
									$sigue = false;
									$secondpError = ' data-error="Debe ingresar un valor numérico."';
									$secondpErrorc = ' frm-rrr';
								}
							}
						}
						
						if(isset($_POST['thirdpc'])){
							if($_POST['thirdpc'] == 1){
								if(is_numeric($_POST['thirdp'])){
									$thirdp = intval($_POST['thirdp']);
									$adde3 = true;
								}else{
									$thirdp = my_clean($_POST['thirdp']);
									$sigue = false;
									$thirdpError = ' data-error="Debe ingresar un valor numérico."';
									$thirdpErrorc = ' frm-rrr';
								}
							}
						}
						
						if($sigue){
							$account = my_clean($_POST['account']);
							$amount = intval($_POST['amount']);
							
							$sql = "UPDATE " . USERS . " SET paymenttype = '$paymenttype', account = '$account', amount = '$amount', netterms = '$nett'$changeswift WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
							
							if($adde1){
								$sql = "SELECT id FROM " . EXCEPTIONS . " WHERE idUser = '$idUser' AND Payment = 1 LIMIT 1";
								$idE = $db->getOne($sql);
								if($idE > 0){
									$sql = "UPDATE " . EXCEPTIONS . " SET Terms = '$firstp' WHERE id = '$idE' LIMIT 1";
								}else{
									$sql = "INSERT INTO " . EXCEPTIONS . " (idUser, Payment, Terms) VALUES ('$idUser','1','$firstp')";
								}
								$db->query($sql);
							}
							if($adde2){
								$sql = "SELECT id FROM " . EXCEPTIONS . " WHERE idUser = '$idUser' AND Payment = 2 LIMIT 1";
								$idE = $db->getOne($sql);
								if($idE > 0){
									$sql = "UPDATE " . EXCEPTIONS . " SET Terms = '$secondp' WHERE id = '$idE' LIMIT 1";
								}else{
									$sql = "INSERT INTO " . EXCEPTIONS . " (idUser, Payment, Terms) VALUES ('$idUser','2','$secondp')";
								}
								$db->query($sql);
							}
							if($adde3){
								$sql = "SELECT id FROM " . EXCEPTIONS . " WHERE idUser = '$idUser' AND Payment = 3 LIMIT 1";
								$idE = $db->getOne($sql);
								if($idE > 0){
									$sql = "UPDATE " . EXCEPTIONS . " SET Terms = '$thirdp' WHERE id = '$idE' LIMIT 1";
								}else{
									$sql = "INSERT INTO " . EXCEPTIONS . " (idUser, Payment, Terms) VALUES ('$idUser','3','$thirdp')";
								}
								$db->query($sql);
							}
						}
					}elseif($_POST['update'] == 5){
						$sigue = true;
						if($_POST['lkqd'] != ''){
							$lkqd = intval($_POST['lkqd']);
						}else{
							$lkqd = intval($_POST['lkqd']);
							//$sigue = false;
							//$lkqdError = ' data-error="Debe completar este campo."';
							//$lkqdErrorc = ' frm-rrr';
						}
						
						if($_POST['springserver'] != ''){
							$springserver = intval($_POST['springserver']);
						}else{
							$springserver = intval($_POST['springserver']);
							//$sigue = false;
							//$springserverError = ' data-error="Debe completar este campo."';
							//$springserverErrorc = ' frm-rrr';
						}
						
						if(isset($_POST['showi'])){
							$showi = intval($_POST['showi']);
						}else{
							$showi = 0;
						}
						
						if(isset($_POST['accm'])){
							$accm = intval($_POST['accm']);
						}else{
							$accm = 0;
						}
						
						if($showi == 0){
							$sigue = false;
							$showiError = ' data-error="Debe completar este campo."';
							$showiErrorc = ' frm-rrr';
						}
						
						if($_SESSION['Type'] == 3){
							$ifacc = ", AccM = '$accm' ";
						}else{
							$ifacc = "";
						}
						
						if($sigue){
							$sql = "UPDATE " . USERS . " SET LKQD_id = '$lkqd', SS_id = '$springserver', showi = '$showi' $ifacc WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
						}
					}
				}
				
				$sql = "SELECT * FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
				$query = $db->query($sql);
				$userData = $db->fetch_array($query);
				$idCurrency = $userData['currency'];
				$oldPass = $userData['password'];
				$userName = $userData['name'] . ' ' . $userData['lastname'];
				
				$sql = "SELECT * FROM " . EXCEPTIONS . " WHERE idUser = '$idUser'";
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					while($Exc = $db->fetch_array($query)){
						if($Exc['Payment'] == 1){
							$showfp = true;
							$adde1 = true;
							$firstp = $Exc['Terms'];
						}
						if($Exc['Payment'] == 2){
							$showsp = true;
							$adde2 = true;
							$secondp = $Exc['Terms'];
						}
						if($Exc['Payment'] == 3){
							$showtp = true;
							$adde3 = true;
							$thirdp = $Exc['Terms'];
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
    <link rel="stylesheet" href="css/jquery-ui.structure.min.css">
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
							<!--<Mi Cuenta, información de contacto y facturación>-->
							<div class="bx-cn bx-shnone">
								<div class="bx-hd dfl b-fx">
									<div class="titl">Cuenta Pulisher: <?php echo $userData['user']; ?></div>
								</div>
								<div class="bx-bd">
									<div class="bx-pd">
                                         <div id="tabs">
                                            <ul class="lst-tbs b-fx mb2">
                                            	<li><a href="#fragment-0">Perfil y Conexión</a></li>
                                                <li><a href="#fragment-1">Detalles de contacto</a></li>
                                                <li><a href="#fragment-2">Detalles de facturación</a></li>
                                                <li><a href="#fragment-3">Forma de pago</a></li>
                                                <li><a href="#fragment-4">Plataformas y Permisos</a></li>
                                            </ul>
                                            <div id="fragment-0">
	                                               <div class="usr-sdbr">
													<div class="prf-cn d-fx">
														<figure><img src="<?php
															if($userData['image'] != '' && file_exists('../images/' . $userData['image'])){
																echo '../images/' . $userData['image'];
															}else{
																echo '../img/cnt/user.png';
															}
														?>" alt="user" id="profilephoto"></figure>
														<div class="d-flx1">
															<p class="usr-tx"><a href="#"><?php echo $userName; ?></a> <span>Ultima conexión <?php echo date('d.m.Y / H:i', $userData['lastlogin']); ?></span></p>
															<!--<div class="user-cnx"><label><input type="checkbox" name="aviso" value="2"> Activar aviso de conexión</label></div>-->
															<div><a href="#" id="addImage" class="fa-picture-o user-cps">Cambiar imágen al perfil</a></div>
														</div>
													</div>
												</div>
												<form action="" method="post" autocomplete="off">
													<input autocomplete="off" name="hidden" type="text" style="display:none;">
													<div class="bx-hd dfl b-fx">
														<div  class="titl">Cambiar Usuario y Contraseña</div>
													</div>
												
		                                            <div class="clsd-fx">
														<div class="clmd06">
			                                                <!--<Usuario>-->
															<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
																<label>Usuario</label>
																<div class="d-flx1">
																	<label<?php echo $userError; ?>>
																		<input type="user" name="user" value="<?php echo $userData['user']; ?>" disabled="disabled" />
																	</label>
																</div>
															</div>
															<!--</Usuario>-->
	                                            		</div>
														<div class="clmd06">
			                                                <!--<Nick>-->
															<div class="frm-group d-fx lbl-lf">
																<label>DBA</label>
																<div class="d-flx1">
																	<label>
																		<input type="text" name="nick" autocomplete="new-password" value="<?php echo $userData['nick']; ?>"<?php if($userData['nick'] != ''){ echo 'disabled="disabled"'; }?> />
																	</label>
																</div>
															</div>
															<!--</Usuario>-->
	                                            		</div>
		                                                <div class="clmd06">
			                                                <!--<Nueva>-->
															<div class="frm-group d-fx lbl-lf<?php echo $passwordErrorc; ?>">
																<label>Nueva</label>
																<div class="d-flx1">
																	<label<?php echo $passwordError; ?>>
																		<input autocomplete="new-password" type="password" name="password" />
																	</label>
																</div>
															</div>
															<!--</Nueva>-->
	                                            		</div>
														<div class="clmd06">
		                                                    <!--<Repetir Nueva>-->
															<div class="frm-group d-fx lbl-lf<?php echo $password2Errorc; ?>">
																<label>Repetir Nueva</label>
																<div class="d-flx1">
																	<label<?php echo $password2Error; ?>>
																		<input autocomplete="new-password" type="password" name="password2" />
																	</label>
																</div>
															</div>
															<!--</Repetir Nueva>-->
	                                                    </div>
	                                                </div>
	                                                <input type="hidden" value="1" name="update" />
	                                                <div class="frm-group"><input type="submit" value="Guardar Cambios" class="fa-save saver" style="float:right;" /></div>	
													<br style="clear:both;"/>
												</form>
											</div>
                                            <div id="fragment-1">
                                                <!--<ingresos-ultimo-ano>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Detalles de contacto</div>
												</div>
												<form action="#fragment-1" method="post" autocomplete="off">
													<input autocomplete="off" name="hidden" type="text" style="display:none;">
													<div class="clsd-fx">
														<!--<Nombre>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
																<label>Nombre</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
																		<input type="text" name="name" value="<?php echo $userData['name']; ?>" />
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu nombre"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Nombre>-->
														<!--<Apellidos>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf"<?php echo $lastnameErrorc; ?>>
																<label>Apellidos</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $lastnameError; ?>>
																		<input type="text" name="lastname" value="<?php echo $userData['lastname']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tus dos apellidos para tenerte administrado"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Apellidos>-->
														<!--<Email>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $emailErrorc; ?>">
																<label>Email</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $emailError; ?>>
																		<input type="text" name="email" value="<?php echo $userData['email']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu dirección de correo electrónico"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Email>-->
														<!--<telefono>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $phoneErrorc; ?>">
																<label>Teléfono</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $phoneError; ?>>
																		<input type="text" name="phone" value="<?php echo $userData['phone']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu número de teléfono"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</telefono>-->
														<!--<Movil>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $movilErrorc; ?>">
																<label>Movil</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $movilError; ?>>
																		<input type="text" name="movil" value="<?php echo $userData['movil']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu número de teléfono movil"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Movil>-->
														<!--<Whatsapp>-->
														<div class="clmd06">
															<div class="frm-group">
																<div class="chk-b fa-whatsapp"><label><input type="checkbox" name="whatsapp" value="1"<?php if($userData['whatsapp'] == 1){ echo ' checked="checked"';} ?> />Whatsapp? <i class="fa-toggle-off" style="position: absolute; top: 14px;"></i></label></div>
															</div>
														</div>
														<!--</Whatsapp>-->
														<!--<Skype>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf">
																<label>Skype</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf">
																		<input type="text" name="skype" value="<?php echo $userData['sykpe']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu usuario de Skype"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Skype>-->
														<!--<contrasena>-->
														<!--<div class="clmd06">
															<div class="frm-group">
																<div class="chk-b fa-envelope-o"><label><input type="checkbox" name="remember" value="1"<?php if($userData['remember'] == 1){ echo ' checked="checked"';} ?>>Recordarme la contraseña <i class="fa-toggle-off" style="position: absolute; top: 14px;"></i></label></div>
															</div>
														</div>-->
														<!--</contrasena>-->
													</div>
													<input type="hidden" value="2" name="update" />
													<div class="frm-group"><input type="submit" value="Guardar Cambios" class="fa-save saver" style="float:right;" /></div>	
													<br style="clear:both;"/>
												</form>
												<!--</ingresos-ultimo-ano>-->
		                                    </div>
		                                    <div id="fragment-2">
		                                        <!--<detalles-de-facturacion>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Detalles de facturación</div>
												</div>
												<form action="#fragment-2" method="post" autocomplete="off">
													<input autocomplete="off" name="hidden" type="text" style="display:none;">
													<div class="clsd-fx">
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $efErrorc; ?>">
																<label>Estado Fiscal</label>
																<div class="d-flx1">
																	<label<?php echo $efError; ?>>
																	<select name="ef" id="ef" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																		<optgroup label="Selecciona tu estado Fiscal">
																			<option value="1">Empresa</option>
																			<option value="2">Autonomo</option>
																		</optgroup>
																	</select>
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $nifErrorc; ?>">
																<label>NIF/CIF</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $nifError; ?>>
																		<input type="text" name="nif" value="<?php echo $userData['nifcif']; ?>" >
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $companyErrorc; ?>">
																<label>Empresa</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $companyError; ?>>
																		<input type="text" name="company" value="<?php echo $userData['company']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group">
																<label class="lbl-lnht"><strong>Nota:</strong> Si eres Autonomo será tu nombre y apellidos</label>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $countryErrorc; ?>">
																<label>Pais</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $countryError; ?>>
																		<select name="country" id="country" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																			<optgroup label="Selecciona tu pais" id="country2">
																				<?php 
															                        $sql = "SELECT * FROM " . COUNTRIES . " ORDER BY id ASC";
																					$query = $db->query($sql);
																					if($db->num_rows($query) > 0){
																						while($Co = $db->fetch_array($query)){
																							
																							?><option value="<?php echo $Co['id']; ?>"><?php echo $Co['country_name']; ?></option><?php
																						}
																					}
														                        ?>
																			</optgroup>
																		</select>
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $cityErrorc; ?>">
																<label>Ciudad</label>
																<div class="d-flx1">
																	<label<?php echo $cityError; ?>>
																		<input type="text" name="city" value="<?php echo $userData['city']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $provinceErrorc; ?>">
																<label>Provincia</label>
																<div class="d-flx1">
																	<label<?php echo $provinceError; ?>>
																		<input type="text" name="province" value="<?php echo $userData['province']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $cpErrorc; ?>">
																<label>Código Postal</label>
																<div class="d-flx1">
																	<label<?php echo $cpError; ?>>
																		<input type="text" name="cp" value="<?php echo $userData['cp']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd12">
															<div class="frm-group d-fx lbl-lf<?php echo $addressErrorc; ?>">
																<label>Dirección</label>
																<div class="d-flx1">
																	<label<?php echo $addressError; ?>>
																		<input type="text" name="address" value="<?php echo $userData['address']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
													</div>
													<input type="hidden" value="3" name="update" />
													<div class="frm-group"><input type="submit" value="Guardar Cambios" class="fa-save saver" style="float:right;" /></div>	
													<br style="clear:both;"/>
												</form>
												<!--</detalles-de-facturacion>-->
		                                    </div>
		                                    <div id="fragment-3">
		                                        <!--<formas-de-pago>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Forma de pago</div>
												</div>
												<form action="#fragment-3" method="post" autocomplete="off">
													<input autocomplete="off" name="hidden" type="text" style="display:none;">
													<div class="clsd-fx">
														<!--<Tipo de pago>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $paymenttypeErrorc; ?>">
																<label>Tipo de pago</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $paymenttypeError; ?>>
																		<select name="payment-type" id="payment-type" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																			<optgroup label="Selecciona tu tipo de pago">
																				<option value="1">Paypal</option>
																				<option value="2">Transferencia</option>
																			</optgroup>
																		</select>
																	</label>
																</div>
															</div>
														</div>
														<!--</Tipo de pago>-->
														<!--<Importe>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf">
																<label>Importe mínimo</label>
																<div class="d-flx1">
																	<select name="amount" id="amount" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																		<optgroup label="Selecciona tu importe">
																			<option value="100">100</option>
																			<option value="200">200</option>
																			<option value="500">500</option>
																			<option value="1000">1000</option>
																			<option value="5000">5000</option>
																			<option value="10000">10000</option>
																		</optgroup>
																	</select>
																</div>
															</div>
														</div>
														<!--</Importe>-->
														<!--<Cuenta>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf">
																<label>Cuenta</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf">
																		<input type="text" name="account" value="<?php echo $userData['account']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Cuenta>-->
														<!--<NombreBanco>-->
														<div class="clmd06 showbank"<?php if($userData['paymenttype'] != 2) { echo ' style="display:none"';} ?>>
															<div class="frm-group d-fx lbl-lf<?php echo $banknameErrorc; ?>">
																<label>Nombre del Banco</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $banknameError; ?>>
																		<input type="text" name="bankname" value="<?php echo $userData['bankname']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</NombreBanco>-->
														<!--<DireccionBanco>-->
														<div class="clmd06 showbank"<?php if($userData['paymenttype'] != 2) { echo ' style="display:none"';} ?>>
															<div class="frm-group d-fx lbl-lf<?php echo $bankaddressErrorc; ?>">
																<label>Dirección del Banco</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $bankaddressError; ?>>
																		<input type="text" name="bankaddress" value="<?php echo $userData['bankaddress']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</DireccionBanco>-->
														<!--<PaisBanco>-->
														<div class="clmd06 showbank"<?php if($userData['paymenttype'] != 2) { echo ' style="display:none"';} ?>>
															<div class="frm-group d-fx lbl-lf<?php echo $bankcountryErrorc; ?>">
																<label>País del Banco</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $bankcountryError; ?>>
																		<select name="bankcountry" id="bankcountry" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																			<optgroup label="Selecciona">
																				<?php 
															                        $sql = "SELECT * FROM " . COUNTRIES . " ORDER BY id ASC";
																					$query = $db->query($sql);
																					if($db->num_rows($query) > 0){
																						while($Co = $db->fetch_array($query)){
																							
																							?><option value="<?php echo $Co['id']; ?>"><?php echo $Co['country_name']; ?></option><?php
																						}
																					}
														                        ?>
																			</optgroup>
																		</select>
																	</label>
																</div>
															</div>
														</div>
														<!--</PaisBanco>-->
														<!--<SWIFT>-->
														<div class="clmd06 showbank"<?php if($userData['paymenttype'] != 2) { echo ' style="display:none"';} ?>>
															<div class="frm-group d-fx lbl-lf">
																<label>SWIFT</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf">
																		<input type="text" name="swift" value="<?php echo $userData['swift']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</SWIFT>-->
														<!--<IBAN>-->
														<div class="clmd06 showbank"<?php if($userData['paymenttype'] != 2) { echo ' style="display:none"';} ?>>
															<div class="frm-group d-fx lbl-lf<?php echo $ibanErrorc; ?>">
																<label>IBAN</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $ibanError; ?>>
																		<input type="text" name="iban" value="<?php echo $userData['iban']; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</IBAN>-->
														
														<!--<NetTerms>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $nettErrorc; ?>">
																<label>NetTerms</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $nettError; ?>>
																		<input type="text" name="nett" value="<?php echo $userData['netterms']; ?>">
																		<?php if($showfp === false) { ?><a href="#" id="adde">Añadir Excepción</a><?php } ?>
																	</label>
																	
																</div>
															</div>
														</div>
														<!--</NetTerms>-->
														
														<!--<PrimerMes>-->
														<div class="clmd06" <?php if($showfp !== true) { echo ' style="display:none"'; } ?> id="first">
															<div class="frm-group d-fx lbl-lf<?php echo $firstpErrorc; ?>">
																<label>Primer Mes</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $firstpError; ?>>
																		<input type="text" name="firstp" value="<?php echo $firstp; ?>">
																		<input type="hidden" value="<?php if($showfp) { echo 1; } else { echo 0;} ?>" name="firstpc" id="firstpc" />
																		<?php if($showsp === false) { ?><a href="#" id="adde2">Añadir Otra Excepción</a><?php } ?>
																	</label>
																	
																</div>
															</div>
														</div>
														<!--</PrimerMes>-->
														<!--<SegundoMes>-->
														<div class="clmd06" <?php if($showsp !== true) { echo ' style="display:none"'; } ?> id="second">
															<div class="frm-group d-fx lbl-lf<?php echo $secondpErrorc; ?>">
																<label>Segundo Mes</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $secondpError; ?>>
																		<input type="text" name="secondp" value="<?php echo $secondp; ?>">
																		<input type="hidden" value="<?php if($showsp) { echo 1; } else { echo 0;} ?>" name="secondpc" id="secondpc" />
																		<?php if($showtp === false) { ?><a href="#" id="adde3">Añadir Otra Excepción</a><?php } ?>
																	</label>
																	
																</div>
															</div>
														</div>
														<!--</SegundoMes>-->
														<!--<TercerMes>-->
														<div class="clmd06" <?php if($showtp !== true) { echo ' style="display:none"'; } ?> id="third">
															<div class="frm-group d-fx lbl-lf<?php echo $thirdpErrorc; ?>">
																<label>Tercer Mes</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $thirdpError; ?>>
																		<input type="text" name="thirdp" value="<?php echo $thirdp; ?>">
																		<input type="hidden" value="<?php if($showtp) { echo 1; } else { echo 0;} ?>" name="thirdpc" id="thirdpc" />
																	</label>
																	
																</div>
															</div>
														</div>
														<!--</TercerMes>-->
														
													</div>
												<input type="hidden" value="4" name="update" />
												<div class="frm-group"><input type="submit" value="Guardar Cambios" class="fa-save saver" style="float:right;" /></div>	
												<br style="clear:both;"/>
												</form>
												<!--</formas-de-pago>-->
                                            </div>
                                            
                                            <div id="fragment-4">
                                                <!--<plataformas>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Plataformas y Permisos</div>
												</div>
												<form action="#fragment-4" method="post" autocomplete="off">
													<input autocomplete="off" name="hidden" type="text" style="display:none;">
													<div class="clsd-fx">
														<!--<LKQD>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $lkqdErrorc; ?>">
																<label>ID LKQD</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $lkqdError; ?>>
																		<input type="text" name="lkqd" value="<?php if($userData['LKQD_id'] > 0 ) { echo $userData['LKQD_id']; } ?>" />
																	</label>
																</div>
															</div>
														</div>
														<!--</LKQD>-->
														<!--<Springserve>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $springserverErrorc; ?>">
																<label>ID SpringServer</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $springserverError; ?>>
																		<input type="text" name="springserver" value="<?php if($userData['SS_id'] > 0 ) { echo $userData['SS_id']; } ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Springserve>-->
														<!--<Permisos>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $showiErrorc; ?>">
																<label>Permisos</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $showiError; ?>>
																		<select name="showi" id="showi" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																			<optgroup label="Selecciona los permisos">
																				<option value="3">No ver CPM, Plataformas ni Sitios</option>
																				<option value="1">Ver Sitios y Plataformas, No ver CPM</option>
																				<option value="2">Ver CPM, Plataformas y Sitios</option>
																			</optgroup>
																		</select>
																	</label>
																</div>
															</div>
														</div>
														<!--</Permisos>--><?php
															if($_SESSION['Type'] == 3){
														?>
														<!--<Acc Manager>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $accMErrorc; ?>">
																<label>Account Manager</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $accMError; ?>>
																		<select name="accm" id="accm" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																			<optgroup label="Selecciona Account Manager"><?php 
															                        $sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE Deleted = 0  AND id != '15' AND id != 1  ORDER BY Name ASC";
																					$query = $db->query($sql);
																					if($db->num_rows($query) > 0){
																						while($Acc = $db->fetch_array($query)){
																							
																							?><option value="<?php echo $Acc['id']; ?>"><?php echo $Acc['Name']; ?></option><?php
																						}
																					}
														                        ?>																													</optgroup>
																		</select>
																	</label>
																</div>
															</div>
														</div>
														<!--</Acc Manager>--><?php
															}	
														?>
													</div>
												<input type="hidden" value="5" name="update" />
												<div class="frm-group"><input type="submit" value="Guardar Cambios" class="fa-save saver" style="float:right;" /></div>	
												<br style="clear:both;"/>
												</form>
												<!--</plataformas>-->
                                            </div>
                                        </div>
									</div>
								</div>
							</div>
							<!--</Mi Cuenta, información de contacto y facturación>-->
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
    <script src="js/lib/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    <script src="js/jquery.mousewheel-3.0.6.min.js"></script>
    <script src="js/jquery.mCustomScrollbar.min.js"></script>   
	<script src="js/dropdown.js"></script>
	<script src="js/AjaxUpload.2.0.min.js"></script>
<script>
    jQuery(document).ready(function($){
		$( "#tabs" ).tabs();
		<?php if(intval($userData['country']) > 0) {?>
		$("#country").val(<?php echo $userData['country']; ?>).trigger("change");
		<?php } ?> 
		<?php if(intval($userData['bankcountry']) > 0) {?>
		$("#bankcountry").val(<?php echo $userData['bankcountry']; ?>).trigger("change");
		<?php } ?>
		<?php if(intval($userData['ef']) > 0) {?>
		$("#ef").val(<?php echo $userData['ef']; ?>).trigger("change");
		<?php } ?>
		<?php if(intval($userData['paymenttype']) > 0) {?>
		$("#payment-type").val(<?php echo $userData['paymenttype']; ?>).trigger("change");
		<?php } ?>
		<?php if(intval($userData['showi']) > 0) {?>
		$("#showi").val(<?php echo $userData['showi']; ?>).trigger("change");
		<?php } ?>
		<?php if(intval($userData['amount']) > 0) {?>
		$("#amount").val(<?php echo $userData['amount']; ?>).trigger("change");
		<?php } ?>
		<?php if(intval($userData['AccM']) > 0) {?>
		if ( $( "#accm" ).length ) {
			$("#accm").val(<?php echo $userData['AccM']; ?>).trigger("change");
		}
		<?php } ?>
		$("#payment-type").change(function(){
			if($("#payment-type").val() == 2){
				$(".showbank").show();
			}else{
				$(".showbank").hide();
			}
		});
		
		$("#adde").click(function(e){
			e.preventDefault();
			$("#first").show();
			$("#adde").hide();
			
			$("#firstpc").val('1');
		});
		$("#adde2").click(function(e){
			e.preventDefault();
			$("#second").show();
			$("#adde2").hide();
			
			$("#secondpc").val('1');
		});
		$("#adde3").click(function(e){
			e.preventDefault();
			$("#third").show();
			$("#adde3").hide();
			
			$("#thirdpc").val('1');
		});
		
		
	});
	
    $(function() {
    	// Botón para subir la firma
		var btn_firma = $('#addImage'), interval;
			new AjaxUpload('#addImage', {
				action: 'uploadFile.php',
				onSubmit : function(file , ext){
					if (! (ext && /^(jpg|png)$/.test(ext))){
						// extensiones permitidas
						alert('Sólo se permiten Imagenes .jpg o .png');
						// cancela upload
						return false;
					} else {
						$('#loaderAjax').show();
						btn_firma.text('Espere por favor');
						this.disable();
					}
				},
				onComplete: function(file, response){

					// alert(response);
					
					btn_firma.text('Cambiar imágen al perfil');
					
					respuesta = $.parseJSON(response);

					if(respuesta.respuesta == 'done'){
						$('#profilephoto').removeAttr('scr');
						$('#profilephoto').attr('src','images/' + respuesta.fileName);
						$('#loaderAjax').show();
						// alert(respuesta.mensaje);
					}
					else{
						alert(respuesta.mensaje);
					}
						
					$('#loaderAjax').hide();	
					this.enable();	
				}
		});
    });
</script>
</body>
</html><?php
			}else{
				header('Location: index.php');
				exit(0);
			}
		}else{
			header('Location: index.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>