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
	require('libs/display.lib.php');
		
	if(@$_SESSION['Admin'] == 1){
		
		if($_SESSION['Type'] != 3){
			$AccM = $_SESSION['AccM'];
		}else{
			$AccM = 0;
		}
		
		if(isset($_GET['def'])){
			$Default = 1;
			$idAd = 0;
		}else if(isset($_GET['idsite'])){
			$Default = 0;
			$idAd = intval($_GET['idad']);
		}
		
		$jQstart = '';
		$Name = '';
		$Platform = 0;
		$Tam = 0;
		$ID = '';
		$Custom1 = '';
		$Custom2 = '';
		$LoadTiers = 0;
		$TiersData = false;
		$Placements = array();
			
		$nameError = '';
		$nameErrorc = '';
		$nameErrors = '';
		$plaError = '';
		$plaErrorc = '';
		$sizeError = '';
		$sizeErrorc = '';
		$positionError = '';
		$positionErrorc = '';
		$defError = '';
		$defErrorc = '';
		$Disabled = '';
		$NoDisplayCapas = ' style="display:none;"';
		$HiddenInputs = '';
				
		if(isset($_POST['save'])){
			$Name = my_clean($_POST['name']);
			$Platform = intval($_POST['platform']);
			$Default = intval($_POST['default']);
			$Tam = intval($_POST['size'.$Platform]);
			$Position = intval($_POST['position'.$Platform]);
			$TiersData = array();
			for($i = 1; $i <= 2000; $i++){
				if(isset($_POST["floor-$i"])){
					$LoadTiers = $i;
					$Floor = my_clean($_POST["floor-$i"]);
					
					$Placements = array();
					foreach($_POST as $Key => $Val){
						if(substr($Key, 0, 7) == 'bidder-'){
							$arKey = explode('-', $Key);
							if($arKey[2] == $i){
								$Bidder = $arKey[1];
								$Placements[$Bidder] = $Val;
							}
						}
					}
					
					$TiersData[$i] = array('Floor' => $Floor, 'Placements' => $Placements);
					
				}
			}
			
			//print_r($TiersData);
			
			$Sigue = true;

			if($Name == ''){
				$Sigue = false;
				$nameError = ' data-error="Debe completar el Nombre del nuevo Ad Unit."';
				$nameErrorc = ' frm-rrr';
				$nameErrors = ' style="margin-bottom:20px;"';
			}
			
			if($Platform == 0){
				$Sigue = false;
				$plaError = ' data-error="Debe seleccionar la Plataforma para el nuevo Ad Unit."';
				$plaErrorc = ' frm-rrr';
			}

			if($Tam == 0){
				$Sigue = false;
				$sizeError = ' data-error="Debe seleccionar el Tamaño del nuevo Ad Unit."';
				$sizeErrorc = ' frm-rrr';
			}
			
			if($Tam > 0 && $Platform > 0){
				$Disabled = ' disabled="disabled"';
				$HiddenInputs = '<input type="hidden" name="platform" value="'.$Platform.'" /><input type="hidden" name="size'.$Platform.'" value="'.$Tam.'" />';
				$NoDisplayCapas = '';
			}
			
			if($Sigue){
				$Time = time();	
					
				$sql = "INSERT INTO " . ADUNITS . " (Platform, Size, Name, isDefault, Position, AccM, Deleted, Time) VALUES ('$Platform', '$Tam', '$Name', '$Default', '$Position', '$AccM', 0, '$Time')";
				$db->query($sql);
				$adUnitId = mysqli_insert_id($db->link);
				
				foreach($TiersData as $TierNr => $Data){
					$Floor = $Data['Floor'];
					$sql = "INSERT INTO " . ADUNITSTIERS . " (idAdunit, Tier, Floor, Time) VALUES ('$adUnitId', '$TierNr', '$Floor', '$Time')";
					$db->query($sql);
					$tierId = mysqli_insert_id($db->link);
					
					foreach($Data['Placements'] as $Bidder => $Placements){
						$Placements = str_replace('[','',$Placements);
						$Placements = str_replace(']','',$Placements);
						$Placements = str_replace('"','',$Placements);
						if($Placements != ''){
							if(strpos($Placements, ',') === false){
								$ArPlacements = array($Placements);
							}else{
								$ArPlacements = explode(',', $Placements);
							}
							
							foreach($ArPlacements as $Placement){
								$sql = "SELECT id FROM " . PLACEMENTS . " WHERE Name = '" . $Placement . "' LIMIT 1";
								$idPlacement = $db->getOne($sql);
								
								$sql = "INSERT INTO " . ADUNITSPLACE . " (idTier, idAdunit, idPlacement, Time) VALUES ('$tierId', '$adUnitId' , '$idPlacement', '$Time')";
								$db->query($sql);
							}
						}
						
					}	
				}
				if($Default == 1){
					header('Location: ad-units.php?default=1');
				}else{
					header('Location: ad-units.php');
				}
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
    <link rel="stylesheet" href="css/textext.core.css" type="text/css" />
	<link rel="stylesheet" href="css/textext.plugin.tags.css" type="text/css" />
	<link rel="stylesheet" href="css/textext.plugin.autocomplete.css" type="text/css" />
	<link rel="stylesheet" href="css/textext.plugin.focus.css" type="text/css" />
	<link rel="stylesheet" href="css/textext.plugin.prompt.css" type="text/css" />
	<link rel="stylesheet" href="css/textext.plugin.arrow.css" type="text/css" />
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
					<form action="" method="post" class="frm-adrsit">
					<main class="clmc12 c-flx1">
						<!--<Estadisticas Avanzadas>-->
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx">
								<div class="titl">Añadir Ad Unit</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									
										<div class="bx-hd dfl b-fx">
											<div class="titl">Configuración general</div>
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
															<select name="platform" id="platform"<?php echo $Disabled; ?> data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Seleccionar Dispositivo">
																	<option value="0">Seleccionar...</option>
																	<option value="1"<?php if($Platform == 1){ echo " selected"; } ?>>Desktop</option>
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
															<select name="size1" id="size1"<?php echo $Disabled; ?> class="sizes" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'<?php if($Platform == 2){ echo ' style="display:none;"'; } ?>>
																<optgroup label="Seleccionar Tamaño">
																	<option value="0">Seleccionar...</option><?php
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
															<select name="size2" id="size2"<?php echo $Disabled; ?> class="sizes" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'<?php if($Platform != 2){ echo ' style="display:none;"'; } ?>>
																<optgroup label="Seleccionar Tamaño">
																	<option value="0">Seleccionar...</option><?php
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
												<!--<Posicion>-->
												<div class="frm-group d-fx lbl-lf<?php echo $positionErrorc; ?>">
													<label>Posición</label>
													<div class="d-flx1">
														<label<?php echo $positionError; ?>>
															<select name="position1" id="position1" class="sizes" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'<?php if($Platform == 2){ echo ' style="display:none;"'; } ?>>
																<optgroup label="Seleccionar Posición"><?php
																		$sql = "SELECT id, Position FROM " . DPOSITIONS . " WHERE Platform = '1'";
																		$query = $db->query($sql);
																		if($db->num_rows($query) > 0){
																			while($Pos = $db->fetch_array($query)){
																				?><option value="<?php echo $Pos['id']; ?>"<?php if($Tam == $Pos['id']) { echo " selected"; } ?>><?php echo $Pos['Position']; ?></option><?php
																			}
																		}
																		?>
																</optgroup>
															</select>
															<select name="position2" id="position2" class="sizes" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'<?php if($Platform != 2){ echo ' style="display:none;"'; } ?>>
																<optgroup label="Seleccionar Posición"><?php
																		$sql = "SELECT id, Position FROM " . DPOSITIONS . " WHERE Platform = '2'";
																		$query = $db->query($sql);
																		if($db->num_rows($query) > 0){
																			while($Pos = $db->fetch_array($query)){
																				?><option value="<?php echo $Pos['id']; ?>"<?php if($Tam == $Pos['id']) { echo " selected"; } ?>><?php echo $Pos['Position']; ?></option><?php
																			}
																		}
																		?>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Posicion>-->
											</div>
											<?php
												if($_SESSION['Type'] == 3){
											?>
											<div class="clmd06">
												<!--<Default>-->
												<div class="frm-group d-fx lbl-lf<?php echo $defErrorc; ?>">
													<label>Default</label>
													<div class="d-flx1">
														<label<?php echo $defError; ?>>
															<select name="default" id="default" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Seleccionar Default">
																	<option value="0">No</option>
																	<option value="1"<?php if($Default == 1) { echo ' selected'; } ?>>Si</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Default>-->
											</div>
											<?php
												} else {
													?><input type="hidden"  name="default" id="default" value="0" /><?php
												}
											?>
										</div>
									</div>
								</div>
							</div>
							<div id="hiddeninputs"><?php echo $HiddenInputs; ?></div>
							<div class="bx-pd" id="capas"<?php echo $NoDisplayCapas; ?>>
								
								<div class="bx-hd dfl b-fx">
									<div class="titl">Capas</div>
								</div>
								
								<ul class="lst-tbs b-fx mb2" style="float:right; margin-top:-50px;">
									<li class="b-rt"><a href="#" class="fa-minus-circle" id="del-tier" style="display: none;">Eliminar Tier</a></li>
									<li class="b-rt"><a href="#" class="fa-plus-circle" id="add-tier">Añadir Tier</a></li>
								</ul>
								
								<div id="tiers" class="clmc12">
									<?php 
										if(is_array($TiersData)){
											for($i = 1; $i <= $LoadTiers; $i++){										
												$jQstart .= displayShowTierConfig($i, $Platform, $Tam, 0, 0, $TiersData[$i]);
											}
										}else{
											//$jQstart .= displayShowTierConfig(1, 0, 0, $TiersData);
										}
									?>
								</div>
								
								
								<br style="clear:both;">
								<div class="botnr-cn">
									<input type="submit" class="fa-save" value="Crear Nuevo Ad Unit" name="save" /> 	
								</div>
							
							</div>

								
							</div>
						</div>
						<!--</Estadisticas Avanzadas>-->
					</main>
					<!--<main>-->
					</form>
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
    <script src="js/autoNumeric.js"></script>
    
    <script src="js/textext.core.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.tags.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.autocomplete.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.suggestions.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.filter.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.focus.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.prompt.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.ajax.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/textext.plugin.arrow.js" type="text/javascript" charset="utf-8"></script>
    <script>
	    
	jQuery(document).ready(function($){
		$('#platform').change(function(){
			$('.sizes').hide();
			$('#size'+$(this).val()).show();
			$('#position'+$(this).val()).show();
		});

		var loadedTires = <?php echo $LoadTiers; ?>;
		var selPlatform = $('#platform').val();
		var selSize = 0;
		if(selPlatform > 0){
			selSize = $('#size'+selPlatform).val();
		}
		
		$('#size1, #size2, #platform').change(function(){
			selPlatform = $('#platform').val();
			if(selPlatform > 0){
				selSize = $('#size'+selPlatform).val();
				if(selSize > 0){
					
					$('#hiddeninputs').append('<input type="hidden" name="platform" value="'+selPlatform+'" /><input type="hidden" name="size1" value="'+selSize+'" /><input type="hidden" name="size2" value="'+selSize+'" />');
					
					$('#size1').prop( "disabled", true );
					$('#size2').prop( "disabled", true );
					$('#platform').prop( "disabled", true );
					
					$('#capas').show();
					
					loadedTires = loadedTires + 1;
					$("#tiers").append('<div id="tier'+loadedTires+'"></div>');
				    $("#tier"+loadedTires).load("new-tier.php?tier="+loadedTires+"&pla="+selPlatform+"&size="+selSize, function() { $('.numeric').autoNumeric('init', {mDec: '2'}); });
				}
			}
		});
		
		$("#add-tier").click(function(e) {
			loadedTires = loadedTires + 1;
			e.preventDefault();
			$("#tiers").append('<div id="tier'+loadedTires+'"></div>');
		    $("#tier"+loadedTires).load("new-tier.php?tier="+loadedTires+"&pla="+selPlatform+"&size="+selSize, function() { $('.numeric').autoNumeric('init', {mDec: '2'}); });
		    if(loadedTires > 1){
				$('#del-tier').show();
			}
		});
		
		$("#del-tier").click(function(e) {
			e.preventDefault();
			
			$("#tier"+loadedTires).remove();
			
			loadedTires = loadedTires - 1;
			if(loadedTires <= 1){
				$('#del-tier').hide();
			}
		});
		
		<?php echo $jQstart; ?>
		
		$('.numeric').autoNumeric('init', {mDec: '2'});
		
	});
	
	</script>
	<style>

	</style>
</body>
</html>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>