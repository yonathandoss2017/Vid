<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(@$_SESSION['Admin']==1){
		if($_SESSION['Type'] != 1){
			//print_r($_POST);
			
			$userError = '';
			$userErrorc = '';
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
			$amountError = '';
			$amountErrorc = '';
			$accountError = '';
			$accountErrorc = '';
			$swiftError = '';
			$swiftErrorc = '';
			$currencyError = '';
			$currencyErrorc = '';
			$lkqdError = '';
			$lkqdErrorc = '';
			$springserverError = '';
			$springserverErrorc = '';
			$showiError = '';
			$showiErrorc = '';
			$accMError = '';
			$accMErrorc = '';
			
			$user = '';
			$name = '';
			$lastname = '';
			$email = '';
			$phone = '';
			$movil = '';
			$sykpe = '';
			$whatsapp = 0;
			$remember = 0;
			
			$nif = '';
			$company = '';
			$country = 0;
			$city = '';
			$province = '';
			$cp = '';
			$address = '';
			$ef = 0;
			
			$paymenttype = 0;
			$account = '';
			$amount = 0;
			$swift = '';
			$currency = 0;
			
			$lkqd = '';
			$springserver = '';
			$showi = 0;
			$accm = 0;
			
			if(isset($_POST['update'])){
				if($_POST['update'] == 1){
					$sigue = true;
					
					if($_POST['user'] != ''){
						$user = my_clean($_POST['user']);
						$sql = "SELECT COUNT(*) FROM " . USERS . " WHERE user LIKE '$user' LIMIT 1";
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
	
					if($_POST['name'] != ''){
						$name = my_clean($_POST['name']);
					}else{
						$sigue = false;
						$nameError = ' data-error="Debe completar este campo."';
						$nameErrorc = ' frm-rrr';
					}
					
					if($_POST['lastname'] != ''){
						$lastname = my_clean($_POST['lastname']);
					}else{
						$sigue = false;
						$lastnameError = ' data-error="Debe completar este campo."';
						$lastnameErrorc = ' frm-rrr';
					}
					
					if($_POST['email'] != ''){
						$email = my_clean($_POST['email']);
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
						$phone = my_clean($_POST['phone']);
					}else{
						$sigue = false;
						$phoneError = ' data-error="Debe completar este campo."';
						$phoneErrorc = ' frm-rrr';
					}
					
					$movil = my_clean($_POST['movil']);
					$sykpe = my_clean($_POST['skype']);
					if(isset($_POST['whatsapp'])){$whatsapp = intval($_POST['whatsapp']);}else{$whatsapp = 0;}
					if(isset($_POST['remember'])){$remember = intval($_POST['remember']);}else{$remember = 0;}
					
					if(isset($_POST['ef'])){
						$ef = intval($_POST['ef']);
					}else{
						$ef = 0;
					}
					if($ef == 0){
						$sigue = false;
						$efError = ' data-error="Debe completar este campo."';
						$efErrorc = ' frm-rrr';
					}
					
					if($_POST['nif'] != ''){
						$nif = my_clean($_POST['nif']);	
					}else{
						$sigue = false;
						$nifError = ' data-error="Debe completar este campo."';
						$nifErrorc = ' frm-rrr';
					}
					
					if($_POST['company'] != ''){
						$company = my_clean($_POST['company']);
					}else{
						$sigue = false;
						$companyError = ' data-error="Debe completar este campo."';
						$companyErrorc = ' frm-rrr';
					}
					
					if(isset($_POST['country'])){
						$country = intval($_POST['country']);
					}else{
						$country = 0;
					}
					if($country > 0){
						
					}else{
						$sigue = false;
						$countryError = ' data-error="Debe completar este campo."';
						$countryErrorc = ' frm-rrr';
					}
							
					if($_POST['city'] != ''){
						$city = my_clean($_POST['city']);	
					}else{
						$sigue = false;
						$cityError = ' data-error="Debe completar este campo."';
						$cityErrorc = ' frm-rrr';
					}
							
					if($_POST['province'] != ''){
						$province = my_clean($_POST['province']);
					}else{
						$sigue = false;
						$provinceError = ' data-error="Debe completar este campo."';
						$provinceErrorc = ' frm-rrr';
					}
							
					if($_POST['cp'] != ''){
						$cp = my_clean($_POST['cp']);
					}else{
						$sigue = false;
						$cpError = ' data-error="Debe completar este campo."';
						$cpErrorc = ' frm-rrr';
					}
							
					if($_POST['address'] != ''){
						$address = my_clean($_POST['address']);
					}else{
						$sigue = false;
						$addressError = ' data-error="Debe completar este campo."';
						$addressErrorc = ' frm-rrr';
					}
					
					$lkqd = intval($_POST['lkqd']);
					$springserver = intval($_POST['springserver']);
					if($_SESSION['Type'] == 3){
						$accm = intval($_POST['accm']);
					}else{
						$accm = $_SESSION['idAdmin'];
					}
					
					if(isset($_POST['showi'])){
						$showi = intval($_POST['showi']);
					}else{
						$showi = 0;
					}
					
					if($showi == 0){
						$sigue = false;
						$showiError = ' data-error="Debe completar este campo."';
						$showiErrorc = ' frm-rrr';
					}
					
					if($sigue){
						$time = time();
						$date = date('Y-m-d');
						$sql = "INSERT INTO " . USERS . " (user, password, email, name, lastname, phone, movil, whatsapp, sykpe, ef, nifcif, company, country, province, city, cp, address, paymenttype, account, amount, swift, currency, LKQD_id, SS_id, lastlogin, image, remember, showi, AccM, type, time, date) 
							VALUES ('$user', '$password','$email', '$name', '$lastname', '$phone', '$movil', '$whatsapp', '$sykpe', '$ef', '$nif', '$company', '$country', '$province', '$city', '$cp', '$address', '$paymenttype', '$account', '$amount', '$swift', '$currency', '$lkqd', '$springserver', '0', '', '$remember', '$showi', '$accm', 2, '$time', '$date')";
						$db->query($sql);
						
						header('Location: advertisers.php');
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
									<div class="titl">Añadir Anunciante</div>
								</div>
								<div class="bx-bd">
									<div class="bx-pd">
                                    <form action="" method="post">	
                                         <div>
                                            <div id="fragment-0">
	                                            <!--<div class="usr-sdbr">
													<div class="prf-cn d-fx">
														<figure><img src="<?php	echo '../img/cnt/user.png'; ?>" alt="user" id="profilephoto"></figure>
														<div class="d-flx1">
															<div class="user-cnx"><label><input type="checkbox" name="aviso" value="2"> Activar aviso de conexión</label></div>
															<div><a href="#" id="addImage" class="fa-picture-o user-cps">Cambiar imágen al perfil</a></div>
														</div>
													</div>
												</div>-->
												
													<div class="bx-hd dfl b-fx">
														<div  class="titl">Usuario y contraseña</div>
													</div>
												
		                                            <div class="clsd-fx">
		                                                <div class="clmd04">
			                                                <!--<Nueva>-->
															<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
																<label>Usuario</label>
																<div class="d-flx1">
																	<label<?php echo $userError; ?>>
																		<input type="user" name="user" value="<?php echo $user; ?>" />
																	</label>
																</div>
															</div>
															<!--</Nueva>-->
	                                            		</div>
		                                                <div class="clmd04">
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
	                                            		</div>
														<div class="clmd04">
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
													<br style="clear:both;"/>
											</div>
                                            <div id="fragment-1">
                                                <!--<ingresos-ultimo-ano>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Detalles de contacto</div>
												</div>

													<div class="clsd-fx">
														<!--<Nombre>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
																<label>Nombre</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
																		<input type="text" name="name" value="<?php echo $name; ?>" />
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu nombre"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Nombre>-->
														<!--<Apellidos>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $lastnameErrorc; ?>">
																<label>Apellidos</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $lastnameError; ?>>
																		<input type="text" name="lastname" value="<?php echo $lastname; ?>">
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
																		<input type="text" name="email" value="<?php echo $email; ?>">
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
																		<input type="text" name="phone" value="<?php echo $phone; ?>">
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
																		<input type="text" name="movil" value="<?php echo $movil; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu número de teléfono movil"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Movil>-->
														<!--<Whatsapp>-->
														<div class="clmd06">
															<div class="frm-group">
																<div class="chk-b fa-whatsapp"><label><input type="checkbox" name="whatsapp" value="1"<?php if($whatsapp == 1){ echo ' checked="checked"';} ?> />Whatsapp? <i class="fa-toggle-off" style="position: absolute; top: 14px;"></i></label></div>
															</div>
														</div>
														<!--</Whatsapp>-->
														<!--<Skype>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf">
																<label>Skype</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf">
																		<input type="text" name="skype" value="<?php echo $sykpe; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con tu usuario de Skype"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Skype>-->
													</div>
													<br style="clear:both;"/>

												<!--</ingresos-ultimo-ano>-->
		                                    </div>
		                                    <div id="fragment-2">
		                                        <!--<detalles-de-facturacion>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Detalles de facturación</div>
												</div>

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
																		<input type="text" name="nif" value="<?php echo $nif; ?>" >
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
																		<input type="text" name="company" value="<?php echo $company; ?>">
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
																		<input type="text" name="city" value="<?php echo $city; ?>">
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
																		<input type="text" name="province" value="<?php echo $province; ?>">
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
																		<input type="text" name="cp" value="<?php echo $cp; ?>">
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
																		<input type="text" name="address" value="<?php echo $address; ?>">
																	</label>
																</div>
															</div>
														</div>
														<!--</Campo>-->
													</div>
													<br style="clear:both;"/>

												<!--</detalles-de-facturacion>-->
		                                    </div>
		                                    
                                            <div id="fragment-5">
                                                <!--<plataformas>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Plataformas y Permisos</div>
												</div>

													<div class="clsd-fx">
														<!--<LKQD>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $lkqdErrorc; ?>">
																<label>ID LKQD</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $lkqdError; ?>>
																		<input type="text" name="lkqd" value="<?php echo $lkqd; ?>" />
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
																		<input type="text" name="springserver" value="<?php echo $springserver; ?>">
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
												<input type="hidden" value="1" name="update" />
												<div class="frm-group"><input type="submit" value="Añadir Anunciante" class="fa-save saver" style="float:right;" /></div>	
												<br style="clear:both;"/>
												
												<!--</plataformas>-->
                                            </div>
                                        </div>
									</form>
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
		
		$("#payment-type").change(function(){
			if($("#payment-type").val() == 2){
				$("#showswift").show();
			}else{
				$("#showswift").hide();
			}
		});
		
		<?php if($country > 0){ ?>
		$("#country").val(<?php echo $country; ?>).trigger("change");
		<?php } ?>
		<?php if($ef > 0){ ?>
		$("#ef").val(<?php echo $ef; ?>).trigger("change");
		<?php } ?>
		<?php if($paymenttype > 0){ ?>
		$("#payment-type").val(<?php echo $paymenttype; ?>).trigger("change");
		<?php } ?>
		<?php if($amount > 0){ ?>
		$("#amount").val(<?php echo $amount; ?>).trigger("change");
		<?php } ?>
		<?php if($showi > 0){ ?>
		$("#showi").val(<?php echo $showi; ?>).trigger("change");
		<?php } ?>
		<?php if($accm > 0){ ?>
		$("#accm").val(<?php echo $accm; ?>).trigger("change");
		<?php } ?>
		<?php if($currency > 0){ ?>
		$("#currency").val(<?php echo $currency; ?>).trigger("change");
		<?php } ?>
		
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
?>