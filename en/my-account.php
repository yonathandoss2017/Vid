<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(@$_SESSION['login'] >= 1){
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
		
		
		/*$companyError = ' data-error="Saraza"';
		$companyErrorc = ' frm-rrr';
		$password2Errorc = ' frm-rrr';
		$password2Error = ' data-error="Las contraseñas no coinciden"';*/
		
		//print_r($_POST);
		if(isset($_POST['update'])){
			if($_POST['update'] == 1){
				if(md5($_POST['old']) == $oldPass){
					if(strlen($_POST['password']) >= 8){
						if($_POST['password'] == $_POST['password2']){
							$newpassword = md5($_POST['password']);
							$sql = "UPDATE " . USERS . " SET password = '$newpassword' WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
							$db->query($sql);
						}else{
							$password2Error = ' data-error="Password and Confirm Password does not match."';
							$password2Errorc = ' frm-rrr';
						}
					}else{
						$passwordError = ' data-error="Password lenght is insufficient."';
						$passwordErrorc = ' frm-rrr';
					}
				}else{
					$oldError = ' data-error="Old password is incorrect."';
					$oldErrorc = ' frm-rrr';
				}
			}elseif($_POST['update'] == 2){
				$sigue = true;
				if($_POST['name'] != ''){
					
				}else{
					$sigue = false;
					$nameError = ' data-error="You must fill in this filed."';
					$nameErrorc = ' frm-rrr';
				}
				if($_POST['lastname'] != ''){
					
				}else{
					$sigue = false;
					$lastnameError = ' data-error="You must fill in this filed."';
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
					$emailError = ' data-error="You must fill in this filed."';
					$emailErrorc = ' frm-rrr';
				}
				if($_POST['phone'] != ''){
					
				}else{
					$sigue = false;
					$nameError = ' data-error="You must fill in this filed."';
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
					
					$sql = "UPDATE " . USERS . " SET name = '$name', lastname = '$lastname', email = '$email', phone = '$phone', movil = '$movil', sykpe = '$skype', whatsapp = '$whatsapp', remember = '$remember' WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
					$db->query($sql);
				}
			}elseif($_POST['update'] == 3){
				$sigue = true;
				$ef = intval($_POST['ef']);
				if($ef > 0){
					
				}else{
					$sigue = false;
					$efError = ' data-error="You must fill in this filed."';
					$efErrorc = ' frm-rrr';
				}
				if($_POST['nif'] != ''){
					
				}else{
					$sigue = false;
					$nifError = ' data-error="You must fill in this filed."';
					$nifErrorc = ' frm-rrr';
				}
				if($_POST['company'] != ''){

				}else{
					$sigue = false;
					$companyError = ' data-error="You must fill in this filed."';
					$companyErrorc = ' frm-rrr';
				}
				
				$country = intval($_POST['country']);
				if($country > 0){
					
				}else{
					$sigue = false;
					$counrtyError = ' data-error="You must fill in this filed."';
					$counrtyErrorc = ' frm-rrr';
				}
				
				if($_POST['city'] != ''){
					
				}else{
					$sigue = false;
					$cityError = ' data-error="You must fill in this filed."';
					$cityErrorc = ' frm-rrr';
				}
				
				if($_POST['province'] != ''){
					
				}else{
					$sigue = false;
					$provinceError = ' data-error="You must fill in this filed."';
					$provinceErrorc = ' frm-rrr';
				}
				
				if($_POST['cp'] != ''){
					
				}else{
					$sigue = false;
					$cpError = ' data-error="You must fill in this filed."';
					$cpErrorc = ' frm-rrr';
				}
				
				if($_POST['address'] != ''){
					
				}else{
					$sigue = false;
					$addressError = ' data-error="You must fill in this filed."';
					$addressErrorc = ' frm-rrr';
				}
				
				if($sigue){
					$nif = my_clean($_POST['nif']);
					$company = my_clean($_POST['company']);
					$city = my_clean($_POST['city']);
					$province = my_clean($_POST['province']);
					$cp = my_clean($_POST['cp']);
					$address = my_clean($_POST['address']);
					
					$sql = "UPDATE " . USERS . " SET ef = '$ef', nifcif = '$nif', company = '$company', country = '$country', city = '$city', province = '$province', cp = '$cp', address = '$address' WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
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
					$sql = "UPDATE " . USERS . " SET paymenttype = '$paymenttype', account = '$account', amount = '$amount'$changeswift WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
					$db->query($sql);
				}else{
					$paymenttypeError = ' data-error="You must choose a Payment method."';
					$paymenttypeErrorc = ' frm-rrr';
				}
			}
		}
		
		$sql = "SELECT * FROM " . USERS . " WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
		$query = $db->query($sql);
		$userData = $db->fetch_array($query);
		//print_r($userData);
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
									<div class="titl">My Account, contact information and billing</div>
								</div>
								<div class="bx-bd">
									<div class="bx-pd">
                                         <div id="tabs">
                                            <ul class="lst-tbs b-fx mb2">
                                            	<li><a href="#fragment-0">Account and connection</a></li>
                                                <li><a href="#fragment-1">Contact Details</a></li>
                                                <li><a href="#fragment-2">Billing Details</a></li>
                                                <li><a href="#fragment-3">Payment</a></li>
                                            </ul>
                                            <div id="fragment-0">
	                                               <div class="usr-sdbr">
													<div class="prf-cn d-fx">
														<figure><img src="<?php
															if($userData['image'] != '' && file_exists('images/' . $userData['image'])){
																echo 'images/' . $userData['image'];
															}else{
																echo 'img/cnt/user.png';
															}
														?>" alt="user" id="profilephoto"></figure>
														<div class="d-flx1">
															<p class="usr-tx"><a href="#"><?php echo $userName; ?></a> <span>Last Connection <?php echo date('d.m.Y / H:i', $userData['lastlogin']); ?></span></p>
															<div class="user-cnx"><label><input type="checkbox" name="aviso" value="2"> Activate last conection notification</label></div>
															<div><a href="#" id="addImage" class="fa-picture-o user-cps">Change profile photo</a></div>
														</div>
													</div>
													<div class="prf-tx">
														<p>To change the password you need to write the old password, if you don't remember it you can <a href="lost.php">click here</a> to receive a mail to change the password on your mail</p>
													</div>
												</div>
												<form action="#fragment-0" method="post">	
													<div class="bx-hd dfl b-fx">
														<div  class="titl">Change passsword</div>
													</div>
												
		                                            <div class="clsd-fx">
	                                                    <div class="clmd04">
	                                                        <!--<Cuenta>-->
															<div class="frm-group d-fx lbl-lf<?php echo $oldErrorc; ?>">
																<label>Account</label>
																<div class="d-flx1">
																	<label<?php echo $oldError; ?>>
																		<input type="password" name="old">
																	</label>
																</div>
															</div>
															<!--</Cuenta>-->
		                                                </div>
		                                                <div class="clmd04">
			                                                <!--<Nueva>-->
															<div class="frm-group d-fx lbl-lf<?php echo $passwordErrorc; ?>">
																<label>New</label>
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
																<label>Repeat New</label>
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
	                                                <div class="frm-group"><input type="submit" value="Save" class="fa-save saver" style="float:right;" /></div>	
													<br style="clear:both;"/>
												</form>
											</div>
                                            <div id="fragment-1">
                                                <!--<ingresos-ultimo-ano>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Contact Details</div>
												</div>
												<form action="#fragment-1" method="post">
													<div class="clsd-fx">
														<!--<Nombre>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
																<label>Name</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
																		<input type="text" name="name" value="<?php echo $userData['name']; ?>" />
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Name"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Nombre>-->
														<!--<Apellidos>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf"<?php echo $lastnameErrorc; ?>>
																<label>Last Name</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $lastnameError; ?>>
																		<input type="text" name="lastname" value="<?php echo $userData['lastname']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Last Name"></span>
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
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="E-mail"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</Email>-->
														<!--<telefono>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $phoneErrorc; ?>">
																<label>Phone</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $phoneError; ?>>
																		<input type="text" name="phone" value="<?php echo $userData['phone']; ?>">
																		<span class="fa-info tt-lt" data-toggle="tooltip" title="Phone Number"></span>
																	</label>
																</div>
															</div>
														</div>
														<!--</telefono>-->
														<!--<Movil>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $movilErrorc; ?>">
																<label>Mobile Phone</label>
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
														<div class="clmd06">
															<div class="frm-group">
																<div class="chk-b fa-envelope-o"><label><input type="checkbox" name="remember" value="1"<?php if($userData['remember'] == 1){ echo ' checked="checked"';} ?>>Remind my password <i class="fa-toggle-off" style="position: absolute; top: 14px;"></i></label></div>
															</div>
														</div>
														<!--</contrasena>-->
													</div>
													<input type="hidden" value="2" name="update" />
													<div class="frm-group"><input type="submit" value="Save" class="fa-save saver" style="float:right;" /></div>	
													<br style="clear:both;"/>
												</form>
												<!--</ingresos-ultimo-ano>-->
		                                    </div>
		                                    <div id="fragment-2">
		                                        <!--<detalles-de-facturacion>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Billing Details</div>
												</div>
												<form action="#fragment-2" method="post">
													<div class="clsd-fx">
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $efErrorc; ?>">
																<label>Account Type</label>
																<div class="d-flx1">
																	<label<?php echo $efError; ?>>
																	<select name="ef" id="ef" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																		<optgroup label="Selecciona tu estado Fiscal">
																			<option value="1">Company</option>
																			<option value="2">Self-employed</option>
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
																<label>NIF/VAT</label>
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
																<label>Company</label>
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
																<label class="lbl-lnht"><strong>Note:</strong> If you are a self-employed it will be your name and last name</label>
															</div>
														</div>
														<!--</Campo>-->
														<!--<Campo>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $countryErrorc; ?>">
																<label>Country</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $countryError; ?>>
																		<select name="country" id="country" data-dropdown-options='{"customClass":"slct-hd", "label":"Select"}'>
																			<optgroup label="Select your country" id="country2">
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
																<label>City</label>
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
																<label>State</label>
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
																<label>Postal Code</label>
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
																<label>Address</label>
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
													<div class="frm-group"><input type="submit" value="Save" class="fa-save saver" style="float:right;" /></div>	
													<br style="clear:both;"/>
												</form>
												<!--</detalles-de-facturacion>-->
		                                    </div>
		                                    <div id="fragment-3">
		                                        <!--<formas-de-pago>-->
												<div class="bx-hd dfl b-fx">
													<div class="titl">Payment</div>
												</div>
												<form action="#fragment-3" method="post">
													<div class="clsd-fx">
														<!--<Tipo de pago>-->
														<div class="clmd06">
															<div class="frm-group d-fx lbl-lf<?php echo $paymenttypeErrorc; ?>">
																<label>Payment Method</label>
																<div class="d-flx1">
																	<label class="lbl-icon ncn-lf"<?php echo $paymenttypeError; ?>>
																		<select name="payment-type" id="payment-type" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																			<optgroup label="Selecciona tu tipo de pago">
																				<option value="1">Paypal</option>
																				<option value="2">Wire Transfer</option>
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
																<label>Minimum amount</label>
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
																<label>Account</label>
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
												<div class="frm-group"><input type="submit" value="Save" class="fa-save saver" style="float:right;" /></div>	
												<br style="clear:both;"/>
												</form>
												<!--</formas-de-pago>-->
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
		$("#country").val(<?php echo intval($userData['country']); ?>).trigger("change");
		$("#ef").val(<?php echo intval($userData['ef']); ?>).trigger("change");
		$("#payment-type").val(<?php echo intval($userData['paymenttype']); ?>).trigger("change");
		$("#amount").val(<?php echo intval($userData['amount']); ?>).trigger("change");
		
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
?>