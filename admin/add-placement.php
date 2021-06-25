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
		
	if(@$_SESSION['Type'] == 3){
		if(isset($_GET['idb'])){
			$idBidder = intval($_GET['idb']);
			
			$sql = "SELECT * FROM " . BIDDERS . " WHERE id = '$idBidder' LIMIT 1";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$BidderData = $db->fetch_array($query);
				$Bidder = $BidderData['Name'];
			
				$Name = '';
				$Platform = 1;
				$Default = 1;
				$Tam = 1;
				$ID = '';
				$Custom1 = '';
				$Custom2 = '';
					
				$nameError = '';
				$nameErrorc = '';
				$nameErrors = '';
				$plaError = '';
				$plaErrorc = '';
				$sizeError = '';
				$sizeErrorc = '';
				$defError = '';
				$defErrorc = '';
						
				if(isset($_POST['save'])){
					$Name = my_clean($_POST['name']);
					$Platform = intval($_POST['platform']);
					$Default = intval($_POST['default']);
					$Tam = intval($_POST['size'.$Platform]);
					$ID = my_clean($_POST['id']);
					$Custom1 = my_clean($_POST['custom1']);
					$Custom2 = my_clean($_POST['custom2']);
					$Custom = $Custom1 . '[]' . $Custom2;
					
					$Sigue = true;
		
					if($Name == ''){
						$Sigue = false;
						$nameError = ' data-error="Debe completar el Nombre del nuevo Placement."';
						$nameErrorc = ' frm-rrr';
						$nameErrors = ' style="margin-bottom:20px;"';
					}else{
						$sql = "SELECT id FROM " . PLACEMENTS . " WHERE Name LIKE '$Name' LIMIT 1";
						if($db->getOne() > 0){
							$Sigue = false;
							$nameError = ' data-error="El Nombre ya esta siendo utilizado."';
							$nameErrorc = ' frm-rrr';
							$nameErrors = ' style="margin-bottom:20px;"';
						}
					}
					
					
					
					if($ID == ''){
						$Sigue = false;
						$idError = ' data-error="Debe ingresar el ID del Placement."';
						$idErrorc = ' frm-rrr';
						$idErrors = ' style="margin-bottom:20px;"';
					}
					
					/*
					if($PlacementParam == ''){
						$Sigue = false;
						$placementError = ' data-error="Debe ingresar el Placement Param (Ej: zoneID)."';
						$placementErrorc = ' frm-rrr';
						$placementErrors = ' style="margin-bottom:20px;"';
					}
					*/
					
					if($Sigue){
						$Time = time();	
							
						$sql = "INSERT INTO " . PLACEMENTS . " (idBidder, Name, Platform, isDefault, idZone, Position, Size, Custom, Active, Deleted, Time) VALUES ('$idBidder', '$Name', '$Platform', '$Default', '$ID', '', '$Tam', '$Custom', 1, 0, '$Time')";
						$db->query($sql);
						
						header('Location: edit-bidder.php?idb=' . $idBidder);
						exit(0);
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
								<div class="titl">Añadir Placement a <?php echo $Bidder; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Añadir Placement</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="name" value="<?php echo $Name; ?>" />
														</label>
													</div>
												</div>
												<!--</Nombre>-->
											</div>
											<div class="clmd06">
												<!--<Plataforma>-->
												<div class="frm-group d-fx lbl-lf<?php echo $plaErrorc; ?>">
													<label>Dispositivo</label>
													<div class="d-flx1">
														<label<?php echo $plaError; ?>>
															<select name="platform" id="platform" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Seleccionar Dispositivo">
																	<option value="1">Desktop</option>
																	<option value="2"<?php if($Platform == 2){ echo " selected"; } ?>>Mobile Web</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Plataforma>-->
											</div>
											<div class="clmd06">
												<!--<Tamaño>-->
												<div class="frm-group d-fx lbl-lf<?php echo $sizeErrorc; ?>">
													<label>Tamaño</label>
													<div class="d-flx1">
														<label<?php echo $sizeError; ?>>
															<select name="size1" id="size1" class="sizes" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'<?php if($Platform == 2){ echo ' style="display:none;"'; } ?>>
																<optgroup label="Seleccionar Tamaño"><?php
																		$sql = "SELECT id, Size FROM " . DSIZES . " WHERE Platform = '1'";
																		$query = $db->query($sql);
																		if($db->num_rows($query) > 0){
																			while($Size = $db->fetch_array($query)){
																				?><option value="<?php echo $Size['id']; ?>"<?php if($Tam == $Size['id']) { echo " selected"; } ?>><?php echo $Size['Size']; ?></option><?php
																			}
																		}
																		?>
																</optgroup>
															</select>
															<select name="size2" id="size2" class="sizes" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'<?php if($Platform != 2){ echo ' style="display:none;"'; } ?>>
																<optgroup label="Seleccionar Tamaño"><?php
																		$sql = "SELECT id, Size FROM " . DSIZES . " WHERE Platform = '2'";
																		$query = $db->query($sql);
																		if($db->num_rows($query) > 0){
																			while($Size = $db->fetch_array($query)){
																				?><option value="<?php echo $Size['id']; ?>"<?php if($Tam == $Size['id']) { echo " selected"; } ?>><?php echo $Size['Size']; ?></option><?php
																			}
																		}
																		?>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Tamaño>-->
											</div>
											<div class="clmd06">
												<!--<Default>-->
												<div class="frm-group d-fx lbl-lf<?php echo $defErrorc; ?>">
													<label>Default</label>
													<div class="d-flx1">
														<label<?php echo $defError; ?>>
															<select name="default" id="default" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Seleccionar Dispositivo">
																	<option value="1">Si</option>
																	<option value="2"<?php if($Default == 2) { echo ' selected'; } ?>>No</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Default>-->
											</div>
											<div class="clmd06">
												<!--<ID>-->
												<div class="frm-group d-fx lbl-lf<?php echo $idErrorc; ?>">
													<label<?php echo $idErrors; ?>>ID</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $idError; ?>>
															<input type="text" name="id" value="<?php echo $ID; ?>" />
														</label>
													</div>
												</div>
												<!--</ID>-->
											</div>
											<div class="clmd06">
												<!--<ID>-->
												<div class="frm-group d-fx lbl-lf">
													<label>Custom</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf">
															<input type="text" name="custom1" value="<?php echo $Custom1; ?>" style="width:46%; display: inline-block;" />
															<input type="text" name="custom2" value="<?php echo $Custom2; ?>" style="width:46%; display: inline-block; float: right;" />
														</label>
													</div>
												</div>
												<!--</ID>-->
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Crear Nuevo Placement" name="save" /> 	
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
		
		$('#platform').change(function(){
			$('.sizes').hide();
			$('#size'+$(this).val()).show();
		});
		
	});
	
	</script>
</body>
</html>
</html><?php
			}else{
				header('Location: bidders.php');
				exit(0);
			}
		}else{
			header('Location: bidders.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>