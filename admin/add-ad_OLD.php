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
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(@$_SESSION['Admin'] >= 1){
		
		if(isset($_GET['idpage'])){
			$siteId = intval($_GET['idpage']);
			if($siteId > 0){
				
				$Type = 1;
				$idLkqd = '';
				$divID = '';
				$Width = '640';
				$Height = '360';
				$Close = 2;
				$DFP = 0;
				$Override = 2;
				$Spos = 1;
				$AA = 0;
				$code = '';
				
				$typeError = '';
				$typeErrors = '';
				$typeErrorc = '';
				$idLkqdError = '';
				$idLkqdErrors = '';
				$idLkqdErrorc = '';
				$divIDError = '';
				$divIDErrors = '';
				$divIDErrorc = '';
				$WidthError = '';
				$WidthErrors = '';
				$WidthErrorc = '';
				$HeightError = '';
				$HeightErrors = '';
				$HeightErrorc = '';
				$dfpError = '';
				$dfpErrorc = '';
				$dfpErrors = '';
				$overrideError = '';
				$overrideErrorc = '';
				$closeError = '';
				$closeErrorc = '';
				$sposError = '';
				$sposErrorc = '';				
				$aaError = '';
				$aaErrors = '';
				$aaErrorc = '';
				
				$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
				$query = $db->query($sql);
				$siteData = $db->fetch_array($query);
				$iduser = $siteData['idUser'];
								
				if(isset($_POST['save'])){
					$Type = intval($_POST['type']);
					
					if($Type == 5){
						$CCode = $_POST['appbundle_zonas']['codigo'];
					}else{
						$CCode = '';
						
						$idLkqd = my_clean($_POST['idLkqd']);
						$divID = my_clean($_POST['divID']);
						$Width = my_clean($_POST['Width']);
						$Height = my_clean($_POST['Height']);
						$DFP = intval($_POST['dfp']);
						$Override = intval($_POST['override']);
						$Close = intval($_POST['close']);
						$Spos = intval($_POST['spos']);
						$AA = intval($_POST['aa']);
					}
										
					if($_POST['save'] != ''){
						$sigue = true;
						
						if($Type == 0){
							$typeError = ' data-error="Debe elegir el tipo de anuncio."';
							$typeErrorc = ' frm-rrr';
							$typeErrors = ' style="margin-bottom:20px;"';
							
							$sigue = false;
						}
						
						if($idLkqd == '' && $Type != 5){
							$idLkqdError = ' data-error="Debe ingresar el ID de LKQD."';
							$idLkqdErrorc = ' frm-rrr';
							$idLkqdErrors = ' style="margin-bottom:20px;"';
							
							$sigue = false;
						}
						
						if($divID == '' && ($Type == 1 || $Type == 2)){
							$divIDError = ' data-error="Debe ingresar el ID del DIV."';
							$divIDErrorc = ' frm-rrr';
							$divIDErrors = ' style="margin-bottom:20px;"';
							
							$sigue = false;
						}
						
						if($Width == 0 && $Type != 5){
							$WidthError = ' data-error="El ancho debe ser mayor a 0."';
							$WidthErrorc = ' frm-rrr';
							$WidthErrors = ' style="margin-bottom:20px;"';
							
							$sigue = false;
						}
						
						if($Height == 0 && $Type != 5){
							$HeightError = ' data-error="Debe alto debe ser mayor a 0."';
							$HeightErrorc = ' frm-rrr';
							$HeightErrors = ' style="margin-bottom:20px;"';
							
							$sigue = false;
						}
						
						if($DFP == 0 && $Type != 5){
							$dfpError = ' data-error="Debe completar el campo DFP."';
							$dfpErrorc = ' frm-rrr';
							$dfpErrors = ' style="margin-bottom:20px;"';
							
							$sigue = false;
						}
						
						if($sigue){
							
							$Time = time();
							$Date = date('Y-m-d');
							$CCode = mysqli_real_escape_string($db->link, $CCode);
							
							$sql = "INSERT INTO " . ADS . " 
							(idSite, idSCode, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) 
							VALUES 
							('$siteId','0','$idLkqd','$divID','$Type','$Width','$Height','$Close','$DFP','$Override','$AA','$Spos',\"$CCode\",'$Time','$Date')";
							$db->query($sql);
							
							generateJS($siteId);
							
							header('Location: edit-page.php?idpage='.$siteId);
							exit(0);
							
						}
					}
				}
				//echo 'AA';
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
    <link rel="stylesheet" href="css/autocomplete.css">
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
								<div class="titl">Crear Anuncio para <?php echo $siteData['sitename']; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Crear Anuncio</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd12">
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $typeErrorc; ?>">
													<label>Tipo</label>
													<div class="d-flx1">
														<label<?php echo $typeError; ?>>
															<select name="type" id="type" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo"><?php
																	foreach($AdType as $K => $V){
																		?><option value="<?php echo $K; ?>" <?php if ($Type == $K) { echo  ' selected="selected"'; } ?>><?php echo $V; ?></option><?php
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Tipo>-->
											</div>
											<div class="clmd06">
												<div class="hide-cc">
												<!--<ID LKQD>-->
												<div class="frm-group d-fx lbl-lf<?php echo $idLkqdErrorc; ?>">
													<label<?php echo $idLkqdErrors; ?>>ID LKQD</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $idLkqdError; ?>>
															<input type="text" name="idLkqd" value="<?php echo $idLkqd; ?>" />
														</label>
													</div>
												</div>
												<!--</ID LKQD>-->
												<div class="no-slider-type">
												<!--<Div ID>-->
												<div class="frm-group d-fx lbl-lf<?php echo $divIDErrorc; ?>">
													<label<?php echo $divIDErrors; ?>>Div ID</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $divIDError; ?>>
															<input type="text" name="divID" value="<?php echo $divID; ?>" />
														</label>
													</div>
												</div>
												</div>
												<!--</Div ID>-->
												<!--<DFP>-->
												<div class="frm-group d-fx lbl-lf<?php echo $dfpErrorc; ?>">
													<label>DFP</label>
													<div class="d-flx1">
														<label<?php echo $dfpError; ?>>
															<select name="dfp" id="dfp" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	<option value="0"></option>
																	<option value="1" <?php if ($DFP == 1) { echo  ' selected="selected"'; } ?>>true</option>
																	<option value="2" <?php if ($DFP == 2) { echo  ' selected="selected"'; } ?>>false</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</DFP>-->
												<div class="slider-type">
												<!--<Close>-->
												<div class="frm-group d-fx lbl-lf<?php echo $closeErrorc; ?>">
													<label>Close</label>
													<div class="d-flx1">
														<label<?php echo $closeError; ?>>
															<select name="close" id="close" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	<option value="1">true</option>
																	<option value="2" <?php if ($Close == 2) { echo  ' selected="selected"'; } ?>>false</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Close>-->
												<!--<Slider Position>-->
												<div class="frm-group d-fx lbl-lf<?php echo $sposErrorc; ?>">
													<label>Slider Position</label>
													<div class="d-flx1">
														<label<?php echo $sposError; ?>>
															<select name="spos" id="spos" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	<option value="1">right</option>
																	<option value="2" <?php if ($Spos != 1) { echo  ' selected="selected"'; } ?>>left</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Slider Position>-->
												</div>
												</div>
											</div>
											<div class="clmd06">
												<div class="hide-cc">
												<!--<Width>-->
												<div class="frm-group d-fx lbl-lf<?php echo $WidthErrorc; ?>">
													<label<?php echo $WidthErrors; ?>>Width</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $WidthError; ?>>
															<input type="text" name="Width" id="Width" value="<?php echo $Width; ?>" />
														</label>
													</div>
												</div>
												<!--</Width>-->
												<!--<Height>-->
												<div class="frm-group d-fx lbl-lf<?php echo $HeightErrorc; ?>">
													<label<?php echo $HeightErrors; ?>>Height</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $HeightError; ?>>
															<input type="text" name="Height" id="Height" value="<?php echo $Height; ?>" />
														</label>
													</div>
												</div>
												<!--</Height>-->
												<!--<Override>-->
												<div class="frm-group d-fx lbl-lf<?php echo $overrideErrorc; ?>">
													<label>Override</label>
													<div class="d-flx1">
														<label<?php echo $overrideError; ?>>
															<select name="override" id="override" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	
																	<option value="1">true</option>
																	<option value="2" <?php if ($Override == 2) { echo  ' selected="selected"'; } ?>>false</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Override>-->
												<!--< Ajuste altura>-->
												<div class="slider-type">
												<div class="frm-group d-fx lbl-lf<?php echo $aaErrorc; ?>">
													<label<?php echo $aaErrors; ?>>Ajuste altura</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $aaError; ?>>
															<input type="text" name="aa" value="<?php echo $AA; ?>" />
														</label>
													</div>
												</div>
												</div>
												<!--</Ajuste altura>-->
											</div>
											</div>
											<div class="clmd12 show-cc">
												<div class="frm-nln">
													<label>Código</label>
													<div class="d-flx1" style="position:relative;">
													<input type="hidden" id="appbundle_zonas_codigo" name="appbundle_zonas[codigo]" value="<?php echo htmlspecialchars($code); ?>" />
														<div id="codigo"></div>
													</div>
													<div style="position:relative; clear:both;"><br/></div>
												</div>
											</div>
										</div>
										
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Cambios" name="save" />
										</div>
									</form>
								<div>		
								
									
									
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
    
    <style type="text/css">
	    #codigo { 
	        position: relative;
	        top: 0;
	        right: 0;
	        bottom: 0;
	        left: 0;
	        height:400px;
	    }
	    <?php if($Type != 5){ echo ".show-cc{display:none;}"; } ?>
	    <?php if($Type <= 2){ echo ".slider-type{display:none;}"; } else { echo ".no-slider-type{display:none;}"; } ?>
	    
	</style>
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    <script src="js/jquery.autocomplete.js"></script>
    
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.8/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
		jQuery(document).ready(function($){
			$("#type").change(function(){
				if($("#type").val() == 1 || $("#type").val() == 2){
					$(".hide-cc").show();
					$(".show-cc").hide();
					
					$(".slider-type").hide();
					$(".no-slider-type").show();
				}
				if($("#type").val() == 3 || $("#type").val() == 4){
					$(".hide-cc").show();
					$(".show-cc").hide();
					
					$(".slider-type").show();
					$(".no-slider-type").hide();
					
					if($("#type").val() == 3){
						//$("#close").val(1).trigger("change");
					}else{
						//$("#close").val(0).trigger("change");
					}
				}
				if($("#type").val() == 1){
					$("#Width").val('640');
					$("#Height").val('360');
				}else if($("#type").val() <= 4){
					$("#Width").val('400');
					$("#Height").val('225');
				}
				if($("#type").val() == 5){
					$(".hide-cc").hide();
					$(".show-cc").show();
				}
			});
			
		});
			
		var editor = ace.edit("codigo");
	    editor.setTheme("ace/theme/monokai");
	    editor.getSession().setMode("ace/mode/javascript");
	
	    editor.getSession().on('change', function(e) {
	        $('#appbundle_zonas_codigo').val(editor.getValue())
	    });
	    editor.setValue($('#appbundle_zonas_codigo').val())
	    
	</script>
</body>
</html><?php
			}else{
				header('Location: pages.php');
				exit(0);
			}
		}else{
			header('Location: pages.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>