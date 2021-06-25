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
				$lkqdError = '';
				$lkqdErrorc = '';
				$springserverError = '';
				$springserverErrorc = '';
				$showiError = '';
				$showiErrorc = '';
				$accMError = '';
				$accMErrorc = '';
				
				if(isset($_POST['update'])){
					if($_POST['update'] == 1){
						if(strlen($_POST['password']) >= 8){
							if($_POST['password'] == $_POST['password2']){
								$newpassword = md5($_POST['password']);
								$sql = "UPDATE " . USERS . " SET password = '$newpassword' WHERE id = '$idUser' LIMIT 1";
								$db->query($sql);
							}else{
								$password2Error = ' data-error="La contraseña Nueva y Repetir Nueva deben coincidir."';
								$password2Errorc = ' frm-rrr';
							}
						}else{
							$passwordError = ' data-error="La nueva contraseña debe tener al menos 8 caractéres."';
							$passwordErrorc = ' frm-rrr';
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
						$paymenttype = intval($_POST['payment-type']);
						if($paymenttype > 0){
							if($paymenttype == 2){
								$swift = my_clean($_POST['swift']);
								$changeswift = ", swift = '$swift'";
							}else{
								$changeswift = '';
							}
							$account = my_clean($_POST['account']);
							$amount = intval($_POST['amount']);
							$sql = "UPDATE " . USERS . " SET paymenttype = '$paymenttype', account = '$account', amount = '$amount'$changeswift WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
						}else{
							$oldError = ' data-error="Debe elegir un Tipo de pago."';
							$paymenttypeError = ' frm-rrr';
						}
					}elseif($_POST['update'] == 5){
						$sigue = true;
						if($_POST['lkqd'] != ''){
							$lkqd = my_clean($_POST['lkqd']);
						}else{
							//$sigue = false;
							//$lkqdError = ' data-error="Debe completar este campo."';
							//$lkqdErrorc = ' frm-rrr';
						}
						
						if($_POST['springserver'] != ''){
							$springserver = my_clean($_POST['springserver']);
						}else{
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
												<form action="" method="post">	
													<div class="bx-hd dfl b-fx">
														<div  class="titl">Cambiar contraseña</div>
													</div>
												
		                                            <div class="clsd-fx">
		                                                <div class="clmd04">
			                                                <!--<Nueva>-->
															<div class="frm-group d-fx lbl-lf<?php echo $passwordErrorc; ?>">
																<label>Nueva</label>
																<div class="d-flx1">
																	<label<?php echo $passwordError; ?>>
																		<input type="password" name="password" />
																	</label>
																</div>
															</div>
															<!--</Nueva>-->
	                                            		</div>
														<div class="clmd04">
		                                                    <!--<Repetir Nueva>-->
															<div class="frm-group d-fx lbl-lf<?php echo $password2Errorc; ?>">
																<label>Repetir Nueva</label>
																<div class="d-flx1">
																	<label<?php echo $password2Error; ?>>
																		<input type="password" name="password2" />
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
												<form action="#fragment-1" method="post">
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
												<form action="#fragment-2" method="post">
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
												<form action="#fragment-3" method="post">
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
														<!--<SWIFT>-->
														<div class="clmd06" id="showswift"<?php if($userData['paymenttype'] != 2) { echo ' style="display:none"';} ?>>
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
												<form action="#fragment-4" method="post">
													<div class="clsd-fx">
														<!--<LKQD>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $lkqdErrorc; ?>">
																<label>ID LKQD</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $lkqdError; ?>>
																		<input type="text" name="lkqd" value="<?php echo $userData['LKQD_id']; ?>" />
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
																		<input type="text" name="springserver" value="<?php echo $userData['SS_id']; ?>">
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
															                        $sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE Deleted = 0 AND Type = 1 ORDER BY Name ASC";
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
		$("#country").val(<?php echo $userData['country']; ?>).trigger("change");
		$("#ef").val(<?php echo $userData['ef']; ?>).trigger("change");
		$("#payment-type").val(<?php echo $userData['paymenttype']; ?>).trigger("change");
		$("#showi").val(<?php echo $userData['showi']; ?>).trigger("change");
		$("#amount").val(<?php echo $userData['amount']; ?>).trigger("change");
		if ( $( "#accm" ).length ) {
			$("#accm").val(<?php echo $userData['AccM']; ?>).trigger("change");
		}
		
		$("#payment-type").change(function(){
			if($("#payment-type").val() == 2){
				$("#showswift").show();
			}else{
				$("#showswift").hide();
			}
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