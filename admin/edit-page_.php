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
	require('libs/display.lib.php');
	
	if(@$_SESSION['Admin'] >= 1){
		
		if(isset($_GET['idpage'])){
			$siteId = intval($_GET['idpage']);
			
			if($_SESSION['Type'] != 3){
				$sql = "SELECT idUser FROM " . SITES . " WHERE id = '$siteId'";
				$idUser = $db->getOne($sql);
				
				$sql = "SELECT AccM FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
				if($db->getOne($sql) != $_SESSION['idAdmin']){
					header('Location: pages.php');
					exit(0);
				}
			}else{
				$sql = "SELECT COUNT(*) FROM " . SITES . " WHERE id = '$siteId' ";
				if($db->getOne($sql) == 0){
					header('Location: pages.php');
					exit(0);
				}
			}
			
			if($siteId > 0){
				$linkAnd = '';
				if(isset($_GET['del'])){
					$idDel = intval($_GET['del']);
					if($idDel > 0){
						$sql = "SELECT * FROM " . CODES . " WHERE id = '$idDel' LIMIT 1";
						$query = $db->query($sql);
						$codeData = $db->fetch_array($query);
						
						$sql = "SELECT filename FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
						$delFile = $db->getOne($sql);
						
						$ardFilename = explode('/',$delFile);
						$delFile = $ardFilename[3];
						$delFile = str_replace('.js','',$delFile);
						
						if($codeData['Type'] == 1){
							$delFile = $delFile . '-' . $codeData['Location'] . '.js';
						}else{
							$delFile = $delFile . '_' . $codeData['Location'] . '.js';
						}
						
						if(unlink("../../Vidoomy/ads/" . $delFile)){
							$sql = "DELETE FROM " . CODES . " WHERE id = '$idDel' LIMIT 1";
							$db->query($sql);
							
							header('Location: edit-page.php?idpage=' . $siteId);
							exit(0);
						}
					}
				}
				
				if(isset($_GET['delad'])){
					$idDel = intval($_GET['delad']);
					if($idDel > 0){
						
						$sql = "DELETE FROM " . ADS . " WHERE id = '$idDel' LIMIT 1";
						$db->query($sql);
						
						newGenerateJS($siteId);
						
					}
				}
				
				$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
				$query = $db->query($sql);
				$siteData = $db->fetch_array($query);
				$iduser = $siteData['idUser'];
					
				$sitename = $siteData['sitename'];
				$siteurl = $siteData['siteurl'];
				$nameError = '';
				$nameErrorc = '';
				$nameErrors = '';
				$urlError = '';
				$urlErrorc = '';
				$urlErrors = '';
				$userError = '';
				$userErrorc = '';
				
				$sel[1] = '';
				$sel[2] = '';
				$sel[3] = '';
				$sel[4] = '';
				$sel[10] = '';
				$sel[$siteData['category']] = ' checked="checked"';
				
				$arFilename = explode('/',$siteData['filename']);
				$filename = $arFilename[3];
				$filename2 = str_replace('.js','',$filename);
				
				if(isset($_POST['save'])){
					//echo 1;
					$siteurl = my_clean($_POST['siteurl']);
					$sitename = my_clean($_POST['sitename']);
					$category = intval($_POST['category']);
					$sel[$siteData['category']] = '';
					$sel[$category] = ' checked="checked"';
					if($_POST['save'] != ''){
						if($_POST['sitename'] != ''){
							if($_POST['siteurl'] != ''){
								///print_r($_POST);
								/*
								$txt = $_POST['appbundle_zonas']['codigo'];
								$myfile = fopen("../../Vidoomy/ads/$filename", "w") or die("Unable to open file!");
								fwrite($myfile, $txt);
								fclose($myfile);
								*/
								if(isset($_POST['test'])){
									$test = intval($_POST['test']);
								}else{
									$test = 0;
								}
								
								$newUser = intval($_POST['user']);
								$sql = "UPDATE " . SITES . " SET siteurl = '$siteurl', sitename = '$sitename', category = '$category', idUser = '$newUser', test = '$test' WHERE id = '$siteId' LIMIT 1";
								$db->query($sql);
								
								if($iduser != $newUser && $newUser > 0){
									$sql = "UPDATE " . TAGS . " SET idUser = '$newUser' WHERE idSite = '$siteId'";
									$db->query($sql);
									
									$sql = "UPDATE " . STATS . " SET idUser = '$newUser' WHERE idSite = '$siteId'";
									$db->query($sql);
								}
								
								$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
								$query = $db->query($sql);
								$siteData = $db->fetch_array($query);
								$iduser = $siteData['idUser'];
								/*
								mysqli_select_db($db->link, 'vidoomy');
								
								$txt = mysqli_real_escape_string($db->link, $txt);
								
								$sql = "UPDATE zonas SET codigo = '$txt' WHERE nombre_archivo = '$filename2' LIMIT 1";
								$db->query($sql);
								
								mysqli_select_db($db->link, 'vidoomylogin');
								*/
							}else{
								$urlError = ' data-error="Debe completar el Nombre del sitio web."';
								$urlErrorc = ' frm-rrr';
								$urlErrors = ' style="margin-bottom:20px;"';

							}
						}else{
							$nameError = ' data-error="Debe completar el Nombre del sitio web."';
							$nameErrorc = ' frm-rrr';
							$nameErrors = ' style="margin-bottom:20px;"';
						}
					}
				}
				
				/*
				$myfile2 = fopen("../../Vidoomy/ads/$filename", "a+") or die("Unable to open file!");
				
				$code = '';
				while ($line = fgets($myfile2)) {
				  $code .= $line;
				}
				fclose($myfile2);
				*/
				
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
								<div class="titl">Editar Sitio Web</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar Página</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre Web>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre Web</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="sitename" value="<?php echo $sitename; ?>" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con el nombre del sitio"></span>
														</label>
													</div>
												</div>
												<!--</Nombre Web>-->
												<!--<URL>-->
												<div class="frm-group d-fx lbl-lf<?php echo $urlErrorc; ?>">
													<label<?php echo $urlErrors; ?>>URL</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $urlError; ?>>
															<input type="text" name="siteurl" value="<?php echo $siteurl; ?>" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con la URL"></span>
														</label>
													</div>
												</div>
												<!--</URL>-->
											</div>
											<div class="clmd06">
												<!--<Publisher>-->
												<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
													<label>Publisher</label>
													<div class="d-flx1">
														<label<?php echo $userError; ?>>
															<select name="user" id="user" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Publisher"><?php
																	if($_SESSION['Type'] == 3){
																		$sql = "SELECT * FROM " . USERS . " ORDER BY user ASC";
																	}else{
																		$idAccM = $_SESSION['idAdmin'];
																		$sql = "SELECT * FROM " . USERS . " WHERE AccM = '$idAccM' ORDER BY user ASC";
																	}
																	$query = $db->query($sql);
																	if($db->num_rows($query) > 0){
																		while($User = $db->fetch_array($query)){
																			?><option value="<?php echo $User['id']; ?>"><?php echo $User['user']; ?></option><?php
																		}
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Publisher>-->
												
												<div class="frm-nln">Tipo de contenido 
													<label class="option"><input name="category" type="radio" value="1"<?php echo $sel[1]; ?>> Periódico </label>
													<label class="option"><input name="category" type="radio" value="2"<?php echo $sel[2]; ?>> TV online </label>
													<label class="option"><input name="category" type="radio" value="3"<?php echo $sel[3]; ?>> Viral </label>
													<label class="option"><input name="category" type="radio" value="4"<?php echo $sel[4]; ?>> Juegos </label>
													<label class="option"><input name="category" type="radio" value="10"<?php echo $sel[10]; ?>> Otros</label>
												</div>
											</div>
											
											<div class="clmd12">
												<!--<Script>-->
												<div class="frm-group d-fx lbl-lf">
													<label>Script</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf">
															<textarea disabled cols="66" rows="1">&#60;script type="text/javascript" src="<?php echo str_replace('http:','',$siteData['filename']); ?>" &#62;&#60;/script&#62;</textarea>
														</label>
													</div>
												</div>
												<!--</Script>-->
											</div>
											
											<?php
											/*
											<div class="clmd12">
												<div class="frm-nln">
													<label>Código Default</label>
													<div class="d-flx1" style="position:relative;">
													<input type="hidden" id="appbundle_zonas_codigo" name="appbundle_zonas[codigo]" value="<?php echo htmlspecialchars($code); ?>" />
														<div id="codigo"></div>
													</div>
													<div style="position:relative; clear:both;"><br/></div>
												</div>
											</div>
											*/
											?>
										</div>
										
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Cambios" name="save" />
										</div>
									</form>
								<div>
									
									
									
								<!--<table>-->
								<div class="tbl-cn">
									<table id="tbl-estats">
										<thead>
											<tr>
												<th>Tipo</th>
												<th>ID LKQD</th>
												<th>Div ID</th>
												<th>Creado</th>
												<th>Opciones</th>
											</tr>
										</thead>
										<tbody><?php
										
										$sql = "SELECT * FROM " . ADS . " WHERE idSite = '$siteId' ORDER BY id DESC";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($Ad = $db->fetch_array($query)){
												if(($Ad['Type'] == 10 || $Ad['Type'] == 11) && $Ad['idLKQD'] != ''){}
												else{
												?><tr>
													<td data-title="Tipo"><?php echo $AdType2[$Ad['Type']] ?></td>
													<td data-title="ID LKQD"><?php echo $Ad['idLKQD']; ?></td>
													<td data-title="Div ID"><?php echo $Ad['divID']; ?></td>
													<td data-title="Creado"><?php echo date('d-m-Y',$Ad['Time']); ?></td>
													<td data-title="Opciones">
														<ul class="lst-opt">
															<li><a href="edit-ad.php?idpage=<?php echo $siteId; ?>&idad=<?php echo $Ad['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Codigo"></a></li>
															<li><a href="edit-page.php?idpage=<?php echo $siteId; ?>&delad=<?php echo $Ad['id']; ?><?php echo $linkAnd; ?>" onclick="return false" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Codigo"></a></li>
														</ul>
													</td>
												</tr><?php
												}
											}
										}else{
											?><tr><td colspan="5">No hay anuncios creados.</td></tr><?php
										}
										?>
										</tbody>
									</table>
								</div>
								<!--</table>-->
								
								<br style="clear:both;" />
								
								<ul class="lst-tbs b-fx mb2" style="margin-top:20px;">
									<li class="b-rt"><a href="add-ad.php?idpage=<?php echo $siteId; ?>" class="fa-plus-circle">Añadir Anuncio</a></li>
								</ul>
								
								</div>
									
									
								<!--<table>-->
								<div class="tbl-cn">
									<table id="tbl-estats">
										<thead>
											<tr>
												<th>Tipo</th>
												<th>Ubicación</th>
												<th>Creado</th>
												<th>Opciones</th>
											</tr>
										</thead>
										<tbody><?php
										
										$sql = "SELECT * FROM " . CODES . " WHERE deleted = 0 AND idSite = '$siteId' ORDER BY id DESC";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($Code = $db->fetch_array($query)){
												?><tr>
													<td data-title="Tipo"><?php if( $Code['Type'] == 1 ) { echo 'Continente'; } elseif ($Code['Type'] == 2 ) { echo 'País'; } else { echo 'País - Ciudad'; } ?></td>
													<td data-title="Ubicacion"><?php 
														if( $Code['Type'] == 1 ) {
															echo $continents[$Code['Location']];
														}elseif( $Code['Type'] == 2 ){
															echo $countries[$Code['Location']];
														}else{
															$arLoc = explode('-',$Code['Location']);
															$CC = $arLoc[0];
															echo $countries[$CC];
															$sql = "SELECT id FROM countries WHERE country_code = '$CC' LIMIT 1";
															$idC = $db->getOne($sql);
															$CCode = substr($Code['Location'], 3);
															$sql = "SELECT Name FROM cities WHERE idCountry = '$idC' AND Code = '$CCode' LIMIT 1";
															echo ' - ' . $db->getOne($sql);
														}							
													?></td>
													<td data-title="Usuario"><?php echo date('d-m-Y',$Code['Time']); ?></td>
													<td data-title="Opciones">
														<ul class="lst-opt">
															<li><a href="edit-code.php?idcode=<?php echo $Code['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Codigo"></a></li>
															<li><a href="edit-page.php?idpage=<?php echo $siteId; ?>&delad=<?php echo $linkAnd; ?>" onclick="return false" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Codigo"></a></li>
														</ul>
													</td>
												</tr><?php
											}
										}else{
											?><tr><td colspan="4">No hay códigos de exepci&oacute;n creados.</td></tr><?php
										}
										?>
										</tbody>
									</table>
								</div>
								<!--</table>-->
								
								<br style="clear:both;" />
								
								<ul class="lst-tbs b-fx mb2" style="margin-top:20px;">
									<li class="b-rt"><a href="add-code.php?idpage=<?php echo $siteId; ?>" class="fa-plus-circle">Añadir Exepci&oacute;n</a></li>
								</ul>
								
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
    
    <style type="text/css">
	    #codigo { 
	        position: relative;
	        top: 0;
	        right: 0;
	        bottom: 0;
	        left: 0;
	        height:400px;
	    }
	</style>
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.8/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
		jQuery(document).ready(function($){
			<?php if($iduser > 0){ ?>
			$("#user").val(<?php echo $iduser; ?>).trigger("change");
			<?php } ?>
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
</html>
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