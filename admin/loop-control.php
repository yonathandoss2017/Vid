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
		$segError = '';
		$segErrors = '';
		$segErrorc = '';
		$repError = '';
		$repErrors = '';
		$repErrorc = '';
								
		if(isset($_POST['save'])){
			$Take = intval($_POST['take']);
			$Seg = intval($_POST['seg']);
			$Rep = intval($_POST['rep']);
			$SiteID = intval($_POST['siteid']);
			
					
			if($Take == 2){
				$Take = true;
			}else{
				$Take = false;
			}
		
			$sigue = true;
						
			if($Seg == 0){
				$segError = ' data-error="Debe indicar los segundos."';
				$segErrorc = ' frm-rrr';
				$segErrors = ' style="margin-bottom:20px;"';
							
				$sigue = false;
			}
					
			if($Rep == 0){
				$repError = ' data-error="Debe indicar las repeticiones."';
				$repErrorc = ' frm-rrr';
				$repErrors = ' style="margin-bottom:20px;"';
							
				$sigue = false;
			}
					
			if($sigue){
				if($SiteID > 0){
					$WH = " WHERE id = 2214";
				}else{
					$WH = "";
				}
				$sql = "SELECT * FROM " . SITES . "$WH";// 
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					while($Site = $db->fetch_array($query)){
						if($Take){
							generateJSDouble($Site['id'], true);
						}else{
							generateJSDouble($Site['id'], false, $Seg, $Rep);
						}
					}
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
								<div class="titl">Loop Control</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd12">
												<!--<Sitio>-->
												<div class="frm-group d-fx lbl-lf">
													<label>Sitio</label>
													<div class="d-flx1">
														<label>
															<select name="siteid" id="siteid" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Sitio">
																	<option value="all">Todos</option><?php
																	$sql = "SELECT * FROM " . SITES . " ORDER BY id ASC";// 
																	$query = $db->query($sql);
																	if($db->num_rows($query) > 0){
																		while($Site = $db->fetch_array($query)){
																			?><option value="<?php echo $Site['id']; ?>"><?php echo $Site['id']; ?>: <?php echo $Site['sitename']; ?></option><?php
																		}
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Sitio>-->
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf">
													<label>Funcion</label>
													<div class="d-flx1">
														<label>
															<select name="take" id="take" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Estado">
																	<option value="1">Activar</option>
																	<option value="2">Desactivar</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Tipo>-->
											</div>
											<div class="clmd06" id="rep">
												<!--<Repeticiones>-->
												<div class="frm-group d-fx lbl-lf<?php echo $repErrorc; ?>">
													<label<?php echo $repErrors; ?>>Repeticiones</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $repError; ?>>
															<input type="text" name="rep" value="5" />
														</label>
													</div>
												</div>
												<!--</Repeticiones>-->
											</div>
											<div class="clmd06" id="seg">
												<!--<Segundos de espera>-->
												<div class="frm-group d-fx lbl-lf<?php echo $segErrorc; ?>">
													<label<?php echo $segErrors; ?>>Seg. de espera</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $segError; ?>>
															<input type="text" name="seg" value="5" />
														</label>
													</div>
												</div>
												<!--</Segundos de espera>-->
											</div>
											
										</div>
										
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Regenerar Codigos" name="save" />
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
    
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    <script src="js/jquery.autocomplete.js"></script>
    
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.8/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
		jQuery(document).ready(function($){
			$("#take").change(function(){
				
				if($("#take").val() == 2){
					$("#seg").hide();
					$("#rep").hide();
				}else{
					$("#seg").show();
					$("#rep").show();
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