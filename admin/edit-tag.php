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
	
	exit(0);
	
	if(@$_SESSION['Admin'] >= 1){
		if(isset($_GET['idtag'])){
			$idTag = intval($_GET['idtag']);
			if($idTag > 0){
				$sql = "SELECT * FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
				$query = $db->query($sql);
				$tagData = $db->fetch_array($query);
				
				$idUser = $tagData['idUser'];
				$tagname = $tagData['TagName'];
				$price = $tagData['Revenue'];
				$identify = $tagData['idTag'];
				
				$selPla[1] = '';
				$selPla[2] = '';
				$selPla[3] = '';
				$selPla[4] = '';
				$selPla[$tagData['PlatformType']] = ' selected="selected"';
				
				$selRev[1] = '';
				$selRev[2] = '';
				$selRev[$tagData['RevenueType']] = ' selected="selected"';
				
				$selPla2[1] = '';
				$selPla2[2] = '';
				$selPla2[$tagData['idPlatform']] = ' selected="selected"';
						
				$nameError = '';
				$nameErrorc = '';
				$nameErrors = '';
				$priceError = '';
				$priceErrorc = '';
				$priceErrors = '';
				$pageError = '';
				$pageErrorc = '';
				$plaError = '';
				$plaErrorc = '';
				$pla2Error = '';
				$pla2Errorc = '';
				$revenueError = '';
				$revenueErrorc = '';
				$revenueErrors = '';
				$identifyError = '';
				$identifyErrorc = '';
				$identifyErrors = '';
											
				if(isset($_POST['save'])){					
					if($_POST['save'] != ''){
						if(!empty($_POST['tagname'])){
							$tagname = my_clean($_POST['tagname']);
							$identify = $tagData['idTag'];

							/* if(!empty($_POST['price'])){ */
								$platformtype = intval($_POST['platformtype']);
								//$revenue = intval($_POST['revenue']);
								//$price = my_clean($_POST['price']);
								//$idplatform = $tagData['idPlatform'];
								$idsite = intval($_POST['site']);

								$time = $tagData['time'];
								$date = $tagData['date'];
								$modified = time();
									
								/*
								if($tagData['Revenue'] != $price || $tagData['RevenueType'] != $revenue || $tagData['PlatformType'] != $platformtype){
									$sql = "UPDATE " . TAGS . " SET Old = 1 WHERE id = '$idTag' LIMIT 1";
									$db->query($sql);
												
									$sql = "INSERT INTO " . TAGS . " (idUser, idPlatform, idSite, PlatformType, idTag, TagName, RevenueType, Revenue, Old, time, date, modified) VALUES ('$idUser', '$idplatform', '$idsite', '$platformtype', '$identify', '$tagname', '$revenue', '$price', '0', '$time', '$date', '$modified')";
									$db->query($sql);
								}else{
									$sql = "UPDATE " . TAGS . " SET TagName = '$tagname' WHERE id = '$idTag' LIMIT 1";
									$db->query($sql);
								}
								*/
								
								$sql = "UPDATE " . TAGS . " SET idSite = '$idsite', PlatformType = '$platformtype', idTag = '$identify', TagName = '$tagname', modified = '$modified' WHERE id = '$idTag' LIMIT 1";
								$db->query($sql);
											
								header('Location: tags.php?iduser=' . $idUser);
								exit(0);
									
							/*
							}else{
								$priceError = ' data-error="Debe completar el Precio."';
								$priceErrorc = ' frm-rrr';
								$priceErrors = ' style="margin-bottom:20px;"';
							}
							*/	
						}else{
							$nameError = ' data-error="Debe completar el nombre de la Zona."';
							$nameErrorc = ' frm-rrr';
							$nameErrors = ' style="margin-bottom:20px;"';
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
								<div class="titl">Editar Zona</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar Zona</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="tagname" value="<?php echo $tagname; ?>" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con el nombre del sitio"></span>
														</label>
													</div>
												</div>
												<!--</Nombre>-->
												<!--<Plataforma>-->
												<div class="frm-group d-fx lbl-lf<?php echo $plaErrorc; ?>">
													<label>Plataforma</label>
													<div class="d-flx1">
														<label<?php echo $plaError; ?>>
															<select name="platformtype" id="platformtype" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}' style="margin-bottom:0px !important;">
																<optgroup label="Selecciona la Plataforma">
																	<option value="1"<?php echo $selPla[1]; ?>>Desktop</option>
																	<option value="2"<?php echo $selPla[2]; ?>>Mobile Web</option>
																	<option value="3"<?php echo $selPla[3]; ?>>Mobile App</option>
																	<option value="4"<?php echo $selPla[4]; ?>>CTV</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Plataforma>-->
												<!--<Pagina>-->
												<div class="frm-group d-fx lbl-lf<?php echo $pageErrorc; ?>">
													<label>Pagina Web</label>
													<div class="d-flx1">
														<label<?php echo $pageError; ?>>
															<select name="site" id="site" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona la Pagina Web"><?php
																	$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idUser' ORDER BY id DESC";
																	$query = $db->query($sql);
																	if($db->num_rows($query) > 0){
																		while($Site = $db->fetch_array($query)){
																			?><option value="<?php echo $Site['id']; ?>"<?php if($tagData['idSite'] == $Site['id']){echo ' selected="selected"';} ?>><?php echo $Site['sitename']; ?></option><?php
																		}
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Pagina>-->
											</div>
											<div class="clmd06">
												<?php /*
												<!--<Revenue>-->
												<div class="frm-group d-fx lbl-lf<?php echo $revenueErrorc; ?>">
													<label>Revenue</label>
													<div class="d-flx1">
														<label<?php echo $revenueError; ?>>
															<select name="revenue" id="revenue" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}' style="margin-bottom:0px !important;">
																<optgroup label="Selecciona tipo de Precio">
																	<option value="1"<?php echo $selRev[1]; ?>>Revenue Share</option>
																	<option value="2"<?php echo $selRev[2]; ?>>CPM Fijo</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Revenue>-->
												<!--<Price>-->
												<div class="frm-group d-fx lbl-lf<?php echo $priceErrorc; ?>">
													<label<?php echo $priceErrors; ?>>Precio</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $priceError; ?>>
															<input type="text" name="price" value="<?php echo $price; ?>" placeholder="0.00" />
														</label>
													</div>
												</div>
												<!--</Price>-->
												<!--<Plataforma>-->
												<div class="frm-group d-fx lbl-lf<?php echo $pla2Errorc; ?>">
													<label>Plataforma</label>
													<div class="d-flx1">
														<label<?php echo $pla2Error; ?>>
															<select name="idplatform" id="idplatform" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}' style="margin-bottom:0px !important;" disabled="disabled">
																<optgroup label="Selecciona la Plataforma">
																	<option value="1"<?php echo $selPla2[1]; ?>>LKQD</option>
																	<option value="2"<?php echo $selPla2[2]; ?>>SpringServe</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Plataforma>-->
												*/
												?>
												<!--<Identificador>-->
												<div class="frm-group d-fx lbl-lf<?php echo $identifyErrorc; ?>">
													<label<?php echo $identifyErrors; ?>>Identificador</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $identifyError; ?>>
															<input type="text" name="identify" value="<?php echo $identify; ?>" disabled="disabled" />
														</label>
													</div>
												</div>
												<!--</Identificador>-->
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Cambios" name="save" />
										</div>
									</form>
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
	
</body>
</html>
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